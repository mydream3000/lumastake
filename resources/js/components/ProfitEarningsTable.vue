<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="earnings"
      :columns="columns"
      :grid-cols="23"
      default-sort="created_at"
      default-sort-order="desc"
      :default-per-page="10"
    >
      <template #cell-earned="{ value }">
        <span class="font-semibold text-cabinet-green">{{ value }}</span>
      </template>

      <template #cell-profit="{ value }">
        <span class="text-cabinet-orange font-semibold">{{ value }}</span>
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
    span: 3,
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
    span: 3,
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
    span: 3,
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
