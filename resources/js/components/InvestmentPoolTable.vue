<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="pools"
      :columns="columns"
      :grid-cols="14"
      :hide-header="false"
      :default-per-page="100"
    >
      <template #cell-select="{ row }">
        <input
          type="checkbox"
          v-model="selectedPools"
          :value="row.id"
          class="w-4 h-4 text-cabinet-orange bg-gray-100 border-gray-300 rounded focus:ring-cabinet-orange focus:ring-2"
        >
      </template>

      <template #cell-profit="{ value }">
        <span class="text-cabinet-green font-semibold">{{ value }}</span>
      </template>

      <template #cell-action="{ row }">
        <button
          @click="openStakeModal(row)"
          class="px-4 py-1.5 text-sm rounded bg-cabinet-green text-white hover:bg-cabinet-green/80 font-medium"
        >
          Stake
        </button>
      </template>
    </data-table>

    <stake-modal
      :is-open="isModalOpen"
      :pool="selectedPool"
      :available-balance="availableBalance"
      @close="closeStakeModal"
      @success="handleStakeSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import DataTable from './DataTable.vue'
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
const selectedPools = ref([])
const isModalOpen = ref(false)
const selectedPool = ref(null)
const availableBalance = ref(props.balance)

const columns = [
  {
    key: 'select',
    label: '',
    sortable: false,
    span: 1,
    headerClass: 'text-left',
    cellClass: 'text-left'
  },
  {
    key: 'name',
    label: 'Name',
    sortable: true,
    span: 4,
    headerClass: 'text-left',
    cellClass: 'font-medium'
  },
  {
    key: 'days',
    label: 'Days',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'min_stake',
    label: 'Min Stake',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'font-semibold'
  },
  {
    key: 'profit',
    label: 'Profit',
    sortable: true,
    span: 3,
    headerClass: 'text-center',
    cellClass: 'text-center text-sm'
  },
  {
    key: 'action',
    label: '',
    sortable: false,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-left'
  }
]

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
  // Редирект на страницу Stakings
  window.location.href = '/dashboard/staking'
}

onMounted(() => {
  fetchPools()
})
</script>
