
export async function wappGet(params: string):Promise<any> {
    const token = '785026ea43c1bb0b1b842189cbca9197c05f424e';
    const myHeaders = new Headers();
    myHeaders.append("Authorization", token);
    const reqOptions = {
        method: 'GET',
        headers: myHeaders,
        redirect: 'follow',
    };

    try {
        const response = await fetch(`https://wappi.pro${params}`, reqOptions);
        if (!response.ok) {
            console.error(`HTTP error! status: ${response.status}`);
            return;
        }

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Ошибка при вызове fetch:', error);
    }
}