<?php
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подписание документа</title>
    <script src="cadesplugin_api.js"></script>
    <script src="/local/lib/js/jquery-3.6.0.min.js"></script>
</head>
<body>
<input type="file" id="fileInput" />
<button id="signButton">Подписать файл</button>

    <script>
        document.getElementById('signButton').addEventListener('click', async function() {
        try {
            // Получаем файл из input
            const fileInput = document.getElementById('fileInput');
            if (fileInput.files.length === 0) {
                alert("Пожалуйста, выберите файл для подписи.");
                return;
            }

            const file = fileInput.files[0];
            console.log(file)
            const reader = new FileReader();
            console.log(reader)
            reader.readAsText(file);
            reader.onload = async function(event) {
                console.log(event)
                    const fileContent = event.target.result;
                    console.log("Содержимое файла:", fileContent);
                console.log('reader')


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

                const oSigner = await cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
                await oSigner.propset_Certificate(selectedCert);
                const oSignedData = await cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
                await oSignedData.propset_Content(fileContent); // Используем содержимое файла
                const signature = await oSignedData.SignCades(oSigner, cadesplugin.CADESCOM_CADES_BES, true); // Открепленная подпись
                const signatureBase64 = window.btoa(unescape(encodeURIComponent(signature))); // Безопасное кодирование

                console.log("Подпись:", signatureBase64);
                alert("Файл успешно подписан!");

                $.ajax({
                    type: "POST",
                    url: "/cassoftApp/market/cloudReceiptsMB/ajax/sendBank.php",
                    data: {
                        signatureBase64: signatureBase64,
                        //   paymentData: JSON.stringify(paymentData),
                    },
                    dataType: "html",
                    success: function (response) {
                        console.log(response)
                    }
                })
            }
            reader.onerror = function(event) {
                console.error("Ошибка чтения файла:", event.target.error);
                alert("Произошла ошибка при чтении файла.");
            };
            } catch (err) {
        console.log("Ошибка: ", err);
        alert("Ошибка при подписании: " + err.message);
    }
});

async function createDetachedSignature(selectedCert) {

    try

    { const oSigner = await cadesplugin.CreateObjectAsync("CAdESCOM.CPSigner");
        await oSigner.propset_Certificate(selectedCert);
        const oSignedData = await cadesplugin.CreateObjectAsync("CAdESCOM.CadesSignedData");
        await oSignedData.propset_Content("Hello, world!"); // Добавляем контент
        const signature = await oSignedData.SignCades(oSigner, cadesplugin.CADES_BES, true); // Открепленная подпись
        const signatureBase64 = window.btoa(unescape(encodeURIComponent(signature))); // Безопасное кодирование
        return signatureBase64; }

    catch (error)



    { console.error("Ошибка при создании подписи:", error); }

}
    </script>
</body>
</html>