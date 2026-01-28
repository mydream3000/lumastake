<template>
  <div class="ajax-show-table" :key="renderKey">
    <!-- Debug Info -->
    <div style="display:none;">Loading: {{ loading }}, Data Length: {{ data.length }}, RenderKey: {{ renderKey }}</div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cabinet-orange"></div>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && data.length === 0" class="flex items-center justify-center py-12">
      <p class="text-gray-500">No data available</p>
    </div>

    <!-- Table Content -->
    <div v-if="!loading && data.length > 0" class="table-content-wrapper">
      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th
                v-for="column in columns"
                :key="column.key"
                :style="{ width: column.width }"
                class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                :class="{ 'cursor-pointer hover:bg-gray-100': column.sortable }"
                @click="column.sortable ? sort(column.key) : null"
              >
                <div class="flex items-center gap-2">
                  <span>{{ column.label }}</span>
                  <span v-if="column.sortable" class="text-gray-400">
                    <i v-if="sortKey === column.key && sortDirection === 'asc'" class="fas fa-sort-up"></i>
                    <i v-else-if="sortKey === column.key && sortDirection === 'desc'" class="fas fa-sort-down"></i>
                    <i v-else class="fas fa-sort"></i>
                  </span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(row, index) in data" :key="row.id || index" class="hover:bg-gray-50">
              <td
                v-for="column in columns"
                :key="column.key"
                class="px-2 py-2 whitespace-nowrap"
              >
                <div v-if="column.render" v-html="column.render(row[column.key], row)"></div>
                <span v-else>{{ row[column.key] }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="total > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
        <div class="flex items-center gap-3">
          <label class="text-sm text-gray-700">Per page:</label>
          <select v-model.number="perPage" @change="changePerPage" class="px-2 py-1 border border-gray-300 rounded-md text-sm">
            <option :value="20">20</option>
            <option :value="50">50</option>
            <option :value="100">100</option>
            <option :value="200">200</option>
            <option :value="500">500</option>
          </select>
        </div>
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            Previous
          </button>
          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === lastPage"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
              to
              <span class="font-medium">{{ Math.min(currentPage * perPage, total) }}</span>
              of
              <span class="font-medium">{{ total }}</span>
              results
            </p>
          </div>
          <div class="flex items-center gap-4">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
              >
                <i class="fas fa-chevron-left"></i>
              </button>
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="typeof page === 'number' ? goToPage(page) : null"
                :class="[
                  currentPage === page
                    ? 'z-10 bg-cabinet-orange border-cabinet-orange text-white'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  typeof page !== 'number' ? 'cursor-default' : ''
                ]"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
              >
                <i class="fas fa-chevron-right"></i>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  dataUrl: {
    type: String,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const data = ref([])
const loading = ref(true)
const currentPage = ref(1)
const perPage = ref(20)
const total = ref(0)
const lastPage = ref(1)
const sortKey = ref('created_at')
const sortDirection = ref('desc')
const activeFilters = ref({ ...props.filters })
const renderKey = ref(0)

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5

  if (lastPage.value <= maxVisible) {
    for (let i = 1; i <= lastPage.value; i++) {
      pages.push(i)
    }
  } else {
    if (currentPage.value <= 3) {
      for (let i = 1; i <= 4; i++) pages.push(i)
      pages.push('...')
      pages.push(lastPage.value)
    } else if (currentPage.value >= lastPage.value - 2) {
      pages.push(1)
      pages.push('...')
      for (let i = lastPage.value - 3; i <= lastPage.value; i++) pages.push(i)
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = currentPage.value - 1; i <= currentPage.value + 1; i++) pages.push(i)
      pages.push('...')
      pages.push(lastPage.value)
    }
  }

  return pages
})

async function fetchData() {
  loading.value = true

  try {
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: perPage.value,
      sort: sortKey.value,
      direction: sortDirection.value,
      ...activeFilters.value
    })

    const url = `${props.dataUrl}?${params}`

    const response = await fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    const result = await response.json()

    data.value = result.data || []
    total.value = result.total || 0
    currentPage.value = result.current_page || 1
    lastPage.value = result.last_page || 1
  } catch (error) {
    console.error('AjaxDataTable: Error fetching data:', error)
  } finally {
    loading.value = false
    await nextTick()
    renderKey.value++
    await nextTick()

    // Emit event with current page data (ids and meta) after DOM updated
    try {
      const ids = Array.isArray(data.value)
        ? data.value.map(r => r && typeof r.id !== 'undefined' ? r.id : null).filter(v => v !== null)
        : []
      window.dispatchEvent(new CustomEvent('datatable-page-data', {
        detail: {
          ids,
          currentPage: currentPage.value,
          perPage: perPage.value,
          total: total.value,
          lastPage: lastPage.value,
          sortKey: sortKey.value,
          sortDirection: sortDirection.value,
        }
      }))
    } catch (e) {
      console.warn('AjaxDataTable: Failed to emit page data event', e)
    }
  }
}

function sort(key) {
  if (sortKey.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDirection.value = 'asc'
  }
  currentPage.value = 1
  fetchData()
}

function goToPage(page) {
  if (page >= 1 && page <= lastPage.value) {
    currentPage.value = page
    fetchData()
  }
}

function changePerPage() {
  currentPage.value = 1 // Reset to first page when changing per page
  fetchData()
}

// Watch for filter changes
watch(() => props.filters, (newFilters) => {
  activeFilters.value = { ...newFilters }
  currentPage.value = 1
  fetchData()
}, { deep: true })

// Listen for custom events
onMounted(() => {
  fetchData()

  // Listen for filter change events
  const handleFilterChange = (e) => {
    if (e.detail) {
      activeFilters.value = { ...e.detail }
      currentPage.value = 1
      fetchData()
    }
  }

  // Listen for refresh events
  const handleRefresh = () => {
    fetchData()
  }

  window.addEventListener('datatable-filter-change', handleFilterChange)
  window.addEventListener('datatable-refresh', handleRefresh)
})
</script>
