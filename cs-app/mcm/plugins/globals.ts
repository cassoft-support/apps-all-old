import { Plugin } from '@nuxt/types';

const csPlugin: Plugin = (context, inject) => {
// Добавляем глобальную константу
   inject('keyWappi', '785026ea43c1bb0b1b842189cbca9197c05f424e');
    const profileCsMcm = async (data: string) => {
        try {
            const response = await fetch('https://app.cassoft.ru/local/CSlibs/classes/app/mcm/functionVue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data) // Убедитесь, что данные сериализованы в JSON
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json(); // Предполагается, что ответ в формате JSON
            return result;
        } catch (error) {
            console.error('Ошибка при выполнении запроса:', error);
            throw error; // Пробрасываем ошибку дальше, чтобы её можно было обработать в компоненте
        }
    };
// Инжектируем функцию, чтобы она была доступна через this.$myUtilityFunction
    inject('profileCsMcm', profileCsMcm);
};

export default csPlugin;
