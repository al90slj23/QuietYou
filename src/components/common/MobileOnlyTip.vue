<template>
  <div class="mobile-only-page">
    <div class="content">
      <div class="icon-wrap">
        <MobileIcon size="80px" />
      </div>
      <h1 class="title">{{ title }}</h1>
      <p class="desc">{{ description }}</p>
      <div class="qrcode-area" v-if="showQrcode">
        <div class="qrcode-placeholder">
          <QrcodeIcon size="120px" />
        </div>
        <p class="qrcode-tip">扫码访问移动端</p>
      </div>
      <div class="features">
        <div class="feature-item" v-for="(item, index) in features" :key="index">
          <component :is="item.icon" size="24px" />
          <span>{{ item.text }}</span>
        </div>
      </div>
    </div>
    <div class="footer">
      <p>轻养到家 · 专业上门服务平台</p>
    </div>
  </div>
</template>

<script setup>
import { MobileIcon, QrcodeIcon, SecuredIcon, ServiceIcon, TimeIcon } from 'tdesign-icons-vue-next'

const props = defineProps({
  title: {
    type: String,
    default: '请使用移动端访问'
  },
  description: {
    type: String,
    default: '当前页面仅支持手机端访问，请使用手机扫码或直接在手机浏览器中打开'
  },
  showQrcode: {
    type: Boolean,
    default: true
  },
  features: {
    type: Array,
    default: () => [
      { icon: SecuredIcon, text: '安全可靠' },
      { icon: ServiceIcon, text: '专业服务' },
      { icon: TimeIcon, text: '随时预约' }
    ]
  }
})
</script>

<style lang="scss" scoped>
.mobile-only-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f8faf9 0%, #e8f5e9 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.content {
  text-align: center;
  max-width: 500px;
}

.icon-wrap {
  margin-bottom: 32px;
  color: #07c160;
  opacity: 0.8;
}

.title {
  font-size: 32px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 16px;
}

.desc {
  font-size: 16px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 40px;
}

.qrcode-area {
  margin-bottom: 48px;
}

.qrcode-placeholder {
  width: 160px;
  height: 160px;
  background: #fff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  color: #ddd;
}

.qrcode-tip {
  font-size: 14px;
  color: #999;
}

.features {
  display: flex;
  justify-content: center;
  gap: 48px;
}

.feature-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: #07c160;
  
  span {
    font-size: 14px;
    color: #666;
  }
}

.footer {
  position: absolute;
  bottom: 40px;
  
  p {
    font-size: 14px;
    color: #999;
  }
}
</style>
