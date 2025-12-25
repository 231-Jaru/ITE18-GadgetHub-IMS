<template>
  <span>{{ displayValue }}</span>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps<{
  value: number | null | undefined
  duration?: number
  decimals?: number
}>()

const displayValue = ref('0')
let frameId: number | null = null
let startTime: number | null = null

const getNumber = (val: number | null | undefined): number => {
  if (typeof val !== 'number' || isNaN(val)) return 0
  return val
}

const formatNumber = (val: number, decimals: number): string => {
  return val.toLocaleString('en-US', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  })
}

const runAnimation = (from: number, to: number) => {
  if (frameId !== null && typeof cancelAnimationFrame !== 'undefined') {
    cancelAnimationFrame(frameId)
    frameId = null
  }

  const duration = props.duration ?? 800
  const decimals = props.decimals ?? 0
  startTime = null

  // If we're on the server or animation APIs aren't available, just set the final value
  if (typeof window === 'undefined' || typeof requestAnimationFrame === 'undefined') {
    displayValue.value = formatNumber(to, decimals)
    return
  }

  const step = (timestamp: number) => {
    if (startTime === null) startTime = timestamp
    const progress = Math.min((timestamp - startTime) / duration, 1)
    const current = from + (to - from) * progress
    displayValue.value = formatNumber(current, decimals)

    if (progress < 1) {
      frameId = requestAnimationFrame(step)
    }
  }

  frameId = requestAnimationFrame(step)
}

onMounted(() => {
  const target = getNumber(props.value)
  runAnimation(0, target)
})

watch(
  () => props.value,
  (newVal, oldVal) => {
    const from = getNumber(oldVal as number | null | undefined)
    const to = getNumber(newVal)
    runAnimation(from, to)
  }
)

onBeforeUnmount(() => {
  if (frameId !== null && typeof cancelAnimationFrame !== 'undefined') {
    cancelAnimationFrame(frameId)
  }
})
</script>

<style scoped>
span {
  display: inline-block;
}
</style>

