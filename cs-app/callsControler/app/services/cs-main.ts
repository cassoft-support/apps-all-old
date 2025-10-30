// import {
//     getThumbnailSrc,
//     formatDate,
//     formatSubscriptionEndDate,
//     authCheck,
// } from '@/services/cs-main';


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