<template>
  <div class="withdraw-page">
    <!-- 可提现金额 -->
    <div class="balance-card">
      <div class="label">可提现余额（元）</div>
      <div class="amount">{{ wallet.available }}</div>
    </div>

    <!-- 提现表单 -->
    <div class="form-section">
      <div class="form-item">
        <label>提现金额</label>
        <div class="input-wrapper">
          <span class="prefix">¥</span>
          <input 
            type="number" 
            v-model="withdrawAmount" 
            placeholder="请输入提现金额"
            :max="wallet.availableNum"
          />
          <span class="all-btn" @click="withdrawAll">全部提现</span>
        </div>
      </div>

      <div class="form-item">
        <label>提现方式</label>
        <div class="method-list">
          <div 
            class="method-item" 
            :class="{ active: method === 'wechat' }"
            @click="method = 'wechat'"
          >
            <WechatIcon size="24px" />
            <span>微信零钱</span>
            <CheckCircleFilledIcon v-if="method === 'wechat'" class="check" />
          </div>
          <div 
            class="method-item" 
            :class="{ active: method === 'alipay' }"
            @click="method = 'alipay'"
          >
            <WalletIcon size="24px" />
            <span>支付宝</span>
            <CheckCircleFilledIcon v-if="method === 'alipay'" class="check" />
          </div>
        </div>
      </div>

      <div class="tips">
        <InfoCircleIcon size="14px" />
        <span>提现将在1-3个工作日内到账，单笔最低10元，最高5000元</span>
      </div>
    </div>

    <!-- 提交按钮 -->
    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="submitWithdraw" :disabled="!canSubmit">
        确认提现
      </t-button>
    </div>

    <!-- 提现记录 -->
    <div class="section">
      <div class="section-header">
        <span class="title">提现记录</span>
      </div>
      <div class="record-list">
        <div class="record-item" v-for="item in records" :key="item.id">
          <div class="info">
            <div class="method">{{ item.methodText }}</div>
            <div class="time">{{ item.time }}</div>
          </div>
          <div class="right">
            <div class="amount">-{{ item.amount }}</div>
            <div class="status" :class="item.statusClass">{{ item.statusText }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'
import { WalletIcon, CheckCircleFilledIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

// 微信图标用 Wallet 代替
const WechatIcon = WalletIcon

const router = useRouter()

const wallet = ref({
  available: '1,280.00',
  availableNum: 1280
})

const withdrawAmount = ref('')
const method = ref('wechat')

const canSubmit = computed(() => {
  const amount = parseFloat(withdrawAmount.value)
  return amount >= 10 && amount <= wallet.value.availableNum
})

const withdrawAll = () => {
  withdrawAmount.value = wallet.value.availableNum
}

const submitWithdraw = () => {
  Toast({ message: '提现申请已提交', theme: 'success' })
  setTimeout(() => router.back(), 1500)
}

const records = ref([
  { id: 1, methodText: '微信零钱', time: '2026-01-12 18:00', amount: '500.00', statusText: '已到账', statusClass: 'success' },
  { id: 2, methodText: '微信零钱', time: '2026-01-05 10:30', amount: '800.00', statusText: '已到账', statusClass: 'success' },
  { id: 3, methodText: '支付宝', time: '2025-12-28 14:20', amount: '600.00', statusText: '已到账', statusClass: 'success' }
])
</script>

<style lang="scss" scoped>
$primary: #07c160;

.withdraw-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 100px;
}

.balance-card {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 30px 20px;
  text-align: center;
  color: #fff;
  
  .label { font-size: 14px; opacity: 0.9; margin-bottom: 8px; }
  .amount { font-size: 36px; font-weight: 700; }
}

.form-section {
  background: #fff;
  margin: 15px;
  border-radius: 12px;
  padding: 20px;
}

.form-item {
  margin-bottom: 20px;
  
  label {
    display: block;
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
  }
}

.input-wrapper {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 10px;
  
  .prefix {
    font-size: 24px;
    font-weight: 600;
    color: #1a1a1a;
    margin-right: 8px;
  }
  
  input {
    flex: 1;
    border: none;
    font-size: 24px;
    font-weight: 600;
    outline: none;
    
    &::placeholder { color: #ccc; font-weight: 400; }
  }
  
  .all-btn {
    font-size: 14px;
    color: $primary;
    cursor: pointer;
  }
}

.method-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.method-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px;
  background: #f9f9f9;
  border-radius: 10px;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.2s;
  
  &.active {
    border-color: $primary;
    background: #f0fff4;
  }
  
  span { flex: 1; font-size: 15px; color: #1a1a1a; }
  .check { color: $primary; }
}

.tips {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  font-size: 12px;
  color: #999;
  margin-top: 15px;
}

.submit-area {
  padding: 0 15px;
  margin-top: 20px;
}

.section {
  background: #fff;
  margin: 15px;
  border-radius: 12px;
  padding: 15px;
}

.section-header {
  margin-bottom: 15px;
  .title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
}

.record-list {
  display: flex;
  flex-direction: column;
}

.record-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  
  &:last-child { border-bottom: none; }
  
  .info {
    .method { font-size: 15px; color: #1a1a1a; margin-bottom: 4px; }
    .time { font-size: 12px; color: #999; }
  }
  
  .right {
    text-align: right;
    .amount { font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 4px; }
    .status {
      font-size: 12px;
      &.success { color: $primary; }
      &.pending { color: #f57c00; }
    }
  }
}
</style>
