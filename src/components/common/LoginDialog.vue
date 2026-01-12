<template>
  <t-popup :visible="visible" placement="bottom" :close-on-overlay-click="true" @visible-change="onVisibleChange">
    <div class="login-dialog">
      <div class="dialog-header">
        <h3>{{ title }}</h3>
        <CloseIcon size="24px" class="close-btn" @click="emit('update:modelValue', false)" />
      </div>
      
      <div class="dialog-body">
        <!-- 开发提示 -->
        <div class="dev-notice">
          <InfoCircleIcon size="16px" />
          <span>开发测试阶段，随意输入即可进入</span>
        </div>
        
        <div class="form-item">
          <label>手机号</label>
          <t-input 
            v-model="form.phone" 
            placeholder="请输入手机号"
            :maxlength="11"
            type="tel"
          />
        </div>
        
        <div class="form-item">
          <label>验证码</label>
          <div class="code-input">
            <t-input 
              v-model="form.code" 
              placeholder="请输入验证码"
              :maxlength="6"
            />
            <t-button 
              variant="text" 
              :disabled="countdown > 0"
              @click="sendCode"
            >
              {{ countdown > 0 ? `${countdown}s` : '获取验证码' }}
            </t-button>
          </div>
        </div>
        
        <t-button 
          block 
          theme="primary" 
          size="large"
          class="submit-btn"
          @click="handleLogin"
        >
          登录 / 注册
        </t-button>
        
        <p class="agreement">
          登录即表示同意
          <a href="javascript:;">《用户协议》</a>
          和
          <a href="javascript:;">《隐私政策》</a>
        </p>
      </div>
    </div>
  </t-popup>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Popup as TPopup, Input as TInput, Button as TButton } from 'tdesign-mobile-vue'
import { CloseIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'user', // user, tech, merchant
    validator: (v) => ['user', 'tech', 'merchant'].includes(v)
  }
})

const emit = defineEmits(['update:modelValue', 'success'])

const visible = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v)
})

const onVisibleChange = (v) => {
  emit('update:modelValue', v)
}

const title = computed(() => {
  const titles = {
    user: '用户登录',
    tech: '技师登录',
    merchant: '商户登录'
  }
  return titles[props.type]
})

const form = ref({
  phone: '',
  code: ''
})

const countdown = ref(0)
let timer = null

const sendCode = () => {
  if (!form.value.phone) {
    return
  }
  // 开发阶段直接开始倒计时
  countdown.value = 60
  timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timer)
    }
  }, 1000)
}

const handleLogin = () => {
  // 开发阶段：随意输入即可登录
  // 生成模拟 token
  const token = `dev_${props.type}_${Date.now()}`
  
  // 存储登录状态
  const storageKey = `${props.type}_token`
  localStorage.setItem(storageKey, token)
  localStorage.setItem(`${props.type}_phone`, form.value.phone || '13800138000')
  
  emit('success', {
    token,
    phone: form.value.phone || '13800138000',
    type: props.type
  })
  
  emit('update:modelValue', false)
}

// 清理定时器
watch(visible, (v) => {
  if (!v && timer) {
    clearInterval(timer)
  }
})
</script>

<style lang="scss" scoped>
.login-dialog {
  background: #fff;
  border-radius: 16px 16px 0 0;
  padding: 20px;
  padding-bottom: calc(20px + env(safe-area-inset-bottom));
}

.dialog-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  
  h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
  }
  
  .close-btn {
    color: #999;
    cursor: pointer;
  }
}

.dev-notice {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #fff7e6;
  border-radius: 8px;
  margin-bottom: 24px;
  font-size: 13px;
  color: #fa8c16;
}

.form-item {
  margin-bottom: 20px;
  
  label {
    display: block;
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
  }
}

.code-input {
  display: flex;
  gap: 12px;
  
  :deep(.t-input) {
    flex: 1;
  }
  
  :deep(.t-button) {
    white-space: nowrap;
    color: #07c160;
  }
}

.submit-btn {
  margin-top: 32px;
  height: 48px;
  border-radius: 24px;
  font-size: 16px;
}

.agreement {
  text-align: center;
  font-size: 12px;
  color: #999;
  margin-top: 16px;
  
  a {
    color: #07c160;
    text-decoration: none;
  }
}
</style>
