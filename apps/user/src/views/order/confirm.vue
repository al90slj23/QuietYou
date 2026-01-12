<template>
  <div class="order-confirm-page">
    <t-navbar title="确认订单" left-arrow @left-click="router.back()" />
    <t-empty description="确认订单页面" />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
</script>
