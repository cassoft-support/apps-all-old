<?php
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подписание документа</title>
    <script src="cadesplugin_api.js"></script>
</head>
<body>
    <button id="signButton">Подписать документ</button>

    <script>
document.getElementById('signButton').addEventListener('click', async function() {
    try {
        // Инициализация плагина
        await cadesplugin;

                // Получение списка сертификатов
                const oStore = await cadesplugin.CreateObjectAsync("CAdESCOM.Store");
                await oStore.Open();
                const certs = await oStore.Certificates;
                const count = await certs.Count;

                if (count === 0) {
                    alert("Сертификаты не найдены");
                    return;
                }

                // Перебор сертификатов и выбор пользователем
                let selectedCert = null;
                for (let i = 1; i <= count; i++) {
            const cert = await certs.Item(i);
                    const subjectName = await cert.SubjectName;
                    if (confirm(`Использовать сертификат: ${subjectName}?`)) {
                        selectedCert = cert;
                        break;
                    }
                }

                if (!selectedCert) {
                    alert("Сертификат не выбран");
                    return;
                }

                // Данные для подписи
                const dataToSign = "24534659-8cd4-4dde-b9d3-b27b01902fb2";

                // Создание подписи
                const oSigner = await cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
                await oSigner.propset_Certificate(selectedCert);

                const oSignedData = await cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
                await oSignedData.propset_Content(dataToSign);

                const signature = await oSignedData.SignCades(oSigner, cadesplugin.CADESCOM_CADES_BES);
// Преобразование подписи в Base64
        const signatureBase64 = btoa(signature);

        // Подготовка данных для отправки
        // const paymentData = {
        //     Operations: [dataToSign],
        //     SignBase64: signatureBase64
        // };
       //  sendPaymentData(paymentData)

        // Отправка данных в Модульбанк
        // const response = await fetch('https://api.modulbank.ru/v1/operation-upload/sign', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'Authorization': 'Bearer ZjE5MDRlN2MtYjgxMC00ZGU4LTgxZTUtZTVmMDAwMWJlMGE1MzE0MzgzYmUtNTNiNi00Mjk0LWFmYzMtNTljNmRlOGNjN2Zj'
        //     },
        //     body: JSON.stringify(paymentData)
        // });
// console.log('response')
//         if (response.ok) {
//             const responseData = await response.json();
//             alert("Платеж успешно отправлен: " + JSON.stringify(responseData));
//         } else {
//             alert("Ошибка при отправке платежа: " + response.statusText);
//         }
//                 alert("Подпись создана: " + signature);
//
//                 await oStore.Close();
            } catch (err) {
        console.log("Ошибка: ", err);
        alert("Ошибка при подписании: " + err.message);
    }
});

async function sendPaymentData(paymentData) {
    try {
        const response = await fetch('https://api.modulbank.ru/v1/operation-upload/sign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ZjE5MDRlN2MtYjgxMC00ZGU4LTgxZTUtZTVmMDAwMWJlMGE1MzE0MzgzYmUtNTNiNi00Mjk0LWFmYzMtNTljNmRlOGNjN2Zj'
            },
            body: JSON.stringify(paymentData)
        });
console.log(response)
        if (!response.ok) {
            throw new Error(`Ошибка HTTP: ${response.status}`);
        }

        const responseData = await response.json();
        alert("Платеж успешно отправлен: " + JSON.stringify(responseData));
    } catch (error) {
        console.log("Ошибка при отправке платежа: ", error);
        alert("Ошибка при отправке платежа: " + error.message);
    }
}
    </script>
</body>
</html>