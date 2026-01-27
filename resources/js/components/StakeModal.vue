<template>
  <div
    v-if="isOpen"
    @click="close"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-[100] transition-opacity duration-300"
  >
    <div
      @click.stop="tooltipOpen = false"
      class="bg-white rounded-[32px] p-10 max-w-md w-full mx-4 border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.1)] relative overflow-hidden"
    >
      <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-cabinet-blue/5 rounded-full blur-3xl"></div>

      <div class="flex flex-col items-center mb-8 relative">
        <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-cabinet-blue/10 to-cabinet-blue/5 flex items-center justify-center mb-6 shadow-inner">
          <svg class="w-10 h-10 text-cabinet-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <h3 class="text-3xl font-extrabold text-cabinet-text-main mb-3 tracking-tight">Stake Confirmation</h3>
        <p class="text-gray-400 text-center font-medium">Specify the amount you want to stake to start earning rewards.</p>
      </div>

      <div class="mb-6">
        <div class="flex items-center justify-between mb-3 px-1">
          <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Amount to Stake</label>
          <div class="flex items-center gap-1.5">
             <span class="text-[10px] font-medium text-gray-400">Balance:</span>
             <span class="text-xs font-bold text-cabinet-blue">{{ formatCurrency(availableBalance) }} USDT</span>
          </div>
        </div>
        <div class="relative group">
          <input
            v-model="amount"
            type="number"
            step="0.01"
            min="0"
            :max="availableBalance"
            placeholder="0.00"
            class="w-full px-6 py-5 bg-gray-50 border-2 border-transparent rounded-2xl focus:outline-none focus:border-cabinet-blue/30 focus:bg-white transition-all text-xl font-bold text-cabinet-text-main placeholder:text-gray-300"
          >
          <button
            @click="setMaxAmount"
            class="absolute right-4 top-1/2 -translate-y-1/2 px-4 py-2 text-[10px] font-black text-cabinet-blue bg-cabinet-blue/10 rounded-xl hover:bg-cabinet-blue hover:text-white transition-all duration-300 uppercase tracking-widest"
          >
            Max
          </button>
        </div>
        <p v-if="errorMessage" class="text-[11px] text-cabinet-red mt-3 ml-1 font-semibold flex items-center gap-1">
          <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          {{ errorMessage }}
        </p>
      </div>

      <div class="mb-10 bg-gray-50/80 p-5 rounded-3xl border border-gray-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <label class="text-sm font-bold text-cabinet-text-main">Auto Stake</label>
                    <div class="relative">
                      <svg
                        @click.stop="tooltipOpen = !tooltipOpen"
                        @mouseenter="tooltipOpen = true"
                        @mouseleave="tooltipOpen = false"
                        class="w-3.5 h-3.5 text-gray-300 cursor-help hover:text-gray-400 transition-colors"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                      >
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                      </svg>
                      <div
                        v-show="tooltipOpen"
                        @click.stop
                        class="absolute left-0 bottom-full mb-3 w-56 p-4 bg-cabinet-text-main text-white text-[11px] leading-relaxed rounded-2xl shadow-2xl z-50 transition-all after:content-[''] after:absolute after:top-full after:left-4 after:border-8 after:border-transparent after:border-t-cabinet-text-main"
                      >
                        Auto Stake automatically restakes your initial investment when the period ends.
                      </div>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 font-medium mt-0.5">Renew stake automatically</p>
            </div>
          </div>

          <button
            @click="toggleAutoStake"
            class="relative inline-flex items-center h-8 rounded-full transition-all duration-500 ease-in-out focus:outline-none"
            :class="autoStake ? 'w-32 bg-cabinet-blue' : 'w-14 bg-gray-200'"
          >
            <span
              class="absolute left-1 inline-block h-6 w-6 transform rounded-full bg-white transition-all duration-500 ease-in-out shadow-sm z-10"
              :class="autoStake ? 'translate-x-[96px]' : 'translate-x-0'"
            ></span>
            <span
                class="absolute left-3 text-[9px] font-black text-white uppercase tracking-wider transition-all duration-500 overflow-hidden whitespace-nowrap"
                :class="autoStake ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4'"
            >
                Auto Stake
            </span>
          </button>
        </div>
      </div>

      <div class="flex flex-col gap-3">
        <button
          @click="confirmStake"
          :disabled="!isValid || isSubmitting"
          class="w-full px-8 py-5 bg-gradient-to-r from-cabinet-blue to-[#5D6DFF] text-white text-xs font-black rounded-2xl hover:shadow-[0_10px_30px_-5px_rgba(59,78,252,0.4)] hover:-translate-y-0.5 disabled:opacity-40 disabled:hover:shadow-none disabled:hover:translate-y-0 transition-all duration-300 uppercase tracking-[0.2em] flex items-center justify-center gap-3"
        >
          <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          {{ isSubmitting ? 'Processing' : 'Confirm Stake' }}
        </button>

        <button
          @click="close"
          class="w-full py-3 text-gray-400 hover:text-gray-600 text-[10px] font-black transition-colors uppercase tracking-[0.2em]"
        >
          Cancel
        </button>
      </div>
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
