<template>
  <div class="recruit-mobile">
    <section class="banner">
      <h1>商户合作</h1>
      <p>MERCHANT COOPERATION</p>
    </section>

    <section class="cities">
      <h2>开放城市</h2>
      <div class="region" v-for="region in regions" :key="region.name">
        <h3>{{ region.name }}</h3>
        <div class="province" v-for="province in region.provinces" :key="province.name">
          <span class="name">{{ province.name }}：</span>
          <span class="list">{{ province.cities.join('、') }}</span>
        </div>
      </div>
    </section>

    <section class="form-section">
      <h2>入驻申请</h2>
      <div class="form">
        <div class="field">
          <label>店铺名称 *</label>
          <input type="text" v-model="form.shopName" placeholder="请输入店铺名称" />
        </div>
        <div class="field">
          <label>联系人 *</label>
          <input type="text" v-model="form.contactName" placeholder="请输入联系人姓名" />
        </div>
        <div class="field">
          <label>手机号码 *</label>
          <input type="tel" v-model="form.phone" placeholder="请输入手机号码" />
        </div>
        <div class="field">
          <label>所在城市 *</label>
          <input type="text" v-model="form.city" placeholder="请输入所在城市" />
        </div>
        <div class="field">
          <label>店铺地址</label>
          <input type="text" v-model="form.address" placeholder="请输入详细地址" />
        </div>
        <div class="field">
          <label>备注说明</label>
          <textarea v-model="form.remark" placeholder="请输入备注信息" rows="3"></textarea>
        </div>
        <button class="btn btn-primary" @click="submitForm">提交申请</button>
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
    { name: '重庆', cities: ['重庆'] }
  ]},
  { name: '华东地区', provinces: [
    { name: '上海', cities: ['上海'] },
    { name: '浙江', cities: ['杭州', '宁波', '温州'] }
  ]},
  { name: '华南地区', provinces: [
    { name: '广东', cities: ['深圳', '广州', '东莞'] }
  ]}
])

const submitForm = () => {
  if (!form.value.shopName || !form.value.contactName || !form.value.phone || !form.value.city) {
    alert('请填写必填项')
    return
  }
  alert('提交成功！')
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; letter-spacing: 2px; } }

.cities { padding: 20px; h2 { font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #333; } }
.region { background: #f9fafb; border-radius: 10px; padding: 16px; margin-bottom: 12px; h3 { font-size: 15px; font-weight: 600; color: $primary; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #e5e5e5; } }
.province { font-size: 13px; margin-bottom: 8px; .name { font-weight: 500; color: #333; } .list { color: #666; } }

.form-section { padding: 20px; h2 { font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #333; } }
.form { background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; }
.field { margin-bottom: 16px; label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #333; } input, textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; &:focus { outline: none; border-color: $primary; } } }
.btn { width: 100%; padding: 12px; font-size: 15px; }
</style>
