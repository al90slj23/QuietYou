<template>
  <MobileOnlyTip 
    title="技师端"
    description="技师端仅支持手机访问，请使用手机扫码或在手机浏览器中打开"
    :features="features"
  />
</template>

<script setup>
import MobileOnlyTip from '@/components/common/MobileOnlyTip.vue'
import { WalletIcon, TimeIcon, UserIcon } from 'tdesign-icons-vue-next'

const features = [
  { icon: WalletIcon, text: '收入管理' },
  { icon: TimeIcon, text: '接单便捷' },
  { icon: UserIcon, text: '客户管理' }
]
</script>
