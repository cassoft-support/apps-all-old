<script setup lang="ts">
import "/assets/css/cs-root.css";
import "/assets/css/cs-messengers.css";
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import * as locales from '#b24ui/locale';
import { wappGet } from '@/services/cs-wappi';
import { LoggerBrowser, LoggerType } from '@bitrix24/b24jssdk';
import { B24Icon } from '@bitrix24/b24icons-vue';
import { useNuxtApp } from '#app';
import { useRouter } from 'vue-router';
import {
  getThumbnailSrc,
  formatDate,
  formatSubscriptionEndDate,
  authCheck,
} from '@/services/cs-main';

const logger = LoggerBrowser.build('MCM', import.meta.env?.DEV === true);
logger.info('>> start >>>');
if (process.env.NODE_ENV === 'development') {
  logger.enable(LoggerType.log);
}

const router = useRouter();
const contactData = ref({});
const contactName = ref('');
const contactPhone = {};
const isExpanded = ref(false);
const { $initializeB24Frame } = useNuxtApp();
const $b24 = await $initializeB24Frame();
const authManager = $b24.auth;
const authData = authManager.getAuthData();
var processedData ={}
// 🔹 Блок 1: Получение профилей (processProfile)
async function processProfile() {
  const resUser = await $b24.callMethod('user.current');
  const user = resUser.getData().result;

  interface SetupMessagerItem {
    PROPERTY_VALUES: {
      CS_PROFILE_ID: string;
      CS_LINE: string;
      CS_TYPE: string;
    };
  }

  interface LineItem {
    ID: string;
    LINE_NAME: string;
    QUEUE?: string[];
  }

  interface ProfileLineEntry {
    key: string;
    value: string | null;
    type: string;
  }

  const setupMesGet = await $b24.callMethod(
      'entity.item.get',
      {
        entity: 'setup_messager',
        filter: { ACTIVE: 'Y' }
      }
  );

  const setup = setupMesGet.getData().result as SetupMessagerItem[];
  const profileLineMap = new Map<string, { line: string; type: string }>(
      setup
          .filter(item => {
            const { CS_PROFILE_ID, CS_LINE, CS_TYPE } = item.PROPERTY_VALUES;
            return CS_PROFILE_ID && CS_LINE && CS_TYPE;
          })
          .map(item => [
            item.PROPERTY_VALUES.CS_PROFILE_ID,
            {
              line: item.PROPERTY_VALUES.CS_LINE,
              type: item.PROPERTY_VALUES.CS_TYPE
            }
          ])
  );

  const imopenlinesGet = await $b24.callBatch({
    OpenLines: {
      method: 'imopenlines.config.list.get',
      params: {
        PARAMS: { order: { ID: 'ASC' }, filter: {} },
        OPTIONS: { QUEUE: 'Y' }
      }
    }
  }, true);

  const imopenlinesData = imopenlinesGet.getData().OpenLines as LineItem[];
  const filteredimopenlinesData = imopenlinesData.filter(config => {
    return config.QUEUE && config.QUEUE.includes(user.ID);
  });

  const arProfiles: ProfileLineEntry[] = Array.from(profileLineMap.entries()).map(
      ([profileKey, { line, type }]) => {
        const lineData = filteredimopenlinesData.find(item => item.ID === line);
        const lineName = lineData?.LINE_NAME || null;

        return {
          key: profileKey,
          value: lineName,
          type: type
        };
      }
  );

  return arProfiles;
}

// 🔹 Блок 2: Обработка телефонов (processContactPhone)
async function processContactPhone(contactPhone: any, resProfiles: any) {
  if (!contactPhone || Object.keys(contactPhone).length === 0) return {};

  const result = {};
console.log(contactPhone,'contactPhone')
  for (const contactKey in contactPhone) {

    const phones = contactPhone[contactKey];
console.log(phones)
    for (const number of phones) {
      const phoneValue = number.VALUE;

      if (!result[contactKey]) result[contactKey] = {};
      if (!result[contactKey][phoneValue]) result[contactKey][phoneValue] = {};

      for (const profile of resProfiles) {
        if (profile.type === 'Whatsapp') {
          const profileCode = profile.key;

          const urlCheck = `/api/sync/contact/check?profile_id=${profileCode}&phone=${phoneValue}`;
          const response = await wappGet(urlCheck);

          if (response.on_whatsapp === true) {
            const urlUser = `/api/sync/contact/info?profile_id=${profileCode}&user_id=${phoneValue}`;
            const resUser = await wappGet(urlUser);

            const whatsappData = {
              name: resUser.profile.contact.PushName || resUser.profile.contact.BusinessName,
              phone: phoneValue,
              logo: resUser.profile.thumbnail
                  ? getThumbnailSrc(resUser.profile.thumbnail)
                  : "https://app.cassoft.ru/local/images/avatar/no-avatar.jpg",
              profileName: profile.value,
              profileType: profile.type,
              profileCode: profile.key
            };

            result[contactKey][phoneValue][profileCode] = {
              whatsapp: whatsappData
            };
           // logger.log(resUser);
          } else {
            result[contactKey][phoneValue]['no'] = {
              whatsapp: 'no'
            };
          }
        }
      }
    }
  }

  return result;
}

// 🔹 Блок 3: Получение контактов в зависимости от сущности
if (authData) {
  const resAuthCheck = await authCheck(authData.member_id);
  if (resAuthCheck === "Y") {
    const options = $b24.placement.options;

    if ($b24.placement.title === "USERFIELD_TYPE" && options.ENTITY_ID === "CRM_CONTACT") {
      try {
        const resContact = await $b24.callMethod('crm.contact.get', { id: options.ENTITY_VALUE_ID });
        const arContact = resContact.getData().result;
        if(arContact.PHONE && Array.isArray(arContact.PHONE)) {
          const arName = `Контакт ${arContact.NAME || ''} ${arContact.LAST_NAME || ''}`.trim();
          contactPhone[arName] = arContact.PHONE;
        }
        if (Object.keys(contactPhone).length > 0) {
          const resProfiles = await processProfile();
           processedData = await processContactPhone(contactPhone, resProfiles);
          contactData.value = processedData;
        } else {
          router.push('/close');
        }
      } catch (error) {
        console.error('Ошибка при получении контакта:', error);
      }
    }

    else if ($b24.placement.title === "USERFIELD_TYPE" && options.ENTITY_ID === "CRM_LEAD") {
      try {
        const resLead = await $b24.callMethod('crm.lead.get', {id: options.ENTITY_VALUE_ID});
        const arLead = resLead.getData().result;
        if(arLead.PHONE && Array.isArray(arLead.PHONE)) {
          const arName = `Контакт ${arLead.NAME || ''} ${arLead.LAST_NAME || ''}`.trim();
          contactPhone[arName] = arLead.PHONE;
        }
        if (Object.keys(contactPhone).length > 0) {
          const resProfiles = await processProfile();
// Вызываем функцию и ждём результат
          processedData = await processContactPhone(contactPhone, resProfiles);
// Записываем результат в contactData.value
          contactData.value = processedData;
          console.log(contactData, 'contactData');
        } else {
          router.push('/close');
        }
      } catch (error) {
        console.error('Ошибка при получении контакта:', error);
      }
    }
    else if ($b24.placement.title === "USERFIELD_TYPE" && options.ENTITY_ID === "CRM_COMPANY") {
      try {
        const resCompany = await $b24.callMethod('crm.company.get', { id: options.ENTITY_VALUE_ID });
        const arCompany = resCompany.getData().result;
        console.log(arCompany,'arCompany')
        if(arCompany.PHONE && Array.isArray(arCompany.PHONE)) {
          const arName = `Компания ${arCompany.TITLE || ''}`.trim();
          contactPhone[arName] = arCompany.PHONE;
        }
        const resContactCompany = await $b24.callMethod('crm.company.contact.items.get', { id: options.ENTITY_VALUE_ID });
        const arContactCompany = resContactCompany.getData().result;

        if (arContactCompany && Array.isArray(arContactCompany)) {
          for (const contactId of arContactCompany) {
            const resContact = await $b24.callMethod('crm.contact.get', { id: contactId.CONTACT_ID });
            const arContact = resContact.getData().result;
            if(arContact.PHONE && Array.isArray(arContact.PHONE)) {
              const arName = `Контакт ${arContact.NAME || ''} ${arContact.LAST_NAME || ''}`.trim();
              contactPhone[arName] = arContact.PHONE;
            }
          }
        }

        if (Object.keys(contactPhone).length > 0) {
          const resProfiles = await processProfile();
          processedData = await processContactPhone(contactPhone, resProfiles);
          contactData.value = processedData;
        } else {
          router.push('/close');
        }
      } catch (error) {
        console.error('Ошибка при получении контакта:', error);
      }
    }
    else if ($b24.placement.title === "USERFIELD_TYPE" && options.ENTITY_ID === "CRM_DEAL") {
      try {
        const resDeal = await $b24.callMethod('crm.deal.get', {id: options.ENTITY_VALUE_ID});
        const arDeal = resDeal.getData().result;
        const resContactDeal = await $b24.callMethod('crm.deal.contact.items.get', {id: options.ENTITY_VALUE_ID});
        const arContactDeal = resContactDeal.getData().result;

        if (arContactDeal && Array.isArray(arContactDeal)) {
          for (const contactId of arContactDeal) {
            const resContact = await $b24.callMethod('crm.contact.get', {id: contactId.CONTACT_ID});
            const arContact = resContact.getData().result;
            if (arContact.PHONE && Array.isArray(arContact.PHONE)) {
              const arName = `Контакт ${arContact.NAME || ''} ${arContact.LAST_NAME || ''}`.trim();
              contactPhone[arName] = arContact.PHONE;
            }
          }
        } else if (arDeal.COMPANY_ID) {
          const resCompany = await $b24.callMethod('crm.company.get', {id: arDeal.COMPANY_ID});
          const arCompany = resCompany.getData().result;
          if (arCompany.PHONE && Array.isArray(arCompany.PHONE)) {
            const arName = `Компания ${arCompany.TITLE || ''}`.trim();
            contactPhone[arName] = arCompany.PHONE;
          }
        }


        if (Object.keys(contactPhone).length > 0) {
          const resProfiles = await processProfile();
           processedData = await processContactPhone(contactPhone, resProfiles);
          contactData.value = processedData;
        } else {
          router.push('/close');
        }
      } catch (error) {
        console.error('Ошибка при получении контакта:', error);
      }
    }
  } else {
    console.log('Данные аутентификации истекли или недоступны.');
  }
}

// 🔹 Блок 4: Обработка клика и открытия слайдера
function handleClick(phone: any) {
  $b24.slider.openPath(
      $b24.slider.getUrl(`/marketplace/view/cassoft.mcm/?params=${encodeURIComponent(JSON.stringify(phone))}`),
      950
  ).then((response) => {
    if (!response.isOpenAtNewWindow && response.isClose) {
      console.log('Slider is closed! Reinit the application');
    }
  });
}

// 🔹 Блок 5: Локализация и тема
const { locale, setLocale, t, defaultLocale } = useI18n();

definePageMeta({
  layout: 'clear'
});

useHead({
  title: t('page.index.seo.title')
});

const colorMode = useColorMode();

const isDark = computed({
  get() {
    return colorMode.value === 'dark';
  },
  set() {
    colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark';
  }
});

const dir = computed(() => locales[locale.value]?.dir || 'ltr');

onMounted(() => {
  if (locale.value?.length < 1) {
    setLocale(defaultLocale);
  }
});

// 🔹 Блок 6: Изменение размера окна Bitrix24
function resizeWindow() {
// Создаём скрипт для загрузки API Bitrix24
  const script = document.createElement('script');
  script.src = 'https://api.bitrix24.com/api/v1/';
  script.onload = () => {
    if (typeof BX24 !== 'undefined' && typeof BX24.resizeWindow === 'function') {
      nextTick(() => {
        // Получаем размеры окна с учётом содержимого
        const { scrollWidth, scrollHeight } = BX24.getScrollSize();
        // Добавляем небольшой отступ для корректного отображения
        BX24.resizeWindow(scrollWidth, scrollHeight + 50);
      });
    } else {
      console.error('Метод BX24.resizeWindow не найден');
    }
  };
  script.onerror = () => {
    console.error('Ошибка загрузки скрипта BX24');
  };
  document.head.appendChild(script);
}

watch(isExpanded, async () => {
  console.log(isExpanded, 'isExpanded')
  await nextTick();
  resizeWindow();
});

onMounted(() => {
  resizeWindow();
});
</script>
<style>
body{
background: #fff!important;
}
</style>
<template>
  <div v-if="contactData && Object.keys(contactData).length > 0">
    <!-- Перебираем все ключи контактов -->
    <template v-for="(phoneData, contactKey, index) in contactData" :key="contactKey">
      <!-- Отображаем только первый контакт по умолчанию -->
      <template v-if="index === 0 || isExpanded">
        <div class="text-black text-base ml-5 mt-5 mb-5">
          {{ contactKey }}
        </div>

        <!-- Перебираем все номера телефонов -->
        <template v-for="(profileData, phoneNumber) in phoneData" :key="phoneNumber">
          <div class="mb-2 rounded-md p-2">
            <div class="text-slate-400 text-xs mr-2">Номер:</div>
            <div class="font-gray-600 text-sm mb-2">
              {{ phoneNumber }}
            </div>

            <div class="rounded-md border-gray-50 border-1 p-2">
              <!-- Перебираем профили, в которых этот номер был найден -->
              <template v-for="(profileInfo, profileCode) in profileData" :key="profileCode">
                <div class="flex flex-row items-center justify-between mb-2">
                  <div v-if="profileCode !== 'no'" class="mr-5 flex flex-row items-center">
                    <div class="text-slate-400 text-xs mr-2">Открытая линия:</div>
                    <div class="">{{ profileInfo.whatsapp.profileName }}</div>
                  </div>
                  <div v-else class="mr-5 flex flex-row items-center">
                    <div class="text-slate-400 text-xs mr-2">Подключение не найдено:</div>
                  </div>

                  <div>
                    <!-- Если нет в WhatsApp -->
                    <B24Icon v-if="profileInfo.whatsapp === 'no'" name="Common-service::WhatsappIcon" class="w-10 h-10 text-slate-400" />

                    <!-- Если есть в WhatsApp -->
                    <div v-else class="flex flex-row items-center">
                      <div class="vac-room-container">
                        <div
                            class="cs-crm-avatar position_relative cursor-pointer w-[22px] h-[22px]"
                            :style="{ backgroundImage: `url(${profileInfo.whatsapp.logo})` }"
                            @click="handleClick(profileInfo.whatsapp)"
                        >
                          <svg width="18" height="18" viewBox="0 0 22 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="platform_avatar" style="color: rgb(35, 187, 134);">
                            <path
                                d="M11.0027 0H10.9972C4.93211 0 0 4.93349 0 11C0 13.4062 0.775498 15.6365 2.09412 17.4473L0.723248 21.5338L4.95136 20.1822C6.69073 21.3344 8.7656 21.9999 11.0027 21.9999C17.0678 21.9999 21.9999 17.0651 21.9999 11C21.9999 4.93486 17.0678 0 11.0027 0ZM17.4033 15.5333C17.138 16.2827 16.0847 16.9042 15.2446 17.0857C14.6698 17.2081 13.9191 17.3057 11.3918 16.258C8.15923 14.9187 6.07748 11.6338 5.91523 11.4207C5.75986 11.2076 4.60899 9.68135 4.60899 8.10285C4.60899 6.52436 5.41061 5.75573 5.73374 5.42574C5.99911 5.15486 6.43773 5.03111 6.85848 5.03111C6.99461 5.03111 7.11698 5.03799 7.22698 5.04349C7.55011 5.05724 7.71235 5.07649 7.92548 5.58661C8.19085 6.22598 8.8371 7.80448 8.9141 7.96673C8.99248 8.12898 9.07085 8.34898 8.96085 8.5621C8.85773 8.7821 8.76698 8.87973 8.60473 9.06673C8.44248 9.25373 8.28848 9.39673 8.12623 9.59748C7.97773 9.7721 7.80998 9.9591 7.99698 10.2822C8.18398 10.5985 8.83023 11.6531 9.78172 12.5001C11.0096 13.5932 12.0051 13.9425 12.3612 14.091C12.6266 14.201 12.9428 14.1748 13.1367 13.9686C13.3828 13.7032 13.6867 13.2632 13.9961 12.8301C14.2161 12.5193 14.4938 12.4808 14.7853 12.5908C15.0823 12.694 16.654 13.4708 16.9771 13.6317C17.3002 13.794 17.5133 13.871 17.5917 14.0071C17.6687 14.1432 17.6687 14.7826 17.4033 15.5333Z"
                                fill="currentColor"
                            ></path>
                            </svg>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </template>
      </template>
    </template>

    <!-- Кнопка "Показать все", если контактов больше одного -->
    <div v-if="Object.keys(contactData).length > 1" class="text-center mt-4">
      <button
          @click="isExpanded = !isExpanded"
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
      >
        {{ isExpanded ? 'Скрыть' : 'Показать все' }}
      </button>
    </div>
  </div>
</template>
