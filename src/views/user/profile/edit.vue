<template>
  <div class="edit-profile-page">
    <div class="avatar-section">
      <t-avatar :image="form.avatar" size="80px" />
      <t-button size="small" variant="outline" @click="changeAvatar">更换头像</t-button>
    </div>

    <div class="form-section">
      <div class="form-item">
        <label>昵称</label>
        <input v-model="form.nickname" placeholder="请输入昵称" />
      </div>
      <div class="form-item">
        <label>手机号</label>
        <div class="phone-row">
          <span>{{ form.phone }}</span>
          <t-button size="small" variant="text" @click="changePhone">更换</t-button>
        </div>
      </div>
      <div class="form-item">
        <label>性别</label>
        <div class="gender-options">
          <div class="option" :class="{ active: form.gender === 1 }" @click="form.gender = 1">男</div>
          <div class="option" :class="{ active: form.gender === 2 }" @click="form.gender = 2">女</div>
          <div class="option" :class="{ active: form.gender === 0 }" @click="form.gender = 0">保密</div>
        </div>
      </div>
      <div class="form-item">
        <label>生日</label>
        <div class="picker-trigger" @click="showDatePicker = true">
          <span :class="{ placeholder: !form.birthday }">{{ form.birthday || '请选择生日' }}</span>
          <ChevronRightIcon size="16px" />
        </div>
      </div>
    </div>

    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="saveProfile">保存</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { ChevronRightIcon } from 'tdesign-icons-vue-next'

const router = useRouter()
const showDatePicker = ref(false)

const form = ref({
  avatar: '',
  nickname: '张先生',
  phone: '138****8888',
  gender: 1,
  birthday: ''
})

const changeAvatar = () => Toast({ message: '头像上传功能开发中', theme: 'warning' })
const changePhone = () => Toast({ message: '更换手机号功能开发中', theme: 'warning' })

const saveProfile = () => {
  Toast({ message: '保存成功', theme: 'success' })
  setTimeout(() => router.back(), 1000)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.edit-profile-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 100px; }

.avatar-section {
  background: #fff;
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.form-section { background: #fff; margin: 15px; border-radius: 12px; padding: 5px 15px; }

.form-item {
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  label { display: block; font-size: 14px; color: #999; margin-bottom: 10px; }
  
  input {
    width: 100%;
    border: none;
    font-size: 15px;
    color: #1a1a1a;
    outline: none;
    &::placeholder { color: #ccc; }
  }
}

.phone-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 15px;
  color: #1a1a1a;
}

.gender-options {
  display: flex;
  gap: 15px;
  
  .option {
    padding: 8px 24px;
    background: #f5f5f5;
    border-radius: 6px;
    font-size: 14px;
    color: #666;
    cursor: pointer;
    border: 2px solid transparent;
    
    &.active {
      background: #f0fff4;
      border-color: $primary;
      color: $primary;
    }
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

.submit-area { padding: 0 15px; margin-top: 30px; }
</style>
