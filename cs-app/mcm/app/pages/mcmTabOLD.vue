<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { B24Icon } from '@bitrix24/b24icons-vue';
import InfoIcon from '@bitrix24/b24icons-vue/button/InfoIcon'
import DotsIcon from '@bitrix24/b24icons-vue/button/DotsIcon'
import PersonIcon from '@bitrix24/b24icons-vue/main/PersonIcon'

const searchQuery = ref('');
const searchResults = ref(null);
const messagesResults = ref(null);
const isLoading = ref(false);

const { locale, t, defaultLocale } = useI18n();
const dir = computed(() => locales[locale.value]?.dir || 'ltr');

const raw = ref({
  filter: {
    ids: []
  }
});

// Асинхронная функция для получения дополнительных данных
async function fetchAdditionalData() {
  isLoading.value = true;
  const profileId = '';
  const token = '785026ea43c1bb0b1b842189cbca9197c05f424e';
  const myHeaders = new Headers();
  myHeaders.append("Authorization", token); // Убираем 'Bearer'
  const params = new URLSearchParams();
  if (raw.value.filter.length > 0) {
    params.append('client_name', raw.value.filter.join(','));
  }

  const reqOptions = {
    method: 'GET',
    headers: myHeaders,
    redirect: 'follow',
  };

  try {
    console.log('Отправка запроса с параметрами:', profileId, params.toString());
    const response = await fetch(`https://wappi.pro/api/sync/chats/filter?profile_id=${profileId}&${params.toString()}`, reqOptions);
    console.log(response)
    if (!response.ok) {
      console.error(`HTTP error! status: ${response.status}`);
      return;
    }
    searchResults.value = await response.json();
    console.log(searchResults.value)
  } catch (error) {
    console.error('Ошибка при вызове fetch:', error);
  } finally {
    isLoading.value = false;
  }
}

// Следим за изменениями в searchQuery и обновляем raw.filter.ids
watch(searchQuery, (newQuery) => {
  raw.value.filter = newQuery ? [newQuery] : [];
  fetchAdditionalData();
});

async function messagesUploadList(chatId: string, Name: string, icon: string) {
  isLoading.value = true;
  const profileId = '';
  const token = '785026ea43c1bb0b1b842189cbca9197c05f424e';
  const myHeaders = new Headers();
  myHeaders.append("Authorization", token); // Убираем 'Bearer'
  const reqOptions = {
    method: 'GET',
    headers: myHeaders,
    redirect: 'follow',
  };

  try {
    console.log('Отправка запроса с параметрами:', profileId, chatId);
    const response = await fetch(`https://wappi.pro/api/sync/messages/get?profile_id=${profileId}&chat_id=${chatId}`, reqOptions);
    console.log(response)
    if (!response.ok) {
      console.error(`HTTP error! status: ${response.status}`);
      return;
    }
    messagesResults.value = await response.json();
    messagesResults.value.name = Name;
    messagesResults.value.icon = icon;

    console.log(messagesResults.value)
  } catch (error) {
    console.error('Ошибка при вызове fetch:', error);
  } finally {
    isLoading.value = false;
  }
}

// Отправляем запрос при монтировании компонента
onMounted(() => {
  fetchAdditionalData();
});

</script>

<template>
  <div class="">mcmTabs</div>
<B24SidebarLayout :use-light-content="false"  >
<template #sidebar="{ handleClick }" style="margin-right: 10px">
<B24SidebarHeader>
    <B24SidebarSection class="ps-[18px] flex-row items-center justify-start gap-0.5 text-primary">
<div class="my-8 relative">
<B24Input
    v-model="searchQuery"
type="search"
:icon="SearchIcon"
:placeholder="$t('page.list.ui.searchInput.placeholder')"
class="min-w-[110px] max-w-[400px]"
rounded
/>
</div>
</B24SidebarSection>
</B24SidebarHeader>
<B24SidebarBody>
  <div v-if="searchResults"
      class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-sm ml-2"
  >
    <template v-for="(activity, activityIndex) in searchResults.dialogs" :key="activity.id">
      <div
          class="relative bg-white dark:bg-white/10 p-2xs cursor-pointer rounded-md flex flex-row gap-2xs2 items-center transition-shadow shadow hover:shadow-lg "
          :class="[
              activity?.isInstall ? 'border-green-300 dark:border-green-800' : 'border-base-master/10 dark:border-base-100/20'
            ]"
          @click.stop="async () => { return messagesUploadList(activity.id, activity.contact.PushName, activity.thumbnail) }"
      >
        <div class="relative">
        <B24Avatar
            v-if="activity.thumbnail"
            :src="activity.thumbnail"
            size="md"
            class=""
        />
        <B24Avatar v-else
            :icon="PersonIcon"
            size="md"
            class=""
        />

          <B24Icon name="Social::WhatsappIcon" class="w-4 h-4 color-collab-500 absolute -right-2 bottom-5" />

          </div>
        <div class="w-full flex flex-col items-start justify-between gap-2">
          <div>
            <div v-if="activity.contact.PushName" class="font-b24-secondary text-black dark:text-base-150 text-h6 leading-4 mb-xs font-semibold line-clamp-1">
              <div>{{ activity.contact.PushName }}</div>
            </div>
<div class="flex flex-row items-center justify-start">
  <div v-if="activity.last_message_delivery_status === 'delivered'" class="">
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
  </svg>
  </div>
  <div v-else class="">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-all" viewBox="0 0 16 16">
      <path d="M8.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z"/>
    </svg>
  </div>
            <div v-if="activity.last_message_data" class="font-b24-primary text-4xs text-base-500 line-clamp-1">
              <div>{{ activity.last_message_data }}</div>
            </div>
</div>
          </div>
          <div class="w-full flex flex-row gap-1 items-center justify-end">

          </div>
        </div>
      </div>
    </template>
  </div>
<B24SidebarSpacer />
</B24SidebarBody>
<B24SidebarFooter >
<div class="">{{searchQuery}}</div>
    </B24SidebarFooter>
    </template>
    <template #navbar>
<B24NavbarSpacer />
<B24NavbarSection>
    <IntegratorNav />
</B24NavbarSection>
</template>

<!-- Header -->
<div>
<slot name="header-title">
    header-title
    </slot>
    </div>
  <div class=""><pre>{{searchResults}}</pre></div>
  <div v-if="messagesResults"  class="flex flex-col w-100 ml-2 relative" >
    <div class="flex flex-row items-start fixed bg-white  w-[100%]">
      <B24Avatar
          v-if="messagesResults.icon"
          :src="messagesResults.icon"
          size="lg"
          class=""
      />
      <B24Avatar v-else
                 :icon="PersonIcon"
                 size="lg"
      />
      <div class="">{{messagesResults.name}}</div>
    </div>
    <div class="flex flex-col w-[100%] gap-2xs p-xs mt-5xl">
    <template v-for="(message, messageIndex) in messagesResults.messages" :key="message.id">

      <div  :class="['flex', 'flex-row', 'w-[100%]', message.fromMe ? 'justify-end' : 'justify-start']">
        <div  :class="['flex', 'flex-col', 'w-[45%]', 'p-xs', 'rounded-md', message.fromMe ? 'bg-gray-20' : 'bg-blue-100']">

          <div v-if="message.type === 'image'" class="flex flex-col">
            <div class="w-[50%]">
              <img  v-if="message.s3Info.url && message.s3Info.expire < Date.now()"  :src="message.s3Info.url" >
              <div v-else class=" ">
                <B24Icon name="Main::FileDownloadIcon" class="w-15 h-15 bg-white rounded" />
              </div>
            </div>
            <div v-if="message.body.caption" class="">{{ message.body.caption }}</div>
          </div>

          <!-- Условие для типа 'video' -->
          <div v-else-if="message.type === 'video'" class="flex flex-col">
            <div class="w-[50%]">
              <video v-if="message.s3Info.url" controls>
                <source :src="message.s3Info.url" type="video/mp4">
                Your browser does not support the video tag.
              </video>
            </div>
            <div v-if="message.body.caption" class="">{{ message.body.caption }}</div>
          </div>

          <!-- Условие для всех остальных типов -->
          <div v-else class="">{{ message.body }}</div>
        </div>
      </div>



    </template>
    </div>
  </div>
  <div class="flex fixed botton-0" name="block-messege-send">
    <div class="message-input-bar">
      <button class="icon-button" @click="addEmoji">
        😊
      </button>
      <input
          type="text"
          v-model="messageText"
          placeholder="Type a message"
          class="text-input"
      />
      <button class="icon-button" @click="sendFile">
        📎
      </button>
      <button class="icon-button" @click="sendMessage">
        📤
      </button>
      <button class="icon-button" @click="recordAudio">
        🎤
      </button>
    </div>
  </div>

  <div class=""><pre>{{messagesResults}}</pre></div>
    <!-- Content -->
    </B24SidebarLayout>
    </template>

