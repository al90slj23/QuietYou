<template>
  <div class="income-page">
    <!-- 收入概览 -->
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

    <!-- 收入明细 -->
    <div class="section">
      <div class="section-header">
        <span class="title">收入明细</span>
        <span class="filter" @click="showMonthPicker = true">{{ currentMonth }} <ChevronDownIcon size="14px" /></span>
      </div>
      
      <!-- 月份选择器 -->
      <t-action-sheet
        :visible="showMonthPicker"
        :items="monthOptions"
        @selected="onMonthChange"
        @close="showMonthPicker = false"
      />
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
      <div class="empty" v-if="!incomeList.length">
        <WalletIcon size="48px" />
        <p>暂无收入记录</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton, ActionSheet as TActionSheet } from 'tdesign-mobile-vue'
import { ChevronDownIcon, WalletIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const wallet = ref({
  available: '1,280.00',
  pending: '580.00',
  total: '12,680.00'
})

const currentMonth = ref('2026年1月')
const showMonthPicker = ref(false)
const monthOptions = [
  { label: '2026年1月', value: '2026-01' },
  { label: '2025年12月', value: '2025-12' },
  { label: '2025年11月', value: '2025-11' }
]

const incomeList = ref([
  { id: 1, title: '全身推拿服务收入', time: '2026-01-13 15:30', amount: 158 },
  { id: 2, title: '肩颈按摩服务收入', time: '2026-01-13 11:20', amount: 128 },
  { id: 3, title: '提现到微信', time: '2026-01-12 18:00', amount: -500 },
  { id: 4, title: '足底按摩服务收入', time: '2026-01-12 14:00', amount: 168 }
])

const onMonthChange = (item) => {
  currentMonth.value = item.label
  showMonthPicker.value = false
}

const goWithdraw = () => {
  router.push('/tech/income/withdraw')
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.income-page {
  min-height: 100vh;
  background: #f5f5f5;
}

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

.section {
  background: #fff;
  margin: 15px;
  border-radius: 12px;
  padding: 15px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  
  .title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
  .filter {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    color: #666;
  }
}

.income-list {
  display: flex;
  flex-direction: column;
}

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

.empty {
  text-align: center;
  padding: 40px 0;
  color: #999;
  p { margin-top: 10px; font-size: 14px; }
}
</style>
