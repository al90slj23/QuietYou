<template>
  <div class="recruit">
    <section class="page-banner">
      <div class="container">
        <h1>商户合作</h1>
        <p class="subtitle">MERCHANT COOPERATION</p>
      </div>
    </section>

    <section class="cities section">
      <div class="container">
        <div class="section-title">
          <h2>开放城市</h2>
          <p class="subtitle">OPEN CITIES</p>
        </div>
        <div class="city-regions">
          <div class="region" v-for="region in regions" :key="region.name">
            <h3 class="region-name">{{ region.name }}</h3>
            <div class="provinces">
              <div class="province" v-for="province in region.provinces" :key="province.name">
                <span class="province-name">{{ province.name }}</span>
                <span class="province-cities">{{ province.cities.join('、') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="form-section section">
      <div class="container">
        <div class="section-title">
          <h2>入驻申请</h2>
          <p class="subtitle">JOIN US</p>
        </div>
        <div class="form-card">
          <div class="form-group">
            <label>店铺名称 <span class="required">*</span></label>
            <input type="text" v-model="form.shopName" placeholder="请输入店铺名称" />
          </div>
          <div class="form-group">
            <label>联系人 <span class="required">*</span></label>
            <input type="text" v-model="form.contactName" placeholder="请输入联系人姓名" />
          </div>
          <div class="form-group">
            <label>手机号码 <span class="required">*</span></label>
            <input type="tel" v-model="form.phone" placeholder="请输入手机号码" />
          </div>
          <div class="form-group">
            <label>所在城市 <span class="required">*</span></label>
            <input type="text" v-model="form.city" placeholder="请输入所在城市" />
          </div>
          <div class="form-group">
            <label>店铺地址</label>
            <input type="text" v-model="form.address" placeholder="请输入详细地址" />
          </div>
          <div class="form-group">
            <label>备注说明</label>
            <textarea v-model="form.remark" placeholder="请输入备注信息（选填）" rows="3"></textarea>
          </div>
          <button class="btn btn-primary btn-block" @click="submitForm">提交申请</button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'

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
.page-banner { background: linear-gradient(135deg, $primary-color 0%, #10b981 100%); color: #fff; text-align: center; padding: 140px 0 80px; h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; } .subtitle { font-size: 14px; opacity: 0.8; letter-spacing: 3px; } }
.city-regions { display: flex; flex-direction: column; gap: 30px; }
.region { background: #f9fafb; border-radius: 12px; padding: 24px; }
.region-name { font-size: 18px; font-weight: 600; color: $primary-color; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e5e5; }
.provinces { display: flex; flex-direction: column; gap: 12px; }
.province { display: flex; flex-wrap: wrap; gap: 8px; .province-name { font-weight: 500; color: #333; min-width: 60px; } .province-cities { color: #666; font-size: 14px; } }
.form-card { max-width: 600px; margin: 0 auto; background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 40px; }
.form-group { margin-bottom: 20px; label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px; color: #333; .required { color: #ef4444; } } input, textarea { width: 100%; padding: 12px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; &:focus { outline: none; border-color: $primary-color; } } }
.btn-block { width: 100%; padding: 14px; font-size: 16px; }
</style>
