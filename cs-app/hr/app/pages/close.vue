<script setup lang="ts">
import SettingsIcon from '@bitrix24/b24icons-vue/common-service/SettingsIcon'
import * as locales from '@bitrix24/b24ui-nuxt/locale'
</script>

<template>
  <div class=""></div>
  <div class="flex flex-col items-center justify-center gap-16 h-screen">
    <h1 class="font-b24-secondary text-h1 sm:text-8xl font-light">
      {{ $t('page.index.seo.title') }}
    </h1>

    <div class="flex flex-wrap items-center gap-3">
      <B24DropdownMenu
          :items="helpItems"
          :content="{
          align: 'start',
          side: dir === 'ltr' ? 'right' : 'left'
        }"
          :b24ui="{
          content: 'w-[240px] max-h-[245px]'
        }"
          class="me-5"
      >
        <B24Button
            rounded
            :icon="SettingsIcon"
            color="link"
            depth="dark"
        />
      </B24DropdownMenu>

      <B24Button
          rounded
          :label="$t('page.index.page.main')"
          color="primary"
          to="/main"
      />
      <B24Button
          rounded
          :label="$t('page.index.page.cs')"
          color="primary"
          to="https://cassoft.ru"
      />
    </div>
  </div>
</template>

<style scoped>

</style>
