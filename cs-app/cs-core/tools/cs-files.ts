import { uploadFileToBitrix } from './bitrix';

/**
 * Загружает файл в Base64
 * @param file - выбранный файл
 * @returns Promise<void>
 */
export async function handleFileUpload(file, uploadedFileRef) {
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async () => {
        const base64 = reader.result.split(',')[1]; // берем только base64 без префикса
        await uploadFileToBitrix(file.name, base64, uploadedFileRef);
    };
    reader.readAsDataURL(file);
}

/**
 * Начинаем запись аудио
 * @param  -
 * @returns
 */
export async function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream, {
                mimeType: 'audio/webm'
            });
            audioChunks = [];

            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };

            mediaRecorder.onstop = () => {
                const webmBlob = new Blob(audioChunks, { type: 'audio/webm' });

                // Конвертируем в base64
                const reader = new FileReader();
                reader.onload = async () => {
                    const base64 = reader.result.split(',')[1]; // берем только base64 без префикса
                    await sendAudioMessage(base64);
                };
                reader.readAsDataURL(webmBlob);
            };

            mediaRecorder.start();
        })
        .catch(err => {
            console.error('Ошибка при доступе к микрофону:', err);
        });
}