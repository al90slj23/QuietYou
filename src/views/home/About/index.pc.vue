<template>
  <div class="about">
    <section class="page-banner">
      <div class="container">
        <h1>关于轻养到家</h1>
        <p class="subtitle">ABOUT US</p>
      </div>
    </section>

    <section class="intro section">
      <div class="container">
        <div class="section-title">
          <h2>公司简介</h2>
          <p class="subtitle">INTRODUCTION</p>
        </div>
        <div class="intro-content">
          <p>轻养到家是一家专注于上门养生推拿服务的O2O平台，致力于"让专业养生服务触手可及，为用户带来更健康、更舒适的生活体验"。</p>
          <p>我们秉承"用科技传递健康"的战略愿景，通过不断迭代的互联网系统、严格的服务培训及可追溯的服务品质，与众多专业技师一同为消费者提供便捷、安全、高效的健康服务。</p>
          <p>轻养到家始终以用户需求为中心，合规为先，注重技术研发与服务创新，不断提升服务体验。</p>
        </div>
      </div>
    </section>

    <section class="what-we-do section" style="background: #f9fafb;">
      <div class="container">
        <div class="section-title">
          <h2>轻养到家是什么</h2>
          <p class="subtitle">WHAT WE DO</p>
        </div>
        <div class="what-content">
          <p>轻养到家秉承"用科技传递健康"的战略愿景，践行"打造新型灵活就业+创业平台"的经营理念，构建轻养到家养生推拿直约平台，打造"ToB+ToC"的商业闭环。</p>
          <p>我们服务用户，提供O2O上门养生推拿，足不出户静享舒缓与健康；我们服务商户，提供全方位的专业培训、供应链、接单、通勤和安全体系，一键入驻开启灵活执业新纪元。</p>
          <p>同时，轻养到家作为合规运营的O2O上门养生服务平台，始终秉持合法经营理念，坚决抵制违规行为。</p>
        </div>
      </div>
    </section>

    <section class="professional section">
      <div class="container">
        <div class="section-title">
          <h2>专业服务</h2>
          <p class="subtitle">PROFESSIONAL SERVICES</p>
        </div>
        <div class="stats-grid">
          <div class="stat-card" v-for="stat in stats" :key="stat.label">
            <div class="stat-number">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
          </div>
        </div>
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
$primary-color: #07c160;

.page-banner { background: linear-gradient(135deg, $primary-color 0%, #10b981 100%); color: #fff; text-align: center; padding: 140px 0 80px; h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; } .subtitle { font-size: 14px; opacity: 0.8; letter-spacing: 3px; } }

.intro-content, .what-content { max-width: 900px; margin: 0 auto; p { font-size: 16px; line-height: 2; color: #555; margin-bottom: 20px; text-align: justify; &:last-child { margin-bottom: 0; } } }

.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
.stat-card { background: #f9fafb; padding: 50px 30px; border-radius: 16px; text-align: center; .stat-number { font-size: 48px; font-weight: 700; color: $primary-color; margin-bottom: 10px; } .stat-label { font-size: 16px; color: #666; } }
</style>
