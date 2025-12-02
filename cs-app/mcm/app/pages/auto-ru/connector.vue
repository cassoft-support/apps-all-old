<script setup lang="ts">
import { reactive, ref, useTemplateRef, nextTick, onMounted } from 'vue'
import * as z from 'zod'
import {  authCheck } from '@/tools/cs-main';
import { useNuxtApp } from '#app'
import { ConnectorAutoRu } from '#components'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
const router = useRouter()
const { $initializeB24Frame } = useNuxtApp();
const $b24 = await $initializeB24Frame();
const authManager = $b24.auth;
const authData = authManager.getAuthData();
let connector = ''
if (authData) {
  const resAuthCheck = await authCheck(authData.member_id, "mcm", 'auto_ru')
  console.log(resAuthCheck,'resAuthCheck')
  if (resAuthCheck === "Y") {
    connector = $b24.placement.options.CONNECTOR
  } else {
    console.log("then NO", resAuthCheck);
    router.push('/close'); // Используем router для навигации
  }


} else {
  console.log('Данные аутентификации истекли или недоступны.');
}


    //"cs_auto_ru"
// Функция для получения и обновления списка профилей
console.log(connector,'connector')

onMounted(() => {


})
</script>

<template>
  <B24Container>
  <div v-if="connector === 'cs_auto_ru'" class="mt-10">
   <ConnectorAutoRu />
    </div>
  </B24Container>
</template>