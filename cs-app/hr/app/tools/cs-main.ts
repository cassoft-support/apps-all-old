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
export function formatDateString(dateString: string): string {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // месяцы с 0
    const year = date.getFullYear();
    return `${day}.${month}.${year}`;
}
// Функция для форматирования даты в формате "Февраль 2023"
export function formatExperienceDate(date: string): string {
    const dateObj = new Date(date)
    return `${dateObj.toLocaleString('ru', { month: 'long' })} ${dateObj.getFullYear()}`
}

// Функция для расчёта продолжительности работы
export function formatDuration(start: string, end: string | null): string {
    const startD = new Date(start)
    const endD = end ? new Date(end) : new Date()

    const diffMs = endD.getTime() - startD.getTime()

    const years = Math.floor(diffMs / (1000 * 60 * 60 * 24 * 365))
    const months = Math.floor((diffMs % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30))

    const result = []
    if (years > 0) result.push(`${years} ${years === 1 ? 'год' : years < 5 ? 'года' : 'лет'}`)
    if (months > 0) result.push(`${months} ${months === 1 ? 'месяц' : months < 5 ? 'месяца' : 'месяцев'}`)

    return result.join(' ')
}
// Функция для проверки авторизации пользователя
export async function authCheck(member: string, app_access: string, app: string, ): Promise<Response> {
    const data = {
        member_id: member,
        app: app,
        app_access: app_access,
    };
console.log(data,'dataAuth')
  //  const response = await fetch('https://app.cassoft.ru/local/CSlibs/tools/authCheck.php', {
    const response = await fetch('https://app.cassoft.ru/cs-app/cs-core/app/base/authCheck.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    const result = await response.text(); // или .json(), если это JSON
    return result; // возвращает "Y" или "N"
}



export async function processProfileOLD($b24: B24Frame) {

    const resUser = await $b24.callMethod('user.current');
    const user = resUser.getData().result;
    console.log(user,'userMain')
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
export async function processProfile($b24: B24Frame) {

    const resUser = await $b24.callMethod('user.current');
    const user = resUser.getData().result;
    console.log(user,'userMain')
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
        const { NAME } = item;

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
                profileCode: CS_PROFILE_ID,
                profileType: CS_TYPE,
                profileName: NAME,
                profileTypeUrl: CS_TYPE === 'Whatsapp' ? "/api" : "/tapi",
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
                profileCode: CS_PROFILE_ID,
                profileType: CS_TYPE,
                profileName: NAME,
                profileTypeUrl: CS_TYPE === 'Whatsapp' ? "/api" : "/tapi",
                key: CS_PROFILE_ID,
                value: lineName,
                type: CS_TYPE,
                status:status
            });
        }
    }

    return Object.fromEntries(tempProfiles.entries());
   // return Array.from(tempProfiles.values());
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
                BX24.resizeWindow(scrollWidth, scrollHeight);
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

export function scrollToBottom(containerRef) {
    if (!containerRef.value) return;

    const lastMessage = containerRef.value.lastElementChild;
    if (lastMessage) {
        lastMessage.scrollIntoView({ behavior: 'smooth' });
    }
}

//---------------------------------files---------

export async function searchUsersSetup($b24: any) {
    const itemResponse = await $b24.callMethod('entity.item.get', {
        ENTITY: 'setup',
        PARAMS: { filter: {}, select: ['*'] }
    })

    const itemData = itemResponse.getData().result[0]
    const resUsers = itemData?.PROPERTY_VALUES?.CS_USERS

    let usersArray: any[] = []

    if (typeof resUsers === 'string' && resUsers.trim() !== '') {
        try {
            usersArray = JSON.parse(resUsers)
        } catch (error) {
            console.error('Ошибка при разжатии JSON:', error)
            usersArray = [] // или обработать ошибку по-другому
        }
    }

    return usersArray
}
interface User {
    [key: string]: {
        manager_id: string
        position: string
    }
}

export async function editUsersSetup($b24: any, user: User, key: string) {
    const itemResponse = await $b24.callMethod('entity.item.get', {
        ENTITY: 'setup',
        PARAMS: { filter: {}, select: ['*'] }
    })

    const itemData = itemResponse.getData().result[0]
    const resUsers = itemData?.PROPERTY_VALUES?.CS_USERS

    let usersObject: Record<string, any> = {}

    if (typeof resUsers === 'string' && resUsers.trim() !== '') {
        try {
            usersObject = JSON.parse(resUsers)
        } catch (error) {
            console.error('Ошибка при разжатии JSON:', error)
            usersObject = {}
        }
    }

    usersObject = {
        ...usersObject,
        [key]: user
    }
console.log(usersObject,'usersObject')
    const usersObjectJson = JSON.stringify(usersObject)
console.log(usersObjectJson,'usersObjectJson')
    const params = {
        ENTITY: 'setup',
        ID: itemData.ID,
        PROPERTY_VALUES: {
            CS_USERS: usersObjectJson
        }
    }

    const itemUpdate = await $b24.callMethod('entity.item.update', params)
    return itemUpdate
}
export async function searchUsers($b24: any) {
    const resUsers = await $b24.callMethod("user.get", {
        USER_TYPE:'employee',
        ACTIVE:'Y'
    })
    const users = resUsers.getData().result
    return users;
}