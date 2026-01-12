<template>
  <div class="edit-profile-page">
    <div class="avatar-section">
      <t-avatar :image="form.logo" size="80px" shape="round" />
      <t-button size="small" variant="outline" @click="changeLogo">更换店铺Logo</t-button>
    </div>

    <div class="form-section">
      <div class="form-item">
        <label>店铺名称</label>
        <input v-model="form.name" placeholder="请输入店铺名称" />
      </div>
      <div class="form-item">
        <label>联系电话</label>
        <input v-model="form.phone" placeholder="请输入联系电话" />
      </div>
      <div class="form-item">
        <label>店铺地址</label>
        <input v-model="form.address" placeholder="请输入店铺地址" />
      </div>
      <div class="form-item">
        <label>营业时间</label>
        <input v-model="form.businessHours" placeholder="如：10:00-22:00" />
      </div>
    </div>

    <div class="form-section">
      <div class="form-item textarea">
        <label>店铺简介</label>
        <textarea v-model="form.intro" placeholder="介绍一下您的店铺" rows="4"></textarea>
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

const router = useRouter()

const form = ref({
  logo: '',
  name: '悦享养生馆',
  phone: '028-88888888',
  address: '成都市武侯区天府大道100号',
  businessHours: '10:00-22:00',
  intro: '专业养生按摩，舒适环境，优质服务'
})

const changeLogo = () => Toast({ message: 'Logo上传功能开发中', theme: 'warning' })
const saveProfile = () => { Toast({ message: '保存成功', theme: 'success' }); setTimeout(() => router.back(), 1000) }
</script>

<style lang="scss" scoped>
$primary: #07c160;

.edit-profile-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 100px; }

.avatar-section { background: #fff; padding: 30px; display: flex; flex-direction: column; align-items: center; gap: 15px; }

.form-section { background: #fff; margin: 15px; border-radius: 12px; padding: 5px 15px; }

.form-item {
  padding: 15px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  label { display: block; font-size: 14px; color: #999; margin-bottom: 10px; }
  input, textarea { width: 100%; border: none; font-size: 15px; color: #1a1a1a; outline: none; background: transparent; &::placeholder { color: #ccc; } }
  textarea { resize: none; line-height: 1.6; }
}

.submit-area { padding: 0 15px; margin-top: 30px; }
</style>
