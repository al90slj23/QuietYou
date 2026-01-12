<template>
  <div class="order-pay-page">
    <t-navbar title="支付订单" left-arrow @left-click="router.back()" />
    
    <div class="pay-amount">
      <div class="amount-label">支付金额</div>
      <div class="amount-value">¥{{ order.amount }}</div>
    </div>

    <div class="card">
      <div class="card-title">选择支付方式</div>
      <t-cell-group>
        <t-cell title="微信支付" @click="payMethod = 'wechat'">
          <template #left-icon>
            <t-icon name="logo-wechat" size="24px" color="#07c160" />
          </template>
          <template #right-icon>
            <t-radio :checked="payMethod === 'wechat'" />
          </template>
        </t-cell>
        <t-cell title="支付宝" @click="payMethod = 'alipay'">
          <template #left-icon>
            <t-icon name="wallet" size="24px" color="#1677ff" />
          </template>
          <template #right-icon>
            <t-radio :checked="payMethod === 'alipay'" />
          </template>
        </t-cell>
      </t-cell-group>
    </div>

    <div class="bottom-bar">
      <t-button theme="primary" block size="large" @click="pay">
        确认支付 ¥{{ order.amount }}
      </t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Toast, Dialog } from 'tdesign-mobile-vue'

const router = useRouter()
const route = useRoute()

const order = ref({
  id: route.params.id,
  amount: 298
})

const payMethod = ref('wechat')

const pay = () => {
  Toast.loading('支付中...')
  setTimeout(() => {
    Dialog.confirm({
      title: '支付成功',
      content: '您的订单已支付成功，技师将准时上门服务',
      confirmBtn: '查看订单',
      cancelBtn: '返回首页'
    }).then(() => {
      router.push(`/order/detail/${order.value.id}`)
    }).catch(() => {
      router.push('/home')
    })
  }, 1500)
}
</script>

<style lang="scss" scoped>
.order-pay-page {
  background: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 80px;
}

.pay-amount {
  background: #fff;
  padding: 40px 20px;
  text-align: center;
}

.amount-label {
  font-size: 14px;
  color: #666;
  margin-bottom: 8px;
}

.amount-value {
  font-size: 36px;
  font-weight: 600;
  color: #ef4444;
}

.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e5e5e5;
}
</style>
