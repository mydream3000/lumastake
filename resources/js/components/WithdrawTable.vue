<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="transactions"
      :columns="columns"
      :grid-cols="16"
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
          class="px-4 lg:px-6 py-1.5 lg:py-2 text-xs lg:text-sm rounded-md bg-cabinet-orange text-white hover:bg-cabinet-orange/80 font-semibold transition"
        >
          View
        </button>
      </template>

      <template #cell-action="{ row }">
        <button
          v-if="row.can_cancel"
          @click="cancelWithdraw(row.id)"
          class="px-4 lg:px-6 py-1.5 lg:py-2 text-xs lg:text-sm rounded-md bg-cabinet-red text-white  hover:bg-cabinet-red/80  font-semibold transition"
        >
          Cancel
        </button>
      </template>
    </data-table>

    <!-- Details Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModal = false">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/70 transition-opacity"></div>
        <div class="relative bg-cabinet-dark rounded-lg max-w-3xl w-full p-6 shadow-2xl border border-cabinet-grey">
          <div class="flex justify-between items-center mb-6 pb-4 border-b border-cabinet-grey">
            <h3 class="text-2xl font-bold text-cabinet-text-dark">
              Transaction #{{ selectedTransaction?.id }}
            </h3>
            <button @click="showModal = false" class="text-cabinet-text-grey hover:text-white transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div v-if="selectedTransaction" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-cabinet-dark/50 p-4 rounded-lg">
                <div class="text-sm text-cabinet-text-grey mb-1">Type</div>
                <div class="text-cabinet-text-dark font-semibold">{{ selectedTransaction.type }}</div>
              </div>
              <div class="bg-cabinet-dark/50 p-4 rounded-lg">
                <div class="text-sm text-cabinet-text-grey mb-1">Amount</div>
                <div class="text-cabinet-text-dark font-semibold text-lg">{{ selectedTransaction.amount }}</div>
              </div>
              <div class="bg-cabinet-dark/50 p-4 rounded-lg">
                <div class="text-sm text-cabinet-text-grey mb-1">Status</div>
                <span
                  class="px-3 py-1 rounded-full text-sm font-medium inline-block"
                  :class="getStatusClass(selectedTransaction.status)"
                >
                  {{ selectedTransaction.status.charAt(0).toUpperCase() + selectedTransaction.status.slice(1) }}
                </span>
              </div>
              <div class="bg-cabinet-dark/50 p-4 rounded-lg">
                <div class="text-sm text-cabinet-text-grey mb-1">Created</div>
                <div class="text-cabinet-text-dark font-medium">{{ selectedTransaction.created_at }}</div>
              </div>
            </div>

            <div v-if="selectedTransaction.description" class="bg-cabinet-dark/50 p-4 rounded-lg">
              <div class="text-sm text-cabinet-text-grey mb-2">Description</div>
              <div class="text-cabinet-text-dark">{{ selectedTransaction.description }}</div>
            </div>

            <div v-if="selectedTransaction.tx_hash" class="bg-cabinet-dark/50 p-4 rounded-lg">
              <div class="text-sm text-cabinet-text-grey mb-2">Transaction Hash</div>
              <div class="text-cabinet-text-dark font-mono text-xs break-all">{{ selectedTransaction.tx_hash }}</div>
            </div>

            <div v-if="selectedTransaction.meta && Object.keys(selectedTransaction.meta).length > 0" class="bg-cabinet-dark/50 p-4 rounded-lg">
              <div class="text-sm text-cabinet-text-grey mb-3">Additional Information</div>
              <div class="space-y-2">
                <div v-if="selectedTransaction.meta.wallet_address" class="flex justify-between items-start">
                  <span class="text-cabinet-text-grey">Wallet Address:</span>
                  <span class="text-cabinet-text-dark font-mono text-xs break-all ml-4">{{ selectedTransaction.meta.wallet_address }}</span>
                </div>
                <div v-if="selectedTransaction.meta.network" class="flex justify-between">
                  <span class="text-cabinet-text-grey">Network:</span>
                  <span class="text-cabinet-text-dark font-semibold">{{ selectedTransaction.meta.network }}</span>
                </div>
                <div v-if="selectedTransaction.meta.token" class="flex justify-between">
                  <span class="text-cabinet-text-grey">Withdrawal In:</span>
                  <span class="text-cabinet-text-dark font-semibold">{{ selectedTransaction.meta.token }}</span>
                </div>
                <div v-if="selectedTransaction.meta.net_amount" class="flex justify-between">
                  <span class="text-cabinet-text-grey">Net Amount:</span>
                  <span class="text-cabinet-green font-semibold">${{ Number(selectedTransaction.meta.net_amount).toFixed(2) }}</span>
                </div>
                <div v-if="selectedTransaction.meta.confirmations !== undefined" class="flex justify-between">
                  <span class="text-cabinet-text-grey">Confirmations:</span>
                  <span class="text-cabinet-text-dark">{{ selectedTransaction.meta.confirmations }}</span>
                </div>
                <div v-if="selectedTransaction.meta.estimated_time" class="flex justify-between">
                  <span class="text-cabinet-text-grey">Estimated Time:</span>
                  <span class="text-cabinet-text-dark">{{ selectedTransaction.meta.estimated_time }}</span>
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
    key: 'type',
    label: 'Transaction Type',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-cabinet-orange font-medium'
  },
  {
    key: 'amount',
    label: 'Transaction Amount',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'font-semibold'
  },
  {
    key: 'withdrawal_currency',
    label: 'Withdrawal In',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm font-semibold'
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
    'cancelled': 'bg-gray-700/20 text-gray-400'
  }
  return classes[status] || 'bg-gray-700/20 text-gray-400'
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
