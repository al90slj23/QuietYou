<template>
  <div class="edit-job-page">
    <div class="form-section">
      <div class="form-item">
        <label>职位名称</label>
        <input v-model="form.title" placeholder="如：高级推拿师" />
      </div>
      <div class="form-item">
        <label>薪资范围</label>
        <input v-model="form.salary" placeholder="如：8000-15000" />
      </div>
      <div class="form-item">
        <label>职位要求</label>
        <textarea v-model="form.requirements" placeholder="请输入职位要求" rows="3"></textarea>
      </div>
      <div class="form-item">
        <label>福利待遇</label>
        <input v-model="form.benefits" placeholder="如：五险一金、包住、高提成" />
      </div>
      <div class="form-item">
        <label>工作时间</label>
        <input v-model="form.workTime" placeholder="如：10:00-22:00，月休4天" />
      </div>
    </div>

    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="saveJob">发布招聘</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'

const route = useRoute()
const router = useRouter()

const form = ref({
  title: '',
  salary: '',
  requirements: '',
  benefits: '',
  workTime: ''
})

const saveJob = () => {
  Toast({ message: '发布成功', theme: 'success' })
  setTimeout(() => router.back(), 1000)
}
</script>

<style lang="scss" scoped>
.edit-job-page { min-height: 100vh; background: #f5f5f5; padding: 15px; padding-bottom: 100px; }

.form-section { background: #fff; border-radius: 12px; padding: 15px; }

.form-item {
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  label { display: block; font-size: 14px; color: #999; margin-bottom: 10px; }
  input, textarea { width: 100%; border: none; font-size: 15px; color: #1a1a1a; outline: none; &::placeholder { color: #ccc; } }
  textarea { resize: none; line-height: 1.6; }
}

.submit-area { margin-top: 30px; }
</style>
