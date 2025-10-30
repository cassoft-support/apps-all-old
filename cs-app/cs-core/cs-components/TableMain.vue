<script setup lang="ts">
import { ref, computed, onMounted, defineProps, defineEmits } from 'vue'
import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import { B24Input, B24Button, B24TableWrapper, B24DropdownMenu } from '@bitrix24/b24ui'
import { EyeClosedIcon, Settings4Icon, SearchIcon, MenuIcon } from '@bitrix24/b24icons-vue'

export interface TableMainProps {
entity: string
itemsBtn: Array<{ label: string, icon?: any, color?: string }>
defaultVisibleFields?: string[]
}

const props = defineProps<TableMainProps>()
const emit = defineEmits(['row-click'])

const searchQuery = ref('')
const selectedFields = ref<Record<string, boolean>>({})
const tableData = ref<any[]>([])
const fields = ref<Record<string, string>>({})
let $b24: B24Frame

// Загрузка полей
const loadFields = async () => {
if (!$b24) return

const response = await $b24.callMethod('entity.item.property.get', {
ENTITY: props.entity
})

const data = response.getData().result
const fieldMap: Record<string, string> = {}
data.forEach((item: any) => {
fieldMap[item.PROPERTY] = item.NAME
})

fields.value = fieldMap
}

// Загрузка данных
const loadTableData = async () => {
if (!$b24) return

const response = await $b24.callBatch({
List: {
method: 'entity.item.get',
params: {
ENTITY: props.entity,
PARAMS: {
FILTER: {
SEARCH: searchQuery.value
}
}
}
}
}, true)

tableData.value = response.getData().List
}

// Инициализация
onMounted(async () => {
$b24 = await initializeB24Frame()
await loadFields()

if (Object.keys(fields.value).length > 0) {
selectedFields.value = Object.keys(fields.value).reduce((acc, key) => {
acc[key] = props.defaultVisibleFields?.includes(key) || false
return acc
}, {} as Record<string, boolean>)
}

await loadTableData()
})

// Видимые поля
const visibleFields = computed(() => {
return Object.entries(selectedFields.value)
.filter(([key, visible]) => visible)
.map(([key]) => key)
})

// Переключение поля
const toggleField = (field: string) => {
selectedFields.value[field] = !selectedFields.value[field]
}
</script>

<template>
  <div class="p-6">
    <!-- Поле поиска -->
    <div class="mb-4">
      <B24Input
          v-model="searchQuery"
          type="search"
          :icon="SearchIcon"
          placeholder="Поиск..."
          class="vac-input"
          rounded
      />
    </div>

    <!-- Кнопки -->
    <div class="flex justify-between mb-4">
      <B24Button
          label="Поиск"
          :icon="SearchIcon"
          @click="loadTableData"
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

    <!-- Таблица -->
    <B24TableWrapper class="overflow-x-auto w-full">
      <table class="w-full border-collapse">
        <thead>
        <tr>
          <th>Действия</th>
          <th v-for="field in visibleFields" :key="field" class="cursor-pointer">
            {{ fields[field] }}
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, index) in tableData" :key="item.ID">
          <td>
            <B24DropdownMenu
                arrow
                :items="props.itemsBtn"
                :content="{ align: 'start', side: 'right', sideOffset: 8 }"
            >
              <B24Button color="link" depth="dark" :icon="MenuIcon" />
            </B24DropdownMenu>
          </td>
          <td v-for="field in visibleFields" :key="field">
            {{ item.PROPERTY_VALUES?.[field] || '-' }}
          </td>
        </tr>
        </tbody>
      </table>
    </B24TableWrapper>
  </div>
</template>