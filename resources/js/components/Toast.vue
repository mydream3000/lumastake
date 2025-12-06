<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <transition-group name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="[
          'min-w-80 max-w-md p-4 rounded-lg shadow-lg flex items-start gap-3',
          'transform transition-all duration-300',
          toast.redirect_url ? 'cursor-pointer hover:opacity-90' : '',
          getToastClass(toast.type)
        ]"
        @click="handleToastClick(toast)"
      >
        <div class="flex-shrink-0">
          <component :is="getIcon(toast.type)" class="w-5 h-5" />
        </div>
        <div class="flex-1 pt-0.5">
          <p class="text-sm font-medium text-white">{{ toast.message }}</p>
        </div>
        <button
          @click.stop="removeToast(toast.id)"
          class="flex-shrink-0 text-white hover:opacity-80"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, h } from 'vue'
import axios from 'axios'

const toasts = ref([])
let pollInterval = null
let toastIdCounter = 0

const SuccessIcon = {
  render() {
    return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 13l4 4L19 7' })
    ])
  }
}

const ErrorIcon = {
  render() {
    return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M6 18L18 6M6 6l12 12' })
    ])
  }
}

const InfoIcon = {
  render() {
    return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
    ])
  }
}

const WarningIcon = {
  render() {
    return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
    ])
  }
}

function getIcon(type) {
  const icons = {
    success: SuccessIcon,
    error: ErrorIcon,
    info: InfoIcon,
    warning: WarningIcon
  }
  return icons[type] || InfoIcon
}

function getToastClass(type) {
  const classes = {
    success: 'bg-cabinet-green',
    error: 'bg-cabinet-red',
    info: 'bg-blue-600',
    warning: 'bg-cabinet-orange'
  }
  return classes[type] || 'bg-gray-600'
}

async function fetchToasts() {
  try {
    const response = await axios.get('/dashboard/toasts')
    const newToasts = response.data.data

    newToasts.forEach(toast => {
      // Проверяем, нет ли уже такого toast
      if (!toasts.value.find(t => t.id === toast.id)) {
        toasts.value.push(toast)

        // Автоматически удаляем или редиректим через 5 секунд
        setTimeout(() => {
          if (toast.redirect_url) {
            // Если есть redirect_url - перенаправляем
            window.location.href = toast.redirect_url
          } else {
            // Иначе просто удаляем
            removeToast(toast.id)
          }
        }, 5000)
      }
    })
  } catch (error) {
    console.error('Failed to fetch toasts:', error)
  }
}

function removeToast(id) {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index !== -1) {
    toasts.value.splice(index, 1)
  }
}

function handleToastClick(toast) {
  console.log('Toast clicked:', toast)
  if (toast.redirect_url) {
    console.log('Redirecting to:', toast.redirect_url)
    // Перенаправляем
    window.location.href = toast.redirect_url
  }
}

// Добавляем метод для клиентских toast
function addToast(message, type = 'info') {
  const toast = {
    id: `client-${toastIdCounter++}`,
    message,
    type,
    redirect_url: null
  }

  toasts.value.push(toast)

  // Автоматически удаляем через 5 секунд
  setTimeout(() => {
    removeToast(toast.id)
  }, 5000)
}

// Экспортируем для доступа извне
defineExpose({ addToast })

onMounted(() => {
  // Первая загрузка
  fetchToasts()

  // Запускаем опрос каждые 5 секунд
  pollInterval = setInterval(fetchToasts, 5000)
})

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval)
  }
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
