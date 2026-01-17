<template>
  <div class="data-table-wrapper flex flex-col h-full">
  <!-- Header Row -->
  <div
      v-if="!hideHeader"
      class="grid gap-4 px-4 py-3 text-sm font-semibold rounded-lg sticky top-0 z-10"
      :class="headerClass"
      :style="gridStyle"
  >
      <div
          v-for="column in columns"
          :key="column.key"
          :class="[column.headerClass || 'text-left', { 'cursor-pointer': column.sortable }]"
          :style="{ gridColumn: `span ${column.span || 1}` }"
          @click="column.sortable ? sort(column.key) : null"
      >
          <div
            class="flex items-center gap-1.5"
            :class="column.headerClass?.includes('text-center') ? 'justify-center' : 'justify-start'"
          >
              <span>{{ column.label }}</span>
              <svg
                  v-if="column.sortable"
                  width="6"
                  height="12"
                  viewBox="0 0 6 12"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                  class="flex-shrink-0"
              >
                  <path
                      d="M3.5655 0.184608C3.5945 0.212859 3.7185 0.319532 3.8205 0.418899C4.462 1.00146 5.512 2.52119 5.8325 3.31661C5.884 3.43741 5.993 3.74282 6 3.90599C6 4.06235 5.964 4.2114 5.891 4.35363C5.789 4.53093 5.6285 4.67316 5.439 4.7511C5.3075 4.80127 4.914 4.8792 4.907 4.8792C4.4765 4.95714 3.777 5 3.004 5C2.2675 5 1.5965 4.95714 1.1595 4.89333C1.1525 4.88602 0.6635 4.80809 0.496 4.72284C0.19 4.56649 0 4.26108 0 3.93424V3.90599C0.00750017 3.69313 0.1975 3.24549 0.2045 3.24549C0.5255 2.49294 1.524 1.00828 2.1875 0.411593C2.1875 0.411593 2.358 0.243546 2.4645 0.170482C2.6175 0.0565028 2.807 0 2.9965 0C3.208 0 3.405 0.0638089 3.5655 0.184608Z"
                      :fill="sortKey === column.key && sortOrder === 'asc' ? '#3B4EFC' : '#CCCCCC'"
                  />
                  <path
                      d="M2.4345 11.8154C2.4055 11.7871 2.2815 11.6805 2.1795 11.5811C1.538 10.9985 0.488 9.47881 0.1675 8.68339C0.116 8.56259 0.007 8.25718 0 8.09401C0 7.93765 0.036 7.7886 0.109 7.64637C0.211 7.46907 0.3715 7.32684 0.561 7.2489C0.6925 7.19873 1.086 7.1208 1.093 7.1208C1.5235 7.04286 2.223 7 2.996 7C3.7325 7 4.4035 7.04286 4.8405 7.10667C4.8475 7.11398 5.3365 7.19191 5.504 7.27716C5.81 7.43351 6 7.73892 6 8.06576V8.09401C5.9925 8.30687 5.8025 8.75451 5.7955 8.75451C5.4745 9.50706 4.476 10.9917 3.8125 11.5884C3.8125 11.5884 3.642 11.7565 3.5355 11.8295C3.3825 11.9435 3.193 12 3.0035 12C2.792 12 2.595 11.9362 2.4345 11.8154Z"
                      :fill="sortKey === column.key && sortOrder === 'desc' ? '#3B4EFC' : '#CCCCCC'"
                  />
              </svg>
          </div>
      </div>
  </div>
    <!-- Table -->
    <div class="space-y-3 flex-1 overflow-y-auto min-h-0 pt-3">

      <!-- Data Rows -->
      <div
        v-for="(row, index) in paginatedData"
        :key="row.id || index"
        class="grid gap-4 px-4 py-3 rounded-lg transition-colors"
        :class="getRowClass(row)"
        :style="gridStyle"
      >
        <div
          v-for="column in columns"
          :key="column.key"
          :class="[column.cellClass || 'text-left']"
          :style="{ gridColumn: `span ${column.span || 1}` }"
        >
          <slot
            :name="`cell-${column.key}`"
            :row="row"
            :value="getNestedValue(row, column.key)"
          >
            <span v-if="column.format">
              {{ column.format(getNestedValue(row, column.key), row) }}
            </span>
            <span v-else>
              {{ getNestedValue(row, column.key) }}
            </span>
          </slot>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!paginatedData.length" class="text-center py-8 text-gray-500">
        {{ emptyText }}
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="data.length > 0" class="mt-6 flex items-center justify-between flex-shrink-0">
      <!-- Per Page Selector -->
      <div class="flex items-center gap-2 text-cabinet-blue font-medium">
        <span>Row per page</span>
        <select
          v-model.number="perPage"
          class="px-3 py-1 border border-gray-200 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-cabinet-blue"
        >
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
      </div>

      <!-- Pagination Controls -->
      <div class="flex items-center gap-2">
        <!-- Previous Page -->
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-1.5 text-sm transition-colors disabled:cursor-not-allowed rounded-md"
          :class="currentPage === 1 ? 'bg-gray-100 text-gray-400' : 'text-gray-700 hover:bg-cabinet-blue/10 hover:text-cabinet-blue'"
        >
          &lt; Previous
        </button>

        <!-- Page Numbers -->
        <template v-for="page in visiblePages" :key="page">
          <button
            v-if="page !== '...'"
            @click="goToPage(page)"
            class="px-3 py-1.5 text-sm transition-colors rounded-md"
            :class="currentPage === page ? 'bg-cabinet-blue text-white' : 'text-gray-700 hover:bg-cabinet-blue/10 hover:text-cabinet-blue'"
          >
            {{ page }}
          </button>
          <span v-else class="px-2 text-gray-400">...</span>
        </template>

        <!-- Next Page -->
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="px-3 py-1.5 text-sm transition-colors disabled:cursor-not-allowed rounded-md"
          :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400' : 'text-gray-700 hover:bg-cabinet-blue/10 hover:text-cabinet-blue'"
        >
          Next &gt;
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true
    // Expected format: [{ key: 'id', label: 'ID', sortable: true, span: 1, headerClass: '', cellClass: '', format: (value, row) => value, sortValue: (row) => value }]
  },
  gridCols: {
    type: Number,
    default: 12 // Total columns in grid
  },
  hideHeader: {
    type: Boolean,
    default: false
  },
  headerClass: {
    type: String,
    default: 'bg-white border-b border-gray-200 text-gray-400'
  },
  rowClass: {
    type: [String, Function],
    default: 'bg-white border-b border-gray-100 hover:bg-gray-50'
  },
  paginationButtonClass: {
    type: String,
    default: 'border-gray-200 hover:bg-gray-50'
  },
  paginationActiveClass: {
    type: String,
    default: 'bg-cabinet-blue text-white border-cabinet-blue'
  },
  emptyText: {
    type: String,
    default: 'No data'
  },
  defaultPerPage: {
    type: Number,
    default: 10
  },
  defaultSort: {
    type: String,
    default: ''
  },
  defaultSortOrder: {
    type: String,
    default: 'asc',
    validator: (value) => ['asc', 'desc'].includes(value)
  }
})

// Pagination state
const currentPage = ref(1)
const perPage = ref(props.defaultPerPage)

// Sorting state
const sortKey = ref(props.defaultSort)
const sortOrder = ref(props.defaultSortOrder)

// Computed grid style
const gridStyle = computed(() => {
  return {
    gridTemplateColumns: `repeat(${props.gridCols}, minmax(0, 1fr))`
  }
})

// Sorted data
const sortedData = computed(() => {
  if (!sortKey.value) return props.data

  return [...props.data].sort((a, b) => {
    const column = props.columns.find(col => col.key === sortKey.value)

    // Use custom sortValue function if provided
    let aVal, bVal
    if (column?.sortValue) {
      aVal = column.sortValue(a)
      bVal = column.sortValue(b)
    } else {
      aVal = getNestedValue(a, sortKey.value)
      bVal = getNestedValue(b, sortKey.value)
    }

    if (aVal === bVal) return 0

    const comparison = aVal > bVal ? 1 : -1
    return sortOrder.value === 'asc' ? comparison : -comparison
  })
})

// Pagination computed
const totalPages = computed(() => Math.ceil(sortedData.value.length / perPage.value))
const startIndex = computed(() => (currentPage.value - 1) * perPage.value)
const endIndex = computed(() => Math.min(startIndex.value + perPage.value, sortedData.value.length))

const paginatedData = computed(() => {
  return sortedData.value.slice(startIndex.value, endIndex.value)
})

// Visible page numbers with ellipsis
const visiblePages = computed(() => {
  const total = totalPages.value
  const current = currentPage.value
  const delta = 2 // Pages to show around current page

  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }

  const pages = []

  if (current <= 3) {
    pages.push(1, 2, 3, 4, '...', total)
  } else if (current >= total - 2) {
    pages.push(1, '...', total - 3, total - 2, total - 1, total)
  } else {
    pages.push(1, '...', current - 1, current, current + 1, '...', total)
  }

  return pages
})

// Methods
function sort(key) {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortOrder.value = 'asc'
  }
  currentPage.value = 1 // Reset to first page when sorting
}

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

function getNestedValue(obj, path) {
  return path.split('.').reduce((value, key) => value?.[key], obj)
}

function getRowClass(row) {
  if (typeof props.rowClass === 'function') {
    return props.rowClass(row)
  }
  return props.rowClass
}

// Watch perPage changes
watch(perPage, () => {
  currentPage.value = 1
})
</script>
