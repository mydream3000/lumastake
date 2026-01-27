<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <!-- Mobile Version -->
    <div class="lg:hidden space-y-4">
      <div v-for="row in stakes" :key="row.id" class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm relative overflow-hidden">
          <div class="absolute top-0 right-0 -mt-8 -mr-8 w-24 h-24 bg-cabinet-blue/5 rounded-full blur-2xl"></div>

          <div class="flex items-center justify-between mb-6 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Pool Name</div>
                  <div class="text-lg font-black text-cabinet-text-main leading-tight">{{ row.pool_name }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Auto Stake</div>
                  <label class="inline-flex items-center cursor-pointer mt-1">
                      <input
                        type="checkbox"
                        :checked="row.auto_renewal"
                        @change="toggleAutoRenewal(row.id, $event.target.checked)"
                        class="hidden peer"
                      >
                      <div class="relative w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-cabinet-blue after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                  </label>
              </div>
          </div>

          <div class="grid grid-cols-2 gap-x-4 gap-y-6 mb-8 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Duration</div>
                  <div class="text-sm font-bold text-cabinet-text-main">{{ row.duration }}</div>
              </div>
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 text-right">Profit</div>
                  <div class="text-sm font-black text-cabinet-blue text-right">{{ row.profit }}</div>
              </div>
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Amount Staked</div>
                  <div class="text-sm font-black text-cabinet-text-main">{{ row.amount }}</div>
              </div>
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 text-right">Time Left</div>
                  <div class="flex justify-end mt-1">
                      <time-left :seconds="row.time_left" />
                  </div>
              </div>
          </div>

          <button
            @click="unstakeConfirm(row)"
            class="w-full py-4 bg-gradient-to-r from-cabinet-blue to-[#5D6DFF] text-white text-[11px] font-black rounded-2xl hover:shadow-lg transition-all duration-300 uppercase tracking-[0.2em]"
          >
            Unstake
          </button>
      </div>

      <div v-if="!stakes.length" class="text-center py-12 bg-white rounded-[32px] border border-gray-100 shadow-sm">
          <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4" />
              </svg>
          </div>
          <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No active stakings</p>
      </div>
    </div>

    <!-- Desktop Version -->
    <data-table
      class="hidden lg:block"
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
    <div v-if="showUnstakeModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4 transition-opacity duration-300" @click.self="showUnstakeModal = false">
      <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>
      <div class="relative bg-white rounded-[32px] p-10 max-w-md w-full shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white/20 overflow-hidden">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-red-500/5 rounded-full blur-3xl"></div>

        <div class="text-center relative">
          <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-3xl bg-red-50 mb-8 shadow-inner">
            <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
          </div>
          <h3 class="text-3xl font-extrabold text-cabinet-text-main mb-3 tracking-tight">Unstake Confirmation</h3>
          <p class="text-gray-400 font-medium mb-6 leading-relaxed">Note: If you unstake before the time ends, a fee of <span class="font-bold text-red-500 underline decoration-2 underline-offset-4">10%</span> will be applied.</p>
          <p class="text-lg font-bold text-cabinet-text-main mb-10">Are you sure you want to unstake your funds now?</p>

          <div class="flex flex-col gap-3">
            <button
              @click="performUnstake"
              class="w-full px-8 py-5 bg-red-500 text-white text-xs font-black rounded-2xl hover:bg-red-600 hover:shadow-[0_10px_30px_-5px_rgba(239,68,68,0.4)] hover:-translate-y-0.5 transition-all duration-300 uppercase tracking-[0.2em]"
            >
              Yes, Unstake Now
            </button>
            <button
              @click="showUnstakeModal = false"
              class="w-full py-3 text-gray-400 hover:text-gray-600 text-[10px] font-black transition-colors uppercase tracking-[0.2em]"
            >
              No, Keep Staking
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
