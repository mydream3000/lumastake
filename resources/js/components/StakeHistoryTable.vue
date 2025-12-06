<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="history"
      :columns="columns"
      :grid-cols="20"
      default-sort="start_date"
      default-sort-order="desc"
      :default-per-page="10"
      :row-class="getRowClass"
    >
      <template #cell-profit="{ value }">
        <span class="text-cabinet-green font-semibold">{{ value }}%</span>
      </template>

      <template #cell-earned_profit="{ value, row }">
        <span
          class="font-semibold"
          :class="{
            'text-cabinet-red': row.earned_profit_raw < 0,
            'text-cabinet-green': row.earned_profit_raw > 0 && row.status !== 'active'
          }"
        >
          {{ value }}
        </span>
      </template>

      <template #cell-status="{ value }">
        <span
          class="px-3 py-1 rounded-full text-sm font-medium inline-block"
          :class="getStatusClass(value)"
        >
          {{ value.charAt(0).toUpperCase() + value.slice(1) }}
        </span>
      </template>
    </data-table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import DataTable from './DataTable.vue'
import { parseDateToTimestamp, formatDateShort } from '../utils/dateFormatter'

const props = defineProps({
  dataUrl: {
    type: String,
    required: true
  }
})

const history = ref([])
const loading = ref(true)

const columns = [
  {
    key: 'pool_name',
    label: 'Pool Name',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-medium'
  },
  {
    key: 'duration',
    label: 'Duration',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'profit',
    label: 'Profit',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'amount',
    label: 'Amount Staked',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-semibold'
  },
  {
    key: 'earned_profit',
    label: 'Earned Profit',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-semibold',
    sortValue: (row) => parseFloat(row.earned_profit_raw || 0)
  },
  {
    key: 'start_date',
    label: 'Start Date',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm',
    sortValue: (row) => parseDateToTimestamp(row.start_date),
    format: (value) => formatDateShort(value)
  },
  {
    key: 'end_date',
    label: 'End Date',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm',
    sortValue: (row) => parseDateToTimestamp(row.end_date),
    format: (value) => formatDateShort(value)
  },
  {
    key: 'status',
    label: 'Status',
    sortable: true,
    span: 3,
    headerClass: 'text-center',
    cellClass: 'text-center'
  }
]

async function fetchHistory() {
  try {
    const response = await axios.get(props.dataUrl)
    history.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch stake history:', error)
  } finally {
    loading.value = false
  }
}

function getStatusClass(status) {
  const classes = {
    'completed': 'bg-cabinet-green/20 text-cabinet-green border border-cabinet-green/40',
    'unstaked': 'bg-gray-400/20 text-gray-600 border border-gray-400/40',
    'cancelled': 'bg-cabinet-red/20 text-cabinet-red border border-cabinet-red/40'
  }
  return classes[status] || 'bg-gray-400/20 text-gray-400'
}

function getRowClass(row) {
  // Разные фоны строк в зависимости от статуса
  if (row.status === 'completed') {
    return 'bg-cabinet-green/10 hover:bg-cabinet-green/20'
  } else if (row.status === 'unstaked') {
    return 'bg-cabinet-table-row/40 hover:bg-cabinet-table-row/60'
  } else if (row.highlight) {
    // Для выделенных записей
    return 'bg-cabinet-orange/10 hover:bg-cabinet-orange/20 border border-cabinet-orange/40'
  }
  return 'bg-cabinet-table-row/40 hover:bg-cabinet-table-row/60'
}

onMounted(() => {
  fetchHistory()
})
</script>
