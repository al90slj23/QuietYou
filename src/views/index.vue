<template>
  <component :is="currentView" />
</template>

<script setup>
import { computed } from 'vue'
import { useMediaQuery } from '@/composables/useMediaQuery'
import PcView from './index.pc.vue'
import MobileView from './index.mobile.vue'

const isMobile = useMediaQuery('(max-width: 768px)')
const currentView = computed(() => isMobile.value ? MobileView : PcView)
</script>
