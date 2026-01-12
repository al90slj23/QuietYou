<template>
  <component :is="currentComponent" />
</template>

<script setup>
import { computed } from 'vue'
import { useMediaQuery } from '@/composables/useMediaQuery'
import PcView from './index.pc.vue'
import MobileView from './index.mobile.vue'

const { isMobile } = useMediaQuery()
const currentComponent = computed(() => isMobile.value ? MobileView : PcView)
</script>
