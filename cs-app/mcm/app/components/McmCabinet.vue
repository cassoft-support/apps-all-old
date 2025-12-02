<script setup lang="ts">
const headers = [
  { title: 'Boat Type', key: 'name', align: 'start' },
  { title: 'Speed (knots)', key: 'speed', align: 'end' },
  { title: 'Length (m)', key: 'length', align: 'end' },
  { title: 'Price ($)', key: 'price', align: 'end' },
  { title: 'Year', key: 'year', align: 'end' },
];

// Тестовые данные для таблицы
const boats = [
  {
    name: 'Speedster',
    speed: 35,
    length: 22,
    price: 300000,
    year: 2021,
  },
  {
    name: 'OceanMaster',
    speed: 25,
    length: 35,
    price: 500000,
    year: 2020,
  },
  {
    name: 'Voyager',
    speed: 20,
    length: 45,
    price: 700000,
    year: 2019,
  },
  {
    name: 'WaveRunner',
    speed: 40,
    length: 19,
    price: 250000,
    year: 2022,
  },
];
</script>
<template>
  <v-app>
    <v-main>
      <div class="cabinet-page">
        <h1>Тестовая таблица</h1>

        <!-- Таблица с тестовыми данными -->
        <v-data-table-virtual
            :headers="headers"
            :items="boats"
            height="400"
            item-value="name"
            fixed-header
        ></v-data-table-virtual>
      </div>
    </v-main>
  </v-app>
</template>