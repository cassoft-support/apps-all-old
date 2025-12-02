<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import dayjs from 'dayjs'

// Регистрация всех компонентов Chart.js
Chart.register(...registerables)

// Список действий
const eventsList = {
  ChannelSubscription: 'Подписка на канал',
  ChannelUNSubscribe: 'Отписка от канала',
  BotActivation: 'Активация бота',
  CarSearch: 'Поиск автомобиля',
  CarTracked: 'Добавление авто в отслеживание',
  ApplicationSubmitted: 'Заявка'
}

// Опции для выбора диапазона дат
const dateRangeOptions = ref([
  { value: 'all', label: 'За все время' },
  { value: 'today', label: 'Сегодня' },
  { value: 'yesterday', label: 'Вчера' },
  { value: 'this_week', label: 'На этой неделе' },
  { value: 'this_month', label: 'В этом месяце' },
  { value: 'custom', label: 'Интервал' }
])

// Переменные для фильтрации
const selectedDateRange = ref('all')
const dateFrom = ref<string | null>(null)
const dateTo = ref<string | null>(null)

// Данные для графика
const chartCanvas = ref<HTMLCanvasElement | null>(null)
const chartInstance = ref<Chart | null>(null)
const chartData = ref({
  labels: [], // Метки (даты)
  datasets: [] // Наборы данных
})

// Пример данных из API
const tableData = ref<any[]>([])

// Функция загрузки данных с фильтрацией по диапазону дат
const loadTableData = async () => {
  const dateFilter: Record<string, string> = {}

  if (selectedDateRange.value === 'today') {
    dateFilter['>=DATE_CREATE'] = dayjs().startOf('day').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().endOf('day').toISOString()
  } else if (selectedDateRange.value === 'yesterday') {
    dateFilter['>=DATE_CREATE'] = dayjs().subtract(1, 'day').startOf('day').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().subtract(1, 'day').endOf('day').toISOString()
  } else if (selectedDateRange.value === 'this_week') {
    dateFilter['>=DATE_CREATE'] = dayjs().startOf('week').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().endOf('week').toISOString()
  } else if (selectedDateRange.value === 'this_month') {
    dateFilter['>=DATE_CREATE'] = dayjs().startOf('month').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs().endOf('month').toISOString()
  } else if (selectedDateRange.value === 'custom' && dateFrom.value && dateTo.value) {
    dateFilter['>=DATE_CREATE'] = dayjs(dateFrom.value).startOf('day').toISOString()
    dateFilter['<=DATE_CREATE'] = dayjs(dateTo.value).endOf('day').toISOString()
  }

// Пример вызова API для получения данных
  const response = await $b24.callMethod('entity.item.get', {
    ENTITY: 'events',
    filter: {
      ...dateFilter
    }
  })

  tableData.value = response.getData().result
}

// Функция применения фильтра и обновления графика
const applyDateFilter = async () => {
  await loadTableData()
  updateChartData()
}

// Функция обновления данных графика
const updateChartData = () => {
// Группировка данных по `PROPERTY_action`
  const groupedData: Record<string, number> = {}

  tableData.value.forEach(item => {
    const action = item.PROPERTY_VALUES?.action
    if (action) {
      groupedData[action] = (groupedData[action] || 0) + 1
    }
  })

// Обновление данных графика
  chartData.value = {
    labels: Object.keys(eventsList).map(key => eventsList[key]), // Метки (названия действий)
    datasets: [
      {
        label: 'Количество событий',
        data: Object.keys(eventsList).map(key => groupedData[key] || 0), // Количество событий
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }
    ]
  }

// Перерисовка графика
  destroyChart()
  initializeChart()
}

// Функция инициализации графика
const initializeChart = () => {
  if (chartCanvas.value) {
    chartInstance.value = new Chart(chartCanvas.value, {
      type: 'line', // Линейный график
      data: chartData.value,
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }
}

// Уничтожение графика при обновлении данных
const destroyChart = () => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
    chartInstance.value = null
  }
}

// Инициализация графика при монтировании компонента
onMounted(() => {
  initializeChart()
})

// Обновление графика при изменении данных
watch(chartData, () => {
  destroyChart()
  initializeChart()
})
</script>

<style scoped>
canvas {
max-width: 100%;
height: auto;
}
</style>
<template>
  <div>
    <!-- Блок выбора диапазона дат -->
    <div class="flex flex-row justify-start items-center gap-2 mb-4 w-fit">
      <B24Select
          v-model="selectedDateRange"
          :items="dateRangeOptions"
          class="w-auto"
      />
      <div v-if="selectedDateRange === 'custom'" class="flex items-center gap-2">
        <B24Input
            v-model="dateFrom"
            type="date"
            placeholder="От"
            class="w-36"
        />
        <B24Input
            v-model="dateTo"
            type="date"
            placeholder="До"
            class="w-36"
        />
      </div>
    </div>

    <!-- Кнопка "Применить" -->
    <div class="mb-4 mt-4 flex flex-row w-full justify-end">
      <B24Button
          label="Применить"
          :color="color"
          @click="applyDateFilter"
      />
    </div>

    <!-- График -->
    <div class="relative bg-white dark:bg-base-900 rounded w-full p-4">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>