<template>
  <div class="home">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-bg"></div>
      <div class="container">
        <div class="hero-content">
          <h1 class="hero-title">轻养到家</h1>
          <p class="hero-subtitle">专业上门养生推拿服务平台</p>
          <p class="hero-desc">
            我们致力于"让专业养生服务触手可及"，为用户带来更健康、更舒适的生活体验。
            通过严格的服务培训及可追溯的服务品质，为消费者提供便捷、安全、高效的健康服务。
          </p>
          <div class="hero-actions">
            <a href="#download" class="btn btn-primary">立即预约</a>
            <router-link to="/about" class="btn btn-outline-white">了解更多</router-link>
          </div>
        </div>
      </div>
      
      <!-- 下载入口 -->
      <div class="hero-download" id="download">
        <div class="download-item">
          <div class="download-icon">📱</div>
          <span>微信小程序</span>
        </div>
        <div class="download-item">
          <div class="download-icon">💬</div>
          <span>微信公众号</span>
        </div>
      </div>
    </section>

    <!-- 服务特色 -->
    <section class="features section">
      <div class="container">
        <div class="section-title">
          <h2>为什么选择轻养到家</h2>
          <p class="subtitle">WHY CHOOSE US</p>
        </div>
        
        <div class="features-grid">
          <div class="feature-card" v-for="feature in features" :key="feature.title">
            <div class="feature-icon">{{ feature.icon }}</div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 数据展示 -->
    <section class="stats section">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item" v-for="stat in stats" :key="stat.label">
            <div class="stat-number">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const features = [
  { icon: '🏆', title: '专业认证', desc: '所有技师均经过严格培训和资质认证，持证上岗' },
  { icon: '🔒', title: '安全保障', desc: '全程服务可追溯，平台担保交易，让您放心享受' },
  { icon: '⏰', title: '随叫随到', desc: '24小时在线预约，快速响应，服务上门' },
  { icon: '💯', title: '品质服务', desc: '标准化服务流程，专业手法，效果显著' }
]

const stats = [
  { value: '10万+', label: '服务用户' },
  { value: '1000+', label: '认证技师' },
  { value: '20+', label: '覆盖城市' },
  { value: '99%', label: '好评率' }
]
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$mobile: 768px;

.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: #fff;
  text-align: center;
  padding: 100px 0 60px;
  
  @media (max-width: $mobile) {
    min-height: auto;
    padding: 120px 0 80px;
  }
}

.hero-bg {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 50%, #059669 100%);
  z-index: -1;
  
  &::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
}

.hero-content {
  max-width: 700px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-title {
  font-size: 56px;
  font-weight: 700;
  margin-bottom: 15px;
  
  @media (max-width: $mobile) {
    font-size: 36px;
  }
}

.hero-subtitle {
  font-size: 24px;
  opacity: 0.95;
  margin-bottom: 25px;
  
  @media (max-width: $mobile) {
    font-size: 18px;
  }
}

.hero-desc {
  font-size: 16px;
  line-height: 1.8;
  opacity: 0.9;
  margin-bottom: 40px;
  
  @media (max-width: $mobile) {
    font-size: 14px;
  }
}

.hero-actions {
  display: flex;
  gap: 20px;
  justify-content: center;
  flex-wrap: wrap;
  
  .btn {
    min-width: 140px;
  }
  
  .btn-outline-white {
    background: transparent;
    color: #fff;
    border: 2px solid #fff;
    
    &:hover {
      background: #fff;
      color: $primary-color;
    }
  }
}

.hero-download {
  display: flex;
  gap: 40px;
  margin-top: 60px;
  
  @media (max-width: $mobile) {
    gap: 30px;
    margin-top: 40px;
  }
}

.download-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: transform 0.3s;
  
  &:hover {
    transform: translateY(-5px);
  }
  
  .download-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    
    @media (max-width: $mobile) {
      width: 50px;
      height: 50px;
      font-size: 24px;
    }
  }
  
  span {
    font-size: 14px;
    opacity: 0.9;
  }
}

// Features
.features {
  background: #f9fafb;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
  
  @media (max-width: 1024px) {
    grid-template-columns: repeat(2, 1fr);
  }
  
  @media (max-width: $mobile) {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}

.feature-card {
  background: #fff;
  padding: 40px 30px;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  
  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  }
  
  .feature-icon {
    font-size: 48px;
    margin-bottom: 20px;
  }
  
  h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 12px;
    color: #333;
  }
  
  p {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
  }
}

// Stats
.stats {
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  color: #fff;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
  text-align: center;
  
  @media (max-width: $mobile) {
    grid-template-columns: repeat(2, 1fr);
    gap: 40px 20px;
  }
}

.stat-item {
  .stat-number {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 10px;
    
    @media (max-width: $mobile) {
      font-size: 36px;
    }
  }
  
  .stat-label {
    font-size: 16px;
    opacity: 0.9;
  }
}
</style>
