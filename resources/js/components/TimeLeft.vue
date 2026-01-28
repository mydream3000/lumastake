a<template>
  <span :class="badgeClass">
<img :src="'/img/dashboard-img/time_clock.png'" />
    {{ displayText }}
  </span>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  seconds: {
    type: Number,
    required: true
  }
})

const currentDiff = ref(0)
const initialSeconds = ref(0)
const startTime = ref(0)
let intervalId = null

const isPast = computed(() => currentDiff.value < 0)

const badgeClass = computed(() => {
  const baseClass = 'px-3 py-1 rounded-full text-sm font-medium inline-flex items-center'
  if (isPast.value) {
    return `${baseClass} bg-cabinet-green/20 text-black`
  }
  return `${baseClass} bg-cabinet-orange/20 text-black`
})

const displayText = computed(() => {
  const absDiff = Math.abs(currentDiff.value)

  const days = Math.floor(absDiff / 86400)
  const hours = Math.floor((absDiff % 86400) / 3600)
  const minutes = Math.floor((absDiff % 3600) / 60)

  // Если время истекло (в прошлом)
  if (isPast.value) {
    // Показываем сколько времени прошло с момента окончания
    if (absDiff >= 86400) {
      return `${days}D ago`
    } else if (absDiff >= 3600) {
      return `${hours}H ago`
    } else if (absDiff >= 60) {
      return `${minutes}M ago`
    } else {
      return 'Just now'
    }
  }

  // Если время еще не истекло - показываем оставшееся время
  if (absDiff >= 86400) {
    return `${days}D ${hours}H left`
  } else if (absDiff >= 3600) {
    return `${hours}H ${minutes}M left`
  } else if (absDiff >= 60) {
    return `${minutes}M left`
  } else {
    return '< 1M left'
  }
})

function updateTimer() {
  // Вычисляем сколько секунд прошло с момента монтирования
  const elapsed = Math.floor((Date.now() - startTime.value) / 1000)
  // Вычитаем из исходных секунд
  currentDiff.value = initialSeconds.value - elapsed
}

onMounted(() => {
  // Запоминаем исходное значение секунд с сервера
  initialSeconds.value = props.seconds
  currentDiff.value = props.seconds
  startTime.value = Date.now()

  intervalId = setInterval(updateTimer, 1000)
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>
