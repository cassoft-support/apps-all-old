<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'
import * as locales from '@bitrix24/b24ui-nuxt/locale'
import type { Collections } from '@nuxt/content'
import { B24Icon } from '@bitrix24/b24icons-vue';
import {
  initializeB24Frame,
  LoggerBrowser,
  B24Frame,
  EnumCrmEntityTypeId,
  Text,
} from '@bitrix24/b24jssdk';

const isShowDebug = ref(false);
const isLoading = ref(true);
const toast = useToast();
const overlay = useOverlay();
import { ProfileSliderActivation } from '#components'
const profileSliderActivation = overlay.create(ProfileSliderActivation); // Убедитесь, что ProfileSliderActivation импортирован
 const { locale, t, defaultLocale } = useI18n()
 const dir = computed(() => locales[locale.value]?.dir || 'ltr')
 const contentCollection = computed<keyof Collections>(() => `contentActivities_${locale.value.length > 0 ? locale.value : defaultLocale}`)
const $logger = LoggerBrowser.build('MyApp', import.meta.env?.DEV === true);
let $b24: B24Frame;

const user = ref(null);
const profileAll = ref([]);
const dataList = ref([]);

// Функция для форматирования даты
function formatSubscriptionEndDate(timestamp: string): string {
  const date = new Date(Number(timestamp) * 1000);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return `Дата окончания подписки: ${date.toLocaleDateString('ru-RU', options)}`;
}

// Асинхронная функция для получения дополнительных данных
async function fetchAdditionalData(profileId: string): Promise<any> {
  const token = '785026ea43c1bb0b1b842189cbca9197c05f424e';
  const myHeaders = new Headers();
  myHeaders.append("Authorization", `Bearer ${token}`);

  const reqOptions = {
    method: 'GET',
    headers: myHeaders,
    redirect: 'follow',
  };

  const response = await fetch(`https://wappi.pro/api/sync/get/status?profile_id=${profileId}`, reqOptions);
  console.log('wappi')
  console.log(response)
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  return response.json();
}

onMounted(async () => {
  try {
    $b24 = await initializeB24Frame();
    const authManager = $b24.auth;
    const authData = authManager.getAuthData();

    // Получение текущего пользователя
    const userResponse = await $b24.callMethod('user.current');
    user.value = userResponse.getData().result;

    // Получение списка компаний
    const response = await $b24.callBatch({
      CompanyList: {
        method: 'crm.item.list',
        params: {
          entityTypeId: EnumCrmEntityTypeId.company,
          order: { id: 'desc' },
          select: ['id', 'title', 'createdTime'],
        },
      },
    }, true);

    const data = response.getData();
    dataList.value = (data.CompanyList.items || []).map((item) => ({
      id: Number(item.id),
      title: item.title,
      createdTime: Text.toDateTime(item.createdTime),
    }));

    // Получение профилей
    const resProfile = await fetch('https://app.cassoft.ru/local/CSlibs/classes/app/mcm/functionVue.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        member_id: authData.member_id,
        fn: 'profileCsMcm',
      }),
    });

    if (!resProfile.ok) {
      throw new Error(`HTTP error! status: ${resProfile.status}`);
    }

    const arProfile = await resProfile.json();
    profileAll.value = await Promise.all((arProfile || []).map(async (item) => {
      let additionalData = null;
      if (item.UF_PROFILE_ID && !item.UF_DATE_CLOSE_FACT) {
        additionalData = await fetchAdditionalData(item.UF_PROFILE_ID);
        console.log(additionalData)
      }
      return {
        id: item.UF_PROFILE_ID,
        connector: item.UF_CONNECTOR,
        csCode: item.UF_CS_CODE,
        dateClose: item.UF_DATE_CLOSE,
        dateCloseFact: item.UF_DATE_CLOSE_FACT,
        dateCreate: item.UF_DATE_CREATE,
        line: item.UF_LINE_B24,
        name: item.UF_NAME,
        profile: item.UF_PROFILE_ID,
        profileName: item.UF_PROFILE_NAME,
        resource: item.UF_RESOURCE,
        status: item.UF_STATUS_B24,
        type: item.UF_TYPE,
        createdTime: formatSubscriptionEndDate(item.UF_DATE_CLOSE),
        activeLine:additionalData, // Добавляем дополнительные данные
      };
    }));

  } catch (error) {
    console.error('Ошибка при получении данных:', error);
  }
});

// Функции для активации и деактивации
async function makeActive(profile: IActivity): Promise<void> {
  await profileSliderActivation.open({ profile });
}

async function makeDeActive(profile: IActivity): Promise<void> {
  await profileSliderActivation.open({ profile });
}


</script>

<template>
  <NuxtLayout name="menu">
  <div>
<!--    <template #header-title-page>-->
      <ProseH1 class="mt-3 mb-10 max-lg:ps-3">
        {{ $t('page.settingProfile.title') }}
      </ProseH1>
<!--    </template>-->
    <div
        v-if="profileAll.length"
        class="grid grid-cols-[repeat(auto-fill,minmax(310px,1fr))] gap-sm"
    >
      <template v-for="profile in profileAll" :key="profile.id">
        <div
            class="relative bg-white dark:bg-white/10 p-sm2 cursor-pointer rounded-md flex flex-row gap-sm border-2 transition-shadow shadow hover:shadow-lg hover:border-primary"
            :class="[
              !profile?.dateCloseFact ? 'border-collab-500 dark:border-collab-400' : 'border-base-master/10 dark:border-base-100/20'
            ]"

        >
          <div
              v-if="!profile?.dateCloseFact"
              class="absolute -top-2 ltr:right-3 rtl:left-3 rounded-full bg-collab-500 dark:bg-collab-400 size-5 text-white flex items-center justify-center"
          >
            <CheckIcon class="size-md" />
          </div>

          <div v-if="profile?.type === 'whatsapp'" class="rounded w-16 h-16">
            <B24Icon
                :name="'Social::WhatsappIcon'"
                :class="{
     'size-15': true,
     'text-collab-600': !profile?.dateCloseFact
    }"
            />
          </div>

          <div v-else-if="profile?.type === 'telegram'" class="rounded w-16 h-16">
            <B24Icon
                :name="'Social::TelegramInCircleIcon'"
                :class="{
     'size-15': true,
     'text-blue-600': !profile?.dateCloseFact
    }"
            />
          </div>
          <div class="w-full flex flex-col items-start justify-between gap-2">
            <div>
              <div v-if="profile.profileName" class="font-b24-secondary text-black dark:text-base-150 text-h4 leading-4 mb-xs font-semibold line-clamp-1">
                <div>{{ profile.profileName }}</div>
              </div>
              <div v-if="profile.dateClose" class="mb-2 w-full flex flex-row flex-wrap items-start justify-start gap-2">
                <B24Badge
                    size="xs"
                    :label=profile.createdTime
                />
              </div>
              <div v-if="profile.description" class="font-b24-primary text-sm text-base-500 line-clamp-2">
                <div>{{ profile.description }}</div>
              </div>
            </div>
            <div class="w-full flex flex-row gap-1 items-center justify-end">
              <B24Button
                  v-if="profile?.dateCloseFact"
                  size="xs"
                  rounded
                  :label="$t('page.list.ui.make.active')"
                  color="primary"
                  loading-auto
                  @click.stop="async () => { return makeActive(profile) }"
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
      </template>
    </div>
    <!-- Вывод списка компаний -->

  </div>
    </NuxtLayout >
</template>

