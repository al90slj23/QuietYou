<template>
  <MobileOnlyTip 
    title="商户端"
    description="商户端仅支持手机访问，请使用手机扫码或在手机浏览器中打开"
    :features="features"
  />
</template>

<script setup>
import MobileOnlyTip from '@/components/common/MobileOnlyTip.vue'
import { ShopIcon, ChartIcon, UserIcon } from 'tdesign-icons-vue-next'

const features = [
  { icon: ShopIcon, text: '店铺管理' },
  { icon: ChartIcon, text: '数据统计' },
  { icon: UserIcon, text: '技师管理' }
]
</script>
