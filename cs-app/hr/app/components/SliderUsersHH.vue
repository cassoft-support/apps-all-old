<script setup lang="ts">
import { ref, defineProps, defineEmits, withDefaults, computed, onMounted } from 'vue'
import { useNuxtApp } from '#app'
import { getHhApi, tokenUserHH } from '@/services/cs_hh'
import { searchUsersSetup, searchUsers, editUsersSetup } from '@/tools/cs-main'

const emit = defineEmits(['close', 'refresh-profiles'])
const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth
const authData = authManager.getAuthData()

const title = ref('Пользователи HH')
const descriptionSlader = ref('')
const token = ref('')
const managerHH = ref([])
const managerSetup = ref([])
let company_id = ''
const items = ref([])
const value = ref(null)
const selectedManagersMap = ref<Record<string, number | null>>({}) // Хранит выбранные значения

const props = defineProps<{
  userId: string
}>()

const fetchUsersHH = async () => {
  try {
    const accessToken = await tokenUserHH(props.userId, $b24)
    token.value = accessToken
    const resManager = await getHhApi('/me', token.value)
    company_id = resManager.employer.id

    const resManagers = await getHhApi(`/employers/${company_id}/managers`, token.value)
    managerHH.value = resManagers.items.map(item => ({
      id: item.id,
      full_name: item.full_name,
      position: item.position
    }))
    console.log(managerHH,'managerHH')
  } catch (error) {
    console.error('Ошибка получения токена:', error)
    token.value = ''
    managerHH.value = null
  }
}

const fetchUsersSetup = async () => {
  try {
    const resManagerSetup = await searchUsersSetup($b24)
    managerSetup.value = resManagerSetup
    console.log(managerSetup.value,'managerSetup.value')
  } catch (error) {
    console.error('Ошибка получения пользователь:', error)
    managerSetup.value = []
  }
}

const fetchUsers = async () => {
  try {
    const resManager = await searchUsers($b24)
    items.value = resManager.map(item => ({
      label: `${item.LAST_NAME} ${item.NAME} ${item.SECOND_NAME}`,
      value: item.ID
    }))
  } catch (error) {
    console.error('Ошибка получения пользователь:', error)
    managerSetup.value = []
  }
}

interface SavedUser {
  id: number
  manager_id: number
  position: string
}

interface SavedUser {
  manager_id: number
  position: string
}

const saveUserSetup = (): void => {
  const result: Record<string, { manager_id: number; position: string }> = {}
console.log(selectedManagersMap,'selectedManagersMap')

  Object.entries(selectedManagersMap.value).forEach(([managerId, selectedId]) => {
    // Проверяем, что selectedId — это объект и у него есть value
    if (!selectedId || selectedId.value === null) return
    const manager = managerHH.value.find(m => m.id === managerId)
    if (!manager) return
    // Используем selectedId.value как ключ
    result[selectedId.value] = {
      manager_id: manager.id,
      position: manager.position
    }
  })

  console.log('Результат:', result)

  if (Object.keys(result).length > 0) {
    editUsersSetup($b24, result, 'hh')
        .then(() => {
          console.log('Данные успешно сохранены')
          fetchUsersSetup()
        })
        .catch((error) => {
          console.error('Ошибка сохранения:', error)
        })
  } else {
    console.warn('Нет выбранных пользователей для сохранения')
  }
}
onMounted(() => {
  fetchUsersSetup()
  fetchUsersHH()
  fetchUsers()
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
      <div class="space-y-4">
        <div v-for="manager in managerHH" :key="manager.id" class="flex items-center justify-between border-b pb-2">
          <div>
            <p class="font-medium">{{ manager.full_name }}</p>
            <p class="text-sm text-gray-500">{{ manager.position }}</p>
          </div>
          <div class="w-64">
            <B24SelectMenu
                v-model="selectedManagersMap[manager.id]"
                :items="items"
                class="w-full"
            />
          </div>
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <B24Button
            color="primary"
            depth="normal"
            @click="saveUserSetup"
        >
          Сохранить
        </B24Button>
      </div>
    </template>
  </B24Slideover>
</template>