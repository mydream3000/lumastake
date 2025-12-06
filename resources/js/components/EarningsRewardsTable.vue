<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="rewards"
      :columns="columns"
      :grid-cols="12"
      default-sort="date"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-amount="{ value }">
        <span class="font-semibold text-cabinet-green">${{ value }}</span>
      </template>

      <template #cell-profit_amount="{ value }">
        <span class="text-sm">${{ value }}</span>
      </template>

      <template #cell-reward_percentage="{ value }">
        <span class="text-sm">{{ value }}%</span>
      </template>
    </data-table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import DataTable from './DataTable.vue'
import { parseDateToTimestamp, formatDateShort } from '../utils/dateFormatter'

const props = defineProps({
  dataUrl: {
    type: String,
    required: true
  }
})

const rewards = ref([])
const loading = ref(true)

const columns = [
  {
    key: 'date',
    label: 'Date',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm',
    sortValue: (row) => parseDateToTimestamp(row.date),
    format: (value) => value
  },
  {
    key: 'referral_name',
    label: 'Referral',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-medium'
  },
  {
    key: 'profit_amount',
    label: 'Their Profit',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'reward_percentage',
    label: 'Your %',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'amount',
    label: 'Your Reward',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-semibold'
  }
]

async function fetchRewards() {
  try {
    const response = await axios.get(props.dataUrl)
    rewards.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch earnings rewards:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchRewards()
})
</script>
