<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else class="relative">

    <!-- Mobile Version (Cards) - visible only on small screens -->
    <div class="block lg:hidden space-y-0">
      <div
        v-for="pool in pools"
        :key="pool.id"
        class="flex items-center justify-between py-4 border-b border-gray-100 last:border-b-0"
      >
        <div>
          <div class="text-sm font-semibold text-cabinet-text-main">{{ pool.name }}</div>
          <div class="text-xs text-cabinet-blue font-medium">{{ pool.profit }}</div>
        </div>
        <button
          @click="openStakeModal(pool)"
          :disabled="!isEligible(pool)"
          :title="!isEligible(pool) ? `Available from ${formatCurrency(minStakeVal(pool))} USDT` : 'Stake'"
          :class="[
            'px-4 py-1.5 text-xs rounded-md font-semibold uppercase transition',
            isEligible(pool)
              ? 'bg-cabinet-lime text-cabinet-text-main hover:bg-cabinet-lime/90'
              : 'bg-gray-200 text-gray-400 cursor-not-allowed'
          ]"
        >
          Stake
        </button>
      </div>
    </div>

    <!-- Desktop Version (Table) - visible only on large screens -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="w-full">
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
import { ref, onMounted } from 'vue'
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
  // Show lightweight overlay to prevent visual jumps on mobile and navigate
  isNavigating.value = true
  // Use replace to avoid extra history entry and ensure faster transition
  window.location.replace('/dashboard/staking')
}

onMounted(() => {
  fetchPools()
})
</script>
