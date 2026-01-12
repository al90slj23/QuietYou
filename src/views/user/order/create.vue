<template>
  <div class="order-create-page">
    <t-navbar title="预约服务" left-arrow @left-click="router.back()" />
    
    <!-- 选择服务 -->
    <div class="card">
      <div class="card-title">选择服务</div>
      <t-cell-group>
        <t-cell
          v-for="service in services"
          :key="service.id"
          :title="service.name"
          :description="`${service.duration}分钟`"
          :note="`¥${service.price}`"
          :class="{ selected: selectedService?.id === service.id }"
          @click="selectService(service)"
        >
          <template #left-icon>
            <t-radio :checked="selectedService?.id === service.id" />
          </template>
        </t-cell>
      </t-cell-group>
    </div>

    <!-- 选择技师 -->
    <div class="card">
      <div class="card-title">选择技师</div>
      <div class="tech-list">
        <div
          v-for="tech in technicians"
          :key="tech.id"
          class="tech-item"
          :class="{ selected: selectedTech?.id === tech.id }"
          @click="selectTech(tech)"
        >
          <div class="tech-avatar">
            <t-icon name="user" size="24px" />
          </div>
          <div class="tech-name">{{ tech.name }}</div>
          <div class="tech-rating">
            <t-icon name="star-filled" size="10px" /> {{ tech.rating }}
          </div>
        </div>
      </div>
    </div>

    <!-- 选择时间 -->
    <div class="card">
      <div class="card-title">选择时间</div>
      <t-cell title="预约日期" :note="selectedDate || '请选择'" arrow @click="showDatePicker = true" />
      <t-cell title="预约时段" :note="selectedTime || '请选择'" arrow @click="showTimePicker = true" />
    </div>

    <!-- 服务地址 -->
    <div class="card">
      <div class="card-title">服务地址</div>
      <t-cell
        :title="address.name ? `${address.name} ${address.phone}` : '请选择地址'"
        :description="address.detail"
        arrow
        @click="selectAddress"
      />
    </div>

    <!-- 备注 -->
    <div class="card">
      <div class="card-title">备注</div>
      <t-textarea v-model="remark" placeholder="请输入备注信息（选填）" :maxlength="200" />
    </div>

    <!-- 底部 -->
    <div class="bottom-bar">
      <div class="price-info">
        <span class="price-label">合计</span>
        <span class="price-value">¥{{ totalPrice }}</span>
      </div>
      <t-button theme="primary" size="large" :disabled="!canSubmit" @click="submitOrder">
        提交订单
      </t-button>
    </div>

    <!-- 日期选择器 -->
    <t-date-time-picker
      v-model="showDatePicker"
      title="选择日期"
      mode="date"
      :value="dateValue"
      @confirm="onDateConfirm"
    />

    <!-- 时间选择器 -->
    <t-picker
      v-model="showTimePicker"
      title="选择时段"
      :columns="timeColumns"
      @confirm="onTimeConfirm"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Toast } from 'tdesign-mobile-vue'

const router = useRouter()
const route = useRoute()

const services = ref([
  { id: 1, name: '全身中式推拿', duration: 60, price: 298 },
  { id: 2, name: '肩颈深度调理', duration: 45, price: 198 },
  { id: 3, name: '背部推拿放松', duration: 45, price: 218 }
])

const technicians = ref([
  { id: 1, name: '张师傅', rating: 4.9 },
  { id: 2, name: '李师傅', rating: 4.8 },
  { id: 3, name: '王师傅', rating: 4.9 }
])

const selectedService = ref(null)
const selectedTech = ref(null)
const selectedDate = ref('')
const selectedTime = ref('')
const dateValue = ref(new Date())
const remark = ref('')

const address = ref({
  name: '张先生',
  phone: '138****8888',
  detail: '上海市浦东新区陆家嘴环路1000号 恒生银行大厦 1801室'
})

const showDatePicker = ref(false)
const showTimePicker = ref(false)

const timeColumns = ref([
  { label: '09:00', value: '09:00' },
  { label: '10:00', value: '10:00' },
  { label: '11:00', value: '11:00' },
  { label: '14:00', value: '14:00' },
  { label: '15:00', value: '15:00' },
  { label: '16:00', value: '16:00' },
  { label: '17:00', value: '17:00' },
  { label: '18:00', value: '18:00' },
  { label: '19:00', value: '19:00' },
  { label: '20:00', value: '20:00' }
])

const totalPrice = computed(() => {
  return selectedService.value?.price || 0
})

const canSubmit = computed(() => {
  return selectedService.value && selectedTech.value && selectedDate.value && selectedTime.value && address.value.name
})

const selectService = (service) => {
  selectedService.value = service
}

const selectTech = (tech) => {
  selectedTech.value = tech
}

const onDateConfirm = (val) => {
  const date = new Date(val)
  selectedDate.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`
  showDatePicker.value = false
}

const onTimeConfirm = (val) => {
  selectedTime.value = val.value[0]
  showTimePicker.value = false
}

const selectAddress = () => {
  Toast.info('地址选择功能开发中')
}

const submitOrder = () => {
  if (!canSubmit.value) {
    Toast.info('请完善订单信息')
    return
  }
  
  Toast.loading('提交中...')
  setTimeout(() => {
    Toast.success('订单提交成功')
    router.push('/user/order/pay/1')
  }, 1000)
}

// 初始化
if (route.query.serviceId) {
  const service = services.value.find(s => s.id === Number(route.query.serviceId))
  if (service) selectedService.value = service
}

if (route.query.techId) {
  const tech = technicians.value.find(t => t.id === Number(route.query.techId))
  if (tech) selectedTech.value = tech
}
</script>

<style lang="scss" scoped>
.order-create-page {
  background: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 80px;
}

.tech-list {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 4px 0;
  
  &::-webkit-scrollbar { display: none; }
}

.tech-item {
  flex-shrink: 0;
  width: 80px;
  padding: 12px 8px;
  background: #f5f5f5;
  border-radius: 8px;
  text-align: center;
  border: 2px solid transparent;
  
  &.selected {
    border-color: #07c160;
    background: #e8f8ef;
  }
}

.tech-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  margin: 0 auto 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tech-name {
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 4px;
}

.tech-rating {
  font-size: 11px;
  color: #f59e0b;
}

:deep(.t-cell.selected) {
  background: #e8f8ef;
}

.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  align-items: center;
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e5e5e5;
}

.price-info {
  flex: 1;
}

.price-label {
  font-size: 12px;
  color: #999;
}

.price-value {
  font-size: 20px;
  font-weight: 600;
  color: #ef4444;
}
</style>
