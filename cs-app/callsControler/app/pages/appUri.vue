<script setup lang="ts">
import "/assets/css/cs-root.css";
import "/assets/css/cs-messengers.css";
import { ref, computed, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';
import { B24Icon } from '@bitrix24/b24icons-vue';
import EmojiPicker from 'vue3-emoji-picker';
import SearchIcon from '@bitrix24/b24icons-vue/button/SearchIcon';
import * as csMain from '@/services/cs-main';
import { wappGet, wappPost } from '@/services/cs-wappi';
import { formatDate } from '@/services/cs-main';



// ✅ Блок: Инициализация Bitrix24 и параметров профиля
const { $initializeB24Frame } = useNuxtApp();
const $b24 = await $initializeB24Frame();
const authManager = $b24.auth;
const authData = authManager.getAuthData();

const profileId = ref('');
const profileType = ref('');
const profileName = ref('');
const profileTypeUrl = ref('');
let parsedParams;

try {
  parsedParams = JSON.parse(decodeURIComponent($b24.placement.options));
  profileId.value = parsedParams.profileCode;
  profileType.value = parsedParams.profileType;
  profileName.value = parsedParams.profileName;

  if (parsedParams.profileType === 'Whatsapp') {
    profileTypeUrl.value = "/api";
  } else if (parsedParams.profileType === 'Telegram') {
    profileTypeUrl.value = "/tapi";
  }
} catch (error) {
  console.error('Ошибка при разборе параметров из $b24:', error);
}

// ✅ Блок: Реактивные переменные
const searchQuery = ref('');
const searchResults = ref(null);
const messagesResults = ref({ messages: [], total_count: 0 });
const isLoading = ref(false);
const newMessage = ref('');
const showEmojiPicker = ref(false);
const file = ref(null);
const messageRefs = ref({});
const selectedActivityId = ref(null);
const loadedMessagesCount = ref(0);

const { locale, t, defaultLocale } = useI18n();
const dir = computed(() => locales[locale.value]?.dir || 'ltr');

const uploadedFile = ref({
  url: null,
  name: null,
  caption: ''
});
const canSend = computed(() => {
  return newMessage.value.trim() !== '' || uploadedFile.value.url !== null;
});
const raw = ref({
  filter: {
    ids: []
  }
});

const messagesContainer = ref(null);

// ✅ Блок: Таймер для автоматического обновления
let autoRefreshInterval = null;

function startAutoRefresh() {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
  }

  autoRefreshInterval = setInterval(() => {
    if (messagesResults.value.id) {
      fetchNewMessages(messagesResults.value.id, 10);
    }
  }, 3000);
}

function stopAutoRefresh() {
  if (autoRefreshInterval) {
    clearInterval(autoRefreshInterval);
    autoRefreshInterval = null;
  }
}

// ✅ Блок: Запись и отправка голосового сообщения
let mediaRecorder = null;
let audioChunks = [];

function startRecording() {
  navigator.mediaDevices.getUserMedia({ audio: true })
      .then(stream => {
        mediaRecorder = new MediaRecorder(stream, {
          mimeType: 'audio/webm'
        });
        audioChunks = [];

        mediaRecorder.ondataavailable = event => {
          audioChunks.push(event.data);
        };

        mediaRecorder.onstop = () => {
          const webmBlob = new Blob(audioChunks, { type: 'audio/webm' });

          // Конвертируем в base64
          const reader = new FileReader();
          reader.onload = async () => {
            const base64 = reader.result.split(',')[1]; // берем только base64 без префикса
            await sendAudioMessage(base64);
          };
          reader.readAsDataURL(webmBlob);
        };

        mediaRecorder.start();
      })
      .catch(err => {
        console.error('Ошибка при доступе к микрофону:', err);
      });
}

function stopRecording() {
  if (mediaRecorder && mediaRecorder.state === 'recording') {
    mediaRecorder.stop();
  }
}

function handleRecordButtonPress() {
  if (!mediaRecorder || mediaRecorder.state !== 'recording') {
    startRecording();
  } else {
    stopRecording();
  }
}

async function sendAudioMessage(base64) {
  const recipient = messagesResults.value.id ? messagesResults.value.id : parsedParams.phone;

  const body = JSON.stringify({
    b64_file: base64,
    recipient: recipient
  });

  if (profileId.value) {
    const url = `/api/sync/message/audio/send?profile_id=${profileId.value}`;
    try {
      const response = await wappPost(url, body);
      console.log('Голосовое сообщение отправлено');

      // После отправки — загружаем последние 10 сообщений
      if (messagesResults.value.id) {
        fetchNewMessages(messagesResults.value.id, 10);
      }
    } catch (error) {
      console.error('Ошибка при отправке голосового сообщения:', error);
    }
  }
}

// ✅ Блок: Отправка текстового сообщения
async function sendMessage() {
  if (newMessage.value.trim()) {
    const recipient = messagesResults.value.id ? messagesResults.value.id : parsedParams.phone;
    const body = JSON.stringify({
      body: newMessage.value,
      recipient: recipient
    });

    if (profileId.value) {
      const params = `/api/async/message/send?profile_id=${profileId.value}`;
      try {
        const response = await wappPost(params, body);
        console.log('Message sent:', newMessage.value);

        // После отправки — загружаем последние 10 сообщений
        if (messagesResults.value.id) {
          fetchNewMessages(messagesResults.value.id, 10);
        }

        newMessage.value = '';
       // file.value = null;
      } catch (error) {
        console.error('Ошибка при отправке сообщения:', error);
      }
    }
  }
}

// ✅ Блок: Загрузка сообщений
async function messagesUploadList(chatId, Name, icon, profileId, offset = 0) {
  isLoading.value = true;
  try {
    console.log('messagesUploadList вызвана для chatId:', chatId);
    console.log('messagesResults.value.messages до обновления:', messagesResults.value.messages);

    const scrollContainer = messagesContainer.value;
    let currentScrollPosition = 0;

    if (offset > 0 && scrollContainer) {
      currentScrollPosition = scrollContainer.scrollTop;
    }

    if (profileId) {
      const params = `/api/sync/messages/get?profile_id=${profileId}&order=desc&limit=50&chat_id=${chatId}&offset=${offset}`;
      console.log('Отправка запроса с параметрами:', params);
      const response = await wappGet(params);
      console.log(response, 'chat');

      const newMessages = response.messages.reverse();

      if (offset === 0) {
        messagesResults.value.messages = [...newMessages];
        messageRefs.value = {}; // Очищаем messageRefs при загрузке новых сообщений
      } else {
        messagesResults.value.messages = [...newMessages, ...messagesResults.value.messages];
      }

      loadedMessagesCount.value += newMessages.length;

      messagesResults.value.name = Name;
      messagesResults.value.icon = icon;
      messagesResults.value.id = chatId;
      messagesResults.value.profileId = profileId;
      messagesResults.value.total_count = response.total_count;
      console.log('messagesResults.value.messages после обновления:', messagesResults.value.messages);
    }

    nextTick(() => {
      if (scrollContainer) {
        if (offset === 0) {
          scrollToBottom();
        } else {
          scrollContainer.scrollTop = currentScrollPosition;
        }
      }
    });
  } catch (error) {
    console.error('Ошибка при вызове fetch:', error);
  } finally {
    isLoading.value = false;
  }
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      const lastMessage = messagesContainer.value.lastElementChild;
      if (lastMessage) {
        lastMessage.scrollIntoView({ behavior: 'smooth' });
      }
    }
  });
}

// ✅ Блок: Поиск диалогов
async function searchDialogs(params) {
  isLoading.value = true;
  const queryParams = new URLSearchParams();
  if (params) {
    queryParams.append('client_name', params);
  } else {
    if (raw.value.filter.length > 0) {
      queryParams.append('client_name', raw.value.filter.join(','));
    }
  }

  try {
    if (profileId.value) {
      const url = `/api/sync/chats/filter?profile_id=${profileId.value}&${queryParams.toString()}`;
      const response = await wappGet(url);
      searchResults.value = await response;

      if (searchResults.value.dialogs && searchResults.value.dialogs.length > 0) {
        selectActivity(searchResults.value.dialogs[0]);
      }
    }
  } catch (error) {
    console.error('Ошибка при вызове fetch:', error);
  } finally {
    isLoading.value = false;
  }
}

// ✅ Блок: Выбор активности
function selectActivity(activity) {
  if (selectedActivityId.value !== activity.id) {
    messagesResults.value = { messages: [], total_count: 0 };
    loadedMessagesCount.value = 0;
  }

  selectedActivityId.value = activity.id;

  if (profileId.value) {
    messagesUploadList(activity.id, activity.contact.PushName, activity.thumbnail, profileId.value, 0);
  } else {
    console.error('profileId is empty, cannot load messages');
  }
}

// ✅ Блок: Загрузка новых сообщений
async function fetchNewMessages(chatId, limit = 10) {
  const order = 'desc';
  const url = `/api/sync/messages/get?profile_id=${profileId.value}&chat_id=${chatId}&limit=${limit}&order=${order}`;

  try {
    const response = await wappGet(url);
    const newMessages = response.messages.reverse(); // reverse для правильного порядка

    // Фильтруем только те сообщения, которых ещё нет в messageRefs
    const filteredNewMessages = newMessages.filter(message => !messageRefs.value[message.id]);

    // Добавляем только новые сообщения в конец
    if (filteredNewMessages.length > 0) {
      messagesResults.value.messages = [...messagesResults.value.messages, ...filteredNewMessages];

      // Скроллим вниз, если не вверху
      if (messagesContainer.value.scrollTop + messagesContainer.value.clientHeight >= messagesContainer.value.scrollHeight - 100) {
        scrollToBottom();
      }
    }
  } catch (error) {
    console.error('Ошибка при загрузке новых сообщений:', error);
  }
}


// ✅ Блок: Выбор файла
const fileInput = ref(null);

function triggerFileInput() {
  fileInput.value.click();
}

async function handleFileUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async () => {
    const base64 = reader.result.split(',')[1]; // берем только base64 без префикса
    await uploadFileToBitrix(file.name, base64);
  };
  reader.readAsDataURL(file);
}

// ✅ Блок: Загрузка файла в Битрикс
async function uploadFileToBitrix(fileName, base64) {
  try {
    // Получаем папку приложения
    const storageResponse = await $b24.callMethod('disk.storage.getforapp');
    console.log(storageResponse, 'storageResponse')
    const folderId = storageResponse.getData().result.ROOT_OBJECT_ID;
console.log(folderId,'folderId')
    // Формируем параметры для загрузки
    const params = {
      id: folderId,
      generateUniqueName: 'Y',
      fileContent: [fileName, base64],
      data: { NAME: fileName }
    };

    // Загружаем файл
    const uploadResponse = await $b24.callMethod('disk.folder.uploadfile', params);
    const fileData = uploadResponse.getData().result;
    console.log(fileData, 'fileData')

    if (fileData.error) {
      console.error('Ошибка загрузки файла:', fileData.error());
      return;
    }

    // Сохраняем данные о загруженном файле
    uploadedFile.value = {
      url: fileData.CONTENT_URL,
      name: fileName,
      caption: newMessage.value
    };
  } catch (error) {
    console.error('Ошибка при загрузке файла:', error);
  }
}

// ✅ Блок: Отправка файла как сообщения
async function sendFileMessage() {
  if (!uploadedFile.value.url) return;

  const recipient = messagesResults.value.id ? messagesResults.value.id : parsedParams.phone;
  const caption = newMessage.value.trim()
  const body = JSON.stringify({
    url: uploadedFile.value.url,
    caption: caption,
    file_name: uploadedFile.value.name,
    recipient: recipient
  });

console.log(body,'body')
  if (profileId.value) {
    const url = `/api/sync/message/file/url/send?profile_id=${profileId.value}`;
    try {
      const response = await wappPost(url, body);
      console.log('Файл отправлен как сообщение');

      // После отправки — загружаем последние 10 сообщений
      if (messagesResults.value.id) {
        fetchNewMessages(messagesResults.value.id, 10);
      }

      // Очистка
      uploadedFile.value = { url: null, name: null, caption: '' };
      newMessage.value = ''
    } catch (error) {
      console.error('Ошибка при отправке файла как сообщения:', error);
    }
  }
}

async function sendMessageOrFile() {
  if (uploadedFile.value.url) {
    await sendFileMessage();
  } else {
    await sendMessage(); // ваша старая функция отправки текста
  }
}
// ✅ Блок: Инициализация и очистка
onMounted(() => {
  if (parsedParams) {
    console.log(parsedParams.phone);
    searchDialogs(parsedParams.phone);
  } else {
    searchDialogs('');
  }

// Запускаем автоматическое обновление
  startAutoRefresh();
});

onBeforeUnmount(() => {
  stopAutoRefresh();
});
</script>
<style>
.vac-svg-button.recording {
  background-color: #1876D2;
  border-radius: 50%;
}
</style>
<template>
  <div class="mainWindowLight" style="width: 100%; height: 100%;">
    <div class="mainWindow">
      <div class="vue-notification-group" >
        <span></span>
      </div>
      <div class="vac-card-window" tasks="" messages-loaded="true"
           style="height: 100vh; ">
        <div class="vac-chat-container">
          <div class="vac-rooms-container vac-app-border-r">
            <div data-scrollbar="true" tabindex="-1" style="overflow: hidden; outline: none;">
              <div class="scroll-content flex flex-col">
                <div>
                  <div class="vac-box-search">
                    <input type="text" value="c40b7054-9f50" ref="profileInput" class="hidden">
                    <B24Input
                        v-model="searchQuery"
                        type="search"
                        :icon="SearchIcon"
                        :placeholder="$t('page.list.ui.searchInput.placeholder')"
                        class="vac-input"
                        rounded
                        style="border: 0 !important; appearance: none; outline: none;"
                    />
                    <div arrow-padding="-12" class="vac-svg-button vac-add-icon v-popper--has-tooltip">
                      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.66675 8.99999H16.3334M9.00008 1.66666V16.3333" stroke="#1876D2" stroke-width="1.98"
                              stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="flex flex-row cursor-pointer">
                  <div class="showChats mr-2">Профиль:</div> <div class="selectChats v-popper--has-tooltip">{{profileName}}</div>
                  </div>
                </div>
                <div ref="chatsList" class="vac-room-list">
                  <div v-if="searchResults" class="grid grid-cols-[repeat(auto-fill,minmax(200px,1fr))] gap-sm ml-2">
                    <template v-for="(activity, activityIndex) in searchResults.dialogs" :key="activity.id">
                      <div class="vac-room-item"
                           :class="{ 'vac-room-selected': selectedActivityId === activity.id }"
                           @click.stop="async () => { return selectActivity(activity) }">
                        <div class="vac-room-container">
                          <div class="vac-avatar position_relative"
                               :style="{ backgroundImage: `url(${activity.thumbnail || 'https://app.cassoft.ru/local/images/avatar/no-avatar.jpg'})` }"
                          >
                            <svg width="18" height="18" viewBox="0 0 22 22" fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg" class="platform_avatar"
                                 style="color: rgb(35, 187, 134);">
                              <path
                                  d="M11.0027 0H10.9972C4.93211 0 0 4.93349 0 11C0 13.4062 0.775498 15.6365 2.09412 17.4473L0.723248 21.5338L4.95136 20.1822C6.69073 21.3344 8.7656 21.9999 11.0027 21.9999C17.0678 21.9999 21.9999 17.0651 21.9999 11C21.9999 4.93486 17.0678 0 11.0027 0ZM17.4033 15.5333C17.138 16.2827 16.0847 16.9042 15.2446 17.0857C14.6698 17.2081 13.9191 17.3057 11.3918 16.258C8.15923 14.9187 6.07748 11.6338 5.91523 11.4207C5.75986 11.2076 4.60899 9.68135 4.60899 8.10285C4.60899 6.52436 5.41061 5.75573 5.73374 5.42574C5.99911 5.15486 6.43773 5.03111 6.85848 5.03111C6.99461 5.03111 7.11698 5.03799 7.22698 5.04349C7.55011 5.05724 7.71235 5.07649 7.92548 5.58661C8.19085 6.22598 8.8371 7.80448 8.9141 7.96673C8.99248 8.12898 9.07085 8.34898 8.96085 8.5621C8.85773 8.7821 8.76698 8.87973 8.60473 9.06673C8.44248 9.25373 8.28848 9.39673 8.12623 9.59748C7.97773 9.7721 7.80998 9.9591 7.99698 10.2822C8.18398 10.5985 8.83023 11.6531 9.78172 12.5001C11.0096 13.5932 12.0051 13.9425 12.3612 14.091C12.6266 14.201 12.9428 14.1748 13.1367 13.9686C13.3828 13.7032 13.6867 13.2632 13.9961 12.8301C14.2161 12.5193 14.4938 12.4808 14.7853 12.5908C15.0823 12.694 16.654 13.4708 16.9771 13.6317C17.3002 13.794 17.5133 13.871 17.5917 14.0071C17.6687 14.1432 17.6687 14.7826 17.4033 15.5333Z"
                                  fill="currentColor"></path>
                            </svg><!----><!----><!----></div>
                          <div class="vac-name-container vac-text-ellipsis">
                            <div class="vac-title-container"><!---->
                              <div class="vac-room-name vac-text-ellipsis"> {{ activity.contact.PushName }}</div>
                              <div class="vac-text-date"> {{ formatDate(activity.last_time)}}</div>
                            </div>
                            <div class="vac-text-last">
                              <span>
                                <svg v-if="!activity.last_message_delivery_status" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24" class="vac-icon-check vac-icon-checkmark">
                                  <path  d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                    width="24" height="24" viewBox="0 0 24 24" class="vac-icon-check"
                                     :class="{
                                   'vac-icon-double-checkmark-seen': activity.last_message_delivery_status === 'read',
                                   'vac-icon-double-checkmark': activity.last_message_delivery_status === 'delivered'
                                   }">
                                  <path  d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"></path>
                              </svg>

                              </span>
                              <div class="vac-format-message-wrapper vac-text-ellipsis">
                                <div class="vac-format-container vac-text-ellipsis"><span class="vac-text-ellipsis"> {{activity.last_message_data}} </span>
                                </div>
                              </div><!---->
                              <div class="vac-room-options-container"><!----><!----><!----></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>

                  <div id="infinite-loader-rooms" style="position: relative;">

                  </div>
                </div>
              </div>
              <div class="scrollbar-track scrollbar-track-x" style="display: none;">
                <div class="scrollbar-thumb scrollbar-thumb-x"
                     style="width: 399px; transform: translate3d(0px, 0px, 0px);"></div>
              </div>
              <div class="scrollbar-track scrollbar-track-y" style="display: block;">
                <div class="scrollbar-thumb scrollbar-thumb-y"
                     style="height: 28.4603px; transform: translate3d(0px, 0px, 0px);"></div>
              </div>
            </div>
            <div class="selectProfiles" style="display: none;"> Показать чаты из профилей</div>
            <div data-scrollbar="true" tabindex="-1" style="overflow: hidden; outline: none; display: none;">
              <div class="scroll-content">
                <div class="vac-room-list">
                  <div class="vac-room-item" style="color: rgb(108, 117, 125);"> Выбрать всех</div>
                </div>
              </div>
              <div class="scrollbar-track scrollbar-track-x" style="display: none;">
                <div class="scrollbar-thumb scrollbar-thumb-x"
                     style="width: 399px; transform: translate3d(0px, 0px, 0px);"></div>
              </div>
              <div class="scrollbar-track scrollbar-track-y" style="display: none;">
                <div class="scrollbar-thumb scrollbar-thumb-y"
                     style="height: 81px; transform: translate3d(0px, 0px, 0px);"></div>
              </div>
            </div>
            <div class="saveProfiles" style="display: none;">
              <button class="selectProfilesSave" style="margin: 24px 0px;">Сохранить</button>
              <button class="selectProfilesAbort" style="margin: 24px 0px 24px 12px;">Отмена</button>
            </div>
          </div>
          <div v-if="messagesResults" class="vac-col-messages" messages-loaded="true" style="position: relative;">
            <div class="isNoFetchingRoom"></div>
            <div class="vac-room-header vac-app-border-b">
              <div class="vac-room-wrapper">
                <div class="vac-info-wrapper">
                  <div class="vac-avatar" :style="{ backgroundImage: `url(${messagesResults.icon || parsedParams.logo } )` }"></div>
                  <div class="vac-text-ellipsis">
                    <div class="vac-room-name vac-text-ellipsis">{{ messagesResults.name || parsedParams.name }}</div>
                    <div class="vac-room-info vac-text-ellipsis" style="display: flex; align-items: center;">
                      <svg width="16" height="16" viewBox="0 0 22 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="color: rgb(35, 187, 134);">
                        <path d="M11.0027 0H10.9972C4.93211 0 0 4.93349 0 11C0 13.4062 0.775498 15.6365 2.09412 17.4473L0.723248 21.5338L4.95136 20.1822C6.69073 21.3344 8.7656 21.9999 11.0027 21.9999C17.0678 21.9999 21.9999 17.0651 21.9999 11C21.9999 4.93486 17.0678 0 11.0027 0ZM17.4033 15.5333C17.138 16.2827 16.0847 16.9042 15.2446 17.0857C14.6698 17.2081 13.9191 17.3057 11.3918 16.258C8.15923 14.9187 6.07748 11.6338 5.91523 11.4207C5.75986 11.2076 4.60899 9.68135 4.60899 8.10285C4.60899 6.52436 5.41061 5.75573 5.73374 5.42574C5.99911 5.15486 6.43773 5.03111 6.85848 5.03111C6.99461 5.03111 7.11698 5.03799 7.22698 5.04349C7.55011 5.05724 7.71235 5.07649 7.92548 5.58661C8.19085 6.22598 8.8371 7.80448 8.9141 7.96673C8.99248 8.12898 9.07085 8.34898 8.96085 8.5621C8.85773 8.7821 8.76698 8.87973 8.60473 9.06673C8.44248 9.25373 8.28848 9.39673 8.12623 9.59748C7.97773 9.7721 7.80998 9.9591 7.99698 10.2822C8.18398 10.5985 8.83023 11.6531 9.78172 12.5001C11.0096 13.5932 12.0051 13.9425 12.3612 14.091C12.6266 14.201 12.9428 14.1748 13.1367 13.9686C13.3828 13.7032 13.6867 13.2632 13.9961 12.8301C14.2161 12.5193 14.4938 12.4808 14.7853 12.5908C15.0823 12.694 16.654 13.4708 16.9771 13.6317C17.3002 13.794 17.5133 13.871 17.5917 14.0071C17.6687 14.1432 17.6687 14.7826 17.4033 15.5333Z" fill="currentColor"></path>
                        </svg>
                        <span style="margin-left: 4px; color: rgb(108, 117, 125);"> {{profileName}}</span>
                    </div>
                  </div>
                </div>
                <div class="contact_toggler hidden">
                  <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="cursor: pointer;">
                    <path d="M1.83337 9.08333C1.83337 5.62636 1.83337 3.89789 2.90731 2.82394C3.98126 1.75 5.70974 1.75 9.16671 1.75H12.8334C16.2903 1.75 18.0189 1.75 19.0927 2.82394C20.1667 3.89789 20.1667 5.62636 20.1667 9.08333V10.9167C20.1667 14.3736 20.1667 16.1022 19.0927 17.176C18.0189 18.25 16.2903 18.25 12.8334 18.25H9.16671C5.70974 18.25 3.98126 18.25 2.90731 17.176C1.83337 16.1022 1.83337 14.3736 1.83337 10.9167V9.08333Z" stroke="#1876D2" stroke-width="1.76"></path>
                    <path d="M13.75 18.25V1.75" stroke="#1876D2" stroke-width="1.76" stroke-linecap="round"></path>
                    </svg>
                </div>
              </div>
            </div>
            <div id="messages-list" class="vac-container-scroll bx-messenger-body-bg" data-scrollbar="true" tabindex="-1" style="position: relative; outline: none;">
              <div class="scroll-content" style="transform: translate3d(0px, -40px, 0px);">
                <div class="vac-messages-container">
                  <div ref="messagesContainer" class="messages-container">
                    <!-- Кнопка "Загрузить ещё" в начале списка -->
                    <div  class="flex flex-row items-center justify-center mt-5" >
                      <div v-if="loadedMessagesCount < messagesResults.total_count" class="readButton" @click="messagesUploadList(messagesResults.id, messagesResults.name, messagesResults.icon, profileId, loadedMessagesCount)"
                           style="justify-content: center; min-width: 110px; margin: 34px 0 4px; padding: 7px 3px 7px 8px;">
                        <span>Загрузить более ранние сообщения</span>
                      </div>
                    </div>
                    <div v-if="messagesResults.messages && messagesResults.messages.length > 0" class="vac-text-started ">
                      Диалог начат с: {{ formatDate(messagesResults.messages[0].time) }}
                    </div>
                    <template v-for="(message, messageIndex) in messagesResults.messages" :key="message.id">
                      <div>
                        <div :ref="el => messageRefs[message.id] = el" class="vac-message-wrapper">
                          <div :class="['vac-message-box', message.fromMe ? 'vac-offset-current' : '']">
                            <div :class="['vac-message-container', message.fromMe ? 'vac-message-container-offset' : '']">
                              <div :class="['vac-message-card', message.fromMe ? 'vac-message-current' : '']">
                                <div class="vac-format-message-wrapper">
                                  <div class="markdown">{{ message.senderName }}
                                    <div v-if="message.type === 'image'" class="flex flex-col">
                                      <div class="w-[50%]">
                                        <img v-if="message.s3Info.url && message.s3Info.expire < Date.now()" :src="message.s3Info.url">
                                        <div v-else class=" ">
                                          <B24Icon name="Main::FileDownloadIcon" class="w-15 h-15 bg-white rounded"/>
                                        </div>
                                      </div>
                                      <div v-if="message.body.caption" class="">{{ message.body.caption }}</div>
                                    </div>
                                    <div v-else-if="message.type === 'video'" class="flex flex-col">
                                      <div class="w-[50%]">
                                        <video v-if="message.s3Info.url" controls>
                                          <source :src="message.s3Info.url" type="video/mp4">
                                          Your browser does not support the video tag.
                                        </video>
                                      </div>
                                      <div v-if="message.body.caption" class="">{{ message.body.caption }}</div>
                                    </div>
                                    <div v-else-if="message.type === 'audio'" class="flex flex-col">
                                      <div class="w-full max-w-md min-w-fit">
                                        <audio v-if="message.s3Info.url" controls>
                                          <source :src="message.s3Info.url" type="audio/ogg; codecs=opus" />
                                          <source :src="message.s3Info.url" type="audio/webm" />
                                          <source :src="message.s3Info.url" type="audio/mpeg" />
                                          Ваш браузер не поддерживает воспроизведение аудио.
                                        </audio>
                                      </div>
                                      <div v-if="message.body.caption" class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ message.body.caption }}
                                      </div>
<!--                                      <div class=""><pre>{{message}}</pre></div>-->
                                    </div>
                                    <div v-else-if="message.type === 'document'" class="flex flex-col">
                                      <div class="w-[50%]">

                                        <B24Icon name="Main::DocumentIcon" class="w-5 h-5" />
                                        <img v-if="message.body.URL " :src="message.body.URL">
                                        <div v-else class=" ">
                                          <B24Icon name="Main::FileDownloadIcon" class="w-15 h-15 bg-white rounded"/>
                                        </div>
                                      </div>
                                      <div v-if="message.body.title" class="">{{ message.body.title }}</div>
                                    </div>
                                    <div v-else class="">{{ message.body }}</div>
                                  </div>
                                </div>
                                <div class="vac-text-timestamp flex flex-row justify-between">

                                  <span>
                                <svg v-if="!message.delivery_status" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24" class="vac-icon-check vac-icon-checkmark">
                                  <path  d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path>
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                     width="24" height="24" viewBox="0 0 24 24" class="vac-icon-check"
                                     :class="{
                                   'vac-icon-double-checkmark-seen': message.delivery_status === 'read',
                                   'vac-icon-double-checkmark': message.delivery_status === 'delivered'
                                   }">
                                  <path  d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"></path>
                              </svg>

                              </span>
                                  <span>{{ formatDate(message.time) }}</span>
                                </div>
                                <div class="vac-message-actions-wrapper">
                                  <div class="vac-options-container" style="display: initial; width: 70px;">
                         <span>
                            <!-- Опции сообщения -->
                         </span>
                                  </div>
                                </div>
                              </div>
                              <span></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
            <div id="room-footer" class="vac-room-footer" style="">
              <div class="flex flex-col w-full">
              <div class="vac-box-footer vac-box-footer-border">
                <div class="vac-icon-textarea-left" style="">
                  <div class="vac-svg-button" @mousedown="handleRecordButtonPress" @mouseup="handleRecordButtonPress" :class="{ 'recording': isRecording }">
                    <svg width="16" height="22" viewBox="0 0 16 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M14.4167 9.16668V11C14.4167 14.5438 11.5439 17.4167 8.00004 17.4167M8.00004 17.4167C4.45622 17.4167 1.58337 14.5438 1.58337 11V9.16668M8.00004 17.4167V20.1667M4.33337 20.1667H11.6667M8.00004 13.75C6.48122 13.75 5.25004 12.5188 5.25004 11V4.58334C5.25004 3.06456 6.48122 1.83334 8.00004 1.83334C9.51887 1.83334 10.75 3.06456 10.75 4.58334V11C10.75 12.5188 9.51887 13.75 8.00004 13.75Z" stroke="#1876D2" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                  </div>
                </div>
                <textarea v-model="newMessage" placeholder="Написать сообщение" class="vac-textarea" style="min-height: 20px; padding-left: 12px; height: 20px;"></textarea>

                <div class="vac-icon-textarea">
                  <div>
                    <div class="vac-emoji-wrapper " style="display: none">
                      <div class="vac-svg-button" @click="toggleEmojiPicker">
                        <svg name="emoji" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_345_99)">
                            <path d="M18.125 10C18.125 7.84512 17.269 5.77849 15.7452 4.25476C14.2215 2.73102 12.1549 1.875 10 1.875C7.84512 1.875 5.77849 2.73102 4.25476 4.25476C2.73102 5.77849 1.875 7.84512 1.875 10C1.875 12.1549 2.73102 14.2215 4.25476 15.7452C5.77849 17.269 7.84512 18.125 10 18.125C12.1549 18.125 14.2215 17.269 15.7452 15.7452C17.269 14.2215 18.125 12.1549 18.125 10ZM0 10C0 7.34784 1.05357 4.8043 2.92893 2.92893C4.8043 1.05357 7.34784 0 10 0C12.6522 0 15.1957 1.05357 17.0711 2.92893C18.9464 4.8043 20 7.34784 20 10C20 12.6522 18.9464 15.1957 17.0711 17.0711C15.1957 18.9464 12.6522 20 10 20C7.34784 20 4.8043 18.9464 2.92893 17.0711C1.05357 15.1957 0 12.6522 0 10ZM6.9375 12.4258C7.53125 13.0664 8.54688 13.75 10 13.75C11.4531 13.75 12.4688 13.0664 13.0625 12.4258C13.4141 12.0469 14.0078 12.0195 14.3867 12.3711C14.7656 12.7227 14.793 13.3164 14.4414 13.6953C13.582 14.625 12.0977 15.625 10.0039 15.625C7.91016 15.625 6.42188 14.6289 5.56641 13.6953C5.21484 13.3164 5.23828 12.7227 5.62109 12.3711C6.00391 12.0195 6.59375 12.043 6.94531 12.4258H6.9375ZM5.64062 8.125C5.64062 7.79348 5.77232 7.47554 6.00674 7.24112C6.24116 7.0067 6.5591 6.875 6.89062 6.875C7.22215 6.875 7.54009 7.0067 7.77451 7.24112C8.00893 7.47554 8.14062 7.79348 8.14062 8.125C8.14062 8.45652 8.00893 8.77446 7.77451 9.00888C7.54009 9.2433 7.22215 9.375 6.89062 9.375C6.5591 9.375 6.24116 9.2433 6.00674 9.00888C5.77232 8.77446 5.64062 8.45652 5.64062 8.125ZM13.1406 6.875C13.4721 6.875 13.7901 7.0067 14.0245 7.24112C14.2589 7.47554 14.3906 7.79348 14.3906 8.125C14.3906 8.45652 14.2589 8.77446 14.0245 9.00888C13.7901 9.2433 13.4721 9.375 13.1406 9.375C12.8091 9.375 12.4912 9.2433 12.2567 9.00888C12.0223 8.77446 11.8906 8.45652 11.8906 8.125C11.8906 7.79348 12.0223 7.47554 12.2567 7.24112C12.4912 7.0067 12.8091 6.875 13.1406 6.875Z" fill="#1976d2"></path>
                          </g>
                          <defs>
                            <clipPath id="clip0_345_99">
                              <rect width="20" height="20" fill="white"></rect>
                            </clipPath>
                          </defs>
                          </svg>
                      </div>
                    </div>
                  </div>
                  <div class="vac-svg-button " @click="triggerFileInput" style="">
                    <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M17.654 9.71218L10.2369 17.1293C8.20292 19.1632 4.90517 19.1632 2.87119 17.1293C0.837208 15.0952 0.837208 11.7975 2.87119 9.76353L9.99135 2.64336C11.3474 1.28738 13.5458 1.28738 14.9019 2.64336C16.2578 3.99935 16.2578 6.19784 14.9019 7.55383L7.79312 14.6626C7.11508 15.3406 6.01583 15.3406 5.33784 14.6626C4.65984 13.9846 4.65984 12.8853 5.33784 12.2074L11.8185 5.72668" stroke="#1876D2" stroke-width="2.08333" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                  </div>
                  <input
                      type="file"
                      ref="fileInput"
                      @change="handleFileUpload"
                      style="display: none"
                  />

                  <div :class="['vac-svg-button', { 'vac-send-disabled': !canSend }]" @click="sendMessageOrFile" style="margin-top: 2px;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="22" height="22" viewBox="0 0 24 24" :class="canSend ? 'vac-icon-send' : 'vac-icon-send-disabled'">
                      <path d="M19.4577 0.218854C19.8523 0.492235 20.0593 0.964793 19.9851 1.43735L17.4851 17.684C17.4265 18.0628 17.196 18.3948 16.86 18.5822C16.5241 18.7697 16.1217 18.7931 15.7663 18.6447L11.0943 16.7037L8.4185 19.5976C8.07084 19.9765 7.52396 20.1015 7.04348 19.914C6.56301 19.7265 6.2505 19.2618 6.2505 18.7463V15.4813C6.2505 15.3251 6.3091 15.1767 6.41457 15.0595L12.9615 7.92038C13.1881 7.67434 13.1803 7.29551 12.9459 7.06119C12.7115 6.82686 12.3326 6.81124 12.0865 7.03385L4.1411 14.091L0.691824 12.3648C0.277755 12.1578 0.0121258 11.7438 0.000406863 11.283C-0.0113121 10.8221 0.230879 10.3925 0.629323 10.1621L18.1296 0.164178C18.5476 -0.074054 19.0632 -0.0506214 19.4577 0.218854Z"></path>
                      </svg>
                  </div>
                </div>
              </div>
                <div v-if="uploadedFile.url" class="mt-2 p-2   ">
                  <div class="flex justify-between items-center relative">
                    <div class="w-[50px] h-[50px] overflow-hidden rounded">
                      <img :src="uploadedFile.url"  class="w-full h--full object-cover">
                    </div>
                    <span class="text-sm font-medium hidden" >{{ uploadedFile.name }}</span>

                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        </div>
    </div>
  </div>
</template>