<template>
  <div class="contact">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-content">
        <p class="hero-subtitle">CONTACT US</p>
        <h1 class="hero-title">联系我们</h1>
        <p class="hero-desc">期待与您的每一次沟通</p>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 联系方式 -->
    <section class="section contact-section">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">GET IN TOUCH</p>
          <h2 class="section-title">联系方式</h2>
        </div>
        <div class="contact-grid">
          <div class="contact-card" v-for="(item, index) in contactList" :key="index">
            <div class="contact-icon">
              <component :is="item.icon" size="32px" />
            </div>
            <h3 class="contact-label">{{ item.label }}</h3>
            <p class="contact-value">{{ item.value }}</p>
            <p class="contact-desc">{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 关注我们 -->
    <section class="section follow-section">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">FOLLOW US</p>
          <h2 class="section-title">关注我们</h2>
        </div>
        <div class="follow-content">
          <div class="qrcode-card">
            <div class="qrcode-box">
              <LogoWechatStrokeIcon size="48px" />
              <span>公众号二维码</span>
            </div>
            <h3>轻养到家公众号</h3>
            <p>关注获取最新资讯和优惠活动</p>
          </div>
          <div class="qrcode-card">
            <div class="qrcode-box">
              <LogoWechatStrokeIcon size="48px" />
              <span>小程序二维码</span>
            </div>
            <h3>轻养到家小程序</h3>
            <p>扫码即可预约上门服务</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 温馨提示 -->
    <section class="section notice-section">
      <div class="container">
        <div class="notice-card">
          <div class="notice-icon">
            <SecuredIcon size="32px" />
          </div>
          <div class="notice-content">
            <h3>温馨提示</h3>
            <p>轻养到家作为合规运营的O2O上门养生服务平台，始终秉持合法经营理念，坚决抵制违规行为。非官方渠道暗藏诈骗、非平台商户服务、个人信息泄露等多重风险，且相关服务不受平台保护。请广大消费者务必认准官方平台下单，共同维护健康安全的服务环境。</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { CallIcon, MailIcon, LogoWechatStrokeIcon, SecuredIcon, TimeIcon } from 'tdesign-icons-vue-next'

const contactList = [
  { icon: CallIcon, label: '客服热线', value: '400-888-8888', desc: '服务咨询、意见反馈' },
  { icon: MailIcon, label: '电子邮箱', value: 'service@qingyang.com', desc: '商务合作、媒体联络' },
  { icon: TimeIcon, label: '服务时间', value: '09:00 - 22:00', desc: '全年无休，随时为您服务' }
]
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$primary-light: #e8f8ef;
$text-primary: #1a1a1a;
$text-secondary: #666666;
$background-light: #f8f9fa;

.hero {
  position: relative;
  min-height: 60vh;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #fff;
  padding: 120px 40px 160px;
  
  &-content { position: relative; z-index: 1; }
  &-subtitle { font-size: 14px; letter-spacing: 4px; opacity: 0.9; margin-bottom: 20px; }
  &-title { font-size: 56px; font-weight: 700; margin-bottom: 24px; line-height: 1.2; }
  &-desc { font-size: 20px; opacity: 0.9; }
  
  &-wave {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    svg { display: block; width: 100%; height: 80px; }
  }
}

.section {
  padding: 100px 0;
  
  &:nth-child(odd) { background: #fff; }
  &:nth-child(even) { background: $background-light; }
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 40px;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.section-subtitle {
  font-size: 14px;
  color: $primary-color;
  letter-spacing: 3px;
  margin-bottom: 12px;
}

.section-title {
  font-size: 40px;
  font-weight: 600;
  color: $text-primary;
}

.contact-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.contact-card {
  background: $background-light;
  border-radius: 20px;
  padding: 48px 36px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  
  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    background: #fff;
  }
}

.contact-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  background: $primary-light;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
}

.contact-label {
  font-size: 16px;
  color: $text-secondary;
  margin-bottom: 12px;
}

.contact-value {
  font-size: 28px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 16px;
}

.contact-desc {
  font-size: 14px;
  color: $text-secondary;
}

.follow-content {
  display: flex;
  justify-content: center;
  gap: 60px;
}

.qrcode-card {
  text-align: center;
}

.qrcode-box {
  width: 200px;
  height: 200px;
  background: #fff;
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  color: $primary-color;
  margin-bottom: 24px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  
  span {
    font-size: 14px;
    color: $text-secondary;
  }
}

.qrcode-card h3 {
  font-size: 18px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 8px;
}

.qrcode-card p {
  font-size: 14px;
  color: $text-secondary;
}

.notice-card {
  display: flex;
  align-items: flex-start;
  gap: 24px;
  background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
  border-radius: 20px;
  padding: 40px;
}

.notice-icon {
  width: 64px;
  height: 64px;
  background: #fbbf24;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  flex-shrink: 0;
}

.notice-content {
  h3 {
    font-size: 20px;
    font-weight: 600;
    color: #92400e;
    margin-bottom: 12px;
  }
  
  p {
    font-size: 15px;
    color: #78350f;
    line-height: 1.8;
  }
}
</style>
