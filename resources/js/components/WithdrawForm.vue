<template>
  <div class="flex flex-col h-full bg-white">
    <!-- Step 1: Request -->
    <div v-if="step === 1" class="space-y-6">
      <!-- Balance Info -->
      <div class="bg-cabinet-blue/5 rounded-xl p-4 border border-cabinet-blue/10">
        <p class="text-sm text-gray-500 font-medium mb-1">Available Balance</p>
        <p class="text-2xl font-black text-cabinet-blue">${{ availableBalance.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</p>
      </div>

      <!-- Token Selection -->
      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Select Token</label>
        <div class="grid grid-cols-2 gap-4">
          <button
            v-for="t in ['USDT', 'USDC']"
            :key="t"
            @click="form.token = t"
            class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm"
            :class="form.token === t ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
          >
            {{ t }}
          </button>
        </div>
      </div>

      <!-- Network Selection -->
      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Network</label>
        <div class="grid grid-cols-2 gap-4">
          <button
            v-for="net in availableNetworks"
            :key="net.id"
            @click="form.network = net.id"
            class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm uppercase"
            :class="form.network === net.id ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
          >
            {{ net.name }}
          </button>
        </div>
      </div>

      <!-- Amount -->
      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Amount</label>
        <div class="relative">
          <input
            v-model="form.amount"
            type="number"
            step="0.01"
            placeholder="0.00"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-cabinet-text-main focus:ring-2 focus:ring-cabinet-blue/20 outline-none transition-all"
          >
          <button
            @click="form.amount = availableBalance"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-cabinet-blue hover:underline"
          >
            MAX
          </button>
        </div>
      </div>

      <!-- Receiver Address -->
      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Receiver Address ({{ selectedNetworkName }})</label>
        <input
          v-model="form.receiver_address"
          type="text"
          :placeholder="addressPlaceholder"
          class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium text-sm text-cabinet-text-main focus:ring-2 focus:ring-cabinet-blue/20 outline-none transition-all"
        >
      </div>

      <!-- Error Message -->
      <div v-if="error" class="p-3 bg-red-50 text-red-500 text-xs font-bold rounded-lg border border-red-100">
        {{ error }}
      </div>

      <!-- Submit Button -->
      <button
        @click="requestWithdraw"
        :disabled="loading"
        class="w-full bg-cabinet-lime hover:bg-cabinet-lime/90 text-cabinet-text-main py-4 rounded-xl font-black uppercase tracking-widest transition-all shadow-lg shadow-cabinet-lime/20 disabled:opacity-50"
      >
        {{ loading ? 'Processing...' : 'Request Withdrawal' }}
      </button>
    </div>

    <!-- Step 2: Confirmation -->
    <div v-else class="space-y-8 py-4">
      <div class="text-center">
        <div class="w-20 h-20 bg-cabinet-blue/5 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-cabinet-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-black text-cabinet-text-main mb-2 uppercase">Check Your Email</h3>
        <p class="text-sm text-gray-500 font-medium">We've sent a 6-digit confirmation code to your registered email address.</p>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 text-center">Confirmation Code</label>
        <div class="flex justify-center gap-2">
          <input
            v-model="confirmCode"
            type="text"
            maxlength="6"
            placeholder="000000"
            class="w-48 bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-center text-2xl font-black tracking-[0.5em] text-cabinet-blue focus:ring-2 focus:ring-cabinet-blue/20 outline-none transition-all"
          >
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="p-3 bg-red-50 text-red-500 text-xs font-bold rounded-lg border border-red-100 text-center">
        {{ error }}
      </div>

      <div class="space-y-4">
        <button
          @click="confirmWithdraw"
          :disabled="loading || confirmCode.length !== 6"
          class="w-full bg-cabinet-blue hover:bg-cabinet-blue/90 text-white py-4 rounded-xl font-black uppercase tracking-widest transition-all shadow-lg shadow-cabinet-blue/20 disabled:opacity-50"
        >
          {{ loading ? 'Verifying...' : 'Confirm Withdrawal' }}
        </button>

        <button
          @click="resendCode"
          :disabled="resending"
          class="w-full text-sm font-bold text-gray-400 hover:text-cabinet-blue transition-colors uppercase tracking-wider"
        >
          {{ resending ? 'Resending...' : 'Resend Code' }}
        </button>
      </div>

      <button @click="step = 1; error = null" class="w-full text-xs font-bold text-gray-300 hover:text-gray-500 transition-colors uppercase">
        Go Back
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const step = ref(1)
const loading = ref(false)
const resending = ref(false)
const error = ref(null)
const confirmCode = ref('')

const form = ref({
  token: 'USDT',
  network: 'tron',
  amount: '',
  receiver_address: ''
})

const availableBalance = computed(() => {
  if (window.Alpine) {
    return window.Alpine.store('userBalance').availableBalance
  }
  return 0
})

const networks = {
  USDT: [
    { id: 'tron', name: 'TRC-20' },
    { id: 'ethereum', name: 'ERC-20' }
  ],
  USDC: [
    { id: 'bsc', name: 'BEP-20' },
    { id: 'ethereum', name: 'ERC-20' }
  ]
}

const availableNetworks = computed(() => networks[form.value.token] || [])

const selectedNetworkName = computed(() => {
  const net = availableNetworks.value.find(n => n.id === form.value.network)
  return net ? net.name : ''
})

const addressPlaceholder = computed(() => {
  if (form.value.network === 'tron') return 'T...'
  return '0x...'
})

watch(() => form.value.token, (newToken) => {
  const supported = networks[newToken].some(n => n.id === form.value.network)
  if (!supported) {
    form.value.network = networks[newToken][0].id
  }
})

async function requestWithdraw() {
  loading.value = true
  error.value = null

  try {
    const response = await axios.post('/dashboard/withdraw', form.value)
    if (response.data.success) {
      step.value = 2
      if (window.showToast) {
        window.showToast(response.data.message, 'success')
      }
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to request withdrawal. Please check your inputs.'
  } finally {
    loading.value = false
  }
}

async function confirmWithdraw() {
  loading.value = true
  error.value = null

  try {
    const response = await axios.post('/dashboard/withdraw/confirm', {
      code: confirmCode.value
    })
    if (response.data.success) {
      if (window.showToast) {
        window.showToast(response.data.message, 'success')
      }
      // Refresh balance
      if (window.Alpine) {
        window.Alpine.store('userBalance').refresh()
      }
      // Close sidebar (assuming the event listener exists)
      window.dispatchEvent(new CustomEvent('close-rightbar'))
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Invalid code or request failed.'
  } finally {
    loading.value = false
  }
}

async function resendCode() {
  resending.value = true
  error.value = null
  try {
    const response = await axios.post('/dashboard/withdraw/resend-code')
    if (response.data.success && window.showToast) {
      window.showToast(response.data.message, 'success')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to resend code.'
  } finally {
    resending.value = false
  }
}
</script>
