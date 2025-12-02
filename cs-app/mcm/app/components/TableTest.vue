<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import EyeClosedIcon from '@bitrix24/b24icons-vue/button/EyeClosedIcon'
import Settings4Icon from '@bitrix24/b24icons-vue/actions/Settings4Icon'
import SearchIcon from '@bitrix24/b24icons-vue/button/SearchIcon'
import MenuIcon from '@bitrix24/b24icons-vue/main/MenuIcon'
import PencilDrawIcon from '@bitrix24/b24icons-vue/actions/PencilDrawIcon'
import CopyPlatesIcon from '@bitrix24/b24icons-vue/actions/CopyPlatesIcon'
import OpenedEyeIcon from '@bitrix24/b24icons-vue/main/OpenedEyeIcon'
import TrashBinIcon from '@bitrix24/b24icons-vue/main/TrashBinIcon'
import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import type { ButtonProps } from '@bitrix24/b24ui-nuxt/types/index.ts'
import dayjs from 'dayjs'

export interface ExampleProps {
  contentAlign?: 'start' | 'center' | 'end'
  contentSide?: 'top' | 'right' | 'bottom' | 'left'
  contentSideOffset?: number
  color?: ButtonProps['color']
}

const props = withDefaults(defineProps<ExampleProps>(), {
  contentAlign: 'start',
  contentSide: 'right',
  contentSideOffset: 8,
  color: 'primary'
})

const itemsBtn = [
  [
    {
      label: 'Просмотр',
      icon: OpenedEyeIcon
    },
    {
      label: 'Копировать',
      icon: CopyPlatesIcon
    },
    {
      label: 'Редактировать',
      icon: PencilDrawIcon
    }
  ],
  [
    {
      label: 'Деактивировать',
      color: 'danger' as const,
      icon: TrashBinIcon
    }
  ]
]

const { t } = useI18n()

const searchQuery = ref('')
const selectedFields = ref<Record<string, boolean>>({})
const selectedAction = ref<string | null>(null)
const selectedDateRange = ref<string>('this_week')
const dateFrom = ref<string | null>(null)
const dateTo = ref<string | null>(null)
let $b24: B24Frame
const tableData = ref<any[]>([])
const totalItems = ref(0)
const currentPage = ref(1)
const itemsPerPage = ref(10)
const sortBy = ref<string | null>(null)
const sortOrder = ref<'ASC' | 'DESC'>('DESC')
const isLoading = ref(false)

const eventsList = {
  All: 'Все события',
  ChannelSubscription: 'Подписка на канал',
  ChannelUNSubscribe: 'Отписка от канала',
  BotActivation: 'Активация бота',
  CarSearch: 'Поиск автомобиля',
  CarTracked: 'Добавление авто в отслеживание',
  ApplicationSubmitted: 'Заявка',
}
const applicationType = {
  Credit: 'Заявка на кредит',
  Call: 'Заявка на звонок'
}
const items = ref([
  ...Object.keys(eventsList).map(key => ({
    value: key,
    label: eventsList[key]
  }))
])

const dateRangeOptions = ref([
  { value: 'all', label: 'За все время' },
  { value: 'today', label: 'Сегодня' },
  { value: 'yesterday', label: 'Вчера' },
  { value: 'this_week', label: 'За неделю' },
  { value: 'this_month', label: 'За месяц' },
  { value: 'custom', label: 'Интервал' }
])

const fields = ref<Record<string, string>>({})

const loadFields = async () => {
  if (!$b24) return

  const response = await $b24.callMethod('entity.item.property.get', {
    ENTITY: 'events'
  })

  const data = response.getData().result

  const fieldMap: Record<string, string> = {
    ID: 'ID',
    DATE_CREATE: 'Дата создания'
  }

  data.forEach((item: any) => {
    fieldMap[item.PROPERTY] = item.NAME
  })

  fields.value = fieldMap
}

const loadTableData = async () => {
  if (!$b24) return

  isLoading.value = true
  tableData.value = []

  const actionFilter = selectedAction.value === 'All' ? undefined : selectedAction.value
  const dateFilter: Record<string, string> = {}

  if (selectedDateRange.value === 'today') {
    dateFilter['>=DATE_CREATE'] = dayjs().startOf('day').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().endOf('day').toISOString()
  } else if (selectedDateRange.value === 'yesterday') {
    dateFilter['>=DATE_CREATE'] = dayjs().subtract(1, 'day').startOf('day').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().subtract(1, 'day').endOf('day').toISOString()
  } else if (selectedDateRange.value === 'this_week') {
    dateFilter['>=DATE_CREATE'] = dayjs().startOf('week').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().endOf('week').toISOString()
  } else if (selectedDateRange.value === 'this_month') {
    dateFilter['>=DATE_CREATE'] = dayjs().startOf('month').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().endOf('month').toISOString()
  } else if (selectedDateRange.value === 'custom' ) {
    if (dateFrom.value){
      dateFilter['>=DATE_CREATE'] = dayjs(dateFrom.value).startOf('day').toISOString()
    }
    if(dateTo.value){
      dateFilter['<=DATE_CREATE'] = dayjs(dateTo.value).endOf('day').toISOString()
    }
  }
  console.log($b24.placement, "placement")
  console.log($b24.placement.options.ID, "placementID")

  if($b24.placement.title === "CRM_LEAD_DETAIL_TAB" && $b24.placement.options.ID){
    const getItem = await $b24.callMethod("crm.lead.get", { id: $b24.placement.options.ID })
    const arItem = getItem.getData().result
    console.log(arItem, 'arItem')
    console.log(arItem.UF_CRM_CS_TG_ID_USER, 'arItem.UF_CRM_CS_TG_ID_USER')
    if( arItem.UF_CRM_CS_TG_ID_USER){
      dateFilter['PROPERTY_telegram_id'] = arItem.UF_CRM_CS_TG_ID_USER
    }
  }

  console.log(dateFilter,'dateFilter')
  const eventsAll = $b24.fetchListMethod(
      'entity.item.get',
      {
        ENTITY: 'events',
        filter: {
          PROPERTY_action: actionFilter,
          ...dateFilter
        },
        sort: { ID: 'ASC' }
      },
      'ID'
  )

  const allItems: any[] = []

  for await (const chunk of eventsAll) {
    if (Array.isArray(chunk)) {
      allItems.push(...chunk)
    }
  }

  tableData.value = allItems
  totalItems.value = allItems.length
  isLoading.value = false

}

const sortTable = (field: string) => {
  if (sortBy.value === field) {
    sortOrder.value = sortOrder.value === 'ASC' ? 'DESC' : 'ASC'
  } else {
    sortBy.value = field
    sortOrder.value = 'ASC'
  }
}

const filterTableData = () => {
  if (searchQuery.value.length < 3) {
    return tableData.value
  }

  const query = searchQuery.value.toLowerCase()

  return tableData.value.filter(item => {
    return Object.values(item).some(value => {
      if (typeof value === 'string' || typeof value === 'number') {
        return value.toString().toLowerCase().includes(query)
      }
      return false
    })
  })
}

const paginatedData = computed(() => {
  if (!Array.isArray(tableData.value)) {
    return []
  }

  const filteredData = filterTableData()

  const sortedData = [...filteredData].sort((a, b) => {
    if (!sortBy.value) return 0

    const fieldA = a[sortBy.value] || a.PROPERTY_VALUES?.[sortBy.value] || ''
    const fieldB = b[sortBy.value] || b.PROPERTY_VALUES?.[sortBy.value] || ''

    const isNumeric = !isNaN(parseFloat(fieldA)) && !isNaN(parseFloat(fieldB))

    if (isNumeric) {
      return sortOrder.value === 'ASC'
          ? parseFloat(fieldA) - parseFloat(fieldB)
          : parseFloat(fieldB) - parseFloat(fieldA)
    } else {
      return sortOrder.value === 'ASC'
          ? fieldA.localeCompare(fieldB)
          : fieldB.localeCompare(fieldA)
    }
  })

  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return sortedData.slice(start, end)
})

onMounted(async () => {
  $b24 = await initializeB24Frame()
  await loadFields()

  if (Object.keys(fields.value).length > 0) {
    selectedFields.value = Object.keys(fields.value).reduce((acc, key) => {
      const defaultVisibleFields = [
        'ID',
        'DATE_CREATE',
        'telegram_id',
        'username',
        'subscription_date',
        'application_date',
        'action',
        'bot_id',
      ]

      acc[key] = defaultVisibleFields.includes(key)
      return acc
    }, {} as Record<string, boolean>)
  }

  await loadTableData()
})

const visibleFields = computed(() => {
  return Object.entries(selectedFields.value)
      .filter(([key, visible]) => visible)
      .map(([key]) => key)
})

const toggleField = (field: string) => {
  selectedFields.value[field] = !selectedFields.value[field]
}

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / itemsPerPage.value)
})
</script>
<style>
.table-container {
  width: 100%;
  overflow-x: auto;
}
tbody tr:first-child td:first-child {
  border-top-left-radius: 12px;
}

tbody tr:first-child td:last-child {
  border-top-right-radius: 12px;
}

tbody tr:last-child td:first-child {
  border-bottom-left-radius: 12px;
}

tbody tr:last-child td:last-child {
  border-bottom-right-radius: 12px;
}

tbody tr td {
  padding: 0.3rem;
}
</style>


<template>
  <div class="p-6">
    <!-- Селект для фильтра по ACTION -->
    <div class="flex flex-row justify-start items-center gap-2 mb-4 w-fit">
      <B24Select
          v-model="selectedAction"
          :items="items"
          placeholder="Выберите действие"
          class="max-w-max w-auto"
          @change="loadTableData"
      />
      <B24Select
          v-model="selectedDateRange"
          :items="dateRangeOptions"
          placeholder="Выберите дату"
          class="w-auto"

      />
      <div v-if="selectedDateRange === 'custom'" class="flex items-center gap-2">
        <B24Input
            v-model="dateFrom"
            type="date"
            placeholder="От"
            class="w-36"
        />
        <B24Input
            v-model="dateTo"
            type="date"
            placeholder="До"
            class="w-36"
        />
      </div>
    </div>
    <div class="mb-4 mt-4 flex flex-row w-full justify-end">
      <B24Button
          :label="$t('page.list.ui.search')"
          :icon="SearchIcon"
          :color="color"
          @click="loadTableData"

      />

    </div>
    <div class="flex justify-between mb-4">
      <!-- Поле поиска -->
      <B24Input
          v-model="searchQuery"
          type="search"
          :icon="SearchIcon"
          :placeholder="$t('page.list.ui.searchInput.placeholder')"
          class="vac-input mr-8 w-[360px]"
          rounded
      />
      <div class="flex flex-row gap-2">
        <B24Modal
            title="Настройка полей"
            description="Выберите поля для показа"
            :close-icon="EyeClosedIcon"
        >
          <B24Button :icon="Settings4Icon" />
          <template #body>
            <div class="grid grid-cols-3 gap-2">
              <div v-for="(label, field) in fields" :key="field">
                <label class="flex items-center gap-2">
                  <input
                      type="checkbox"
                      v-model="selectedFields[field]"
                      class="form-checkbox"
                  />
                  {{ label }}
                </label>
              </div>
            </div>
          </template>
        </B24Modal>
      </div>
    </div>
    <B24TableWrapper v-if="tableData && tableData.length" class="overflow-x-auto w-full">
      <table class="w-full border-collapse">
        <thead>
        <tr>
          <th >Действия</th>
          <th
              v-for="field in visibleFields"
              :key="field"
              @click="sortTable(field)"
              class="cursor-pointer "
          >
            {{ fields[field] }}
            <span v-if="sortBy === field">
                {{ sortOrder === 'ASC' ? ' ↑' : ' ↓' }}
             </span>
          </th>
        </tr>
        </thead>
        <tbody class="rounded-md">
        <tr v-for="(item, index) in paginatedData" :key="item.ID" class="bg-white">
          <td >
            <B24DropdownMenu
                arrow
                :items="itemsBtn"
                :content="content"
            >
              <B24Button color="link" depth="dark" :icon="MenuIcon" class="border-0" />
            </B24DropdownMenu>
          </td>
          <td v-for="field in visibleFields" :key="field">
            {{
              field === 'DATE_CREATE'
                  ? dayjs(item[field]).format('DD.MM.YYYY HH:mm') || '-'
                  : field === 'action'
                      ? eventsList[item.PROPERTY_VALUES?.[field]] || '-'
                      : field === 'application_type'
                          ? applicationType[item.PROPERTY_VALUES?.[field]] || '-'
                          : item.PROPERTY_VALUES?.[field] || item[field] || '-'
            }}
          </td>
        </tr>
        </tbody>
      </table>
    </B24TableWrapper>
    <div v-else class="w-full">
      <div class="grid gap-2 grid-cols-3">
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />
        <B24Skeleton class="h-6 w-auto" />

      </div>
    </div>
    <div class="flex justify-between items-center mt-4">
      <div class="text-sm text-gray-600">
        Показано {{ paginatedData?.length || 0 }} из {{ totalItems || 0 }} записей
      </div>
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600">
         Страница {{ currentPage }} из {{ totalPages }}
        </span>
        <B24Select
            v-model="itemsPerPage"
            :items="[
            { value: 5, label: '5' },
            { value: 10, label: '10' },
            { value: 25, label: '25' },
            { value: 50, label: '50' }
         ]"
            class="w-20"
        />
        <B24Button
            :disabled="currentPage <= 1"
            @click="currentPage--"
        >
          Назад
        </B24Button>
        <B24Button
            :disabled="currentPage * itemsPerPage >= totalItems"
            @click="currentPage++"
        >
          Вперед
        </B24Button>
      </div>
    </div>
  </div>
</template>