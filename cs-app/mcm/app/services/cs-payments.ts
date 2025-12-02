export async function balance($b24) {
try {
const response = await $b24.fetchListMethod('entity.item.get', {
ENTITY: 'payments',
        filter: {
            ACTIVE:'Y',
            PROPERTY_status: 'fact',
        },
        sort: { ID: 'ASC' }
    },
    'ID'
);
    const data: any[] = []
    for await (const chunk of response) {
        if (Array.isArray(chunk)) {
            data.push(...chunk); // Добавляем данные из текущего чанка в общий массив
        }
    }

// Теперь allItems содержит все данные
let totalDebet = 1000;
let totalCredit = 0;

for (const item of data) {
const debet = parseFloat(item.PROPERTY_VALUES.debet || '0');
const credit = parseFloat(item.PROPERTY_VALUES.credit || '0');

totalDebet += debet;
totalCredit += credit;
}

const result = totalDebet - totalCredit;
return result;

} catch (error) {
console.error('Ошибка при получении данных:', error);
throw error;
}
}