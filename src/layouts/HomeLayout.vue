<template>
  <component :is="layoutComponent" />
</template>

<script setup>
import { computed } from 'vue'
import { useMobileDetect } from '../composables/useMediaQuery'
import HomeLayoutPc from './HomeLayout.pc.vue'
import HomeLayoutMobile from './HomeLayout.mobile.vue'

const { isMobile } = useMobileDetect()

const layoutComponent = computed(() => isMobile.value ? HomeLayoutMobile : HomeLayoutPc)
</script>
