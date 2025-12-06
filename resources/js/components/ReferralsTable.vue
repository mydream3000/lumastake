<template>
  <div v-if="loading" class="text-center py-8">
    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
  </div>
  <div v-else>
    <data-table
      :data="referrals"
      :columns="columns"
      :grid-cols="6"
      default-sort="created_at"
      default-sort-order="desc"
      :default-per-page="10"
    >
    </data-table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import DataTable from './DataTable.vue'

const props = defineProps({
  dataUrl: {
    type: String,
    required: true
  }
})

const referrals = ref([])
const loading = ref(true)

const columns = [
  {
    key: 'name',
    label: 'Name',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'font-medium'
  },
  {
    key: 'email',
    label: 'Email',
    sortable: true,
    span: 3,
    headerClass: 'text-left',
    cellClass: 'text-sm'
  }
]

async function fetchReferrals() {
  try {
    const response = await axios.get(props.dataUrl)
    referrals.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch referrals:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchReferrals()
})
</script>
