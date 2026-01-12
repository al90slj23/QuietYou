<template>
  <div class="history">
    <section class="page-banner">
      <div class="container">
        <h1>发展历程</h1>
        <p class="subtitle">DEVELOPMENT HISTORY</p>
      </div>
    </section>

    <section class="timeline-section section">
      <div class="container">
        <div class="timeline">
          <div class="timeline-item" v-for="(item, index) in timeline" :key="item.year" :class="{ reverse: index % 2 === 1 }">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <div class="timeline-year">{{ item.year }}</div>
              <div class="timeline-card">
                <h3>{{ item.title }}</h3>
                <ul class="timeline-events">
                  <li v-for="event in item.events" :key="event">{{ event }}</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="vision section" style="background: #f9fafb;">
      <div class="container">
        <div class="section-title">
          <h2>未来展望</h2>
          <p class="subtitle">FUTURE VISION</p>
        </div>
        <div class="vision-content">
          <p>轻养到家将继续秉承"只看手艺，不碰擦边"的核心理念，专注于为用户、商户、技师三方提供更优质的连接服务。</p>
          <p>我们计划在未来三年内覆盖全国50+城市，建立完善的技师认证培训体系，打造按摩养生行业的标准化服务平台。</p>
          <p>通过持续的技术创新和服务升级，轻养到家致力于成为行业领先的健康服务平台，让专业养生服务触手可及。</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const timeline = [
  { year: '2026', title: '扬帆起航', events: ['轻养到家平台正式上线', '首批认证技师入驻平台', '开放首批合作城市', '建立技师培训认证体系'] },
  { year: '2027', title: '稳步发展（规划）', events: ['计划覆盖20+城市', '深化商户合作模式', '完善多维度评分体系', '推出技师职业发展通道'] },
  { year: '2028', title: '全面拓展（规划）', events: ['计划覆盖50+城市', '建立区域培训中心', '打造行业服务标准', '构建完整生态体系'] }
]
</script>

<style lang="scss" scoped>
$primary-color: #07c160;

.page-banner { background: linear-gradient(135deg, $primary-color 0%, #10b981 100%); color: #fff; text-align: center; padding: 140px 0 80px; h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; } .subtitle { font-size: 14px; opacity: 0.8; letter-spacing: 3px; } }

.timeline { position: relative; max-width: 900px; margin: 0 auto; padding: 40px 0; &::before { content: ''; position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, $primary-color, #10b981); transform: translateX(-50%); } }

.timeline-item { position: relative; display: flex; justify-content: flex-end; padding: 20px 0; &.reverse { justify-content: flex-start; .timeline-content { padding-left: calc(50% + 40px); padding-right: 0; text-align: left; } .timeline-year { text-align: left; } } }

.timeline-dot { position: absolute; left: 50%; top: 30px; width: 16px; height: 16px; background: $primary-color; border: 3px solid #fff; border-radius: 50%; transform: translateX(-50%); box-shadow: 0 0 0 4px rgba($primary-color, 0.2); z-index: 1; }

.timeline-content { width: 100%; padding-right: calc(50% + 40px); text-align: right; }
.timeline-year { font-size: 32px; font-weight: 700; color: $primary-color; margin-bottom: 15px; }
.timeline-card { background: #fff; padding: 25px 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); text-align: left; h3 { font-size: 20px; font-weight: 600; color: #333; margin-bottom: 15px; } }
.timeline-events { list-style: none; padding: 0; margin: 0; li { position: relative; padding-left: 20px; margin-bottom: 10px; font-size: 14px; color: #666; line-height: 1.6; &::before { content: ''; position: absolute; left: 0; top: 8px; width: 6px; height: 6px; background: $primary-color; border-radius: 50%; } &:last-child { margin-bottom: 0; } } }

.vision-content { max-width: 800px; margin: 0 auto; text-align: center; p { font-size: 16px; line-height: 2; color: #555; margin-bottom: 20px; &:last-child { margin-bottom: 0; } } }
</style>
