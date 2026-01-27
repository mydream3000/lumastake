<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <!-- Mobile Version -->
    <div class="lg:hidden space-y-4">
      <div v-for="row in rewards" :key="row.id" class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm relative overflow-hidden">
          <div class="flex items-center justify-between mb-6 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Date</div>
                  <div class="text-sm font-bold text-gray-500">{{ row.date }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Your %</div>
                  <div class="text-sm font-black text-cabinet-blue">{{ row.reward_percentage }}%</div>
              </div>
          </div>

          <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 mb-6 relative">
              <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Referral</div>
              <div class="text-sm font-black text-cabinet-text-main">{{ row.referral_name }}</div>
          </div>

          <div class="flex items-center justify-between pt-4 border-t border-gray-100 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5">Their Profit</div>
                  <div class="text-xs font-bold text-gray-500">${{ row.profit_amount }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5">Your Reward</div>
                  <div class="text-lg font-black text-green-600">${{ row.amount }}</div>
              </div>
          </div>
      </div>

      <div v-if="!rewards.length" class="text-center py-12 bg-white rounded-[32px] border border-gray-100 shadow-sm">
          <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No referral rewards found</p>
      </div>
    </div>

    <!-- Desktop Version -->
    <data-table
      class="hidden lg:block"
      :data="rewards"
      :columns="columns"
      :grid-cols="12"
      default-sort="date"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-amount="{ value }">
        <span class="font-bold text-green-600">${{ value }}</span>
      </template>

      <template #cell-profit_amount="{ value }">
        <span class="text-sm font-medium text-cabinet-text-main">${{ value }}</span>
      </template>

      <template #cell-reward_percentage="{ value }">
        <span class="text-sm font-bold text-[#2BA6FF]">{{ value }}%</span>
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
