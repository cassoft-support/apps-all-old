<script setup lang="ts">
import { reactive, ref, computed, onMounted } from 'vue'
import * as locales from '@bitrix24/b24ui-nuxt/locale'
import { useNuxtApp } from '#app'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useOverlay } from '#imports'
//import { useOverlay } from '#imports'

const overlay = useOverlay()
const props = defineProps<{
  profile: IActivity
  onRefresh?: () => void
}>()
console.log(props, 'props') // должно выводиться
console.log(props.profile, 'profile') // должно выводиться


const { locale, t, defaultLocale } = useI18n()
const dir = computed(() => locales[locale.value]?.dir || 'ltr')
const contentCollection = computed(() => `contentActivities_${locale.value.length > 0 ? locale.value : defaultLocale}`)

const description = ref('Профиль создан, закройте владку, привяжите профиль к мессенджеу и к открытым линиям')
const descriptionError = ref('Произошла ошибка при создании профиля. Попробуйте снова.')
const profileName = ref('')
const profileKey = ref('')
const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth
const authData = authManager.getAuthData()
console.log($b24.placement.options.CONNECTOR, 'b24.placement')
const profileType = ref('Whatsapp') // объявляем вне условия

// if ($b24.placement.options.CONNECTOR) {
//   profileType.value = $b24.placement.options.CONNECTOR === 'cs_mcm_whatsapp1' ? 'Whatsapp' : 'Telegram'
// } else {
//   profileType.value = 'Whatsapp'
// }
//console.log(profileType,'profileType')
const items = ref(['Whatsapp', 'Telegram'])
const router = useRouter()

const isLoading = ref(false)
const isSuccess = ref(false)
const isError = ref(false)

// ✅ Установка значений по умолчанию для B24RadioGroup
const orientation = ref('horizontal')
const variant = ref('card')
const indicator = ref('hidden')
const size = ref('lg')
const color = ref('primary')

// Функция для закрытия слайдера через 15 секунд
function closeSliderAfterDelay() {
  setTimeout(() => {
    overlay.close()
  }, 2000)
}

// Функция для скрытия ошибки через 5 секунд
function hideErrorAfterDelay() {
  setTimeout(() => {
    isError.value = false
  }, 2000)
}

async function handleClick() {
  isLoading.value = true
  isSuccess.value = false
  isError.value = false

  try {
    await ProfileAdd(profileName.value, profileKey.value)
    isSuccess.value = true
    closeSliderAfterDelay()
  } catch (error) {
    console.error('Ошибка:', error)
    isError.value = true
    hideErrorAfterDelay()
  } finally {
    isLoading.value = false
  }
}

async function ProfileAdd(name: string, key: string) {


  if (authData) {
    try {

        const itemAdd = await $b24.callMethod(
            'entity.item.add',
            {
              ENTITY: 'setup_messager',
              DATE_ACTIVE_FROM: new Date(),
              NAME: name,
              PROPERTY_VALUES: {
                CS_PROFILE_NAME: name,
                CS_KEY_AUTO: key,
                CS_RESOURCE: 'auto.ru',
                CS_TYPE: 'auto_ru',
              },
            }
        )

        const resItemAdd = itemAdd.getData().result

        if (resItemAdd) {
          console.log(resItemAdd, 'itemAdd')
          // Вызов функции обновления
          props.onRefresh?.()
          return true
        } else {
          throw new Error('Ошибка при добавлении профиля.')
        }

    } catch (error) {
      throw error
    }
  } else {
    throw new Error('Данные аутентификации истекли или недоступны.')
  }
}
</script>
<template>
  <B24Slideover
      title="Создание нового профиля"
      :close="{ onClick: () => emit('close', false) }"
      :b24ui="{
     content: 'max-w-[90%] md:max-w-1/2',
     body: 'm-5 p-5 bg-white dark:bg-white/10 rounded'
    }"
  >
    <template #body>
      <div class="mt-12">
        <B24Form class="flex flex-col gap-4">

          <B24FormField label="Название профиля" name="profile_name">
            <B24Input v-model="profileName" class="w-full" />
          </B24FormField>
          <B24FormField label="Вставьте ключ auto.ru" name="profile_key">
            <B24Input v-model="profileKey" class="w-full" />
          </B24FormField>

          <!-- Кнопка отправки -->
          <B24Button
              v-if="!isLoading && !isSuccess"
              @click="handleClick"
              label="Отправить"
              color="success"
              class="self-end"
          />

          <!-- Информационный блок при успехе -->
          <div v-if="isSuccess" class="mb-4 flex flex-wrap items-center justify-start gap-4">
            <B24Advice
                angle="top"
                :description="description"
                :avatar="{ src: '/imges/avatar/employee.png' }"
            />
          </div>

          <!-- Информационный блок при ошибке -->
          <div v-if="isError" class="mb-4 flex flex-wrap items-center justify-start gap-4">
            <B24Advice
                angle="top"
                :description="descriptionError"
                :avatar="{ src: '/imges/avatar/employee.png' }"
            />
          </div>
        </B24Form>
      </div>
    </template>

    <template #footer>
      <div class="flex gap-2">
        <B24Button
            rounded
            :label="$t('component.activity.item.slider.close')"
            color="link"
            depth="dark"
            @click="emit('close', false)"
        />
      </div>
    </template>
  </B24Slideover>
</template>
