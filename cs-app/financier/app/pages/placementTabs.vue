<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import * as locales from '#b24ui/locale'
import SunIcon from '@bitrix24/b24icons-vue/main/SunIcon'
import MoonIcon from '@bitrix24/b24icons-vue/main/MoonIcon'
import EarthLanguageIcon from '@bitrix24/b24icons-vue/main/EarthLanguageIcon'
import {
  getThumbnailSrc,
  formatDate,
  formatSubscriptionEndDate,
  authCheck,
} from '@/services/cs-main';
// Получаем доступ к маршрутизатору
const router = useRouter()

const {$initializeB24Frame} = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth;
const authData = authManager.getAuthData();
//console.log(authData)
console.log($b24.placement, 'placement.options')
if (authData) {
  const resAuthCheck = await authCheck(authData.member_id)
    // const data = {
    //     member_id: authData.member_id,
    // };
    // const jsonData = JSON.stringify(data);
    //
    // try {
    //     const response = await fetch('https://app.cassoft.ru/cassoftApp/market/mcm/index.php', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json'
    //         },
    //         body: jsonData
    //     });
    //
    //     if (!response.ok) {
    //         throw new Error('Ошибка сети');
    //     }
    //
    //     const result = response.text();
    //     result.then((value) => {
            if (resAuthCheck === "Y") {
              console.log("then Y");
              console.log($b24.placement.title)
              if($b24.placement.title === "CRM_DEAL_DETAIL_TAB"
                  //  || $b24.placement.title ==="IM_NAVIGATION"
              ){
                router.push('/mcmTab');
              }else{
                router.push('/mcm-wappi');
              }


            } else {
                console.log("then NO", value);
                router.push('/close'); // Используем router для навигации
            }
      //  });

    // } catch (error) {
    //     console.error('Ошибка:', error);
    // }

} else {
    console.log('Данные аутентификации истекли или недоступны.');
}

if ($b24.placement.options?.place) {
    const optionsPlace: string = $b24.placement.options.place
}

const { locale, locales: localesI18n, setLocale, t, defaultLocale } = useI18n()

definePageMeta({
    layout: 'clear'
})

useHead({
    title: t('page.index.seo.title')
})

const colorMode = useColorMode()

const isDark = computed({
    get() {
        return colorMode.value === 'dark'
    },
    set() {
        colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
    }
})

const dir = computed(() => locales[locale.value]?.dir || 'ltr')

const langMap = ref<Map<string, boolean>>(new Map(
    Object.values(localesI18n.value).map(lang => [lang.code, false])
))
langMap.value.set(locale.value, true)

const helpItems = computed(() => {
    return [
        {
            label: isDark.value ? t('page.index.settings.dark') : t('page.index.settings.light'),
            icon: isDark.value ? MoonIcon : SunIcon,
            kbds: ['shift', 'd'],
            onSelect(e: Event) {
                e?.preventDefault()
                isDark.value = !isDark.value
            }
        },
        {
            label: t('page.index.settings.currentLang', {
                code: locales[locale.value]?.code || defaultLocale,
                title: locales[locale.value]?.name || defaultLocale
            }),
            icon: EarthLanguageIcon,
            children: localesI18n.value.map((localeRow) => {
                return {
                    label: localeRow.name,
                    type: 'checkbox' as const,
                    checked: langMap.value.get(localeRow.code),
                    onUpdateChecked() {
                        [...langMap.value.keys()].forEach((lang) => {
                            langMap.value.set(lang, false)
                        })
                        langMap.value.set(localeRow.code, true)

                        setLocale(localeRow.code)

                        nextTick(() => {
                            scrollToTop()
                        })
                    }
                }
            })
        }
    ]
})

onMounted(async () => {
    if (locale.value?.length < 1) {
        setLocale(defaultLocale)
    }
})
</script>


