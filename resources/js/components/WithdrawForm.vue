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
            v-for="t in tokens"
            :key="t.id"
            @click="form.token = t.id"
            class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm flex items-center justify-center gap-2"
            :class="form.token === t.id ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
          >
            <img :src="t.icon" :alt="t.id" class="w-5 h-5">
            {{ t.id }}
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
            class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm uppercase flex items-center justify-center gap-2"
            :class="form.network === net.id ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
          >
            <span v-html="net.icon" class="w-5 h-5 flex-shrink-0"></span>
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
        <button @click="pasteFromClipboard" type="button" class="text-cabinet-blue text-xs font-bold hover:underline flex items-center justify-center gap-1 mx-auto mt-3">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 1H4C2.9 1 2 1.9 2 3V17H4V3H16V1ZM19 5H8C6.9 5 6 5.9 6 7V21C6 22.1 6.9 23 8 23H19C20.1 23 21 22.1 21 21V7C21 5.9 20.1 5 19 5ZM19 21H8V7H19V21Z" fill="currentColor"/>
          </svg>
          Paste from clipboard
        </button>
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

const pasteFromClipboard = async () => {
  try {
    const text = await navigator.clipboard.readText()
    if (text) {
      const code = text.trim().substring(0, 6).replace(/[^0-9]/g, '')
      if (code) {
        confirmCode.value = code
        if (window.showToast) {
          window.showToast('Code pasted from clipboard', 'success')
        }
      }
    }
  } catch (err) {
    console.error('Failed to paste:', err)
  }
}

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

// Network icons (inline SVG)
const networkIcons = {
  tron: `<svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="#ef0027" d="M8 16c4.4183 0 8-3.5817 8-8 0-4.41828-3.5817-8-8-8C3.58172 0 0 3.58172 0 8c0 4.4183 3.58172 8 8 8Z"/><path fill="#fff" d="M10.966 4.95654 3.75 3.62854l3.7975 9.55601 5.2915-6.447-1.873-1.78101ZM10.85 5.54155l1.104 1.0495-3.019 0.5465 1.915-1.596Zm-2.571 1.4865-3.182-2.63901 5.201 0.95701-2.019 1.682Zm-0.2265 0.467-0.519 4.29L4.736 4.74354l3.3165 2.75151Zm0.48 0.2275 3.3435-0.605-3.835 4.6715 0.4915-4.0665Z"/></svg>`,
  ethereum: `<svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#627EEA"/><path d="M16.498 4V12.87L23.995 16.22L16.498 4Z" fill="white" fill-opacity="0.602"/><path d="M16.498 4L9 16.22L16.498 12.87V4Z" fill="white"/><path d="M16.498 21.968V27.995L24 17.616L16.498 21.968Z" fill="white" fill-opacity="0.602"/><path d="M16.498 27.995V21.967L9 17.616L16.498 27.995Z" fill="white"/><path d="M16.498 20.573L23.995 16.22L16.498 12.872V20.573Z" fill="white" fill-opacity="0.2"/><path d="M9 16.22L16.498 20.573V12.872L9 16.22Z" fill="white" fill-opacity="0.602"/></svg>`,
  bsc: `<svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#F3BA2F"/><path d="M12.116 14.404L16 10.52L19.886 14.404L22.146 12.144L16 6L9.856 12.144L12.116 14.404ZM6 16L8.26 13.74L10.52 16L8.26 18.26L6 16ZM12.116 17.596L16 21.48L19.886 17.596L22.146 19.856L16 26L9.856 19.856L12.116 17.596ZM21.48 16L23.74 13.74L26 16L23.74 18.26L21.48 16ZM16 18.26L13.74 16L16 13.74L18.26 16L16 18.26Z" fill="white"/></svg>`
}

const tokens = [
  { id: 'USDT', icon: '/img/usdt-logo-coin.png' },
  { id: 'USDC', icon: '/img/usd-coin-usdc-logo.svg' }
]

const networks = {
  USDT: [
    { id: 'tron', name: 'TRC-20', icon: networkIcons.tron },
    { id: 'ethereum', name: 'ERC-20', icon: networkIcons.ethereum }
  ],
  USDC: [
    { id: 'bsc', name: 'BEP-20', icon: networkIcons.bsc },
    { id: 'ethereum', name: 'ERC-20', icon: networkIcons.ethereum }
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
