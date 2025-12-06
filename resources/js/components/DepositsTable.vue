<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="transactions"
      :columns="columns"
      :grid-cols="14"
      default-sort="created_at"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-status="{ value }">
        <span
          class="px-3 py-1 rounded-full text-sm font-medium inline-block"
          :class="getStatusClass(value)"
        >
          {{ value.charAt(0).toUpperCase() + value.slice(1) }}
        </span>
      </template>

      <template #cell-details="{ row }">
        <button
          @click="showDetails(row)"
          class="px-4 lg:px-6 py-1.5 lg:py-2 text-xs lg:text-sm rounded-md bg-cabinet-green text-white hover:bg-cabinet-green/80 font-semibold transition"
        >
          View
        </button>
      </template>
    </data-table>
  </div>

  <!-- Details Modal -->
  <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModal = false">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/70 transition-opacity"></div>
      <div class="relative bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg max-w-3xl w-full p-6 shadow-2xl border border-gray-700">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-700">
          <h3 class="text-2xl font-bold text-white">
            Deposit #{{ selectedTransaction?.id }}
          </h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div v-if="selectedTransaction" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-800/50 p-4 rounded-lg">
              <div class="text-sm text-gray-400 mb-1">Type</div>
              <div class="text-white font-semibold">{{ selectedTransaction.type }}</div>
            </div>
            <div class="bg-gray-800/50 p-4 rounded-lg">
              <div class="text-sm text-gray-400 mb-1">Amount</div>
              <div class="text-white font-semibold text-lg">{{ selectedTransaction.amount }}</div>
            </div>
            <div class="bg-gray-800/50 p-4 rounded-lg">
              <div class="text-sm text-gray-400 mb-1">Status</div>
              <span
                class="px-3 py-1 rounded-full text-sm font-medium inline-block"
                :class="getStatusClass(selectedTransaction.status)"
              >
                {{ selectedTransaction.status.charAt(0).toUpperCase() + selectedTransaction.status.slice(1) }}
              </span>
            </div>
            <div class="bg-gray-800/50 p-4 rounded-lg">
              <div class="text-sm text-gray-400 mb-1">Created</div>
              <div class="text-white font-medium">{{ selectedTransaction.created_at }}</div>
            </div>
          </div>

          <div v-if="selectedTransaction.description" class="bg-gray-800/50 p-4 rounded-lg">
            <div class="text-sm text-gray-400 mb-2">Description</div>
            <div class="text-white">{{ selectedTransaction.description }}</div>
          </div>

          <div v-if="selectedTransaction.tx_hash" class="bg-gray-800/50 p-4 rounded-lg">
            <div class="text-sm text-gray-400 mb-2">Transaction Hash</div>
            <div class="text-white font-mono text-xs break-all">{{ selectedTransaction.tx_hash }}</div>
          </div>

          <div v-if="selectedTransaction.meta && Object.keys(selectedTransaction.meta).length > 0" class="bg-gray-800/50 p-4 rounded-lg">
            <div class="text-sm text-gray-400 mb-3">Additional Information</div>
            <div class="space-y-2">
              <div v-if="selectedTransaction.meta.wallet_address" class="flex justify-between items-start">
                <span class="text-gray-300">Wallet Address:</span>
                <span class="text-white font-mono text-xs break-all ml-4">{{ selectedTransaction.meta.wallet_address }}</span>
              </div>
              <div v-if="selectedTransaction.meta.network" class="flex justify-between">
                <span class="text-gray-300">Network:</span>
                <span class="text-white font-semibold">{{ selectedTransaction.meta.network }}</span>
              </div>
              <div v-if="selectedTransaction.meta.confirmations !== undefined" class="flex justify-between">
                <span class="text-gray-300">Confirmations:</span>
                <span class="text-white">{{ selectedTransaction.meta.confirmations }}</span>
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
    key: 'number',
    label: 'S.No',
    sortable: true,
    span: 1,
    headerClass: 'text-left',
    cellClass: 'font-medium'
  },
  {
    key: 'type',
    label: 'Transaction Type',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-cabinet-green font-medium'
  },
  {
    key: 'amount',
    label: 'Transaction Amount',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-semibold'
  },
  {
    key: 'created_at',
    label: 'Created At',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-sm',
    sortValue: (row) => parseDateToTimestamp(row.created_at),
    format: (value) => formatDateShort(value)
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
    'confirmed': 'bg-cabinet-green/20 text-cabinet-green',
    'pending': 'bg-cabinet-orange/20 text-cabinet-orange',
    'failed': 'bg-cabinet-red/20 text-cabinet-red',
    'cancelled': 'bg-gray-400/20 text-gray-400'
  }
  return classes[status] || 'bg-gray-400/20 text-gray-400'
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

