<template>
  <div class="history-mobile">
    <section class="banner">
      <h1>发展历程</h1>
      <p>DEVELOPMENT HISTORY</p>
    </section>

    <section class="timeline">
      <div class="timeline-item" v-for="item in timeline" :key="item.year">
        <div class="year">{{ item.year }}</div>
        <div class="card">
          <h3>{{ item.title }}</h3>
          <ul>
            <li v-for="event in item.events" :key="event">{{ event }}</li>
          </ul>
        </div>
      </div>
    </section>

    <section class="vision">
      <h2>未来展望</h2>
      <p>轻养到家将继续秉承"只看手艺，不碰擦边"的核心理念，专注于为用户、商户、技师三方提供更优质的连接服务。</p>
      <p>我们计划在未来三年内覆盖全国50+城市，建立完善的技师认证培训体系。</p>
    </section>
  </div>
</template>

<script setup>
const timeline = [
  { year: '2026', title: '扬帆起航', events: ['平台正式上线', '首批技师入驻', '开放首批城市', '建立培训体系'] },
  { year: '2027', title: '稳步发展', events: ['覆盖20+城市', '深化商户合作', '完善评分体系'] },
  { year: '2028', title: '全面拓展', events: ['覆盖50+城市', '建立培训中心', '打造行业标准'] }
]
</script>

<style lang="scss" scoped>
$primary: #07c160;

.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }

.timeline { padding: 20px; position: relative; &::before { content: ''; position: absolute; left: 30px; top: 20px; bottom: 20px; width: 2px; background: linear-gradient(to bottom, $primary, #10b981); } }

.timeline-item { position: relative; padding-left: 50px; margin-bottom: 20px; }
.year { position: absolute; left: 0; top: 0; width: 60px; height: 28px; background: $primary; color: #fff; font-size: 14px; font-weight: 600; border-radius: 14px; display: flex; align-items: center; justify-content: center; z-index: 1; }
.card { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 16px; margin-left: 20px; h3 { font-size: 15px; font-weight: 600; color: #333; margin-bottom: 10px; } ul { list-style: none; padding: 0; margin: 0; li { position: relative; padding-left: 14px; font-size: 13px; color: #666; margin-bottom: 6px; &::before { content: ''; position: absolute; left: 0; top: 6px; width: 4px; height: 4px; background: $primary; border-radius: 50%; } } } }

.vision { padding: 20px; background: #f9fafb; margin: 0 20px 20px; border-radius: 12px; h2 { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 12px; } p { font-size: 13px; color: #555; line-height: 1.8; margin-bottom: 10px; } }
</style>
