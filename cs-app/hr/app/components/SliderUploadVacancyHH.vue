<script setup lang="ts">
import { ref, defineProps, defineEmits, onMounted, watch } from 'vue'
import { initializeB24Frame } from '@bitrix24/b24jssdk'
import { useNuxtApp } from '#app'
import { getHhApi, tokenUserHH, vacancyAddHh } from '@/services/cs_hh'
import { searchUsersSetup, searchUsers, editUsersSetup } from '@/tools/cs-main'

const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth
const authData = authManager.getAuthData()

const title = ref('Импорт вакансий HH')
const descriptionSlader = ref('')
const token = ref('')
const manager = ref([])
const managerSetup = ref([])
let company_id = ''
const vacancy = ref([])
const items = ref([])
const value = ref(null)
const selectedVacancies = ref<Record<number, boolean>>({})
const valueActive = ref('active')
const placeholder = ref('Выберите ответственного')

const props = defineProps<{
  userId: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const itemsActive = ref([
  {
    label: 'Активные',
    value: 'active'
  },
  {
    label: 'Архивные',
    value: 'archived',
  },
])
const property=ref({})
const response = await $b24.callMethod(
    'entity.item.property.get',
    { ENTITY: 'ads_control', });
property.value = response.getData();

const fetchToken = async () => {
  try {
    const accessToken = await tokenUserHH(props.userId, $b24)
    token.value = accessToken
    const resManager = await getHhApi('/me', token.value)
    company_id = resManager.employer.id

    const resManagers = await getHhApi(`/employers/${company_id}/managers`, token.value)

    manager.value = resManagers.items
    items.value = resManagers.items.map(item => ({
      label: item.full_name,
      value: item.id
    }))

  } catch (error) {
    console.error('Ошибка получения токена:', error)
    token.value = ''
    manager.value = null
  }
}
const fetchUsersSetup = async () => {
  try {
    const resManagerSetup = await searchUsersSetup($b24)
    console.log(managerSetup.value,'managerSetup.value')
    const managerId = Object.entries(resManagerSetup.hh).reduce((acc, [key, value]) => {
      acc[value.manager_id] = key;
      return acc;
    }, {} as Record<string, string>);
    managerSetup.value = managerId
    console.log(managerSetup.value, 'managerSetup.value');
  } catch (error) {
    console.error('Ошибка получения пользователь:', error)
    managerSetup.value = []
  }
}
const fetchVacancy = async (manager_id: number) => {
  if (!manager_id) return
  const status = valueActive.value
  const resVacancy = await getHhApi(`/employers/${company_id}/vacancies/${status}?manager_id=${manager_id.value}`, token.value)
  vacancy.value = resVacancy.items
}

const toggleField = (id: number) => {
  selectedVacancies.value = {
    ...selectedVacancies.value,
    [id]: !selectedVacancies.value[id]
  }
}

const saveSelectedVacancies = async () => {
  const selectedIds = Object.keys(selectedVacancies.value)
      .filter(id => selectedVacancies.value[Number(id)])
      .map(id => Number(id));

  console.log('Выбранные ID вакансий:', selectedIds);

  const active = valueActive.value === 'active' ? 'Y' : 'N';

  const vacancyAdd = await vacancyAddHh(
      $b24,
      token.value,
      managerSetup.value,
      selectedIds,
      active
  );

  console.log(vacancyAdd, 'vacancyAdd');
};

onMounted(() => {
  fetchToken()
  fetchUsersSetup()
})

watch([() => value.value, () => valueActive.value], ([newVal, newStatus]) => {
  if (newVal && newStatus) {
    fetchVacancy(newVal)
  }
})
</script>

<template>
  <B24Slideover
      :title="title"
      :description="descriptionSlader"
      :close="{ onClick: () => emit('close') }"
      :b24ui="{
     content: 'max-w-[90%] md:max-w-1/2',
     body: 'm-5 p-5 bg-white dark:bg-white/10 rounded'
    }"
  >
    <template #body>
      <div class="mb-2">
        <B24Select
            v-model="valueActive"
            :items="itemsActive"
            class="w-full"
        />
      </div>
      <div class="mb-2">
        <B24SelectMenu
            v-model="value"
            :items="items"
            :placeholder="placeholder"
            class="w-full"
        />
      </div>
      <div class="mt-4">
        <B24TableWrapper
            class="overflow-x-auto w-full"
        >
          <table class="w-full">
            <thead>
            <tr>
              <th>#</th>
              <th>ID</th>
              <th>Название вакансии</th>
              <th>Город</th>
              <th>ФИО менеджера</th>
              <th>Тип вакансии</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(vacancy, index) in vacancy" :key="vacancy.id">
              <td>
                <input
                    type="checkbox"
                    :checked="selectedVacancies[vacancy.id] || false"
                    @click="toggleField(vacancy.id)"
                    class="form-checkbox"
                />
              </td>
              <td>{{ vacancy.id }}</td>
              <td>{{ vacancy.name }}</td>
              <td>{{ vacancy.address?.city }}</td>
              <td>{{ vacancy.manager?.last_name }}</td>
              <td>{{ vacancy.vacancy_properties?.appearance?.title }}</td>
            </tr>
            </tbody>
          </table>
        </B24TableWrapper>
      </div>
      <div class="mt-4">
        <B24Button
            @click="saveSelectedVacancies"
            color="primary"
        >
          Сохранить
        </B24Button>
      </div>
    </template>
  </B24Slideover>
</template>