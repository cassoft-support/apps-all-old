// import {
//     getThumbnailSrc,
//     formatDate,
//     formatSubscriptionEndDate,
//     authCheck,
// } from '@/services/cs-main';
import { wappGet } from '@/services/cs-wappi';

//Функция добавления типа файла в base64
export function getThumbnailSrc(base64Data: string): string {
if (!base64Data.startsWith('data:image')) {
    return `data:image/jpeg;base64,${base64Data}`;
}
return base64Data;
}

// Функция для форматирования даты
export function formatDate(timestamp: number): string {
    const date = new Date(timestamp * 1000);
    const now = new Date();

    const isToday =
        date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear();

    if (isToday) {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    } else {
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}.${month}.${year}`;
    }
}

// Функция для форматирования даты окончания подписки
export function formatSubscriptionEndDate(timestamp: string): string {
    const date = new Date(Number(timestamp) * 1000);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return `Дата окончания подписки: ${date.toLocaleDateString('ru-RU', options)}`;
}

// Функция для проверки авторизации пользователя
export async function authCheck(member: string): Promise<Response> {
    const data = {
        member_id: member,
    };

    const response = await fetch('https://app.cassoft.ru/cassoftApp/market/mcm/index.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    const result = await response.text(); // или .json(), если это JSON
    return result; // возвращает "Y" или "N"
}



export async function processProfile($b24: B24Frame) {

    const resUser = await $b24.callMethod('user.current');
    const user = resUser.getData().result;
    const resAdmin = await $b24.callMethod('user.admin');
    const isAdmin = resAdmin.getData().result;

    interface SetupMessagerItem {
        PROPERTY_VALUES: {
            CS_PROFILE_ID: string;
            CS_LINE: string;
            CS_TYPE: string;
            CS_USERS: string;
            CS_ADMIN: string;
        };
    }

    interface LineItem {
        ID: string;
        LINE_NAME: string;
        QUEUE?: string[];
    }

    interface ProfileLineEntry {
        key: string;
        value: string | null;
        type: string;
        status?: boolean;
    }

    const setupMesGet = await $b24.callMethod(
        'entity.item.get',
        {
            entity: 'setup_messager',
            filter: { ACTIVE: 'Y' }
        }
    );

    const setup = setupMesGet.getData().result as SetupMessagerItem[];
    console.log(setup, 'setup');

    const imopenlinesGet = await $b24.callBatch({
        OpenLines: {
            method: 'imopenlines.config.list.get',
            params: {
                PARAMS: { order: { ID: 'ASC' }, filter: {} },
                OPTIONS: { QUEUE: 'Y' }
            }
        }
    }, true);

    const imopenlinesData = imopenlinesGet.getData().OpenLines as LineItem[];
    const lineMap = new Map<string, string>(
        imopenlinesData.map(item => [item.ID, item.LINE_NAME])
    );

    const tempProfiles = new Map<string, ProfileLineEntry>();

    for (const item of setup) {
        const { CS_PROFILE_ID, CS_LINE, CS_TYPE, CS_USERS, CS_ADMIN } = item.PROPERTY_VALUES;

        if (!CS_PROFILE_ID || !CS_TYPE) continue;

        // ✅ Если нет линии — пропускаем
        if (!CS_LINE) continue;

        // ✅ Получаем имя линии
        const lineName = lineMap.get(CS_LINE) || null;

        if (!lineName) continue;

        // ✅ Если пользователь — админ, добавляем профиль без проверки доступа
        if (isAdmin) {
            const status = await checkProfileStatus(CS_PROFILE_ID);
            tempProfiles.set(CS_PROFILE_ID, {
                key: CS_PROFILE_ID,
                value: lineName,
                type: CS_TYPE,
                status:status
            });
            continue;
        }

        // ✅ Если пользователь — не админ, проверяем доступ
        let users = [];
        if (typeof CS_USERS === 'string' && CS_USERS.trim() !== '') {
            try {
                users = JSON.parse(CS_USERS);
            } catch (e) {
                console.error('Ошибка парсинга CS_USERS:', e);
            }
        }

        let admins = [];
        if (typeof CS_ADMIN === 'string' && CS_ADMIN.trim() !== '') {
            try {
                admins = JSON.parse(CS_ADMIN);
            } catch (e) {
                console.error('Ошибка парсинга CS_ADMIN:', e);
            }
        }

        // ✅ Проверка, что CS_USERS — это пустая строка
        const isUsersEmpty = typeof CS_USERS === 'string' && CS_USERS.trim() === '';

        // ✅ Проверка, что CS_USERS — это пустой массив в JSON-формате
        const isUsersEmptyArray = typeof CS_USERS === 'string' && CS_USERS.trim() === '[]';

        // ✅ Определяем, является ли CS_USERS пустым (строка или массив)
        const isUsersEmptyOrEmptyArray = isUsersEmpty || isUsersEmptyArray;

        // ✅ Проверка, что пользователь в списке
        const isUserInUsers = users.includes(user.ID);

        // ✅ Проверка, что пользователь — админ
        const isUserAdmin = admins.includes(user.ID);

        // ✅ Определяем доступ
        const hasAccess =
            isUsersEmptyOrEmptyArray || // Доступ всем (пустая строка) или только админ (пустой массив)
            isUserInUsers || // Пользователь в списке
            isUserAdmin; // Пользователь — админ

        if (hasAccess) {
            const status = await checkProfileStatus(CS_PROFILE_ID);
            tempProfiles.set(CS_PROFILE_ID, {
                key: CS_PROFILE_ID,
                value: lineName,
                type: CS_TYPE,
                status:status
            });
        }
    }

    return Array.from(tempProfiles.values());
}



export async function checkProfileStatus(profileId: string): Promise<boolean> {

    try {
        const result = await wappGet(`/api/sync/get/status?profile_id=${profileId}`);
        return result.authorized || false;
    } catch (error) {
        console.error(`Ошибка при проверке статуса профиля ${profileId}:`, error);
        return false;
    }
}


export function resizeWindow() {
    const script = document.createElement('script');
    script.src = 'https://api.bitrix24.com/api/v1/';
    script.onload = () => {
        if (typeof BX24 !== 'undefined' && typeof BX24.resizeWindow === 'function') {
            nextTick(() => {
                const { scrollWidth, scrollHeight } = BX24.getScrollSize();
                console.log(scrollHeight,'scrollHeight')
                BX24.resizeWindow(scrollWidth, scrollHeight + 50);
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