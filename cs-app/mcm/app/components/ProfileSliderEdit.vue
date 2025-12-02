<script setup lang="ts">
import { ref, onMounted, defineProps, defineEmits, computed } from 'vue'
import { initializeB24Frame } from '@bitrix24/b24jssdk'
import { useNuxtApp } from '#app'
import { wappGet, getChatListTG, getUserContactsTG } from '@/services/cs-wappi'

const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()

const props = defineProps<{
  profile: {
    id: string
    name: string
    admins?: string
    users?: string
    code?: string
    CS_BLOCK_GROUPS_ALL?: string
    CS_BLOCK_BOTS?: string
    CS_BLOCK_GROUP?: string
    CS_BLOCK_USERS?: string
    dateCloseText?: string
  }
  onRefresh?: () => void
}>()

const emit = defineEmits(['close', 'refresh-profiles'])

const description = ref('Изменения в профиль внесены')
const descriptionError = ref('Произошла ошибка при изменении профиля. Попробуйте снова.')

const isLoading = ref(false)
const isSuccess = ref(false)
const isError = ref(false)

const users = ref<{ label: string; value: string }[]>([])
const users2 = ref<{ label: string; value: string }[]>([])
const chanal = ref<{ label: string; value: string }[]>([])
const contacts = ref<{ label: string; value: string }[]>([])

const searchQueryGroups = ref('')
const searchQueryContacts = ref('')
const currentUser = ref<any>(null)

const state = ref({
  profileId: props.profile.id,
  profileName: props.profile.name,
  profileCode: props.profile.code || '',
  profile: props.profile.profile,
  groups: [] as { label: string; value: string }[],
  contacts: [] as { label: string; value: string }[],
  managers: [] as { label: string; value: string }[],
  admins: [] as { label: string; value: string }[],
  bots: props.profile.CS_BLOCK_BOTS === 'Блокировка ботов для Б24',
  groupsAll: props.profile.CS_BLOCK_GROUPS_ALL === 'Блокировка всех групп для Б24',
  closeDate: '',
})
//profile.dateCloseText
const filteredGroups = computed(() => {
  if (!searchQueryGroups.value || searchQueryGroups.value.length < 3) {
    return chanal.value.slice(0, 20)
  }

  const query = searchQueryGroups.value.toLowerCase()
  return chanal.value
      .filter(contact => contact.label.toLowerCase().includes(query))
      .slice(0, 20)
})

const filteredContacts = computed(() => {
  if (!searchQueryContacts.value || searchQueryContacts.value.length < 3) {
    return contacts.value.slice(0, 20)
  }

  const query = searchQueryContacts.value.toLowerCase()
  return contacts.value
      .filter(contact => contact.label.toLowerCase().includes(query))
      .slice(0, 20)
})

// Проверяем права доступа к полю календаря
const canShowDateField = computed(() => {
  if (!currentUser.value) return false
  const lastName = currentUser.value.LAST_NAME
  return lastName === 'Черкасов' || lastName === 'support'
})

async function loadUsers() {
  if ($b24) {
    

    let start = 0 // Начальная позиция для выборки
    const allUsers: any[] = [] // Массив для хранения всех пользователей

    while (true) {
      // Вызываем метод user.get с параметром start
      const res = await $b24.callMethod('user.get', {
        filter: {
          USER_TYPE: 'employee',
          ACTIVE: 'Y',
        },
      }, start)

      // Получаем данные из ответа
      const data = res.getData().result

      // Добавляем пользователей в общий массив
      allUsers.push(...data)

      // Проверяем, есть ли еще записи
      if (data.length < 50) {
        // Если записей меньше 50, значит, это последняя страница
        break
      }

      // Увеличиваем start для следующей страницы
      start += 50
    }

    // Преобразуем данные в нужный формат
    users.value = allUsers.map((user: any) => ({
      label: `${user.NAME || ''} ${user.LAST_NAME || ''}`.trim(),
      value: String(user.ID),
    }))



  }
}

async function initSelects() {
  if (users.value.length === 0) {
    await loadUsers()
  }

  if (props.profile.admins) {
    try {
      const admins = JSON.parse(props.profile.admins)
      state.value.admins = users.value.filter(user => admins.includes(String(user.value)))
    } catch (e) {
      console.error('Ошибка парсинга admins:', e)
    }
  }

  if (props.profile.users) {
    try {
      const usersList = JSON.parse(props.profile.users)
      state.value.managers = users.value.filter(user => usersList.includes(String(user.value)))
    } catch (e) {
      console.error('Ошибка парсинга users:', e)
    }
  }
}

async function handleClick() {
  isLoading.value = true
  isSuccess.value = false
  isError.value = false

  try {
    const adminsValues = state.value.admins.map(item => item.value)
    const usersValues = state.value.managers.map(item => item.value)

    // Подготавливаем базовые поля для обновления
    const propertyValues: any = {
      CS_ADMIN: JSON.stringify(adminsValues),
      CS_USERS: JSON.stringify(usersValues),
      CS_BLOCK_GROUPS_ALL: state.value.groupsAll ? 'Блокировка всех групп для Б24' : '',
      CS_BLOCK_BOTS: state.value.bots ? 'Блокировка ботов для Б24' : '',
      CS_BLOCK_GROUP: JSON.stringify(state.value.groups.map(g => g.value)),
      CS_BLOCK_USERS: JSON.stringify(state.value.contacts.map(c => c.value)),
    }

    // Если заполнено поле календаря, добавляем дату закрытия
    if (state.value.closeDate) {
      // Преобразуем дату в timestamp
      const timestamp = Math.floor(new Date(state.value.closeDate).getTime() / 1000).toString()
      propertyValues.CS_DATE_CLOSE = timestamp
      propertyValues.CS_DATE_CLOSE_FACT = null
    }

    const itemUp = await $b24.callMethod(
        'entity.item.update',
        {
          ENTITY: 'setup_messager',
          ID: state.value.profileId,
          NAME: state.value.profileName,
          PROPERTY_VALUES: propertyValues,
        }
    )

    console.log('Результат обновления:', itemUp)
    isSuccess.value = true
    setTimeout(() => {
      emit('close', false)
      props.onRefresh?.()
    }, 5000)
  } catch (error) {
    console.error('Ошибка при обновлении:', error)
    isError.value = true
    setTimeout(() => {
      isError.value = false
    }, 5000)
  } finally {
    isLoading.value = false
  }
}

async function getCurrentUser() {
  if ($b24) {
    try {
      // Получаем текущего пользователя
      const res = await $b24.callMethod('user.current')
      const user = res.getData().result
      
      // Сохраняем данные пользователя для использования в компоненте
      currentUser.value = user
      
      return user
    } catch (error) {
      console.error('Ошибка при получении текущего пользователя:', error)
    }
  }
}


onMounted(async () => {
  // Выводим данные профиля, переданные в форму
  //logProfileData()
  
  await getCurrentUser()
  await loadUsers()
  await initSelects()

  const groups = await getChatListTG(props.profile.profile)
  const userContacts = await getUserContactsTG(props.profile.profile)

  chanal.value = groups
  contacts.value = userContacts

  if (props.profile.CS_BLOCK_GROUP) {
    try {
      const groupIds = JSON.parse(props.profile.CS_BLOCK_GROUP)
      state.value.groups = groups.filter(g => groupIds.includes(g.value))
    } catch (e) {
      console.error('Ошибка парсинга CS_BLOCK_GROUP:', e)
    }
  }

  if (props.profile.CS_BLOCK_USERS) {
    try {
      const contactIds = JSON.parse(props.profile.CS_BLOCK_USERS)
      state.value.contacts = contacts.value.filter(c => contactIds.includes(c.value))
    } catch (e) {
      console.error('Ошибка парсинга CS_BLOCK_USERS:', e)
    }
  }
})
</script>
<template>
  <B24Slideover
      :title="profile.name"
      :description="profile.name"
      :close="{ onClick: () => emit('close') }"
      :b24ui="{
     content: 'max-w-[90%] md:max-w-1/2',
     body: 'm-5 p-5 bg-white dark:bg-white/10 rounded'
    }"
  >
    <template #body>
      <div class="content-container">
        <B24Form class="flex flex-col gap-4">
          <div class="flex flex-col gap-4 mb-1">
            <div class="text-5xs">Код профиля</div>
            <div class="text-xs">{{ state.profileCode }}</div>
            <div class="text-xs">{{ state.profile }}</div>
          </div>

          <B24FormField label="Название профиля" name="profile_name">
            <B24Input v-model="state.profileName" class="w-full" />
          </B24FormField>

          <B24FormField label="Менеджеры" name="managers">
            <B24SelectMenu v-model="state.managers" multiple :items="users" class="w-full" />
          </B24FormField>

          <B24FormField label="Администраторы" name="admins">
            <B24SelectMenu v-model="state.admins" multiple :items="users" class="w-full" />
          </B24FormField>

          <B24Switch
              v-model="state.bots"
              color="primary"
              label="Отключить Боты"
              description="Сообщения от ботов не будут попадать в открытые линии"
              size="sm"
          />
          <B24Switch
              v-model="state.groupsAll"
              color="primary"
              label="Отключить группы"
              description="Сообщения от групп не будут попадать в Открытые линии"
              size="sm"
          />

          <B24FormField label="Отключить Группы от открытых линий" name="groups">
            <div class="relative">
              <B24Input
                  v-model="searchQueryGroups"
                  placeholder="Поиск..."
                  class="mb-2"
              />
              <B24SelectMenu
                  v-model="state.groups"
                  multiple
                  :items="filteredGroups"
                  class="w-full"
              />
            </div>
          </B24FormField>

          <B24FormField label="Отключить Контакты от открытых линий" name="contacts">
            <div class="relative">
              <B24Input
                  v-model="searchQueryContacts"
                  placeholder="Поиск..."
                  class="mb-2"
              />
              <B24SelectMenu
                  v-model="state.contacts"
                  multiple
                  :items="filteredContacts"
                  class="w-full"
              />
            </div>
          </B24FormField>

          <!-- Поле календаря для даты закрытия (только для определенных пользователей) -->
          <B24FormField 
              v-if="canShowDateField" 
              :label="props.profile.dateCloseText || 'Дата закрытия профиля'" 
              name="closeDate"
          >
            <B24Input
                v-model="state.closeDate"
                type="date"
                class="w-full"
                placeholder="Выберите дату закрытия"
            />
          </B24FormField>

          <B24Separator class="mt-6 mb-3" />

          <B24Button
              v-if="!isLoading && !isSuccess"
              @click="handleClick"
              label="Отправить"
              color="success"
              class="self-end"
          />
        </B24Form>
      </div>

      <div v-if="isSuccess" class="mb-4 flex flex-wrap items-center justify-start gap-4">
        <B24Advice
            angle="top"
            :description="description"
            :avatar="{ src: '/images/avatar/employee.png' }"
        />
      </div>

      <div v-if="isError" class="mb-4 flex flex-wrap items-center justify-start gap-4">
        <B24Advice
            angle="top"
            :description="descriptionError"
            :avatar="{ src: '/images/avatar/employee.png' }"
        />
      </div>
    </template>

    <template #footer>
      <div class="flex gap-2">
        <B24Button
            rounded
            label="Закрыть"
            color="link"
            depth="dark"
            @click="emit('close')"
        />
      </div>
    </template>
  </B24Slideover>
</template>