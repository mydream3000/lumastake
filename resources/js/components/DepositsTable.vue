<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <!-- Mobile Version -->
    <div class="lg:hidden space-y-4">
      <div v-for="row in transactions" :key="row.id" class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm relative overflow-hidden">
          <div class="flex items-center justify-between mb-6 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Type</div>
                  <div class="text-sm font-black text-cabinet-text-main leading-tight">{{ row.type }}</div>
              </div>
              <span
                class="px-2 py-1 rounded-lg text-[10px] font-black inline-block uppercase"
                :class="getStatusClass(row.status)"
              >
                {{ row.status }}
              </span>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Amount</div>
                  <div class="text-base font-black text-cabinet-blue">{{ row.amount }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Date</div>
                  <div class="text-xs font-bold text-gray-500">{{ formatDateShort(row.created_at) }}</div>
              </div>
          </div>

          <button
            @click="showDetails(row)"
            class="w-full py-3.5 bg-gray-50 text-cabinet-text-main text-[11px] font-black rounded-2xl border border-gray-100 transition-all active:scale-95 uppercase tracking-[0.2em]"
          >
            View Details
          </button>
      </div>

      <div v-if="!transactions.length" class="text-center py-12 bg-white rounded-[32px] border border-gray-100 shadow-sm">
          <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No deposits found</p>
      </div>
    </div>

    <!-- Desktop Version -->
    <data-table
      class="hidden lg:block"
      :data="transactions"
      :columns="columns"
      :grid-cols="14"
      default-sort="created_at"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-status="{ value }">
        <span
          class="px-3 py-1 rounded-full text-xs font-bold inline-block uppercase"
          :class="getStatusClass(value)"
        >
          {{ value }}
        </span>
      </template>

      <template #cell-details="{ row }">
        <button
          @click="showDetails(row)"
          class="px-4 lg:px-6 py-1.5 lg:py-2 text-xs lg:text-sm rounded-md bg-cabinet-blue text-white hover:bg-cabinet-blue/80 font-bold uppercase transition"
        >
          View
        </button>
      </template>
    </data-table>
  </div>

  <!-- Details Modal -->
  <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModal = false">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
      <div class="relative bg-white rounded-xl max-w-3xl w-full p-8 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-100">
          <h3 class="text-2xl font-bold text-cabinet-text-main">
            Deposit #{{ selectedTransaction?.id }}
          </h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div v-if="selectedTransaction" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
              <div class="text-xs font-bold text-gray-400 uppercase mb-1">Type</div>
              <div class="text-cabinet-text-main font-bold">{{ selectedTransaction.type }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
              <div class="text-xs font-bold text-gray-400 uppercase mb-1">Amount</div>
              <div class="text-cabinet-blue font-bold text-xl">{{ selectedTransaction.amount }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
              <div class="text-xs font-bold text-gray-400 uppercase mb-1">Status</div>
              <span
                class="px-3 py-1 rounded-full text-xs font-bold inline-block uppercase mt-1"
                :class="getStatusClass(selectedTransaction.status)"
              >
                {{ selectedTransaction.status }}
              </span>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
              <div class="text-xs font-bold text-gray-400 uppercase mb-1">Created</div>
              <div class="text-cabinet-text-main font-medium">{{ selectedTransaction.created_at }}</div>
            </div>
          </div>

          <div v-if="selectedTransaction.description" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <div class="text-xs font-bold text-gray-400 uppercase mb-2">Description</div>
            <div class="text-cabinet-text-main">{{ selectedTransaction.description }}</div>
          </div>

          <div v-if="selectedTransaction.tx_hash" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <div class="text-xs font-bold text-gray-400 uppercase mb-2">Transaction Hash</div>
            <div class="text-cabinet-text-main font-mono text-xs break-all">{{ selectedTransaction.tx_hash }}</div>
          </div>

          <div v-if="selectedTransaction.meta && Object.keys(selectedTransaction.meta).length > 0" class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <div class="text-xs font-bold text-gray-400 uppercase mb-4">Additional Information</div>
            <div class="space-y-3">
              <div v-if="selectedTransaction.meta.wallet_address" class="flex justify-between items-start">
                <span class="text-gray-500 font-medium">Wallet Address:</span>
                <span class="text-cabinet-text-main font-mono text-xs break-all ml-4">{{ selectedTransaction.meta.wallet_address }}</span>
              </div>
              <div v-if="selectedTransaction.meta.network" class="flex justify-between">
                <span class="text-gray-500 font-medium">Network:</span>
                <span class="text-cabinet-text-main font-bold">{{ selectedTransaction.meta.network }}</span>
              </div>
              <div v-if="selectedTransaction.meta.confirmations !== undefined" class="flex justify-between">
                <span class="text-gray-500 font-medium">Confirmations:</span>
                <span class="text-cabinet-text-main">{{ selectedTransaction.meta.confirmations }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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

const transactions = ref([])
const loading = ref(true)
const showModal = ref(false)
const selectedTransaction = ref(null)

const columns = [
  {
    key: 'type',
    label: 'Type',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-cabinet-blue font-bold'
  },
  {
    key: 'amount',
    label: 'Amount',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-bold text-cabinet-text-main'
  },
  {
    key: 'status',
    label: 'Status',
    sortable: false,
    span: 2,
    headerClass: 'text-center',
    cellClass: 'text-center'
  },
  {
    key: 'created_at',
    label: 'Date',
    sortable: true,
    span: 4,
    headerClass: 'text-left',
    cellClass: 'text-sm text-gray-400',
    sortValue: (row) => parseDateToTimestamp(row.created_at),
    format: (value) => formatDateShort(value)
  },
  {
    key: 'details',
    label: 'Details',
    sortable: false,
    span: 2,
    headerClass: 'text-center',
    cellClass: 'text-center'
  }
]

async function fetchTransactions() {
  try {
    const response = await axios.get(props.dataUrl)
    transactions.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch transactions:', error)
  } finally {
    loading.value = false
  }
}

function getStatusClass(status) {
  const classes = {
    'confirmed': 'bg-green-100 text-green-600',
    'pending': 'bg-blue-100 text-cabinet-blue',
    'failed': 'bg-red-100 text-cabinet-red',
    'cancelled': 'bg-gray-100 text-gray-400'
  }
  return classes[status] || 'bg-gray-100 text-gray-400'
}

async function showDetails(row) {
  try {
    const response = await axios.get(`/dashboard/transactions/${row.id}/details`)
    if (response.data.success) {
      selectedTransaction.value = response.data.data
      showModal.value = true
    }
  } catch (error) {
    console.error('Failed to fetch transaction details:', error)
    alert('Failed to load transaction details')
  }
}

onMounted(() => {
  fetchTransactions()
})
</script>

