<template>
  <div class="recruit">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-content">
        <p class="hero-subtitle">MERCHANT COOPERATION</p>
        <h1 class="hero-title">商户合作</h1>
        <p class="hero-desc">携手轻养到家，开启灵活执业新纪元</p>
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
        <div class="section-header">
          <p class="section-subtitle">WHY CHOOSE US</p>
          <h2 class="section-title">合作优势</h2>
        </div>
        <div class="advantage-grid">
          <div class="advantage-card" v-for="(item, index) in advantages" :key="index">
            <div class="advantage-icon">
              <component :is="item.icon" size="32px" />
            </div>
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 开放城市 -->
    <section class="section cities">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">OPEN CITIES</p>
          <h2 class="section-title">开放城市</h2>
          <p class="section-desc">我们正在快速扩展，更多城市即将开放</p>
        </div>
        <div class="city-regions">
          <div class="region-card" v-for="region in regions" :key="region.name">
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

    <!-- 入驻申请 -->
    <section class="section form-section">
      <div class="container">
        <div class="form-wrapper">
          <div class="form-intro">
            <p class="section-subtitle">JOIN US</p>
            <h2 class="section-title">入驻申请</h2>
            <p class="form-desc">填写以下信息，我们的工作人员将在1-3个工作日内与您联系</p>
            <div class="form-features">
              <div class="form-feature">
                <CheckCircleIcon size="20px" />
                <span>免费入驻</span>
              </div>
              <div class="form-feature">
                <CheckCircleIcon size="20px" />
                <span>专业培训</span>
              </div>
              <div class="form-feature">
                <CheckCircleIcon size="20px" />
                <span>全程支持</span>
              </div>
            </div>
          </div>
          <div class="form-card">
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
import { 
  ShopIcon, WalletIcon, UserIcon, SecuredIcon, 
  LocationIcon, CheckCircleIcon, ChevronRightIcon 
} from 'tdesign-icons-vue-next'

const advantages = [
  { icon: WalletIcon, title: '灵活收入', desc: '自主接单，多劳多得，收入透明可追溯' },
  { icon: UserIcon, title: '专业培训', desc: '系统化培训体系，提升专业技能' },
  { icon: ShopIcon, title: '平台赋能', desc: '强大的平台支持，助力业务增长' },
  { icon: SecuredIcon, title: '安全保障', desc: '完善的安全体系，保障服务安全' }
]

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

.advantage-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.advantage-card {
  background: $background-light;
  border-radius: 20px;
  padding: 40px 28px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  
  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    background: #fff;
  }
}

.advantage-icon {
  width: 72px;
  height: 72px;
  margin: 0 auto 20px;
  background: $primary-light;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
}

.advantage-card h3 {
  font-size: 20px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 12px;
}

.advantage-card p {
  font-size: 14px;
  color: $text-secondary;
  line-height: 1.6;
}

.city-regions {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.region-card {
  background: #fff;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.region-name {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 20px;
  font-weight: 600;
  color: $primary-color;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid $primary-light;
}

.provinces {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.province {
  .province-name {
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 10px;
    display: block;
  }
}

.city-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.city-tag {
  padding: 6px 14px;
  background: $background-light;
  border-radius: 16px;
  font-size: 13px;
  color: $text-secondary;
}

.form-wrapper {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 60px;
  align-items: start;
}

.form-intro {
  .section-subtitle { text-align: left; }
  .section-title { text-align: left; font-size: 36px; }
}

.form-desc {
  font-size: 16px;
  color: $text-secondary;
  line-height: 1.7;
  margin: 20px 0 32px;
}

.form-features {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-feature {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 16px;
  color: $text-primary;
  
  svg { color: $primary-color; }
}

.form-card {
  background: #fff;
  border-radius: 24px;
  padding: 40px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-group {
  margin-bottom: 24px;
  
  label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 10px;
    color: $text-primary;
    
    .required { color: #ef4444; }
  }
  
  input, textarea {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid #eee;
    border-radius: 12px;
    font-size: 15px;
    transition: border-color 0.3s;
    
    &:focus {
      outline: none;
      border-color: $primary-color;
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
  gap: 8px;
  padding: 16px;
  background: $primary-color;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 500;
  color: #fff;
  cursor: pointer;
  transition: all 0.3s ease;
  
  &:hover {
    background: darken($primary-color, 5%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(7, 193, 96, 0.3);
  }
}
</style>
