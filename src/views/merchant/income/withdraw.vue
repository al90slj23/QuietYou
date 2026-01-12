<template>
  <div class="withdraw-page">
    <div class="balance-card">
      <div class="label">可提现余额（元）</div>
      <div class="amount">{{ wallet.available }}</div>
    </div>

    <div class="form-section">
      <div class="form-item">
        <label>提现金额</label>
        <div class="input-wrapper">
          <span class="prefix">¥</span>
          <input type="number" v-model="withdrawAmount" placeholder="请输入提现金额" />
          <span class="all-btn" @click="withdrawAll">全部提现</span>
        </div>
      </div>

      <div class="form-item">
        <label>提现方式</label>
        <div class="method-list">
          <div class="method-item" :class="{ active: method === 'bank' }" @click="method = 'bank'">
            <BankIcon size="24px" />
            <span>银行卡</span>
            <CheckCircleFilledIcon v-if="method === 'bank'" class="check" />
          </div>
        </div>
      </div>
    </div>

    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="submitWithdraw">确认提现</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'
import { CheckCircleFilledIcon, WalletIcon } from 'tdesign-icons-vue-next'

const BankIcon = WalletIcon
const router = useRouter()

const wallet = ref({ available: '8,580.00', availableNum: 8580 })
const withdrawAmount = ref('')
const method = ref('bank')

const withdrawAll = () => { withdrawAmount.value = wallet.value.availableNum }

const submitWithdraw = () => {
  Toast({ message: '提现申请已提交', theme: 'success' })
  setTimeout(() => router.back(), 1500)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.withdraw-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 100px; }

.balance-card {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 30px 20px;
  text-align: center;
  color: #fff;
  .label { font-size: 14px; opacity: 0.9; margin-bottom: 8px; }
  .amount { font-size: 36px; font-weight: 700; }
}

.form-section { background: #fff; margin: 15px; border-radius: 12px; padding: 20px; }

.form-item {
  margin-bottom: 20px;
  label { display: block; font-size: 14px; color: #666; margin-bottom: 10px; }
}

.input-wrapper {
  display: flex;
  align-items: center;
  border-bottom: 1px solid #eee;
  padding-bottom: 10px;
  .prefix { font-size: 24px; font-weight: 600; color: #1a1a1a; margin-right: 8px; }
  input { flex: 1; border: none; font-size: 24px; font-weight: 600; outline: none; &::placeholder { color: #ccc; font-weight: 400; } }
  .all-btn { font-size: 14px; color: $primary; cursor: pointer; }
}

.method-list { display: flex; flex-direction: column; gap: 12px; }

.method-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px;
  background: #f9f9f9;
  border-radius: 10px;
  border: 2px solid transparent;
  cursor: pointer;
  &.active { border-color: $primary; background: #f0fff4; }
  span { flex: 1; font-size: 15px; color: #1a1a1a; }
  .check { color: $primary; }
}

.submit-area { padding: 0 15px; margin-top: 20px; }
</style>
