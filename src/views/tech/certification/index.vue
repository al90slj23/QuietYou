<template>
  <div class="certification-page">
    <!-- 认证状态 -->
    <div class="status-card" :class="statusClass">
      <component :is="statusIcon" size="48px" />
      <div class="status-text">{{ statusText }}</div>
      <div class="status-desc">{{ statusDesc }}</div>
    </div>

    <!-- 认证表单 -->
    <div class="form-section" v-if="status !== 'approved'">
      <div class="section-title">基本信息</div>
      <div class="form-item">
        <label>真实姓名</label>
        <input v-model="form.realName" placeholder="请输入真实姓名" />
      </div>
      <div class="form-item">
        <label>身份证号</label>
        <input v-model="form.idCard" placeholder="请输入身份证号" />
      </div>
    </div>

    <div class="form-section" v-if="status !== 'approved'">
      <div class="section-title">证件照片</div>
      <div class="upload-area">
        <div class="upload-item">
          <div class="upload-box" @click="uploadIdFront">
            <AddIcon v-if="!form.idFrontImage" size="32px" />
            <img v-else :src="form.idFrontImage" />
          </div>
          <span>身份证正面</span>
        </div>
        <div class="upload-item">
          <div class="upload-box" @click="uploadIdBack">
            <AddIcon v-if="!form.idBackImage" size="32px" />
            <img v-else :src="form.idBackImage" />
          </div>
          <span>身份证反面</span>
        </div>
      </div>
    </div>

    <div class="form-section" v-if="status !== 'approved'">
      <div class="section-title">资质证书（选填）</div>
      <div class="upload-area">
        <div class="upload-item">
          <div class="upload-box" @click="uploadCert">
            <AddIcon v-if="!form.certImage" size="32px" />
            <img v-else :src="form.certImage" />
          </div>
          <span>资格证书</span>
        </div>
        <div class="upload-item">
          <div class="upload-box" @click="uploadHealth">
            <AddIcon v-if="!form.healthImage" size="32px" />
            <img v-else :src="form.healthImage" />
          </div>
          <span>健康证</span>
        </div>
      </div>
    </div>

    <!-- 提交按钮 -->
    <div class="submit-area" v-if="status !== 'approved'">
      <t-button block theme="primary" size="large" @click="submitCert" :disabled="!canSubmit">
        {{ status === 'pending' ? '重新提交' : '提交认证' }}
      </t-button>
    </div>

    <!-- 认证须知 -->
    <div class="tips-section">
      <div class="tips-title">认证须知</div>
      <ul class="tips-list">
        <li>请确保上传的证件照片清晰可辨</li>
        <li>认证审核通常在1-3个工作日内完成</li>
        <li>认证通过后可获得"已认证"标识，提升顾客信任度</li>
        <li>如有疑问请联系客服</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'
import { AddIcon, CheckCircleFilledIcon, TimeFilledIcon, CloseCircleFilledIcon } from 'tdesign-icons-vue-next'

// 认证状态: none, pending, approved, rejected
const status = ref('none')

const statusClass = computed(() => ({
  pending: status.value === 'pending',
  approved: status.value === 'approved',
  rejected: status.value === 'rejected'
}))

const statusIcon = computed(() => {
  const icons = {
    none: AddIcon,
    pending: TimeFilledIcon,
    approved: CheckCircleFilledIcon,
    rejected: CloseCircleFilledIcon
  }
  return icons[status.value]
})

const statusText = computed(() => {
  const texts = { none: '未认证', pending: '审核中', approved: '已认证', rejected: '认证失败' }
  return texts[status.value]
})

const statusDesc = computed(() => {
  const descs = {
    none: '完成实名认证，获得更多订单机会',
    pending: '您的认证申请正在审核中，请耐心等待',
    approved: '恭喜您已通过实名认证',
    rejected: '认证未通过，请检查信息后重新提交'
  }
  return descs[status.value]
})

const form = ref({
  realName: '',
  idCard: '',
  idFrontImage: '',
  idBackImage: '',
  certImage: '',
  healthImage: ''
})

const canSubmit = computed(() => form.value.realName && form.value.idCard)

const uploadIdFront = () => Toast({ message: '上传功能开发中', theme: 'warning' })
const uploadIdBack = () => Toast({ message: '上传功能开发中', theme: 'warning' })
const uploadCert = () => Toast({ message: '上传功能开发中', theme: 'warning' })
const uploadHealth = () => Toast({ message: '上传功能开发中', theme: 'warning' })

const submitCert = () => {
  Toast({ message: '认证申请已提交', theme: 'success' })
  status.value = 'pending'
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.certification-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 15px;
  padding-bottom: 100px;
}

.status-card {
  background: #fff;
  border-radius: 12px;
  padding: 30px;
  text-align: center;
  margin-bottom: 15px;
  color: #999;
  
  &.pending { color: #f57c00; }
  &.approved { color: $primary; }
  &.rejected { color: #f44336; }
  
  .status-text { font-size: 18px; font-weight: 600; margin: 12px 0 8px; color: inherit; }
  .status-desc { font-size: 13px; color: #999; }
}

.form-section {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 15px;
}

.section-title { font-size: 14px; color: #999; margin-bottom: 15px; }

.form-item {
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  label { display: block; font-size: 14px; color: #999; margin-bottom: 8px; }
  input {
    width: 100%;
    border: none;
    font-size: 15px;
    color: #1a1a1a;
    outline: none;
    &::placeholder { color: #ccc; }
  }
}

.upload-area { display: flex; gap: 20px; }

.upload-item {
  text-align: center;
  span { display: block; font-size: 12px; color: #999; margin-top: 8px; }
}

.upload-box {
  width: 100px;
  height: 70px;
  background: #f9f9f9;
  border: 1px dashed #ddd;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ccc;
  cursor: pointer;
  overflow: hidden;
  
  img { width: 100%; height: 100%; object-fit: cover; }
}

.submit-area { margin-top: 20px; }

.tips-section {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  margin-top: 15px;
}

.tips-title { font-size: 14px; color: #999; margin-bottom: 10px; }

.tips-list {
  padding-left: 20px;
  li { font-size: 13px; color: #666; line-height: 2; }
}
</style>
