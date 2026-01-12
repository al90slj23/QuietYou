<template>
  <div class="download">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-content">
        <p class="hero-subtitle">BOOKING CHANNELS</p>
        <h1 class="hero-title">预约渠道</h1>
        <p class="hero-desc">多种方式，随心选择</p>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 预约渠道 -->
    <section class="section channels">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">OFFICIAL CHANNELS</p>
          <h2 class="section-title">官方预约渠道</h2>
          <p class="section-desc">请认准官方渠道，保障您的服务权益</p>
        </div>
        <div class="channels-grid">
          <div class="channel-card" v-for="(channel, index) in channels" :key="index">
            <div class="channel-badge" v-if="channel.badge">{{ channel.badge }}</div>
            <div class="channel-icon">
              <component :is="channel.icon" size="40px" />
            </div>
            <h3>{{ channel.title }}</h3>
            <p class="channel-desc">{{ channel.desc }}</p>
            <div class="qrcode-box" v-if="channel.qrcode">
              <span>{{ channel.qrcode }}</span>
            </div>
            <router-link v-if="channel.link" :to="channel.link" class="channel-btn">
              {{ channel.btnText }}
              <ChevronRightIcon size="18px" />
            </router-link>
            <p class="channel-tip">{{ channel.tip }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 服务说明 -->
    <section class="section service">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">SERVICE FEATURES</p>
          <h2 class="section-title">服务特色</h2>
        </div>
        <div class="features-grid">
          <div class="feature-item" v-for="(feature, index) in features" :key="index">
            <div class="feature-icon">
              <component :is="feature.icon" size="28px" />
            </div>
            <div class="feature-content">
              <h3>{{ feature.title }}</h3>
              <p>{{ feature.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 安全提示 -->
    <section class="section notice">
      <div class="container">
        <div class="notice-card">
          <div class="notice-icon">
            <SecuredIcon size="32px" />
          </div>
          <div class="notice-content">
            <h3>安全提示</h3>
            <p>轻养到家作为合规运营的O2O上门养生服务平台，始终秉持合法经营理念。非官方渠道暗藏诈骗、非平台商户服务、个人信息泄露等多重风险，且相关服务不受平台保护。请广大消费者务必认准官方平台下单，共同维护健康安全的服务环境。</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { 
  MobileIcon, LogoWechatStrokeIcon, InternetIcon, 
  SecuredIcon, TimeIcon, UserIcon, HeartIcon,
  ChevronRightIcon
} from 'tdesign-icons-vue-next'

const channels = [
  { 
    icon: MobileIcon, 
    title: '轻养到家小程序', 
    desc: '微信官方小程序',
    qrcode: '小程序码',
    tip: '微信扫一扫，即可预约服务',
    badge: '推荐'
  },
  { 
    icon: LogoWechatStrokeIcon, 
    title: '轻养到家公众号', 
    desc: '官方微信公众号',
    qrcode: '公众号二维码',
    tip: '关注公众号，获取最新资讯'
  },
  { 
    icon: InternetIcon, 
    title: '轻养到家网页版', 
    desc: '在线预约平台',
    link: '/user',
    btnText: '立即访问',
    tip: '无需下载，浏览器直接访问'
  }
]

const features = [
  { icon: TimeIcon, title: '24小时在线', desc: '全天候接受预约，随时随地享受服务' },
  { icon: UserIcon, title: '专业技师', desc: '严格筛选认证，专业技能有保障' },
  { icon: SecuredIcon, title: '安全保障', desc: '全程可追溯，服务安全有保障' },
  { icon: HeartIcon, title: '贴心服务', desc: '上门服务，足不出户享受健康' }
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

.section-desc {
  font-size: 16px;
  color: $text-secondary;
  margin-top: 16px;
}

.channels-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.channel-card {
  position: relative;
  background: #fff;
  border-radius: 24px;
  padding: 48px 36px;
  text-align: center;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  
  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }
}

.channel-badge {
  position: absolute;
  top: 20px;
  right: 20px;
  padding: 6px 14px;
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
  color: #fff;
  font-size: 12px;
  font-weight: 500;
  border-radius: 20px;
}

.channel-icon {
  width: 88px;
  height: 88px;
  margin: 0 auto 24px;
  background: $primary-light;
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
}

.channel-card h3 {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 8px;
}

.channel-desc {
  font-size: 14px;
  color: $text-secondary;
  margin-bottom: 28px;
}

.qrcode-box {
  width: 180px;
  height: 180px;
  background: $background-light;
  border-radius: 16px;
  margin: 0 auto 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #bbb;
  font-size: 14px;
}

.channel-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: 180px;
  height: 52px;
  background: $primary-color;
  color: #fff;
  font-size: 16px;
  font-weight: 500;
  border-radius: 26px;
  margin-bottom: 24px;
  transition: all 0.3s ease;
  
  &:hover {
    background: #06a050;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(7, 193, 96, 0.3);
  }
}

.channel-tip {
  font-size: 13px;
  color: #999;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  background: #fff;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.feature-icon {
  width: 60px;
  height: 60px;
  background: $primary-light;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  flex-shrink: 0;
}

.feature-content {
  h3 {
    font-size: 18px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 8px;
  }
  
  p {
    font-size: 15px;
    color: $text-secondary;
    line-height: 1.6;
  }
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
