<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import * as locales from '@bitrix24/b24ui-nuxt/locale'
import type { Collections } from '@nuxt/content'
import { B24Icon } from '@bitrix24/b24icons-vue'
import type { ButtonProps } from '@bitrix24/b24ui-nuxt/types/index.ts'
import * as csMain from '@/tools/cs-main'
import * as csWappi from '@/services/cs-wappi'
import { wappGet } from '@/services/cs-wappi'
import {
  initializeB24Frame,
  LoggerBrowser,
  B24Frame,
} from '@bitrix24/b24jssdk'
import { useToast, useOverlay } from '#imports'
import { ProfileSliderActivation, ProfileSliderAddProfile, ProfileSliderEdit, ProfileSliderActivTelegram } from '#components'

export interface ExampleProps {
  color?: ButtonProps['color']
  isRounded?: boolean
}

withDefaults(defineProps<ExampleProps>(), {
  color: 'primary',
  isRounded: true
})

const isShowDebug = ref(false)
const isLoading = ref(true)
const toast = useToast()
const overlay = useOverlay()

const profileSliderEdit = overlay.create(ProfileSliderEdit)
const profileSliderActivation = overlay.create(ProfileSliderActivation)
const profileSliderActivTelegram = overlay.create(ProfileSliderActivTelegram)
const profileSliderAddProfile = overlay.create(ProfileSliderAddProfile)

const { locale, t, defaultLocale } = useI18n()
const dir = computed(() => locales[locale.value]?.dir || 'ltr')
const contentCollection = computed<keyof Collections>(() => `contentActivities_${locale.value.length > 0 ? locale.value : defaultLocale}`)
const $logger = LoggerBrowser.build('MyApp', import.meta.env?.DEV === true)
let $b24: B24Frame

const profileAll = ref([])

// Асинхронная функция для получения дополнительных данных
async function profileStatus(profileId: string, type: string): Promise<any> {
  let typeUrl = ''
  if(type === 'Whatsapp'){
    typeUrl = '/api'
  }else{
    typeUrl = '/tapi'
  }
  console.log(profileId, typeUrl)
  const result = await wappGet(`${typeUrl}/sync/get/status?profile_id=${profileId}`)

  console.log(result)
  return result
}

// Функция для обновления профилей
async function refreshProfiles() {
  try {
    const setupMesGet = await $b24.callMethod('entity.item.get', {
      entity: 'setup_messager',
      filter: { ACTIVE: 'Y' }
    })

    const setup = setupMesGet.getData().result
    const imopenlinesGet = await $b24.callBatch({
      OpenLines: {
        method: 'imopenlines.config.list.get',
        params: {
          PARAMS: { order: { ID: 'ASC' }, filter: { ACTIVE: 'Y' } },
          OPTIONS: { QUEUE: 'Y' }
        }
      }
    }, true);

    const imopenlinesData = imopenlinesGet.getData().OpenLines as LineItem[];

// ✅ Создаём объект: ID -> LINE_NAME
    const linesMap = imopenlinesData.reduce((acc, item) => {
      acc[item.ID] = item.LINE_NAME;
      return acc;
    }, {} as Record<string, string>);

    console.log(linesMap);
    profileAll.value = await Promise.all(
        setup.map(async item => {
          const values = item.PROPERTY_VALUES

          const profileId = values.CS_PROFILE_ID
          const line = values.CS_LINE || ''
          const type = values.CS_TYPE || ''
          let resProfile = false
          let authorized = false
          let phoneProfile = ''


          return {
            id: item.ID,
            connector: values.CS_CONNECTOR,
            csCode: values.CS_CODE,
            dateClose: values.CS_DATE_CLOSE,
            dateCloseFact: values.CS_DATE_CLOSE_FACT,
            dateCreate: values.CS_DATE_CREATE,
            line: line,
            lineLabel: linesMap[line],
            name: item.NAME,
            profile: profileId,
            profileName: values.CS_PROFILE_NAME,
            resource: values.CS_RESOURCE,
            resourceProfile: resProfile,
            status: values.CS_STATUS,
            type: type,
            admins: values.CS_ADMIN,
            users: values.CS_USERS,
            dateCloseText: csMain.formatSubscriptionEndDate(values.CS_DATE_CLOSE),
            chatId: values.CS_PROFILE_NAME,
          }
        })
    )
  } catch (error) {
    console.error('Ошибка при обновлении профилей:', error)
  }
}

onMounted(async () => {
  try {
    $b24 = await initializeB24Frame()
    await refreshProfiles()
  } catch (error) {
    console.error('Ошибка при получении данных:', error)
  }
})

// Функции для активации и деактивации
async function makeActive(profile: any): Promise<void> {
  await profileSliderActivation.open({ profile })
}
async function makeActiveTelegram(profile: any): Promise<void> {
  await profileSliderActivTelegram.open({ profile })
}

async function editProfile(profile: any): Promise<void> {
  await profileSliderEdit.open({ profile, onRefresh: refreshProfiles })
}

async function makeDeActive(profile: any): Promise<void> {
  await profileSliderActivation.open({ profile }) // Предположим, что у вас есть компонент для деактивации
}

async function makeProfileAdd(): Promise<void> {
  await profileSliderAddProfile.open({ onRefresh: refreshProfiles })
}
</script>
<style>
.ui-page-slider-wrapper-default-theme {
  background: #eef2f400 !important;
}
</style>
<template>
  <NuxtLayout name="menu">
    <div class="ml-6">
      <ProseH1 class="mt-3 mb-10 max-lg:ps-3">
        {{ $t('page.settingProfile.title') }}
      </ProseH1>

      <div
          v-if="profileAll.length"
          class="grid grid-cols-[repeat(auto-fill,minmax(310px,1fr))] gap-sm"
      >
        <template v-for="profile in profileAll" :key="profile.id">
          <div
              class="relative bg-white dark:bg-white/10 p-sm2 cursor-pointer rounded-md flex flex-row gap-sm border-2 transition-shadow shadow hover:shadow-lg hover:border-primary"
              :class="[
             !profile?.dateCloseFact ? 'border-gray-300 dark:border-gray-150' : 'border-base-master/10 dark:border-base-100/20'
            ]"
          >
            <div
                class="absolute -top-2 ltr:right-3 rtl:left-3 rounded-full size-4 text-white flex items-center justify-center"
                :class="[
                profile?.activeLine ? 'bg-collab-500 dark:bg-collab-400' : 'bg-red-500 dark:bg-red-500'
             ]"
            >
              <CheckIcon class="size-xs" />
            </div>
            <div class="flex flex-col justify-between items-start w-12 h-full">
              <div v-if="profile?.type === 'Whatsapp'" class="rounded w-12 h-12">
                <B24Icon
                    :name="'Social::WhatsappIcon'"
                    :class="{
                    'size-15': true,
                    'text-collab-600': !profile?.dateCloseFact
                 }"
                />
              </div>

              <div v-else-if="profile?.type === 'Telegram'" class="rounded w-12 h-12">
                <B24Icon
                    :name="'Social::TelegramInCircleIcon'"
                    :class="{
                    'size-15': true,
                    'text-blue-600': !profile?.dateCloseFact
                 }"
                />
              </div>

              <div  class="rounded w-6 h-6 shadow-md rounded">
                <B24Icon name="Main::EditMenuIcon" class="w-6 h-6 color-gray-400"
                         @click.stop="async () => { return editProfile(profile) }"
                />
              </div>
            </div>
            <div class="w-full flex flex-col items-start justify-between gap-2">
              <div>
                <div v-if="profile.name" class="font-b24-secondary text-black dark:text-base-150 text-h4 leading-6 mb-xs font-semibold line-clamp-1">
                  <div>{{ profile.name }}</div>
                </div>
                <div v-if="profile.phone" class="mb-2 w-full flex flex-row flex-wrap items-start justify-start gap-2">
                  <div class="text-xs">{{ profile.phone }}</div>
                </div>
                <div v-if="profile.dateCloseText" class="mb-2 w-full flex flex-row flex-wrap items-start justify-start gap-2">
                  <div class="text-4xs">{{ profile.dateCloseText }}</div>
                </div>
                <div class="font-b24-primary text-sm text-base-500 line-clamp-2">
                  <div>Битрикс24</div>
                </div>
                <div v-if="profile.line" class="mb-2 w-full flex flex-row flex-wrap items-start justify-start gap-2">
                  <B24Badge
                      size="xs"
                      :label="profile.lineLabel"
                  />
                </div>
              </div>
              <div class="w-full flex flex-row justify-end">
                <div  class="w-full flex flex-row gap-1 items-center justify-end">
                  <B24Button
                      v-if="profile && !profile.activeLine"
                      size="xs"
                      rounded
                      :label="$t('page.list.ui.make.active')"
                      color="primary"
                      loading-auto
                      @click.stop="profile.type === 'Whatsapp' ? makeActive(profile) : makeActiveTelegram(profile)"
                  />
                  <B24Button
                      v-else
                      size="xs"
                      rounded
                      :label="$t('page.list.ui.make.deactive')"
                      color="default"
                      depth="light"
                      loading-auto
                      @click.stop="async () => { return makeDeActive(profile) }"
                  />

                </div>

              </div>
            </div>
          </div>
        </template>
      </div>

      <B24Container class="mt-12">
        <div class="w-full flex flex-col items-center justify-center">
          <p class="text-h2 mb-6">Создание нового профиля</p>
          <div class="w-full flex flex-row gap-1 items-center justify-center">
            <B24Button
                label="Создать"
                size="lg"
                rounded
                color="primary"
                loading-auto
                @click.stop="async () => { return makeProfileAdd() }"
            />
          </div>
        </div>
      </B24Container>
    </div>
  </NuxtLayout>
</template>

