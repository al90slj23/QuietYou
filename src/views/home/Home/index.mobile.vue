<template>
  <div class="home-mobile">
    <section class="hero">
      <div class="hero-content">
        <h1>轻养到家</h1>
        <p class="subtitle">专业上门养生推拿服务平台</p>
        <p class="desc">让专业养生服务触手可及，为用户带来更健康、更舒适的生活体验。</p>
        <div class="actions">
          <a href="#download" class="btn btn-primary">立即预约</a>
          <router-link to="/home/about" class="btn btn-outline">了解更多</router-link>
        </div>
      </div>
      <div class="download-area" id="download">
        <div class="download-item">
          <div class="icon"><MobileIcon size="24px" /></div>
          <span>小程序</span>
        </div>
        <div class="download-item">
          <div class="icon"><LogoWechatStrokeIcon size="24px" /></div>
          <span>公众号</span>
        </div>
      </div>
    </section>

    <section class="features">
      <h2>为什么选择我们</h2>
      <div class="feature-list">
        <div class="feature-item" v-for="f in features" :key="f.title">
          <div class="icon"><component :is="f.icon" size="32px" /></div>
          <div class="info">
            <h3>{{ f.title }}</h3>
            <p>{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="stats">
      <div class="stat-item" v-for="s in stats" :key="s.label">
        <div class="value">{{ s.value }}</div>
        <div class="label">{{ s.label }}</div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { CertificateIcon, SecuredIcon, TimeIcon, StarIcon, MobileIcon, LogoWechatStrokeIcon } from 'tdesign-icons-vue-next'

const features = [
  { icon: CertificateIcon, title: '专业认证', desc: '技师持证上岗' },
  { icon: SecuredIcon, title: '安全保障', desc: '平台担保交易' },
  { icon: TimeIcon, title: '随叫随到', desc: '24小时在线预约' },
  { icon: StarIcon, title: '品质服务', desc: '标准化服务流程' }
]

const stats = [
  { value: '10万+', label: '服务用户' },
  { value: '1000+', label: '认证技师' },
  { value: '20+', label: '覆盖城市' },
  { value: '99%', label: '好评率' }
]
</script>

<style lang="scss" scoped>
$primary: #07c160;

.home-mobile { padding-bottom: 20px; }

.hero {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  color: #fff;
  padding: 80px 20px 40px;
  text-align: center;
  
  .hero-content {
    h1 { font-size: 32px; font-weight: 700; margin-bottom: 10px; }
    .subtitle { font-size: 16px; opacity: 0.95; margin-bottom: 15px; }
    .desc { font-size: 14px; opacity: 0.9; line-height: 1.6; margin-bottom: 25px; }
  }
  
  .actions {
    display: flex; gap: 12px; justify-content: center;
    .btn { padding: 12px 24px; font-size: 14px; }
    .btn-outline { background: transparent; color: #fff; border: 1px solid #fff; }
  }
  
  .download-area {
    display: flex; justify-content: center; gap: 30px; margin-top: 30px;
    .download-item {
      display: flex; flex-direction: column; align-items: center; gap: 6px;
      .icon { width: 44px; height: 44px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
      span { font-size: 12px; opacity: 0.9; }
    }
  }
}

.features {
  padding: 30px 20px;
  h2 { font-size: 18px; font-weight: 600; text-align: center; margin-bottom: 20px; color: #333; }
  .feature-list { display: flex; flex-direction: column; gap: 16px; }
  .feature-item {
    display: flex; align-items: center; gap: 15px; background: #f9fafb; padding: 16px; border-radius: 12px;
    .icon { color: $primary; flex-shrink: 0; }
    .info {
      h3 { font-size: 15px; font-weight: 600; color: #333; margin-bottom: 4px; }
      p { font-size: 13px; color: #666; }
    }
  }
}

.stats {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; padding: 20px; background: linear-gradient(135deg, $primary 0%, #10b981 100%); margin: 0 20px; border-radius: 12px;
  .stat-item { text-align: center; color: #fff; padding: 15px 0; }
  .value { font-size: 28px; font-weight: 700; }
  .label { font-size: 13px; opacity: 0.9; margin-top: 4px; }
}
</style>
