<script setup lang="ts">
import { ref, defineProps, defineEmits, withDefaults, computed } from 'vue'
import { initializeB24Frame } from '@bitrix24/b24jssdk'
import { useNuxtApp } from '#app'
import { handlerAdd } from '@/services/cs_hh'

export interface ExampleProps {
  size?: 'sm' | 'md' | 'lg'
  activity?: {
    userId: string
    userSettings: any
  }
}

const props = withDefaults(defineProps<ExampleProps>(), {
  size: 'md'
})
const { activity } = props
console.log(activity.userSettings.access_token,'activity')
const emit = defineEmits(['close', 'refresh-profiles'])

const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth
const authData = authManager.getAuthData()

const descriptionSlader = ref('')
const title = ref('Подписка на события')

const events = ref([
  {
    id: 'VACANCY_PUBLICATION_FOR_VACANCY_MANAGER',
    name: 'Публикация вакансии',
    description: 'Событие присылается только для менеджера-владельца вакансии при создании вакансии, отложенной публикации или публикации из черновика'
  },
  {
    id: 'VACANCY_PROLONGATION',
    name: 'Продление вакансии',
    description: ''
  },
  {
    id: 'VACANCY_CHANGE',
    name: 'Изменение вакансии',
    description: 'Аккумулирует изменения, внесенные за несколько последних секунд, и отправляет вебхук, содержащий время последнего изменения. Если вы внесете два изменения с разницей в одну секунду, сервис отправит только один вебхук, который будет содержать время последнего изменения. Если изменение одно, сервис отправит вебхук с задержкой в несколько секунд'
  },
  {
    id: 'VACANCY_ARCHIVATION',
    name: 'Архивация вакансии',
    description: ''
  },
  {
    id: 'NEW_RESPONSE_OR_INVITATION_VACANCY',
    name: 'Новый отклик или приглашение на вакансию',
    description: 'Данное событие будет вызываться как на отклик со стороны соискателя, так и на приглашение со стороны работодателя'
  },
  {
    id: 'NEW_NEGOTIATION_VACANCY',
    name: 'Новый отклик на вакансию',
    description: 'Данное событие будет вызываться только на отклик со стороны соискателя'
  },
  {
    id: 'NEGOTIATION_EMPLOYER_STATE_CHANGE',
    name: 'Перемещение резюме кандидата на другой этап вакансии',
    description: 'Содержит следующую информацию: кто переместил резюме; идентификатор резюме; исходный статус резюме; новый статус резюме'
  }
])

const selectedEvents = ref(
    events.value.reduce((acc, event) => {
      acc[event.id] = false
      return acc
    }, {} as Record<string, boolean>)
)


const allSelected = computed({
  get() {
    return Object.values(selectedEvents.value).every(value => value === true)
  },
  set(value: boolean) {
    handleSelectAll(value)
  }
})
const handleSelectAll = (value: boolean) => {
  selectedEvents.value = {
    ...selectedEvents.value,
    ...Object.keys(selectedEvents.value).reduce((acc, key) => {
      acc[key] = value
      return acc
    }, {})
  }
}


  const handleSubscribe = async () => {
    const selected = Object.entries(selectedEvents.value).filter(([_, value]) => value === true)

    if (selected.length === 0) {
      alert('Выберите хотя бы одно событие')
      return
    }

    const handler = await handlerAdd(authData.member_id, activity.userId)
    console.log(handler, 'handler')

    const eventsWithoutSettings = [
      'VACANCY_PUBLICATION_FOR_VACANCY_MANAGER',
      'VACANCY_PROLONGATION',
      'VACANCY_ARCHIVATION'
    ]

    const data = {
      actions: selected.map(([id]) => {
        const requiresSettings = !eventsWithoutSettings.includes(id)
        return {
          type: id,
          ...(requiresSettings && {
            settings: {
              vacancies_only_mine: false
            }
          })
        }
      }),
      url: handler
    }



    console.log('Подписка на события:', data)

    try {
      const response = await fetch('https://api.hh.ru/webhook/subscriptions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${activity.userSettings.access_token}`
        },
        body: JSON.stringify(data)
      })
      console.log(response, 'response')
      if (response.ok) {
        alert('Подписка успешно создана')
          emit('close')
  emit('refresh-profiles')
      } else {
        alert('Ошибка при создании подписки')
      }
    } catch (error) {
      console.error('Ошибка:', error)
      alert('Произошла ошибка при создании подписки')
    }
  }



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
      <div class="mb-4 hidden ">
        <B24Checkbox
            label="Выбрать все"
            :modelValue="allSelected"
            @change="handleSelectAll"
        />
      </div>

      <div class="space-y-4">
        <div v-for="event in events" :key="event.id">
          <B24Checkbox
              :label="event.name"
              :description="event.description"
              v-model="selectedEvents[event.id]"
          />
        </div>
      </div>

      <div class="mt-6 flex justify-end">
        <B24Button
            color="primary"
            depth="normal"
            @click="handleSubscribe"
        >
          Подписаться
        </B24Button>
      </div>
    </template>
  </B24Slideover>
</template>