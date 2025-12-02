<script setup lang="ts">
import { reactive, ref, useTemplateRef, onMounted } from 'vue'
import * as z from 'zod'
import { resizeWindow } from '@/tools/bitrix'
import { makeProfileClose, handleConnectProfile, refreshProfiles } from '@/services/cs-profile'
import { useNuxtApp } from '#app'

const profileKey = ref('')
const { $initializeB24Frame } = useNuxtApp()
const $b24 = await $initializeB24Frame()
const authManager = $b24.auth
const authData = authManager.getAuthData()

defineOptions({ inheritAttrs: false })

// Реактивные переменные
const setupList = ref<SetupMessagerItem[]>([])
const profileList = ref<{ label: string; value: string; id: string }[]>([])
const hasActiveProfiles = ref(false)
const statusActiveProfiles = ref(false)
const itemProfileId = ref(false)
const itemProfile = ref(false)

const schema = z.object({
  selectMenu: z.object({
    value: z.string(),
    label: z.string(),
    id: z.string(),
  }).refine(item => item.value !== '', {
    message: 'Выберите профиль для подключения'
  })
})

type Schema = z.input<typeof schema>

const state = reactive<Partial<Schema>>({
  selectMenu: null,
})

const form = useTemplateRef('form')
const connector = 'AutoRu'

// Объявляем toast
const toast = {
  error: (message: string) => {
    console.error(message);
    // Здесь можно добавить логику отображения ошибки
  }
}

// Объявляем функцию onMounted
async function onMounted() {
  const { profileList: fetchedProfileList, hasActiveProfiles: fetchedHasActive, statusActiveProfiles: fetchedStatus } = await refreshProfiles(connector, $b24)

  profileList.value = fetchedProfileList
  hasActiveProfiles.value = fetchedHasActive
  statusActiveProfiles.value = fetchedStatus

  resizeWindow()
}

// Вызываем onMounted при монтировании
onMounted()
</script>

<template>
  <B24Container class="mt-10" v-if="statusActiveProfiles">
    <div class="w-full flex flex-col items-center justify-center">
      <p class="text-h2 mb-6">Отключение профиля AUTO.RU</p>

      <div class="mb-3">{{statusActiveProfiles.CS_PROFILE_ID}}</div>
      <div class="mb-3">{{statusActiveProfiles.CS_PROFILE_NAME}}</div>
      <div class="mb-3">{{"https:///app.cassoft.ru/cassoftApp/market/mcm/in"+authData.member_id+"_"+statusActiveProfiles.CS_PROFILE_NAME}}</div>
<!--      <div class=""><pre>{{statusActiveProfiles}}</pre></div>-->

      <div class="w-full flex flex-row gap-1 items-center justify-center">

        <B24Button
            label="Отключить"
            size="lg"
            color="primary"
            loading-auto
            @click.stop="async () => {
            if (itemProfileId.value && itemProfile.value) {
             await makeProfileClose(itemProfileId.value, $b24, itemProfile.value)
            }
         }"
        />
      </div>
    </div>
  </B24Container>
  <B24Container v-else>
    <div class="mt-10">
      <p class="text-h2 mb-6">Подключение профиля AUTO.RU</p>
      <B24Form
          v-if="hasActiveProfiles"
          v-bind="$attrs"
          ref="form"
          :state="state"
          :schema="schema"
          class="space-y-6"
      >
        <B24FormField name="selectMenu" label="Выберите профиль">
          <B24SelectMenu
              v-model="state.selectMenu"
              :items="profileList"
              class="w-full"
              :key="profileList.length"
          />
        </B24FormField>

        <B24Separator class="mt-6 mb-3" />
        <B24FormField label="Вставьте ключ AUTO.ru" name="profile_name">
          <B24Input v-model="profileKey" class="w-full" />
        </B24FormField>
        <div class="flex flex-row gap-4 items-center justify-between">
          <B24Button
              type="submit"
              label="Подключить"
              color="success"
              @click="async () => {
    await handleConnectProfile(state, $b24, profileKey.value, schema, toast)
}"
          />
        </div>
      </B24Form>
    </div>
  </B24Container>
</template>