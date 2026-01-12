<template>
  <div class="accept-setting-page">
    <!-- 接单状态 -->
    <div class="section">
      <div class="section-title">接单状态</div>
      <div class="setting-item">
        <div class="info">
          <div class="label">开启接单</div>
          <div class="desc">关闭后将不会收到新订单</div>
        </div>
        <t-switch v-model="settings.acceptOrder" />
      </div>
    </div>

    <!-- 服务范围 -->
    <div class="section">
      <div class="section-title">服务范围</div>
      <div class="setting-item" @click="showRangePicker = true">
        <div class="info">
          <div class="label">上门服务范围</div>
          <div class="desc">设置您愿意上门服务的最大距离</div>
        </div>
        <div class="value">
          <span>{{ settings.serviceRange }}公里</span>
          <ChevronRightIcon size="20px" />
        </div>
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">接受到店服务</div>
          <div class="desc">顾客到您所属店铺消费</div>
        </div>
        <t-switch v-model="settings.acceptShopOrder" />
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">接受借调服务</div>
          <div class="desc">其他店铺借调您去服务</div>
        </div>
        <t-switch v-model="settings.acceptBorrowOrder" />
      </div>
    </div>

    <!-- 工作时间 -->
    <div class="section">
      <div class="section-title">工作时间</div>
      <div class="setting-item" @click="showTimePicker = true">
        <div class="info">
          <div class="label">每日工作时段</div>
          <div class="desc">设置您的可接单时间段</div>
        </div>
        <div class="value">
          <span>{{ settings.workTimeStart }} - {{ settings.workTimeEnd }}</span>
          <ChevronRightIcon size="20px" />
        </div>
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">休息日设置</div>
          <div class="desc">选择每周固定休息日</div>
        </div>
        <div class="value">
          <span>{{ restDaysText }}</span>
          <ChevronRightIcon size="20px" />
        </div>
      </div>
    </div>

    <!-- 订单偏好 -->
    <div class="section">
      <div class="section-title">订单偏好</div>
      <div class="setting-item">
        <div class="info">
          <div class="label">自动接单</div>
          <div class="desc">系统自动接受符合条件的订单</div>
        </div>
        <t-switch v-model="settings.autoAccept" />
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">新订单提醒</div>
          <div class="desc">有新订单时推送通知</div>
        </div>
        <t-switch v-model="settings.orderNotify" />
      </div>
    </div>

    <!-- 服务范围选择器 -->
    <t-popup v-model="showRangePicker" placement="bottom">
      <div class="picker-header">
        <span @click="showRangePicker = false">取消</span>
        <span class="title">服务范围</span>
        <span class="confirm" @click="confirmRange">确定</span>
      </div>
      <div class="range-options">
        <div 
          class="range-item" 
          v-for="range in rangeOptions" 
          :key="range"
          :class="{ active: tempRange === range }"
          @click="tempRange = range"
        >
          {{ range }}公里
        </div>
      </div>
    </t-popup>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Switch as TSwitch, Popup as TPopup, Toast } from 'tdesign-mobile-vue'
import { ChevronRightIcon } from 'tdesign-icons-vue-next'

const settings = ref({
  acceptOrder: true,
  serviceRange: 10,
  acceptShopOrder: true,
  acceptBorrowOrder: true,
  workTimeStart: '09:00',
  workTimeEnd: '22:00',
  restDays: [],
  autoAccept: false,
  orderNotify: true
})

const restDaysText = computed(() => {
  if (!settings.value.restDays.length) return '无'
  const days = ['周日', '周一', '周二', '周三', '周四', '周五', '周六']
  return settings.value.restDays.map(d => days[d]).join('、')
})

const showRangePicker = ref(false)
const showTimePicker = ref(false)
const tempRange = ref(10)
const rangeOptions = [3, 5, 10, 15, 20, 30]

const confirmRange = () => {
  settings.value.serviceRange = tempRange.value
  showRangePicker.value = false
  Toast({ message: '设置已保存', theme: 'success' })
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.accept-setting-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 15px;
}

.section {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 15px;
}

.section-title {
  font-size: 14px;
  color: #999;
  margin-bottom: 15px;
}

.setting-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
  
  &:last-child { border-bottom: none; }
  
  .info {
    flex: 1;
    .label { font-size: 15px; color: #1a1a1a; margin-bottom: 4px; }
    .desc { font-size: 12px; color: #999; }
  }
  
  .value {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    color: #666;
  }
}

.picker-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f0f0f0;
  
  span { font-size: 14px; color: #666; cursor: pointer; }
  .title { color: #1a1a1a; font-weight: 500; }
  .confirm { color: $primary; }
}

.range-options {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  padding: 20px;
}

.range-item {
  text-align: center;
  padding: 15px;
  background: #f5f5f5;
  border-radius: 8px;
  font-size: 14px;
  color: #666;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s;
  
  &.active {
    background: #f0fff4;
    border-color: $primary;
    color: $primary;
  }
}
</style>
