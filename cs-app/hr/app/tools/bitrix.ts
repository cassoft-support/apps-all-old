// import type { BatchCommands, BitrixBatchResponse } from '~/types/bitrix'
import { useNuxtApp } from '#app';
/**
 * Bitrix24 API Service
 * Handles batch requests to Bitrix24 REST API
 */
export const useBitrix = () => {
  /**
   * Execute batch request to Bitrix24 API
   * @returns Parsed response data
   */
  const batch = async <T>(): Promise<T> => {
    try {
      return {} as T
    } catch (error) {
      const errorMessage = error instanceof Error
        ? error.message
        : typeof error === 'string'
          ? error
          : 'Unknown error'

      throw new Error(`Bitrix API Error: ${errorMessage}`)
    }
  }

  return { batch }
}

export async function uploadFileToBitrix(fileName, base64, uploadedFileRef) {
  try {
    const { $initializeB24Frame } = useNuxtApp();
    const $b24 = await $initializeB24Frame();

    // Получаем папку приложения
    const storageResponse = await $b24.callMethod('disk.storage.getforapp');
    const folderId = storageResponse.getData().result.ROOT_OBJECT_ID;

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

    if (fileData.error) {
      console.error('Ошибка загрузки файла:', fileData.error());
      return;
    }

    let publUrl = "";
    let typeImg = "";

    if (fileData.CONTENT_URL) {
      publUrl = fileData.CONTENT_URL;
      typeImg = 'img'

    } else {
      const resUrl = await $b24.callMethod("disk.file.getExternalLink", {
        id: fileData.ID
      });

      const result = resUrl.getData().result;
      console.log(result,'result')
      if (result) {
        publUrl = result;
        typeImg = 'doc'
      } else {
        console.error("Не удалось получить внешнюю ссылку на файл");
      }
    }

    // Сохраняем данные о загруженном файле
    uploadedFileRef.value = {
      url: publUrl,
      name: fileName,
      type: typeImg
    };
  } catch (error) {
    console.error('Ошибка при загрузке файла:', error);
  }
}

export  function resizeWindow() {
  const script = document.createElement('script');
  script.src = 'https://api.bitrix24.com/api/v1/';
  script.onload = () => {
    if (typeof BX24 !== 'undefined' && typeof BX24.resizeWindow === 'function') {
      nextTick(() => {
        const { scrollWidth, scrollHeight } = BX24.getScrollSize();
        BX24.resizeWindow(scrollWidth, scrollHeight + 100);
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

// Функция для открытия слайдера создания профиля
async function makeProfileAdd(): Promise<void> {
  console.log(connector, 'connector')
  await profileSliderAddProfile.open({ connector, onRefresh: refreshProfiles })
}

export  function procIM(IM, arName: string): Promise<void> {
  let contactsIm ={}
  const tempContactsIm = IM
      .filter(item => item.VALUE)
      .map(item => {
        if (item.VALUE_TYPE === 'IMOL') {
          const parts = item.VALUE.split('|');
          const connector = parts[1];
          const type = connector === 'cs_mcm_telegram' ? 'telegram' : (connector === 'cs_mcm_whatsapp' ? 'whatsapp' : 'unknown');
          return {
            [parts[3]]: {
              line: parts[2],
              connector: connector,
              type: type
            }
          };
        } else if (item.VALUE_TYPE === 'TELEGRAM') {
          const value = item.VALUE.startsWith('@') ? item.VALUE.slice(1) : item.VALUE;
          return {
            [value]: {
              type: 'telegram'
            }
          };
        }
        return {};
      });

  const mergedContactsIm = Object.assign({}, ...tempContactsIm);
  contactsIm[arName] = mergedContactsIm;
  return contactsIm
}
