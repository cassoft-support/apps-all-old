
//  Константа токена (вынесена отдельно)
const WAPP_TOKEN = '785026ea43c1bb0b1b842189cbca9197c05f424e';

//  Базовый URL API
const WAPP_API_BASE_URL = 'https://wappi.pro';

// 🛠️ Класс-клиент для работы с Wappi API
class WappiClient {
    private readonly token: string;
    private readonly baseUrl: string;

    constructor(token: string, baseUrl: string = WAPP_API_BASE_URL) {
        this.token = token;
        this.baseUrl = baseUrl;
    }

    private getHeaders(): HeadersInit {
        return {
            Authorization: this.token,
        };
    }

//  GET-запрос
    public async get<T>(endpoint: string): Promise<T | null> {
        const url = `${this.baseUrl}${endpoint}`;
        const options: RequestInit = {
            method: 'GET',
            headers: this.getHeaders(),
            redirect: 'follow',
        };

        try {
            const response = await fetch(url, options);

            if (!response.ok) {
                console.log(`HTTP error! status: ` ,response);
               
                return response.json();
            }

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Ошибка при вызове fetch (GET):', error);
            return null;
        }
    }

//  POST-запрос
    public async post<T>(endpoint: string, body: any): Promise<T | null> {
        const url = `${this.baseUrl}${endpoint}`;
        const options: RequestInit = {
            method: 'POST',
            headers: this.getHeaders(),
            body: body,
            redirect: 'follow',
        };

        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                console.error(`HTTP error! status: ${response.status}`);
                return await response.json();;
            }else {
                const result = await response.json();
                return result;
            }


        } catch (error) {
            console.error('Ошибка при вызове fetch (POST):', error);
            return null;
        }
    }
}



//  Экземпляр клиента
const wappiClient = new WappiClient(WAPP_TOKEN);

//  Экспортируемые функции
export async function wappGet<T>(endpoint: string): Promise<T | null> {
    return wappiClient.get<T>(endpoint);
}

export async function wappPost<T>(endpoint: string, body: any): Promise<T | null> {
    return wappiClient.post<T>(endpoint, body);
}
/**
 * Отправляет файл как сообщение
 * @param uploadedFile - объект с данными файла
 * @param recipient - получатель
 * @param profileCode - код профиля
 * @param profileTypeUrl - URL профиля
 * @returns Promise<void>
 */
export async function sendFilesMessage(
    uploadedFile: { url: string; name: string;  },
    recipient: string,
    profileCode: string,
    profileTypeUrl: string,
    caption: string
): Promise<void> {
    const body = JSON.stringify({
        url: uploadedFile.url,
        caption: caption,
        file_name: uploadedFile.name,
        recipient: recipient
    });

    const url = `${profileTypeUrl}/sync/message/file/url/send?profile_id=${profileCode}`;

    try {
        const response = await wappPost(url, body);
        console.log('Файл отправлен как сообщение');
    } catch (error) {
        console.error('Ошибка при отправке файла как сообщения:', error);
        throw error;
    }
}

export async function downloadMediaFromMessage(messageId: string) {
    const profileCode = state.selectMenu.value;
    const profileTypeUrl = profile.value[profileCode].profileTypeUrl;

    const url = `${profileTypeUrl}/sync/message/media/download?profile_id=${profileCode}&message_id=${messageId}`;

    try {
        const response = await wappGet(url);
        console.log(response,'responseUri')
        if (response && response.file_link) {
            // Если API возвращает URL, используем его для скачивания
            return response.file_link;
        } else {
            return 'Не удалось получить URL для скачивания медиа';
        }
    } catch (error) {
        return 'Ошибка при загрузке медиа:'+ error;
    }
}
export async function getChatListTG(profileId: string): Promise<{ id: string; name: string }[]> {
    try {
        const resDialogs = await wappGet(`/tapi/sync/chats/filter?profile_id=${profileId}`)

        // Проверяем, что resDialogs.data — это массив
        if (!Array.isArray(resDialogs.dialogs)) {
            console.warn('Ожидался массив, но пришло-', resDialogs.dialogs)
            return []
        }

        // Фильтруем только чаты
        const chatItems = resDialogs.dialogs.filter((item: any) => item.type === 'chat')

        // Формируем массив { id, name }
        const chatList = chatItems.map((item: any) => ({
            value: item.id,
            label: item.name
        }))

        return chatList
    } catch (error) {
        console.error('Ошибка при получении чатов:', error)
        return []
    }
}
export async function getUserContactsTG(profileId: string): Promise<{ id: string; name: string }[]> {
    try {
        const resContacts = await wappGet(`/tapi/sync/contacts/get?profile_id=${profileId}`)

        // Проверяем, что resContacts.contacts — это массив
        if (!Array.isArray(resContacts.contacts)) {
            console.warn('Ожидался массив контактов, но пришло:', resContacts.contacts)
            return []
        }

        // Фильтруем только пользователей
        const userContacts = resContacts.contacts.filter((contact: any) => contact.type === 'user')

        // Формируем массив { id, name }
        const contactList = userContacts.map((contact: any) => ({
            value: contact.id,
            label: contact.pushname
        }))

        return contactList
    } catch (error) {
        console.error('Ошибка при получении контактов:', error)
        return []
    }
}



// Пример GET-запроса
// import { wappGet, wappPost } from '@/services/cs-wappi';
//
// async function fetchQR(profileId: string): Promise<any> {
//     const result = await wappGet(`/api/sync/qr/get?profile_id=${profileId}`);
//     console.log(result);
// }
//
// // Пример POST-запроса
//
// async function sendQRData(data: any): Promise<any> {
//     const result = await wappPost('/api/sync/qr/send', data);
//     console.log(result);
// }
