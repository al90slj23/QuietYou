<template>
  <div class="setting-page">
    <div class="section">
      <div class="section-title">营业设置</div>
      <div class="setting-item">
        <div class="info">
          <div class="label">营业状态</div>
          <div class="desc">关闭后顾客无法下单</div>
        </div>
        <t-switch v-model="settings.isOpen" />
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">接受上门服务</div>
          <div class="desc">技师可上门为顾客服务</div>
        </div>
        <t-switch v-model="settings.acceptHome" />
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">接受到店服务</div>
          <div class="desc">顾客可到店消费</div>
        </div>
        <t-switch v-model="settings.acceptShop" />
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">接受技师借调</div>
          <div class="desc">其他店铺可借调您的技师</div>
        </div>
        <t-switch v-model="settings.acceptBorrow" />
      </div>
    </div>

    <div class="section">
      <div class="section-title">结算设置</div>
      <div class="setting-item" @click="showSettlePicker = true">
        <div class="info">
          <div class="label">技师结算周期</div>
          <div class="desc">设置给技师结算工资的周期</div>
        </div>
        <div class="value">
          <span>{{ settings.settleCycle }}</span>
          <ChevronRightIcon size="20px" />
        </div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">通知设置</div>
      <div class="setting-item">
        <div class="info">
          <div class="label">新订单通知</div>
          <div class="desc">有新订单时推送通知</div>
        </div>
        <t-switch v-model="settings.orderNotify" />
      </div>
      <div class="setting-item">
        <div class="info">
          <div class="label">技师申请通知</div>
          <div class="desc">有技师申请加入时通知</div>
        </div>
        <t-switch v-model="settings.applyNotify" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Switch as TSwitch } from 'tdesign-mobile-vue'
import { ChevronRightIcon } from 'tdesign-icons-vue-next'

const showSettlePicker = ref(false)

const settings = ref({
  isOpen: true,
  acceptHome: true,
  acceptShop: true,
  acceptBorrow: true,
  settleCycle: 'T+3结算',
  orderNotify: true,
  applyNotify: true
})
</script>

<style lang="scss" scoped>
$primary: #07c160;

.setting-page { min-height: 100vh; background: #f5f5f5; padding: 15px; }

.section { background: #fff; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
.section-title { font-size: 14px; color: #999; margin-bottom: 15px; }

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
</style>
