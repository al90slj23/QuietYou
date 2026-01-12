<template>
  <div class="edit-profile-page">
    <!-- 头像 -->
    <div class="avatar-section">
      <t-avatar :image="form.avatar" size="80px" />
      <t-button size="small" variant="outline" @click="changeAvatar">更换头像</t-button>
    </div>

    <!-- 表单 -->
    <div class="form-section">
      <div class="form-item">
        <label>姓名</label>
        <input v-model="form.name" placeholder="请输入姓名" />
      </div>
      <div class="form-item">
        <label>手机号</label>
        <input v-model="form.phone" placeholder="请输入手机号" />
      </div>
      <div class="form-item">
        <label>性别</label>
        <div class="gender-options">
          <div class="option" :class="{ active: form.gender === 1 }" @click="form.gender = 1">男</div>
          <div class="option" :class="{ active: form.gender === 2 }" @click="form.gender = 2">女</div>
        </div>
      </div>
      <div class="form-item">
        <label>从业年限</label>
        <input v-model="form.experience" type="number" placeholder="请输入从业年限" />
      </div>
      <div class="form-item">
        <label>擅长项目</label>
        <input v-model="form.skills" placeholder="如：推拿、足疗、刮痧" />
      </div>
    </div>

    <div class="form-section">
      <div class="form-item textarea">
        <label>个人简介</label>
        <textarea v-model="form.intro" placeholder="介绍一下自己的特长和服务理念" rows="4"></textarea>
      </div>
    </div>

    <!-- 保存按钮 -->
    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="saveProfile">保存</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'

const router = useRouter()

const form = ref({
  avatar: '',
  name: '张师傅',
  phone: '138****8888',
  gender: 1,
  experience: 5,
  skills: '推拿、足疗、刮痧',
  intro: '从业5年，擅长中式推拿和足底按摩，手法专业，服务热情。'
})

const changeAvatar = () => {
  Toast({ message: '头像上传功能开发中', theme: 'warning' })
}

const saveProfile = () => {
  Toast({ message: '保存成功', theme: 'success' })
  setTimeout(() => router.back(), 1000)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.edit-profile-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 100px;
}

.avatar-section {
  background: #fff;
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.form-section {
  background: #fff;
  margin: 15px;
  border-radius: 12px;
  padding: 5px 15px;
}

.form-item {
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  
  &:last-child { border-bottom: none; }
  
  label {
    display: block;
    font-size: 14px;
    color: #999;
    margin-bottom: 10px;
  }
  
  input, textarea {
    width: 100%;
    border: none;
    font-size: 15px;
    color: #1a1a1a;
    outline: none;
    background: transparent;
    
    &::placeholder { color: #ccc; }
  }
  
  textarea { resize: none; line-height: 1.6; }
}

.gender-options {
  display: flex;
  gap: 15px;
  
  .option {
    padding: 8px 30px;
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

.submit-area {
  padding: 0 15px;
  margin-top: 30px;
}
</style>
