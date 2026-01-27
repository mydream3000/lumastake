<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <!-- Mobile Version -->
    <div class="lg:hidden space-y-4">
      <div v-for="row in history" :key="row.id" class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm relative overflow-hidden">
          <div class="flex items-center justify-between mb-4 relative">
              <div class="text-sm font-black text-cabinet-text-main leading-tight">{{ row.pool_name }}</div>
              <span
                class="px-2 py-1 rounded-lg text-[10px] font-black inline-block uppercase"
                :class="getStatusClass(row.status)"
              >
                {{ row.status }}
              </span>
          </div>

          <div class="grid grid-cols-2 gap-x-4 gap-y-4 relative">
              <div class="bg-gray-50 p-3 rounded-2xl">
                  <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Amount</div>
                  <div class="text-xs font-black text-cabinet-text-main">{{ row.amount }}</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-2xl">
                  <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Earned</div>
                  <div
                    class="text-xs font-black"
                    :class="{
                        'text-red-600': row.earned_profit_raw < 0,
                        'text-green-600': row.earned_profit_raw > 0 && row.status !== 'active'
                    }"
                  >
                    {{ row.earned_profit }}
                  </div>
              </div>
              <div>
                  <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Start Date</div>
                  <div class="text-[11px] font-bold text-gray-500">{{ formatDateShort(row.start_date) }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5">End Date</div>
                  <div class="text-[11px] font-bold text-gray-500">{{ formatDateShort(row.end_date) }}</div>
              </div>
          </div>
      </div>

      <div v-if="!history.length" class="text-center py-12 bg-white rounded-[32px] border border-gray-100 shadow-sm">
          <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No history found</p>
      </div>
    </div>

    <!-- Desktop Version -->
    <data-table
      class="hidden lg:block"
      :data="history"
      :columns="columns"
      :grid-cols="20"
      default-sort="start_date"
      default-sort-order="desc"
      :default-per-page="10"
      :row-class="getRowClass"
    >
      <template #cell-profit="{ value }">
        <span class="text-[#2BA6FF] font-bold">{{ value }}%</span>
      </template>

      <template #cell-earned_profit="{ value, row }">
        <span
          class="font-bold"
          :class="{
            'text-red-600': row.earned_profit_raw < 0,
            'text-green-600': row.earned_profit_raw > 0 && row.status !== 'active'
          }"
        >
          {{ value }}
        </span>
      </template>

      <template #cell-status="{ value }">
        <span
          class="px-3 py-1 rounded-full text-xs font-bold inline-block uppercase"
          :class="getStatusClass(value)"
        >
          {{ value }}
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
    'completed': 'bg-green-100 text-green-600',
    'unstaked': 'bg-gray-100 text-gray-500',
    'cancelled': 'bg-red-100 text-red-600'
  }
  return classes[status] || 'bg-gray-100 text-gray-400'
}

function getRowClass(row) {
  // Разные фоны строк в зависимости от статуса
  if (row.status === 'completed') {
    return 'bg-green-50/30 hover:bg-green-50/50 border-b border-gray-100'
  } else if (row.status === 'unstaked') {
    return 'bg-white border-b border-gray-100 hover:bg-gray-50'
  } else if (row.highlight) {
    // Для выделенных записей
    return 'bg-blue-50 hover:bg-blue-100/50 border-b border-gray-100'
  }
  return 'bg-white border-b border-gray-100 hover:bg-gray-50'
}

onMounted(() => {
  fetchHistory()
})
</script>
