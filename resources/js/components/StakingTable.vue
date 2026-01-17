<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <data-table
      :data="stakes"
      :columns="columns"
      :grid-cols="20"
      default-sort="start_date"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-profit="{ value }">
        <span class="text-[#2BA6FF] font-bold">{{ value }}</span>
      </template>

      <template #cell-auto_renewal="{ row }">
        <label class="inline-flex items-center cursor-pointer">
          <input
            type="checkbox"
            :checked="row.auto_renewal"
            @change="toggleAutoRenewal(row.id, $event.target.checked)"
            class="hidden peer"
          >
          <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cabinet-blue/30 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cabinet-blue"></div>
        </label>
      </template>

      <template #cell-time_left="{ row }">
        <time-left :seconds="row.time_left" />
      </template>

      <template #cell-action="{ row }">
        <button
          @click="unstakeConfirm(row)"
          class="px-4 lg:px-6 py-1.5 lg:py-2 text-xs lg:text-sm rounded-md bg-cabinet-blue text-white  hover:bg-cabinet-blue/80  font-bold uppercase transition shadow-sm"
        >
          Unstake
        </button>
      </template>
    </data-table>

    <!-- Unstake Confirmation Modal -->
    <div v-if="showUnstakeModal" class="fixed inset-0 z-50 flex items-center justify-center px-4" @click.self="showUnstakeModal = false">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
      <div class="relative bg-white rounded-xl max-w-md w-full p-8 shadow-2xl border border-gray-200">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
            <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-cabinet-text-main mb-2">Unstake Confirmation</h3>
          <p class="text-base text-gray-500 mb-4">Note: If you unstake before the time ends, a fee of <span class="font-bold text-red-600">10%</span> will be applied.</p>
          <p class="text-lg font-medium text-cabinet-text-main mb-8">Are you sure you want to unstake?</p>

          <div class="grid grid-cols-2 gap-4">
            <button
              @click="performUnstake"
              class="px-6 py-3 bg-cabinet-blue text-white font-bold rounded-lg hover:bg-cabinet-blue/90 transition shadow-lg uppercase"
            >
              Yes, Unstake
            </button>
            <button
              @click="showUnstakeModal = false"
              class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-lg hover:bg-gray-200 transition uppercase"
            >
              No, Cancel
            </button>
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
import TimeLeft from './TimeLeft.vue'
import { parseDateToTimestamp, formatDateShort } from '../utils/dateFormatter'

const props = defineProps({
  dataUrl: {
    type: String,
    required: true
  }
})

const stakes = ref([])
const loading = ref(true)
const showUnstakeModal = ref(false)
const selectedStake = ref(null)

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
    span: 2,
    headerClass: 'text-left',
    cellClass: 'font-semibold'
  },
  {
    key: 'auto_renewal',
    label: 'Auto Stake',
    sortable: false,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-left'
  },
  {
        key: 'start_date',
        label: 'Start Date',
        sortable: true,
        span: 3,
        headerClass: 'text-left',
        cellClass: 'text-sm',
        sortValue: (row) => parseDateToTimestamp(row.start_date),
        format: (value) => formatDateShort(value)
      },
      {
        key: 'time_left',
        label: 'Time Left',
        sortable: false,
        span: 3,
        headerClass: 'text-center',
        cellClass: 'text-center'
      },
      {
        key: 'action',
        label: 'Action',
        sortable: false,
        span: 3,
        headerClass: 'text-center',
        cellClass: 'text-center'
      }
]

async function fetchStakes() {
  try {
    const response = await axios.get(props.dataUrl)
    stakes.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch stakes:', error)
  } finally {
    loading.value = false
  }
}

async function toggleAutoRenewal(id, value) {
  try {
    const response = await axios.post(`/dashboard/staking/${id}/auto-renewal`, { auto_renewal: value })
    const stake = stakes.value.find(s => s.id === id)
    if (stake) {
      stake.auto_renewal = value
    }

    if (window.showToast) {
      window.showToast(
        value ? 'Auto stake enabled successfully' : 'Auto stake disabled successfully',
        'success'
      )
    }
  } catch (error) {
    console.error('Failed to toggle auto renewal:', error)
    if (window.showToast) {
      window.showToast('Failed to update auto stake setting', 'error')
    }
  }
}

function unstakeConfirm(stake) {
  selectedStake.value = stake
  showUnstakeModal.value = true
}

async function performUnstake() {
  try {
    const response = await axios.post(`/dashboard/staking/${selectedStake.value.id}/unstake`)
    if (response.data.success) {
      showUnstakeModal.value = false

      // Show toast notification
      if (window.showToast) {
        window.showToast(response.data.message, 'success')
      }

      await fetchStakes()
    } else {
      if (window.showToast) {
        window.showToast(response.data.message || 'Failed to unstake', 'error')
      }
    }
  } catch (error) {
    console.error('Error:', error)
    if (window.showToast) {
      window.showToast('An error occurred while unstaking', 'error')
    }
  }
}

onMounted(() => {
  fetchStakes()
})
</script>
