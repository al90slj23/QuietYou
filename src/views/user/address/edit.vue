<template>
  <div class="edit-address-page">
    <div class="form-section">
      <div class="form-item">
        <label>联系人</label>
        <input v-model="form.name" placeholder="请输入联系人姓名" />
      </div>
      <div class="form-item">
        <label>手机号</label>
        <input v-model="form.phone" placeholder="请输入手机号" type="tel" />
      </div>
      <div class="form-item">
        <label>所在地区</label>
        <div class="picker-trigger" @click="showAreaPicker = true">
          <span :class="{ placeholder: !form.area }">{{ form.area || '请选择省市区' }}</span>
          <ChevronRightIcon size="16px" />
        </div>
      </div>
      <div class="form-item">
        <label>详细地址</label>
        <textarea v-model="form.detail" placeholder="请输入详细地址，如楼栋、门牌号等" rows="3"></textarea>
      </div>
    </div>

    <div class="form-section">
      <div class="form-item switch-item">
        <span>设为默认地址</span>
        <t-switch v-model="form.isDefault" />
      </div>
    </div>

    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="saveAddress">保存</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Button as TButton, Switch as TSwitch, Toast } from 'tdesign-mobile-vue'
import { ChevronRightIcon } from 'tdesign-icons-vue-next'

const route = useRoute()
const router = useRouter()
const isEdit = !!route.params.id
const showAreaPicker = ref(false)

const form = ref({
  name: '',
  phone: '',
  area: '',
  detail: '',
  isDefault: false
})

const saveAddress = () => {
  if (!form.value.name || !form.value.phone || !form.value.detail) {
    Toast({ message: '请填写完整信息', theme: 'warning' })
    return
  }
  Toast({ message: '保存成功', theme: 'success' })
  setTimeout(() => router.back(), 1000)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.edit-address-page { min-height: 100vh; background: #f5f5f5; padding: 15px; padding-bottom: 100px; }

.form-section { background: #fff; border-radius: 12px; padding: 5px 15px; margin-bottom: 15px; }

.form-item {
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  label { display: block; font-size: 14px; color: #999; margin-bottom: 10px; }
  
  input, textarea {
    width: 100%;
    border: none;
    font-size: 15px;
    color: #1a1a1a;
    outline: none;
    &::placeholder { color: #ccc; }
  }
  
  textarea { resize: none; line-height: 1.6; }
  
  &.switch-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    span { font-size: 15px; color: #1a1a1a; }
  }
}

.picker-trigger {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 15px;
  color: #1a1a1a;
  cursor: pointer;
  .placeholder { color: #ccc; }
}

.submit-area { margin-top: 30px; }
</style>
