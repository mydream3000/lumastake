<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <data-table
      :data="transactions"
      :columns="columns"
      :grid-cols="15"
      default-sort="created_at"
      default-sort-order="desc"
      :default-per-page="10"
      :row-class="getRowClass"
    >
      <template #cell-status="{ value }">
        <span
          class="px-3 py-1 rounded-full text-xs font-medium inline-block capitalize"
          :class="getStatusClass(value)"
        >
          {{ getStatusLabel(value) }}
        </span>
      </template>

      <template #cell-details="{ row }">
        <button
          @click="showDetails(row)"
          class="px-4 py-1.5 text-xs rounded-md border border-cabinet-blue text-cabinet-blue hover:bg-cabinet-blue/10 font-medium transition"
        >
          Details
        </button>
      </template>

      <template #cell-action="{ row }">
        <button
          v-if="row.can_cancel"
          @click="cancelWithdraw(row.id)"
          class="px-4 py-1.5 text-xs rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 font-medium transition"
        >
          Cancel
        </button>
      </template>
    </data-table>

    <!-- Details Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModal = false">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
        <div class="relative bg-white rounded-xl max-w-3xl w-full p-8 shadow-2xl border border-gray-200">
          <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-100">
            <h3 class="text-2xl font-bold text-cabinet-text-main">
              Transaction #{{ selectedTransaction?.id }}
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
                  class="px-3 py-1 rounded-full text-xs font-medium inline-block capitalize mt-1"
                  :class="getStatusClass(selectedTransaction.status)"
                >
                  {{ getStatusLabel(selectedTransaction.status) }}
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
                <div v-if="selectedTransaction.meta.token" class="flex justify-between">
                  <span class="text-gray-500 font-medium">Withdrawal In:</span>
                  <span class="text-cabinet-text-main font-bold">{{ selectedTransaction.meta.token }}</span>
                </div>
                <div v-if="selectedTransaction.meta.net_amount" class="flex justify-between">
                  <span class="text-gray-500 font-medium">Net Amount:</span>
                  <span class="text-cabinet-blue font-bold">${{ Number(selectedTransaction.meta.net_amount).toFixed(2) }}</span>
                </div>
                <div v-if="selectedTransaction.meta.confirmations !== undefined" class="flex justify-between">
                  <span class="text-gray-500 font-medium">Confirmations:</span>
                  <span class="text-cabinet-text-main">{{ selectedTransaction.meta.confirmations }}</span>
                </div>
                <div v-if="selectedTransaction.meta.estimated_time" class="flex justify-between">
                  <span class="text-gray-500 font-medium">Estimated Time:</span>
                  <span class="text-cabinet-text-main">{{ selectedTransaction.meta.estimated_time }}</span>
                </div>
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
    label: 'S. No.',
    sortable: true,
    span: 1,
    headerClass: 'text-left',
    cellClass: 'text-gray-500 font-medium'
  },
  {
    key: 'type',
    label: 'Transaction Type',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-cabinet-blue font-medium'
  },
  {
    key: 'amount',
    label: 'Transaction Amout',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'font-medium text-cabinet-text-main'
  },
  {
    key: 'withdraw_fee',
    label: 'Withdraw Fee',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'font-medium text-cabinet-text-main'
  },
  {
    key: 'created_at',
    label: 'Created At',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm text-gray-500',
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
  },
  {
    key: 'action',
    label: 'Action',
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
    'cancelled': 'bg-gray-200 text-gray-500'
  }
  return classes[status] || 'bg-gray-200 text-gray-500'
}

function getStatusLabel(status) {
  const labels = {
    'confirmed': 'Served',
    'pending': 'Requested',
    'failed': 'Failed',
    'cancelled': 'Cancelled'
  }
  return labels[status] || status
}

function getRowClass(row) {
  if (row.status === 'pending') {
    return 'bg-cabinet-orange/5'
  }
  if (row.status === 'confirmed') {
    return 'bg-cabinet-green/5'
  }
  if (row.status === 'cancelled') {
    return 'bg-gray-50'
  }
  return 'hover:bg-gray-50'
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

async function cancelWithdraw(id) {
  if (!confirm('Are you sure you want to cancel this withdrawal?')) {
    return
  }

  try {
    const response = await axios.post(`/dashboard/transactions/withdraw/${id}/cancel`)
    if (response.data.success) {
      if (window.showToast) {
        window.showToast('Withdrawal cancelled successfully', 'success')
      }
      await fetchTransactions()
    } else {
      if (window.showToast) {
        window.showToast(response.data.message || 'Failed to cancel withdrawal', 'error')
      }
    }
  } catch (error) {
    console.error('Error:', error)
    if (window.showToast) {
      window.showToast('An error occurred while cancelling the withdrawal', 'error')
    }
  }
}

onMounted(() => {
  fetchTransactions()
})
</script>
