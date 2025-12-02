<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels' // Плагин для отображения текста над столбиками
import dayjs from 'dayjs'
import { initializeB24Frame, B24Frame } from '@bitrix24/b24jssdk'
import type { ButtonProps } from '@bitrix24/b24ui-nuxt/types/index.ts'

// Регистрация всех компонентов Chart.js и плагина
Chart.register(...registerables, ChartDataLabels)

let $b24: B24Frame | null = null // Объявляем $b24 как null, чтобы избежать ошибок до инициализации

export interface ExampleProps {
  color?: ButtonProps['color']
}

const props = withDefaults(defineProps<ExampleProps>(), {
  color: 'primary'
})

// Список действий
const eventsList = {
  ChannelSubscription: 'Подписка на канал',
  ChannelUNSubscribe: 'Отписка от канала',
  BotActivation: 'Активация бота',
  CarSearch: 'Поиск автомобиля',
  CarTracked: 'Добавление авто в отслеживание',
  ApplicationSubmitted: 'Заявка',
  IndividualSelection: 'Индивидуальная Заявка'

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
const selectedDateRange = ref('today')
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
  if (!$b24) {
    console.error('B24 Frame не инициализирован')
    return
  }

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

  try {
    // Пример вызова API для получения данных
    const eventsAll = $b24.fetchListMethod(
        'entity.item.get',
        {
          ENTITY: 'events',
          filter: {
            ...dateFilter
          },
          sort: { ID: 'ASC' }
        },
        'ID'
    )

    const allItems: any[] = []

    for await (const chunk of eventsAll) {
      if (Array.isArray(chunk)) {
        allItems.push(...chunk)
      }
    }

    tableData.value = allItems
  } catch (error) {
    console.error('Ошибка при загрузке данных:', error)
  }
}

// Функция применения фильтра и обновления графика
const applyDateFilter = async () => {
  if (!$b24) {
    console.error('B24 Frame не инициализирован')
    return
  }

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
        backgroundColor: 'rgba(75, 192, 192, 0.8)',
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
      type: 'bar', // Тип графика: столбчатый
      data: chartData.value,
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          },
          datalabels: {
            anchor: 'end',
            align: 'top',
            color: '#000',
            font: {
              weight: 'bold'
            },
            formatter: (value: number) => value // Отображение значения над столбиком
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

// Инициализация B24 Frame и графика при монтировании компонента
onMounted(async () => {
  try {
    $b24 = await initializeB24Frame()
    console.log('B24 Frame успешно инициализирован')

    // Загрузка данных после инициализации
    await loadTableData()
    initializeChart()
    await applyDateFilter()
  } catch (error) {
    console.error('Ошибка при инициализации B24 Frame:', error)
  }
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