<template>
  <div class="about-mobile">
    <section class="banner">
      <h1>关于轻养到家</h1>
      <p>ABOUT US</p>
    </section>

    <section class="intro">
      <h2>公司简介</h2>
      <p>轻养到家是一家专注于上门养生推拿服务的O2O平台，致力于"让专业养生服务触手可及"。</p>
      <p>我们秉承"用科技传递健康"的战略愿景，为消费者提供便捷、安全、高效的健康服务。</p>
    </section>

    <section class="what-we-do">
      <h2>我们做什么</h2>
      <p>服务用户，提供O2O上门养生推拿；服务商户，提供全方位的专业培训、供应链、接单体系。</p>
    </section>

    <section class="stats">
      <div class="stat-item" v-for="stat in stats" :key="stat.label">
        <div class="value">{{ stat.value }}</div>
        <div class="label">{{ stat.label }}</div>
      </div>
    </section>
  </div>
</template>

<script setup>
const stats = [
  { value: '10万+', label: '服务用户' },
  { value: '1000+', label: '入驻商户' },
  { value: '20+', label: '覆盖城市' }
]
</script>

<style lang="scss" scoped>
$primary: #07c160;

.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }

.intro, .what-we-do { padding: 20px; h2 { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 12px; } p { font-size: 14px; color: #555; line-height: 1.8; margin-bottom: 10px; } }
.what-we-do { background: #f9fafb; margin: 0 20px; border-radius: 12px; }

.stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 20px; background: linear-gradient(135deg, $primary 0%, #10b981 100%); margin: 20px; border-radius: 12px; }
.stat-item { text-align: center; color: #fff; .value { font-size: 24px; font-weight: 700; } .label { font-size: 12px; opacity: 0.9; margin-top: 4px; } }
</style>
