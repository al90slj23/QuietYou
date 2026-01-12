<template>
  <div class="borrow-tech-page">
    <!-- 筛选条件 -->
    <div class="filter-section">
      <div class="filter-item" @click="showSkillPicker = true">
        <span>{{ selectedSkill || '技能' }}</span>
        <ChevronDownIcon size="16px" />
      </div>
      <div class="filter-item" @click="showDistancePicker = true">
        <span>{{ selectedDistance || '距离' }}</span>
        <ChevronDownIcon size="16px" />
      </div>
      <div class="filter-item" @click="showSortPicker = true">
        <span>{{ selectedSort || '排序' }}</span>
        <ChevronDownIcon size="16px" />
      </div>
    </div>

    <!-- 技师列表 -->
    <div class="tech-list">
      <div class="tech-card" v-for="tech in techList" :key="tech.id">
        <div class="tech-header">
          <t-avatar :image="tech.avatar" size="56px" />
          <div class="info">
            <div class="name">
              {{ tech.name }}
              <span class="badge certified">已认证</span>
            </div>
            <div class="shop">
              <ShopIcon size="12px" />
              <span>{{ tech.shopName }}</span>
              <span class="distance">{{ tech.distance }}</span>
            </div>
            <div class="skills">{{ tech.skills }}</div>
          </div>
        </div>
        <div class="tech-stats">
          <div class="stat">
            <span class="label">评分</span>
            <span class="value">{{ tech.rating }}</span>
          </div>
          <div class="stat">
            <span class="label">订单</span>
            <span class="value">{{ tech.orderCount }}</span>
          </div>
          <div class="stat">
            <span class="label">回头率</span>
            <span class="value">{{ tech.repeatRate }}%</span>
          </div>
          <div class="stat">
            <span class="label">借调费</span>
            <span class="value price">¥{{ tech.borrowFee }}/单</span>
          </div>
        </div>
        <div class="tech-footer">
          <t-button block theme="primary" @click="requestBorrow(tech)">申请借调</t-button>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!techList.length">
      <UserIcon size="48px" />
      <p>暂无可借调技师</p>
    </div>

    <!-- 借调说明 -->
    <div class="tips">
      <InfoCircleIcon size="14px" />
      <span>借调技师需支付借调费给技师所属店铺，技师服务费另计</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { ChevronDownIcon, ShopIcon, UserIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

const showSkillPicker = ref(false)
const showDistancePicker = ref(false)
const showSortPicker = ref(false)
const selectedSkill = ref('')
const selectedDistance = ref('')
const selectedSort = ref('')

const techList = ref([
  { id: 1, avatar: '', name: '陈师傅', shopName: '康乐足道', distance: '1.2km', skills: '推拿、足疗、刮痧', rating: 4.9, orderCount: 520, repeatRate: 72, borrowFee: 50 },
  { id: 2, avatar: '', name: '刘师傅', shopName: '舒心堂', distance: '2.5km', skills: '肩颈、头疗', rating: 4.8, orderCount: 380, repeatRate: 65, borrowFee: 40 },
  { id: 3, avatar: '', name: '赵师傅', shopName: '养生阁', distance: '3.8km', skills: '推拿、拔罐', rating: 4.7, orderCount: 290, repeatRate: 58, borrowFee: 35 }
])

const requestBorrow = (tech) => {
  Toast({ message: '借调申请已发送，等待对方确认', theme: 'success' })
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.borrow-tech-page { min-height: 100vh; background: #f5f5f5; }

.filter-section {
  display: flex;
  background: #fff;
  padding: 12px 15px;
  gap: 15px;
  position: sticky;
  top: 0;
  z-index: 10;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  color: #666;
  cursor: pointer;
}

.tech-list { padding: 15px; display: flex; flex-direction: column; gap: 12px; }

.tech-card { background: #fff; border-radius: 12px; padding: 15px; }

.tech-header {
  display: flex;
  gap: 12px;
  margin-bottom: 15px;
  
  .info {
    flex: 1;
    .name {
      font-size: 16px;
      font-weight: 500;
      color: #1a1a1a;
      display: flex;
      align-items: center;
      gap: 8px;
      .badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; &.certified { background: #e8f5e9; color: $primary; } }
    }
    .shop {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: #999;
      margin-top: 6px;
      .distance { margin-left: auto; color: $primary; }
    }
    .skills { font-size: 13px; color: #666; margin-top: 6px; }
  }
}

.tech-stats {
  display: flex;
  justify-content: space-between;
  padding: 15px 0;
  border-top: 1px solid #f5f5f5;
  
  .stat {
    text-align: center;
    .label { font-size: 12px; color: #999; display: block; margin-bottom: 4px; }
    .value { font-size: 16px; font-weight: 600; color: #1a1a1a; &.price { color: #f44336; } }
  }
}

.tech-footer { margin-top: 15px; }

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }

.tips {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  font-size: 12px;
  color: #999;
  padding: 15px;
}
</style>
