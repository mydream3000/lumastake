<template>
  <div class="flex flex-col h-full bg-white">
    <!-- Header/Selection -->
    <div class="mb-8">
      <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Select Asset & Network</label>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <button
          v-for="token in tokens"
          :key="token.id"
          @click="selectedToken = token.id; fetchAddress()"
          class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm flex items-center justify-center gap-2"
          :class="selectedToken === token.id ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
        >
          <img :src="token.icon" :alt="token.id" class="w-5 h-5">
          {{ token.id }}
        </button>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <button
          v-for="net in availableNetworks"
          :key="net.id"
          @click="selectedNetwork = net.id; fetchAddress()"
          class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm uppercase flex items-center justify-center gap-2"
          :class="selectedNetwork === net.id ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
        >
          <span v-html="net.icon" class="w-5 h-5 flex-shrink-0"></span>
          {{ net.name }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cabinet-blue"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex-1 flex flex-col items-center justify-center text-center p-6">
      <div class="text-red-500 mb-4">
        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 15c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
      </div>
      <p class="text-gray-600 font-medium">{{ error }}</p>
      <button @click="fetchAddress" class="mt-4 text-cabinet-blue font-bold hover:underline uppercase text-sm">Retry</button>
    </div>

    <!-- Address Display -->
    <div v-else class="flex-1 flex flex-col items-center">
      <div class="w-full text-left mb-4">
        <p class="text-[17px] font-bold text-[#222]">Wallet Address</p>
      </div>

      <!-- QR Code -->
      <div class="mb-8 p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
        <div v-html="qrCode" class="w-[200px] h-[200px]"></div>
      </div>

      <!-- Address Input -->
      <div class="w-full relative mb-4">
        <input
          type="text"
          readonly
          :value="address"
          class="w-full bg-gray-50 border border-[#ccc] rounded-[6px] h-[50px] px-4 text-sm font-medium text-[#444] text-center"
        >
      </div>

      <!-- Copy Button -->
      <button
        @click="copyAddress"
        class="w-full bg-[#e3ff3b] hover:bg-[#d4ee30] h-[42px] rounded-[6px] font-black text-[#262262] uppercase text-sm transition-colors shadow-sm"
      >
        Copy
      </button>

      <!-- Instructions -->
      <p class="mt-8 text-center text-[16px] text-[#101221]/70 leading-relaxed px-4">
        Please transfer <span class="font-bold text-cabinet-blue">{{ selectedToken }}</span>
        (<span class="uppercase font-bold">{{ selectedNetworkName }}</span>)
        from your preferred exchange to the above wallet address
      </p>

      <!-- Confirmation Button -->
      <button
        @click="confirmDeposit"
        :disabled="confirming"
        class="mt-auto w-full border-2 border-cabinet-blue text-cabinet-blue py-3 rounded-xl font-bold uppercase hover:bg-cabinet-blue hover:text-white transition-all disabled:opacity-50"
      >
        {{ confirming ? 'Confirming...' : 'I have sent funds' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const selectedToken = ref('USDT')
const selectedNetwork = ref('tron')
const address = ref('')
const qrCode = ref('')
const loading = ref(true)
const error = ref(null)
const confirming = ref(false)

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

const availableNetworks = computed(() => networks[selectedToken.value] || [])

const selectedNetworkName = computed(() => {
  const net = availableNetworks.value.find(n => n.id === selectedNetwork.value)
  return net ? net.name : selectedNetwork.value
})

// Если при смене токена текущая сеть не поддерживается, выбираем первую доступную
watch(selectedToken, (newToken) => {
  const supported = networks[newToken].some(n => n.id === selectedNetwork.value)
  if (!supported) {
    selectedNetwork.value = networks[newToken][0].id
  }
})

async function fetchAddress() {
  loading.value = true
  error.value = null
  try {
    const response = await axios.post('/dashboard/deposit/accept-usdt', {
      token: selectedToken.value,
      network: selectedNetwork.value
    })

    if (response.data.success) {
      address.value = response.data.address
      qrCode.value = response.data.qr_code
    } else {
      error.value = response.data.message || 'Failed to get address'
    }
  } catch (err) {
    console.error('Fetch address error:', err)
    error.value = err.response?.data?.message || 'Connection error. Please try again.'
  } finally {
    loading.value = false
  }
}

async function confirmDeposit() {
  if (confirming.value) return

  confirming.value = true
  try {
    const response = await axios.post('/dashboard/deposit/confirm-payment', {
      token: selectedToken.value,
      network: selectedNetwork.value,
      address: address.value
    })

    if (response.data.success) {
      if (window.showToast) {
        window.showToast('We are looking for your deposit. It will appear in history soon.', 'success')
      }
      // Close sidebar
      window.dispatchEvent(new CustomEvent('close-rightbar'))
    }
  } catch (err) {
    console.error('Confirm deposit error:', err)
    if (window.showToast) {
      window.showToast('Failed to confirm. Please contact support.', 'error')
    }
  } finally {
    confirming.value = false
  }
}

function copyAddress() {
  navigator.clipboard.writeText(address.value).then(() => {
    if (window.showToast) {
      window.showToast('Address copied to clipboard!', 'success')
    }
  })
}

onMounted(() => {
  fetchAddress()
})
</script>
