<template>
  <div class="flex flex-col h-full bg-white">
    <!-- Header/Selection -->
    <div class="mb-8">
      <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Select Asset & Network</label>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <button
          v-for="token in ['USDT', 'USDC']"
          :key="token"
          @click="selectedToken = token; fetchAddress()"
          class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm"
          :class="selectedToken === token ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
        >
          {{ token }}
        </button>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <button
          v-for="net in availableNetworks"
          :key="net.id"
          @click="selectedNetwork = net.id; fetchAddress()"
          class="py-3 px-4 rounded-xl border-2 font-bold transition-all text-sm uppercase"
          :class="selectedNetwork === net.id ? 'border-cabinet-blue bg-cabinet-blue/5 text-cabinet-blue' : 'border-gray-100 text-gray-400 hover:border-gray-200'"
        >
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
