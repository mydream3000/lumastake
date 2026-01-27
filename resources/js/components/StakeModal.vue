<template>
  <div
    v-if="isOpen"
    @click="close"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-[100] transition-opacity duration-300"
  >
    <div
      @click.stop="tooltipOpen = false"
      class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-[0_20px_50px_rgba(0,0,0,0.15)] relative"
    >
      <!-- Icon -->
      <div class="flex flex-col items-center mb-6">
        <div class="w-16 h-16 rounded-full bg-[#D9EEFF] flex items-center justify-center mb-5">
          <svg class="w-8 h-8 text-cabinet-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-2xl font-black text-cabinet-text-main mb-2">Stake Confirmation</h3>
        <p class="text-gray-400 text-center text-sm">Please specify the amount<br>you want to stake</p>
      </div>

      <!-- Available Balance -->
      <div class="mb-3">
        <span class="text-sm font-semibold text-cabinet-lime">Available Balance - {{ formatCurrency(availableBalance) }}</span>
      </div>

      <!-- Input -->
      <div class="relative mb-5">
        <input
          v-model="amount"
          type="number"
          step="0.01"
          min="0"
          :max="availableBalance"
          placeholder="Enter the amount"
          class="w-full px-4 py-4 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-cabinet-blue transition-all text-base text-cabinet-text-main placeholder:text-gray-300"
        >
        <button
          @click="setMaxAmount"
          class="absolute right-3 top-1/2 -translate-y-1/2 px-3 py-1.5 text-sm font-bold text-cabinet-blue hover:text-cabinet-blue/70 transition-colors"
        >
          Max
        </button>
      </div>
      <p v-if="errorMessage" class="text-xs text-cabinet-red mb-4 font-medium flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ errorMessage }}
      </p>

      <!-- Auto Stake -->
      <div class="mb-6">
        <p class="text-sm text-cabinet-text-main mb-3">Do you want to <span class="font-bold">auto-stake?</span></p>
        <div class="flex items-center gap-2">
          <button
            @click="toggleAutoStake"
            class="relative inline-flex items-center h-8 rounded-full transition-all duration-300 focus:outline-none"
            :class="autoStake ? 'w-24 bg-cabinet-lime' : 'w-14 bg-gray-200'"
          >
            <span
              class="absolute left-1 inline-block h-6 w-6 transform rounded-full bg-white transition-all duration-300 shadow-sm z-10"
              :class="autoStake ? 'translate-x-[60px]' : 'translate-x-0'"
            ></span>
            <span
              class="absolute left-2.5 text-[10px] font-bold text-cabinet-text-main transition-all duration-300 whitespace-nowrap"
              :class="autoStake ? 'opacity-100' : 'opacity-0'"
            >
              Auto Stake
            </span>
          </button>
          <div class="relative">
            <svg
              @click.stop="tooltipOpen = !tooltipOpen"
              @mouseenter="tooltipOpen = true"
              @mouseleave="tooltipOpen = false"
              class="w-5 h-5 text-gray-300 cursor-help hover:text-gray-400 transition-colors"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div
              v-show="tooltipOpen"
              @click.stop
              class="absolute left-0 bottom-full mb-2 w-48 p-3 bg-cabinet-text-main text-white text-xs leading-relaxed rounded-lg shadow-xl z-50"
            >
              Auto Stake automatically restakes your investment when the period ends.
            </div>
          </div>
        </div>
      </div>

      <!-- Confirm Button -->
      <button
        @click="confirmStake"
        :disabled="!isValid || isSubmitting"
        class="w-full py-4 bg-cabinet-lime text-cabinet-text-main text-lg font-bold rounded-xl hover:bg-cabinet-lime/90 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-200 flex items-center justify-center gap-2"
      >
        <span v-if="isSubmitting" class="w-5 h-5 border-2 border-cabinet-text-main/30 border-t-cabinet-text-main rounded-full animate-spin"></span>
        {{ isSubmitting ? 'Processing...' : 'Continue' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  pool: {
    type: Object,
    default: null
  },
  availableBalance: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['close', 'success'])

const amount = ref('')
const autoStake = ref(false)
const errorMessage = ref('')
const isSubmitting = ref(false)
const tooltipOpen = ref(false)

function parseAmount(val) {
  const n = parseFloat(String(val).replace(/[^0-9.]/g, ''))
  return isNaN(n) ? 0 : n
}

const isValid = computed(() => {
  if (!amount.value || amount.value <= 0) return false
  if (!props.pool) return false

  const requested = parseFloat(amount.value)
  const minStake = typeof props.pool.min_stake_value !== 'undefined'
    ? Number(props.pool.min_stake_value)
    : parseAmount(props.pool.min_stake)

  if (requested > props.availableBalance) {
    errorMessage.value = 'Insufficient balance'
    return false
  }
  if (requested < minStake) {
    errorMessage.value = `Minimum stake is ${minStake} USD`
    return false
  }
  errorMessage.value = ''
  return true
})

function setMaxAmount() {
  amount.value = props.availableBalance.toString()
}

function toggleAutoStake() {
  autoStake.value = !autoStake.value
}

function close() {
  emit('close')
  amount.value = ''
  autoStake.value = false
  errorMessage.value = ''
}

async function confirmStake() {
  if (!isValid.value || isSubmitting.value) return

  isSubmitting.value = true
  errorMessage.value = ''

  try {
    await axios.post('/dashboard/staking/create', {
      pool_id: props.pool.id,
      amount: parseFloat(amount.value),
      auto_stake: autoStake.value
    })

    emit('success')
    close()
  } catch (error) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'An error occurred. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}
</script>
