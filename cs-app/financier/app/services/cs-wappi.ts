
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
                console.error(`HTTP error! status: ${response.status}`);
                return null;
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
                return null;
            }

            const result = await response.json();
            return result;
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
