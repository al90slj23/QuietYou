<template>
  <div class="job-list-page">
    <!-- 筛选栏 -->
    <div class="filter-bar">
      <div class="filter-item" :class="{ active: filter === 'all' }" @click="filter = 'all'">全部</div>
      <div class="filter-item" :class="{ active: filter === 'nearby' }" @click="filter = 'nearby'">附近</div>
      <div class="filter-item" :class="{ active: filter === 'high_salary' }" @click="filter = 'high_salary'">高薪</div>
      <div class="filter-item" :class="{ active: filter === 'urgent' }" @click="filter = 'urgent'">急招</div>
    </div>

    <!-- 招聘列表 -->
    <div class="job-list">
      <div class="job-card" v-for="job in jobs" :key="job.id" @click="showDetail(job)">
        <div class="job-header">
          <div class="shop-info">
            <t-avatar :image="job.shopLogo" size="44px" shape="round" />
            <div class="info">
              <div class="shop-name">{{ job.shopName }}</div>
              <div class="location">
                <LocationIcon size="12px" />
                <span>{{ job.distance }}</span>
              </div>
            </div>
          </div>
          <div class="salary">{{ job.salary }}</div>
        </div>
        <div class="job-content">
          <div class="title">{{ job.title }}</div>
          <div class="tags">
            <span class="tag" v-for="tag in job.tags" :key="tag">{{ tag }}</span>
          </div>
          <div class="requirements">{{ job.requirements }}</div>
        </div>
        <div class="job-footer">
          <span class="time">{{ job.publishTime }}</span>
          <t-button size="small" theme="primary" @click.stop="applyJob(job)">立即申请</t-button>
        </div>
      </div>
    </div>

    <!-- 空状态 -->
    <div class="empty" v-if="!jobs.length">
      <WorkIcon size="48px" />
      <p>暂无招聘信息</p>
    </div>

    <!-- 职位详情弹窗 -->
    <t-popup v-model="showPopup" placement="bottom" :close-on-overlay-click="true">
      <div class="detail-popup" v-if="currentJob">
        <div class="popup-header">
          <span class="title">职位详情</span>
          <CloseIcon size="20px" @click="showPopup = false" />
        </div>
        <div class="popup-content">
          <div class="shop-section">
            <t-avatar :image="currentJob.shopLogo" size="56px" shape="round" />
            <div class="info">
              <div class="shop-name">{{ currentJob.shopName }}</div>
              <div class="address">{{ currentJob.address }}</div>
            </div>
          </div>
          <div class="job-title">{{ currentJob.title }}</div>
          <div class="salary-range">{{ currentJob.salary }}</div>
          <div class="section">
            <div class="section-title">职位要求</div>
            <div class="section-content">{{ currentJob.requirements }}</div>
          </div>
          <div class="section">
            <div class="section-title">福利待遇</div>
            <div class="benefits">
              <span class="benefit" v-for="b in currentJob.benefits" :key="b">{{ b }}</span>
            </div>
          </div>
          <div class="section">
            <div class="section-title">工作时间</div>
            <div class="section-content">{{ currentJob.workTime }}</div>
          </div>
        </div>
        <div class="popup-footer">
          <t-button block theme="primary" size="large" @click="applyJob(currentJob)">立即申请</t-button>
        </div>
      </div>
    </t-popup>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Avatar as TAvatar, Button as TButton, Popup as TPopup, Toast } from 'tdesign-mobile-vue'
import { LocationIcon, CloseIcon } from 'tdesign-icons-vue-next'

// WorkIcon 用 LocationIcon 代替
const WorkIcon = LocationIcon

const filter = ref('all')
const showPopup = ref(false)
const currentJob = ref(null)

const jobs = ref([
  {
    id: 1,
    shopName: '悦享养生馆',
    shopLogo: '',
    distance: '1.2km',
    salary: '8000-15000',
    title: '高级推拿师',
    tags: ['五险一金', '包住', '高提成'],
    requirements: '3年以上推拿经验，持有相关资格证书优先',
    publishTime: '2小时前',
    address: '成都市武侯区天府大道100号',
    benefits: ['五险一金', '包住宿', '带薪年假', '节日福利'],
    workTime: '10:00-22:00，月休4天'
  },
  {
    id: 2,
    shopName: '康乐足道',
    shopLogo: '',
    distance: '2.5km',
    salary: '6000-12000',
    title: '足疗技师',
    tags: ['急招', '高提成', '弹性工作'],
    requirements: '1年以上足疗经验，形象气质佳',
    publishTime: '5小时前',
    address: '成都市高新区软件园B区',
    benefits: ['高提成', '弹性工作', '免费培训'],
    workTime: '12:00-24:00，排班制'
  },
  {
    id: 3,
    shopName: '舒心堂',
    shopLogo: '',
    distance: '3.8km',
    salary: '10000-20000',
    title: '资深按摩师',
    tags: ['高薪', '管理岗', '股权激励'],
    requirements: '5年以上经验，有团队管理经验优先',
    publishTime: '1天前',
    address: '成都市锦江区春熙路',
    benefits: ['高底薪', '股权激励', '晋升空间大'],
    workTime: '9:00-21:00，双休'
  }
])

const showDetail = (job) => {
  currentJob.value = job
  showPopup.value = true
}

const applyJob = (job) => {
  Toast({ message: '申请已提交，请等待商家联系', theme: 'success' })
  showPopup.value = false
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.job-list-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.filter-bar {
  display: flex;
  background: #fff;
  padding: 12px 15px;
  gap: 20px;
  position: sticky;
  top: 0;
  z-index: 10;
}

.filter-item {
  font-size: 14px;
  color: #666;
  cursor: pointer;
  padding: 4px 0;
  border-bottom: 2px solid transparent;
  
  &.active {
    color: $primary;
    border-color: $primary;
    font-weight: 500;
  }
}

.job-list {
  padding: 15px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.job-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
}

.job-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.shop-info {
  display: flex;
  gap: 10px;
  
  .info {
    .shop-name { font-size: 15px; font-weight: 500; color: #1a1a1a; }
    .location {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: #999;
      margin-top: 4px;
    }
  }
}

.salary {
  font-size: 16px;
  font-weight: 600;
  color: #f44336;
}

.job-content {
  .title {
    font-size: 16px;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 10px;
  }
  
  .tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 10px;
    
    .tag {
      font-size: 11px;
      padding: 3px 8px;
      background: #f0fff4;
      color: $primary;
      border-radius: 4px;
    }
  }
  
  .requirements {
    font-size: 13px;
    color: #666;
    line-height: 1.5;
  }
}

.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #f0f0f0;
  
  .time { font-size: 12px; color: #999; }
}

.empty {
  text-align: center;
  padding: 60px 0;
  color: #999;
  p { margin-top: 12px; font-size: 14px; }
}

.detail-popup {
  max-height: 80vh;
  display: flex;
  flex-direction: column;
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f0f0f0;
  
  .title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
}

.popup-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
}

.shop-section {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
  
  .info {
    .shop-name { font-size: 16px; font-weight: 500; color: #1a1a1a; }
    .address { font-size: 13px; color: #999; margin-top: 4px; }
  }
}

.job-title {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 8px;
}

.salary-range {
  font-size: 24px;
  font-weight: 700;
  color: #f44336;
  margin-bottom: 20px;
}

.section {
  margin-bottom: 20px;
  
  .section-title {
    font-size: 14px;
    color: #999;
    margin-bottom: 8px;
  }
  
  .section-content {
    font-size: 14px;
    color: #333;
    line-height: 1.6;
  }
}

.benefits {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  
  .benefit {
    font-size: 13px;
    padding: 6px 12px;
    background: #f5f5f5;
    border-radius: 4px;
    color: #666;
  }
}

.popup-footer {
  padding: 15px;
  border-top: 1px solid #f0f0f0;
}
</style>
