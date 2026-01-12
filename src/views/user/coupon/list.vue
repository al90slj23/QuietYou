<template>
  <div class="coupon-page">
    <t-tabs v-model="activeTab">
      <t-tab-panel value="available" label="可使用" />
      <t-tab-panel value="used" label="已使用" />
      <t-tab-panel value="expired" label="已过期" />
    </t-tabs>

    <div class="coupon-list">
      <div 
        class="coupon-card" 
        v-for="coupon in filteredCoupons" 
        :key="coupon.id"
        :class="{ disabled: coupon.status !== 'available' }"
      >
        <div class="coupon-left">
          <div class="amount">
            <span class="symbol">¥</span>
            <span class="value">{{ coupon.amount }}</span>
          </div>
          <div class="condition">满{{ coupon.minAmount }}可用</div>
        </div>
        <div class="coupon-right">
          <div class="name">{{ coupon.name }}</div>
          <div class="scope">{{ coupon.scope }}</div>
          <div class="expire">{{ coupon.expireText }}</div>
        </div>
        <div class="coupon-action" v-if="coupon.status === 'available'">
          <t-button size="small" theme="primary" @click="useCoupon(coupon)">去使用</t-button>
        </div>
        <div class="coupon-status" v-else>
          <span v-if="coupon.status === 'used'">已使用</span>
          <span v-else>已过期</span>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!filteredCoupons.length">
      <CouponIcon size="48px" />
      <p>暂无优惠券</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Tabs as TTabs, TabPanel as TTabPanel, Button as TButton } from 'tdesign-mobile-vue'
import { DiscountIcon } from 'tdesign-icons-vue-next'

const CouponIcon = DiscountIcon
const router = useRouter()
const activeTab = ref('available')

const coupons = ref([
  { id: 1, name: '新人专享券', amount: 30, minAmount: 100, scope: '全场通用', expireText: '2026-01-31到期', status: 'available' },
  { id: 2, name: '满减优惠券', amount: 20, minAmount: 150, scope: '推拿服务可用', expireText: '2026-02-15到期', status: 'available' },
  { id: 3, name: '会员专属券', amount: 50, minAmount: 200, scope: '全场通用', expireText: '2026-01-10已使用', status: 'used' },
  { id: 4, name: '限时优惠券', amount: 15, minAmount: 80, scope: '足疗服务可用', expireText: '2025-12-31已过期', status: 'expired' }
])

const filteredCoupons = computed(() => {
  return coupons.value.filter(c => c.status === activeTab.value)
})

const useCoupon = (coupon) => {
  router.push('/user/service/list')
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.coupon-page { min-height: 100vh; background: #f5f5f5; }

.coupon-list { padding: 15px; display: flex; flex-direction: column; gap: 12px; }

.coupon-card {
  background: #fff;
  border-radius: 12px;
  display: flex;
  align-items: center;
  overflow: hidden;
  position: relative;
  
  &.disabled {
    opacity: 0.6;
    .coupon-left { background: #999; }
  }
}

.coupon-left {
  width: 100px;
  padding: 20px 15px;
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  color: #fff;
  text-align: center;
  flex-shrink: 0;
  
  .amount {
    .symbol { font-size: 14px; }
    .value { font-size: 32px; font-weight: 700; }
  }
  .condition { font-size: 11px; opacity: 0.9; margin-top: 4px; }
}

.coupon-right {
  flex: 1;
  padding: 15px;
  
  .name { font-size: 15px; font-weight: 500; color: #1a1a1a; margin-bottom: 6px; }
  .scope { font-size: 12px; color: #666; margin-bottom: 4px; }
  .expire { font-size: 11px; color: #999; }
}

.coupon-action { padding-right: 15px; }

.coupon-status {
  padding-right: 15px;
  font-size: 12px;
  color: #999;
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
