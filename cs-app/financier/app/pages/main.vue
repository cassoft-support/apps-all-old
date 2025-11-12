<script setup lang="ts">
const iframeUrl = 'https://cassoft.ru' // Замените на нужную ссылку
</script>

<style scoped>
.iframe-container {
  width: 100%;
  height: 100vh;
  overflow: hidden;
}

.iframe {
  width: 100%;
  height: 100%;
  border: none;
}
</style>
<template>
  <div class="iframe-container">
    <iframe
         :src="iframeUrl"
         allow="camera; microphone; fullscreen"
         allowfullscreen
         class="iframe"
    ></iframe>
  </div>
</template>
