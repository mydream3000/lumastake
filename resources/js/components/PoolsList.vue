<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else class="relative">

    <!-- Mobile Version (Cards) - visible only on small screens -->
    <div class="lg:hidden block space-y-4">
      <div
        v-for="pool in pools"
        :key="pool.id"
        class="bg-gray-50 rounded-3xl p-6 border border-gray-100"
      >
        <div class="flex items-start justify-between mb-6">
          <div>
            <div class="text-lg font-black text-cabinet-text-main tracking-tight">{{ pool.name }}</div>
            <div class="text-sm font-bold text-cabinet-blue mt-1">{{ pool.profit }}</div>
          </div>
          <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm">
             <svg class="w-6 h-6 text-cabinet-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
             </svg>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
          <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Days</div>
            <div class="text-sm font-black text-cabinet-text-main">{{ pool.days }}</div>
          </div>
          <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Min. Stake</div>
            <div class="text-sm font-black text-cabinet-text-main">{{ pool.min_stake }}</div>
          </div>
        </div>

        <button
          @click="openStakeModal(pool)"
          :disabled="!isEligible(pool)"
          :class="[
            'w-full py-4 text-[11px] rounded-2xl font-black uppercase tracking-[0.2em] transition-all duration-300',
            isEligible(pool)
              ? 'bg-cabinet-lime text-cabinet-text-main shadow-[0_10px_20px_-5px_rgba(227,255,59,0.5)] active:scale-95'
              : 'bg-gray-200 text-gray-400 cursor-not-allowed'
          ]"
        >
          Stake Now
        </button>
      </div>
    </div>

    <!-- Desktop Version (Table) - visible only on large screens -->
    <div class="hidden max-w-lg lg:block overflow-x-auto">
      <table class="">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="text-left py-3 px-4 font-semibold text-sm text-gray-400">Name</th>
            <th class="text-left py-3 px-4 font-semibold text-sm text-gray-400">Days</th>
            <th class="text-left py-3 px-4 font-semibold text-sm text-gray-400">Min - Stake</th>
            <th class="text-left py-3 px-4 font-semibold text-sm text-gray-400">Profit</th>
            <th class="text-right py-3 px-4 font-semibold text-sm text-gray-400">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="pool in pools"
            :key="pool.id"
            class="border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition"
          >
            <!-- Pool Name -->
            <td class="py-2 px-2">
              <div class="font-medium text-base text-gray-500">{{ pool.name }}</div>
            </td>

            <!-- Days -->
            <td class="py-2 px-2">
              <div class="text-base text-cabinet-text-main font-medium">{{ pool.days }}</div>
            </td>

            <!-- Min Stake -->
            <td class="py-2 px-2">
              <div class="text-base text-cabinet-text-main font-medium">{{ pool.min_stake }}</div>
            </td>

            <!-- Profit -->
            <td class="py-2 px-2">
              <div class="text-base font-medium text-[#2BA6FF]">{{ pool.profit }}</div>
            </td>

            <!-- Stake Button -->
            <td class="py-2 px-2 text-right">
              <button
                @click="openStakeModal(pool)"
                :disabled="!isEligible(pool)"
                :title="!isEligible(pool) ? `Available from ${formatCurrency(minStakeVal(pool))} USDT` : 'Stake'"
                :class="[
                  'px-5 py-1 text-sm rounded-md font-bold uppercase transition',
                  isEligible(pool)
                    ? 'bg-cabinet-lime text-cabinet-text-main hover:bg-cabinet-lime/90 shadow-sm'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                ]"
              >
                Stake
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <stake-modal
      :is-open="isModalOpen"
      :pool="selectedPool"
      :available-balance="availableBalance"
      @close="closeStakeModal"
      @success="handleStakeSuccess"
    />

    <!-- Soft navigation overlay to avoid jank on mobile -->
    <div v-if="isNavigating"
         class="fixed inset-0 z-50 bg-white/50 backdrop-blur-sm flex items-center justify-center">
      <div class="inline-block animate-spin rounded-full h-10 w-10 border-2 border-cabinet-blue border-t-transparent"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import StakeModal from './StakeModal.vue'

const props = defineProps({
  dataUrl: {
    type: String,
    required: true
  },
  balance: {
    type: Number,
    default: 0
  }
})

const pools = ref([])
const loading = ref(true)
const isModalOpen = ref(false)
const selectedPool = ref(null)
const availableBalance = ref(props.balance)
const isNavigating = ref(false)

function minStakeVal(pool) {
  // Accept either numeric min_stake_value or formatted min_stake string
  if (typeof pool.min_stake_value !== 'undefined') {
    return Number(pool.min_stake_value)
  }
  const n = parseFloat(String(pool.min_stake).replace(/[^0-9.]/g, ''))
  return isNaN(n) ? 0 : n
}

function isEligible(pool) {
  return availableBalance.value >= minStakeVal(pool)
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(value)
}

async function fetchPools() {
  try {
    const response = await axios.get(props.dataUrl)
    pools.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch investment pools:', error)
  } finally {
    loading.value = false
  }
}

function openStakeModal(pool) {
  selectedPool.value = pool
  isModalOpen.value = true
}

function closeStakeModal() {
  isModalOpen.value = false
  selectedPool.value = null
}

function handleStakeSuccess() {
  if (window.Alpine) {
    // Ждем немного пока стор обновится (хотя он уже должен быть обновлен из StakeModal)
    setTimeout(() => {
      availableBalance.value = window.Alpine.store('userBalance').availableBalance
    }, 500)
  }

  if (window.showToast) {
    window.showToast('Stake created successfully', 'success')
  }

  closeStakeModal()
}

onMounted(() => {
  fetchPools()

  // Sync with Alpine store if available to handle balance changes from other components
  if (window.Alpine) {
    const checkBalance = () => {
      const storeBalance = window.Alpine.store('userBalance').availableBalance
      if (Math.abs(availableBalance.value - storeBalance) > 0.001) {
        availableBalance.value = storeBalance
      }
    }
    const interval = setInterval(checkBalance, 1000)
    onUnmounted(() => clearInterval(interval))
  }
})
</script>
