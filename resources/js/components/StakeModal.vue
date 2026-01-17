<template>
  <div
    v-if="isOpen"
    @click="close"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div
      @click.stop="tooltipOpen = false"
      class="bg-white rounded-xl p-8 max-w-md w-full mx-4 border border-gray-200 shadow-xl"
    >
      <div class="flex flex-col items-center mb-6">
        <div class="w-16 h-16 rounded-full bg-cabinet-blue/10 flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-cabinet-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-cabinet-text-main mb-2">Stake Confirmation</h3>
        <p class="text-base text-gray-500 text-center">Please specify the amount you want to stake</p>
      </div>

      <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
          <label class="text-sm font-semibold text-gray-600">Available Balance</label>
          <span class="text-sm font-bold text-cabinet-blue">{{ formatCurrency(availableBalance) }} USDT</span>
        </div>
        <div class="relative">
          <input
            v-model="amount"
            type="number"
            step="0.01"
            min="0"
            :max="availableBalance"
            placeholder="Enter amount"
            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-cabinet-blue bg-gray-50 text-cabinet-text-main font-medium"
          >
          <button
            @click="setMaxAmount"
            class="absolute right-3 top-1/2 -translate-y-1/2 px-3 py-1 text-xs font-bold text-white bg-cabinet-blue rounded hover:bg-cabinet-blue/80 transition"
          >
            Max
          </button>
        </div>
        <p v-if="errorMessage" class="text-sm text-cabinet-red mt-2 font-medium">{{ errorMessage }}</p>
      </div>

      <div class="mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <label class="text-sm font-semibold text-gray-600">Auto Stake</label>
            <div class="relative">
              <svg
                @click.stop="tooltipOpen = !tooltipOpen"
                @mouseenter="tooltipOpen = true"
                @mouseleave="tooltipOpen = false"
                class="w-4 h-4 text-gray-400 cursor-pointer"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
              </svg>
              <div
                v-show="tooltipOpen"
                @click.stop
                class="absolute left-1/2 -translate-x-1/2 bottom-full mb-3 w-64 p-4 bg-white border border-gray-200 text-gray-600 text-sm rounded-xl shadow-2xl z-50 transition-all"
              >
                Auto Stake automatically restakes your initial investment when the period ends.
              </div>
            </div>
          </div>
          <button
            @click="toggleAutoStake"
            :class="[
              'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
              autoStake ? 'bg-cabinet-blue' : 'bg-gray-200'
            ]"
          >
            <span
              :class="[
                'inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm',
                autoStake ? 'translate-x-6' : 'translate-x-1'
              ]"
            ></span>
          </button>
        </div>
      </div>

      <button
        @click="confirmStake"
        :disabled="!isValid || isSubmitting"
        class="w-full px-6 py-4 bg-cabinet-blue text-white font-bold rounded-lg hover:bg-cabinet-blue/90 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg"
      >
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
