<template>
  <div class="income-page">
    <div class="income-header">
      <div class="balance">
        <div class="label">可提现余额（元）</div>
        <div class="amount">{{ wallet.available }}</div>
        <t-button size="small" theme="primary" @click="goWithdraw">申请提现</t-button>
      </div>
      <div class="stats">
        <div class="stat-item">
          <div class="value">{{ wallet.pending }}</div>
          <div class="label">待结算</div>
        </div>
        <div class="stat-item">
          <div class="value">{{ wallet.total }}</div>
          <div class="label">累计收入</div>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-header">
        <span class="title">收入明细</span>
      </div>
      <div class="income-list">
        <div class="income-item" v-for="item in incomeList" :key="item.id">
          <div class="info">
            <div class="title">{{ item.title }}</div>
            <div class="time">{{ item.time }}</div>
          </div>
          <div class="amount" :class="{ positive: item.amount > 0 }">
            {{ item.amount > 0 ? '+' : '' }}{{ item.amount }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton } from 'tdesign-mobile-vue'

const router = useRouter()

const wallet = ref({
  available: '8,580.00',
  pending: '2,180.00',
  total: '68,920.00'
})

const incomeList = ref([
  { id: 1, title: '订单收入 - 全身推拿', time: '2026-01-13 15:30', amount: 178 },
  { id: 2, title: '订单收入 - 肩颈按摩', time: '2026-01-13 11:20', amount: 138 },
  { id: 3, title: '提现到银行卡', time: '2026-01-12 18:00', amount: -5000 },
  { id: 4, title: '借调收入 - 张师傅', time: '2026-01-12 14:00', amount: 50 }
])

const goWithdraw = () => router.push('/merchant/income/withdraw')
</script>

<style lang="scss" scoped>
$primary: #07c160;

.income-page { min-height: 100vh; background: #f5f5f5; }

.income-header {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 30px 20px;
  color: #fff;
}

.balance {
  text-align: center;
  margin-bottom: 25px;
  .label { font-size: 14px; opacity: 0.9; margin-bottom: 8px; }
  .amount { font-size: 40px; font-weight: 700; margin-bottom: 15px; }
}

.stats {
  display: flex;
  justify-content: center;
  gap: 60px;
  .stat-item {
    text-align: center;
    .value { font-size: 20px; font-weight: 600; }
    .label { font-size: 12px; opacity: 0.8; margin-top: 4px; }
  }
}

.section { background: #fff; margin: 15px; border-radius: 12px; padding: 15px; }
.section-header { margin-bottom: 15px; .title { font-size: 16px; font-weight: 600; color: #1a1a1a; } }

.income-list { display: flex; flex-direction: column; }

.income-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  .info {
    .title { font-size: 15px; color: #1a1a1a; margin-bottom: 4px; }
    .time { font-size: 12px; color: #999; }
  }
  
  .amount {
    font-size: 18px;
    font-weight: 600;
    color: #999;
    &.positive { color: $primary; }
  }
}
</style>
