<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-blue"></div>
  </div>
  <div v-else>
    <!-- Mobile Version -->
    <div class="lg:hidden space-y-4">
      <div v-for="row in earnings" :key="row.id" class="bg-white rounded-[32px] p-6 border border-gray-100 shadow-sm relative overflow-hidden">
          <div class="flex items-center justify-between mb-6 relative">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Date</div>
                  <div class="text-sm font-bold text-gray-500">{{ row.created_at }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Tier</div>
                  <div class="text-sm font-black text-cabinet-text-main">{{ row.tier }}</div>
              </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6 relative">
              <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Principal</div>
                  <div class="text-sm font-black text-cabinet-text-main">${{ row.invested_amount }}</div>
              </div>
              <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">APR %</div>
                  <div class="text-sm font-black text-cabinet-blue">{{ row.profit }}%</div>
              </div>
          </div>

          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
              <div>
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5">Duration</div>
                  <div class="text-xs font-bold text-gray-500">{{ row.duration }}</div>
              </div>
              <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-0.5">Profit Earned</div>
                  <div class="text-lg font-black text-green-600">${{ row.earned }}</div>
              </div>
          </div>
      </div>

      <div v-if="!earnings.length" class="text-center py-12 bg-white rounded-[32px] border border-gray-100 shadow-sm">
          <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No profit earnings found</p>
      </div>
    </div>

    <!-- Desktop Version -->
    <data-table
      class="hidden lg:block"
      :data="earnings"
      :columns="columns"
      :grid-cols="23"
      default-sort="created_at"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-earned="{ value }">
        <span class="font-bold text-green-600">{{ value }}</span>
      </template>

      <template #cell-profit="{ value }">
        <span class="text-[#2BA6FF] font-bold">{{ value }}</span>
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

const earnings = ref([])
const loading = ref(true)

const columns = [
  {
    key: 'created_at',
    label: 'Date',
    sortable: true,
    span: 4,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'invested_amount',
    label: 'Principal',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-semibold',
    format: (value) => `$${value}`
  },
  {
    key: 'earned',
    label: 'Profit Earned',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-sm',
    format: (value) => `$${value}`
  },
  {
    key: 'profit',
    label: 'APR %',
    sortable: true,
    span: 2,
    headerClass: 'text-left',
    cellClass: 'text-sm',
    format: (value) => `${value}%`
  },
  {
    key: 'duration',
    label: 'Duration',
    sortable: true,
    span: 4,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'tier',
    label: 'Tier',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  },
  {
    key: 'description',
    label: 'Description',
    sortable: false,
    span: 4,
    headerClass: 'text-left',
    cellClass: 'text-sm text-cabinet-gray/70'
  }
]

async function fetchEarnings() {
  try {
    const response = await axios.get(props.dataUrl)
    earnings.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch profit earnings:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchEarnings()
})
</script>
