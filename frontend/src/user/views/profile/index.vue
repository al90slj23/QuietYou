<template>
  <div class="profile-page">
    <!-- 用户信息 -->
    <div class="user-header">
      <div class="user-avatar">
        <t-icon name="user" size="40px" />
      </div>
      <div class="user-info">
        <div class="user-name">{{ user.name || '未登录' }}</div>
        <div class="user-phone">{{ user.phone || '点击登录' }}</div>
      </div>
      <t-icon name="chevron-right" size="20px" color="#999" />
    </div>

    <!-- 订单入口 -->
    <div class="card">
      <div class="card-title flex-between">
        <span>我的订单</span>
        <span class="more" @click="router.push('/order/list')">全部订单 &gt;</span>
      </div>
      <t-grid :column="4" :border="false">
        <t-grid-item text="待支付" @click="router.push('/order/list?status=pending')">
          <template #image>
            <div class="order-icon">
              <t-icon name="wallet" size="24px" />
              <span class="badge" v-if="orderCounts.pending">{{ orderCounts.pending }}</span>
            </div>
          </template>
        </t-grid-item>
        <t-grid-item text="待服务" @click="router.push('/order/list?status=confirmed')">
          <template #image>
            <div class="order-icon">
              <t-icon name="time" size="24px" />
              <span class="badge" v-if="orderCounts.confirmed">{{ orderCounts.confirmed }}</span>
            </div>
          </template>
        </t-grid-item>
        <t-grid-item text="待评价" @click="router.push('/order/list?status=completed')">
          <template #image>
            <div class="order-icon">
              <t-icon name="chat" size="24px" />
              <span class="badge" v-if="orderCounts.toReview">{{ orderCounts.toReview }}</span>
            </div>
          </template>
        </t-grid-item>
        <t-grid-item text="退款/售后" @click="router.push('/order/list?status=refund')">
          <template #image>
            <t-icon name="service" size="24px" />
          </template>
        </t-grid-item>
      </t-grid>
    </div>

    <!-- 功能列表 -->
    <div class="card">
      <t-cell-group>
        <t-cell title="我的地址" arrow @click="Toast.info('功能开发中')">
          <template #left-icon>
            <t-icon name="location" size="20px" />
          </template>
        </t-cell>
        <t-cell title="我的收藏" arrow @click="Toast.info('功能开发中')">
          <template #left-icon>
            <t-icon name="heart" size="20px" />
          </template>
        </t-cell>
        <t-cell title="优惠券" arrow @click="Toast.info('功能开发中')">
          <template #left-icon>
            <t-icon name="gift" size="20px" />
          </template>
        </t-cell>
        <t-cell title="帮助中心" arrow @click="Toast.info('功能开发中')">
          <template #left-icon>
            <t-icon name="help-circle" size="20px" />
          </template>
        </t-cell>
        <t-cell title="设置" arrow @click="Toast.info('功能开发中')">
          <template #left-icon>
            <t-icon name="setting" size="20px" />
          </template>
        </t-cell>
      </t-cell-group>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Toast } from 'tdesign-mobile-vue'

const router = useRouter()

const user = ref({
  name: '张先生',
  phone: '138****8888'
})

const orderCounts = ref({
  pending: 1,
  confirmed: 1,
  toReview: 2
})
</script>

<style lang="scss" scoped>
.profile-page {
  background: #f5f5f5;
  min-height: 100vh;
}

.user-header {
  display: flex;
  align-items: center;
  padding: 20px 16px;
  background: linear-gradient(135deg, #07c160 0%, #10b981 100%);
  color: #fff;
}

.user-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
}

.user-info {
  flex: 1;
}

.user-name {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 4px;
}

.user-phone {
  font-size: 14px;
  opacity: 0.9;
}

.flex-between {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.more {
  font-size: 12px;
  color: #999;
  font-weight: normal;
}

.order-icon {
  position: relative;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.badge {
  position: absolute;
  top: -4px;
  right: -4px;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  font-size: 10px;
  line-height: 16px;
  text-align: center;
  color: #fff;
  background: #ef4444;
  border-radius: 8px;
}
</style>
