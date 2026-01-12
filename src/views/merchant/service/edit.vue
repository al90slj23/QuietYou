<template>
  <div class="edit-service-page">
    <div class="form-section">
      <div class="form-item">
        <label>服务名称</label>
        <input v-model="form.name" placeholder="请输入服务名称" />
      </div>
      <div class="form-item">
        <label>服务描述</label>
        <textarea v-model="form.description" placeholder="请输入服务描述" rows="3"></textarea>
      </div>
      <div class="form-item">
        <label>服务时长（分钟）</label>
        <input v-model="form.duration" type="number" placeholder="请输入服务时长" />
      </div>
      <div class="form-item">
        <label>服务价格（元）</label>
        <input v-model="form.price" type="number" placeholder="请输入服务价格" />
      </div>
    </div>

    <div class="form-section">
      <div class="form-item switch-item">
        <div class="info">
          <label>上架状态</label>
          <span class="desc">关闭后顾客无法预约此服务</span>
        </div>
        <t-switch v-model="form.enabled" />
      </div>
    </div>

    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="saveService">保存</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Button as TButton, Switch as TSwitch, Toast } from 'tdesign-mobile-vue'

const route = useRoute()
const router = useRouter()
const isEdit = !!route.params.id

const form = ref({
  name: '',
  description: '',
  duration: '',
  price: '',
  enabled: true
})

const saveService = () => {
  Toast({ message: '保存成功', theme: 'success' })
  setTimeout(() => router.back(), 1000)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.edit-service-page { min-height: 100vh; background: #f5f5f5; padding: 15px; padding-bottom: 100px; }

.form-section { background: #fff; border-radius: 12px; padding: 15px; margin-bottom: 15px; }

.form-item {
  padding: 12px 0;
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
    
    .info {
      label { margin-bottom: 4px; }
      .desc { font-size: 12px; color: #999; }
    }
  }
}

.submit-area { margin-top: 30px; }
</style>
