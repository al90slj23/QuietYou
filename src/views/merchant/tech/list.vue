<template>
  <div class="tech-list-page">
    <!-- 顶部操作 -->
    <div class="top-actions">
      <t-button theme="primary" @click="goAdd">添加技师</t-button>
      <t-button variant="outline" @click="goBorrow">借调技师</t-button>
    </div>

    <!-- 技师列表 -->
    <div class="tech-list">
      <div class="tech-card" v-for="tech in techList" :key="tech.id">
        <div class="tech-info">
          <t-avatar :image="tech.avatar" size="48px" />
          <div class="info">
            <div class="name">
              {{ tech.name }}
              <span class="badge" :class="{ certified: tech.isCertified }">
                {{ tech.isCertified ? '已认证' : '未认证' }}
              </span>
            </div>
            <div class="meta">{{ tech.skills }} · {{ tech.experience }}年经验</div>
          </div>
        </div>
        <div class="tech-stats">
          <div class="stat">
            <span class="value">{{ tech.orderCount }}</span>
            <span class="label">订单</span>
          </div>
          <div class="stat">
            <span class="value">{{ tech.rating }}</span>
            <span class="label">评分</span>
          </div>
          <div class="stat">
            <span class="value">{{ tech.repeatRate }}%</span>
            <span class="label">回头率</span>
          </div>
        </div>
        <div class="tech-footer">
          <div class="status" :class="{ online: tech.isOnline }">
            {{ tech.isOnline ? '在线' : '离线' }}
          </div>
          <div class="actions">
            <t-button size="small" variant="text" @click="viewDetail(tech)">详情</t-button>
            <t-button size="small" variant="text" theme="danger" @click="removeTech(tech)">移除</t-button>
          </div>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!techList.length">
      <UserIcon size="48px" />
      <p>暂无技师</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { UserIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const techList = ref([
  { id: 1, avatar: '', name: '张师傅', isCertified: true, skills: '推拿、足疗', experience: 5, orderCount: 328, rating: 4.9, repeatRate: 68, isOnline: true },
  { id: 2, avatar: '', name: '李师傅', isCertified: true, skills: '肩颈、刮痧', experience: 3, orderCount: 186, rating: 4.7, repeatRate: 55, isOnline: true },
  { id: 3, avatar: '', name: '王师傅', isCertified: false, skills: '足疗', experience: 2, orderCount: 92, rating: 4.5, repeatRate: 42, isOnline: false }
])

const goAdd = () => router.push('/merchant/tech/add')
const goBorrow = () => router.push('/merchant/tech/borrow')
const viewDetail = (tech) => Toast({ message: '技师详情开发中', theme: 'warning' })
const removeTech = (tech) => Toast({ message: '确认移除该技师？', theme: 'warning' })
</script>

<style lang="scss" scoped>
$primary: #07c160;

.tech-list-page { min-height: 100vh; background: #f5f5f5; padding: 15px; }

.top-actions { display: flex; gap: 12px; margin-bottom: 15px; button { flex: 1; } }

.tech-list { display: flex; flex-direction: column; gap: 12px; }

.tech-card { background: #fff; border-radius: 12px; padding: 15px; }

.tech-info {
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
      
      .badge {
        font-size: 10px;
        padding: 2px 6px;
        background: #f5f5f5;
        color: #999;
        border-radius: 4px;
        &.certified { background: #e8f5e9; color: $primary; }
      }
    }
    .meta { font-size: 13px; color: #999; margin-top: 4px; }
  }
}

.tech-stats {
  display: flex;
  justify-content: space-around;
  padding: 15px 0;
  border-top: 1px solid #f5f5f5;
  border-bottom: 1px solid #f5f5f5;
  
  .stat {
    text-align: center;
    .value { font-size: 18px; font-weight: 600; color: #1a1a1a; }
    .label { font-size: 12px; color: #999; margin-top: 4px; display: block; }
  }
}

.tech-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  
  .status {
    font-size: 12px;
    padding: 4px 10px;
    background: #f5f5f5;
    color: #999;
    border-radius: 12px;
    &.online { background: #e8f5e9; color: $primary; }
  }
  
  .actions { display: flex; gap: 8px; }
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
