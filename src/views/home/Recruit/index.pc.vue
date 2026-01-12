<template>
  <div class="recruit">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-bg"></div>
      <div class="hero-content">
        <p class="hero-subtitle animate-on-scroll">MERCHANT COOPERATION</p>
        <h1 class="hero-title animate-on-scroll animate-delay-1">商户合作</h1>
        <p class="hero-desc animate-on-scroll animate-delay-2">携手轻养到家，开启灵活执业新纪元</p>
        <div class="hero-stats animate-on-scroll animate-delay-3">
          <div class="hero-stat">
            <span class="stat-value">1000+</span>
            <span class="stat-label">入驻商户</span>
          </div>
          <div class="hero-stat">
            <span class="stat-value">20+</span>
            <span class="stat-label">覆盖城市</span>
          </div>
          <div class="hero-stat">
            <span class="stat-value">99%</span>
            <span class="stat-label">满意度</span>
          </div>
        </div>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 合作优势 -->
    <section class="section advantages">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">WHY CHOOSE US</p>
          <h2 class="section-title">合作优势</h2>
          <p class="section-desc">全方位赋能，助力商户成长</p>
        </div>
        <div class="advantage-grid">
          <div 
            class="advantage-card animate-on-scroll breathe-hover" 
            v-for="(item, index) in advantages" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="advantage-icon">
              <component :is="item.icon" size="32px" />
            </div>
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
            <div class="advantage-line"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 开放城市 -->
    <section class="section cities">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">OPEN CITIES</p>
          <h2 class="section-title">开放城市</h2>
          <p class="section-desc">我们正在快速扩展，更多城市即将开放</p>
        </div>
        <div class="city-regions">
          <div 
            class="region-card animate-on-scroll breathe-hover" 
            v-for="(region, index) in regions" 
            :key="region.name"
            :class="`animate-delay-${index + 1}`"
          >
            <h3 class="region-name">
              <LocationIcon size="20px" />
              {{ region.name }}
            </h3>
            <div class="provinces">
              <div class="province" v-for="province in region.provinces" :key="province.name">
                <span class="province-name">{{ province.name }}</span>
                <div class="city-tags">
                  <span class="city-tag" v-for="city in province.cities" :key="city">{{ city }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 入驻流程 -->
    <section class="section process">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">HOW TO JOIN</p>
          <h2 class="section-title">入驻流程</h2>
        </div>
        <div class="process-steps">
          <div 
            class="process-step animate-on-scroll" 
            v-for="(step, index) in steps" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="step-number">{{ String(index + 1).padStart(2, '0') }}</div>
            <div class="step-content">
              <h3>{{ step.title }}</h3>
              <p>{{ step.desc }}</p>
            </div>
            <div class="step-line" v-if="index < steps.length - 1"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 入驻申请 -->
    <section class="section form-section">
      <div class="container">
        <div class="form-wrapper">
          <div class="form-intro animate-on-scroll animate-slide-left">
            <p class="section-subtitle">JOIN US</p>
            <h2 class="section-title">入驻申请</h2>
            <p class="form-desc">填写以下信息，我们的工作人员将在1-3个工作日内与您联系</p>
            <div class="form-features">
              <div class="form-feature" v-for="(feature, index) in formFeatures" :key="index">
                <CheckCircleIcon size="20px" />
                <span>{{ feature }}</span>
              </div>
            </div>
            <div class="form-contact">
              <p>咨询热线</p>
              <a href="tel:400-888-8888" class="contact-phone">400-888-8888</a>
            </div>
          </div>
          <div class="form-card animate-on-scroll animate-slide-right animate-delay-2">
            <div class="form-group">
              <label>店铺名称 <span class="required">*</span></label>
              <input type="text" v-model="form.shopName" placeholder="请输入店铺名称" />
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>联系人 <span class="required">*</span></label>
                <input type="text" v-model="form.contactName" placeholder="请输入联系人姓名" />
              </div>
              <div class="form-group">
                <label>手机号码 <span class="required">*</span></label>
                <input type="tel" v-model="form.phone" placeholder="请输入手机号码" />
              </div>
            </div>
            <div class="form-group">
              <label>所在城市 <span class="required">*</span></label>
              <input type="text" v-model="form.city" placeholder="请输入所在城市" />
            </div>
            <div class="form-group">
              <label>店铺地址</label>
              <input type="text" v-model="form.address" placeholder="请输入详细地址（选填）" />
            </div>
            <div class="form-group">
              <label>备注说明</label>
              <textarea v-model="form.remark" placeholder="请输入备注信息（选填）" rows="3"></textarea>
            </div>
            <button class="btn-submit" @click="submitForm">
              提交申请
              <ChevronRightIcon size="20px" />
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useScrollAnimation } from '@/composables/useScrollAnimation'
import { 
  ShopIcon, WalletIcon, UserIcon, SecuredIcon, 
  LocationIcon, CheckCircleIcon, ChevronRightIcon 
} from 'tdesign-icons-vue-next'

useScrollAnimation()

const advantages = [
  { icon: WalletIcon, title: '灵活收入', desc: '自主接单，多劳多得，收入透明可追溯' },
  { icon: UserIcon, title: '专业培训', desc: '系统化培训体系，提升专业技能' },
  { icon: ShopIcon, title: '平台赋能', desc: '强大的平台支持，助力业务增长' },
  { icon: SecuredIcon, title: '安全保障', desc: '完善的安全体系，保障服务安全' }
]

const steps = [
  { title: '提交申请', desc: '填写入驻申请表，提交基本信息' },
  { title: '资质审核', desc: '平台审核商户资质，1-3个工作日' },
  { title: '签约培训', desc: '签署合作协议，参加平台培训' },
  { title: '正式入驻', desc: '开通商户账号，开始接单服务' }
]

const formFeatures = ['免费入驻', '专业培训', '全程支持', '快速审核']

const form = ref({ shopName: '', contactName: '', phone: '', city: '', address: '', remark: '' })

const regions = ref([
  { name: '西南地区', provinces: [
    { name: '四川', cities: ['成都', '绵阳', '德阳', '南充'] },
    { name: '重庆', cities: ['重庆'] },
    { name: '云南', cities: ['昆明'] },
    { name: '贵州', cities: ['贵阳'] }
  ]},
  { name: '华东地区', provinces: [
    { name: '上海', cities: ['上海'] },
    { name: '浙江', cities: ['杭州', '宁波', '温州'] },
    { name: '江苏', cities: ['南京', '苏州', '无锡'] }
  ]},
  { name: '华南地区', provinces: [
    { name: '广东', cities: ['深圳', '广州', '东莞', '佛山'] },
    { name: '海南', cities: ['海口', '三亚'] }
  ]}
])

const submitForm = () => {
  if (!form.value.shopName || !form.value.contactName || !form.value.phone || !form.value.city) {
    alert('请填写必填项')
    return
  }
  alert('提交成功，我们会尽快与您联系！')
}
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$primary-light: #e8f8ef;
$primary-dark: #06a050;
$text-primary: #1a1a1a;
$text-secondary: #666666;
$background-light: #f8f9fa;

.hero {
  position: relative;
  min-height: 80vh;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #fff;
  padding: 140px 40px 180px;
  overflow: hidden;
  
  &-bg {
    position: absolute;
    inset: 0;
    background: 
      radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(255,255,255,0.08) 0%, transparent 40%);
  }
  
  &-content { position: relative; z-index: 1; max-width: 800px; }
  &-subtitle { font-size: 14px; letter-spacing: 6px; opacity: 0.9; margin-bottom: 24px; }
  &-title { font-size: 64px; font-weight: 700; margin-bottom: 24px; line-height: 1.1; }
  &-desc { font-size: 22px; opacity: 0.9; margin-bottom: 48px; }
  
  &-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
  }
  
  &-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    
    .stat-value {
      font-size: 42px;
      font-weight: 700;
      margin-bottom: 8px;
    }
    
    .stat-label {
      font-size: 14px;
      opacity: 0.8;
    }
  }
  
  &-wave {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    svg { display: block; width: 100%; height: 100px; }
  }
}

.section {
  padding: 120px 0;
  
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
  margin-bottom: 80px;
}

.section-subtitle {
  font-size: 14px;
  color: $primary-color;
  letter-spacing: 4px;
  margin-bottom: 16px;
  font-weight: 500;
}

.section-title {
  font-size: 44px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 16px;
}

.section-desc {
  font-size: 18px;
  color: $text-secondary;
}

.advantage-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.advantage-card {
  background: #fff;
  border-radius: 24px;
  padding: 48px 32px;
  text-align: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.advantage-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 28px;
  background: $primary-light;
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  transition: transform 0.4s ease, background 0.4s ease;
}

.advantage-card:hover .advantage-icon {
  transform: scale(1.1) rotate(5deg);
  background: $primary-color;
  color: #fff;
}

.advantage-card h3 {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 14px;
}

.advantage-card p {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.advantage-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 4px;
  background: $primary-color;
  border-radius: 2px;
  transition: width 0.4s ease;
}

.advantage-card:hover .advantage-line {
  width: 60px;
}

.city-regions {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.region-card {
  background: #fff;
  border-radius: 24px;
  padding: 36px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.region-name {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 22px;
  font-weight: 600;
  color: $primary-color;
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 2px solid $primary-light;
}

.provinces {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.province {
  .province-name {
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 12px;
    display: block;
    font-size: 15px;
  }
}

.city-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.city-tag {
  padding: 8px 16px;
  background: $background-light;
  border-radius: 20px;
  font-size: 14px;
  color: $text-secondary;
  transition: all 0.3s ease;
  
  &:hover {
    background: $primary-light;
    color: $primary-color;
  }
}

.process-steps {
  display: flex;
  justify-content: space-between;
  position: relative;
  max-width: 1000px;
  margin: 0 auto;
}

.process-step {
  flex: 1;
  text-align: center;
  position: relative;
  padding: 0 20px;
}

.step-number {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  background: $primary-light;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  font-weight: 700;
  color: $primary-color;
  position: relative;
  z-index: 1;
  transition: all 0.4s ease;
}

.process-step:hover .step-number {
  background: $primary-color;
  color: #fff;
  transform: scale(1.1);
}

.step-content {
  h3 {
    font-size: 20px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 10px;
  }
  
  p {
    font-size: 14px;
    color: $text-secondary;
    line-height: 1.6;
  }
}

.step-line {
  position: absolute;
  top: 40px;
  left: calc(50% + 50px);
  width: calc(100% - 100px);
  height: 2px;
  background: linear-gradient(90deg, $primary-color, $primary-light);
}

.form-wrapper {
  display: grid;
  grid-template-columns: 1fr 1.3fr;
  gap: 80px;
  align-items: start;
}

.form-intro {
  .section-subtitle { text-align: left; }
  .section-title { text-align: left; font-size: 40px; margin-bottom: 20px; }
}

.form-desc {
  font-size: 17px;
  color: $text-secondary;
  line-height: 1.8;
  margin-bottom: 36px;
}

.form-features {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 40px;
}

.form-feature {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 16px;
  color: $text-primary;
  
  svg { color: $primary-color; }
}

.form-contact {
  padding-top: 32px;
  border-top: 1px solid #eee;
  
  p {
    font-size: 14px;
    color: $text-secondary;
    margin-bottom: 8px;
  }
}

.contact-phone {
  font-size: 32px;
  font-weight: 700;
  color: $primary-color;
  transition: opacity 0.3s;
  
  &:hover { opacity: 0.8; }
}

.form-card {
  background: #fff;
  border-radius: 28px;
  padding: 48px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}

.form-group {
  margin-bottom: 28px;
  
  label {
    display: block;
    font-size: 15px;
    font-weight: 500;
    margin-bottom: 12px;
    color: $text-primary;
    
    .required { color: #ef4444; }
  }
  
  input, textarea {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #eee;
    border-radius: 14px;
    font-size: 15px;
    transition: border-color 0.3s, box-shadow 0.3s;
    
    &:focus {
      outline: none;
      border-color: $primary-color;
      box-shadow: 0 0 0 4px rgba($primary-color, 0.1);
    }
    
    &::placeholder { color: #bbb; }
  }
  
  textarea { resize: none; }
}

.btn-submit {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 18px;
  background: $primary-color;
  border: none;
  border-radius: 14px;
  font-size: 17px;
  font-weight: 600;
  color: #fff;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: $primary-dark;
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(7, 193, 96, 0.35);
  }
}
</style>
