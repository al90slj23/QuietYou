<template>
  <component :is="currentComponent" />
</template>

<script setup>
import { computed } from 'vue'
import { useMediaQuery } from '@/composables/useMediaQuery'
import MobileView from './index.mobile.vue'
import PcView from './index.pc.vue'

const { isMobile } = useMediaQuery()

const currentComponent = computed(() => isMobile.value ? MobileView : PcView)
</script>
