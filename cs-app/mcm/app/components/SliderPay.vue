<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import SHA1 from 'crypto-js/sha1';

const {$initializeB24Frame} = useNuxtApp();
const $b24 = await $initializeB24Frame();
const authManager = $b24.auth;
const authData = authManager.getAuthData();
const currentUser = ref(null);

if ($b24) {
  const resUser = await $b24.callMethod('user.current');
  currentUser.value = resUser.getData().result;
  console.log(currentUser.value);
}

// Реактивные данные
const price = ref(2500);
const receiptContact = ref(currentUser.value ? currentUser.value.EMAIL : '');

const data = {
  testing: '1',
  salt: 'dPUTLtbMfcTGzkaBnGtseKlcQymCLrYI',
  order_id: '14425846',
  amount: '2500',
  client_id: authData.member_id,
  merchant: 'c7dd9e09-6676-4dcb-b187-fcc414e8b608',
  description: 'Заказ №14425845',
  client_phone: currentUser.value ? currentUser.value.PERSONAL_MOBILE : '',
  client_email: currentUser.value ? currentUser.value.EMAIL : '',
  callback_url: 'https://app.cassoft.ru/cs-app/cs-core/app/financer/modulbank/callback.php',
 // success_url: 'https://app.cassoft.ru/cs-app/cs-core/app/financer/modulbank/success.php',
  success_url: 'https://pay.modulbank.ru/success',
  receipt_contact: receiptContact.value,
  receipt_items: '[{"discount_sum": 0, "name": "Оплата лицензии", "payment_method": "full_prepayment", "payment_object": "service", "price": 2500, "quantity": 1, "sno": "usn_income", "vat": "none"}]',
  unix_timestamp: Math.floor(Date.now() / 1000),
};

const secretKey = '2C71BACA7FB4C8053768FCF07542E36A';

// Генерация подписи
const signature = ref('');

// Форма
const paymentForm = ref<HTMLFormElement | null>(null);

// Функция для Base64-кодирования UTF-8
function utf8ToBase64(str) {
  return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/gi, (match, p1) => {
    return String.fromCharCode(parseInt(p1, 16));
  }));
}

// Генерация подписи
const generateSignature = async (data, secretKey) => {
// Фильтруем пустые поля и убираем signature
  const filteredParams = {};
  for (const key in data) {
    if (key !== 'signature' && data[key] !== null && data[key] !== undefined && data[key] !== '') {
      filteredParams[key] = data[key];
    }
  }

// Сортируем ключи
  const keys = Object.keys(filteredParams).sort();

// Формируем строку
  const chunks = keys.map(k => {
    const value = String(filteredParams[k]);
    const base64 = utf8ToBase64(value);
    return `${k}=${base64}`;
  });

  const dataStr = chunks.join('&');

// SHA1 хэширование
  const firstHash = SHA1(secretKey + SHA1(secretKey + dataStr).toString()).toString();

  return firstHash.toLowerCase();
};

// Обновление данных при изменении цены или почты
const updateData = async () => {
// Обновляем amount
  data.amount = price.value.toFixed(2);

// Обновляем receipt_items
  const receiptItems = JSON.parse(data.receipt_items);
  receiptItems[0].price = price.value;
  data.receipt_items = JSON.stringify(receiptItems);

// Обновляем receipt_contact
  data.receipt_contact = receiptContact.value;

// Обновляем unix_timestamp
  data.unix_timestamp = Math.floor(Date.now() / 1000);

// Пересчитываем подпись
  signature.value = await generateSignature(data, secretKey);

// Логируем обновлённые данные
  console.log('Обновлённые данные:', data);
  console.log('Новая подпись:', signature.value);
};

// Инициализация данных при загрузке страницы
onMounted(async () => {
  await updateData(); // Вызываем обновление данных и подпись при загрузке
});

// Отправка формы
const submitForm = async () => {
// Генерируем подпись
  signature.value = await generateSignature(data, secretKey);

// Логируем отправляемые данные
  const form = paymentForm.value;
  if (form) {
    const formDataEntries = new FormData(form);
    const data = {};
    for (const [key, value] of formDataEntries) {
      data[key] = value;
    }
    console.log('Отправляемые данные:', data);
  }

// Отправляем форму
  if (paymentForm.value) {
    paymentForm.value.submit();
  }
};
</script>

<style scoped>
.payment-page {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
  font-family: sans-serif;
}

.form-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.pay-button {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.pay-button:hover {
  background-color: #0056b3;
}
</style>

<template>
  <div class="payment-page">
    <h1>Оплата</h1>
    <div class="form-container">
      <label>
        Цена:
        <input type="number" v-model.number="price" @input="updateData" />
      </label>

      <label>
        Почта для чека:
        <input type="email" v-model="receiptContact" @input="updateData" />
      </label>

      <form
          ref="paymentForm"
          method="POST"
          action="https://pay.modulbank.ru/pay"
          target="_blank"
      >
        <input type="text" name="testing" :value="data.testing" />
        <input type="text" name="salt" :value="data.salt" />
        <input type="text" name="order_id" :value="data.order_id" />
        <input type="text" name="amount" :value="data.amount" />
        <input type="text" name="client_id" :value="data.client_id" />
        <input type="text" name="merchant" :value="data.merchant" />
        <input type="text" name="description" :value="data.description" />
        <input type="text" name="client_phone" :value="data.client_phone" />
        <input type="text" name="client_email" :value="data.client_email" />
        <input type="text" name="callback_url" :value="data.callback_url" />
        <input type="text" name="success_url" :value="data.success_url" />
        <input type="text" name="receipt_contact" :value="data.receipt_contact" />
        <input type="text" name="receipt_items" :value="data.receipt_items" />
        <input type="text" name="unix_timestamp" :value="data.unix_timestamp" />
        <input type="text" name="signature" :value="signature" />
      </form>
      <button @click="submitForm">Оплатить</button>
    </div>
  </div>
</template>