<template>
  <div class="job-list-page">
    <div class="top-action">
      <t-button block theme="primary" @click="addJob">发布招聘</t-button>
    </div>

    <div class="job-list">
      <div class="job-card" v-for="job in jobs" :key="job.id">
        <div class="job-header">
          <div class="title">{{ job.title }}</div>
          <div class="salary">{{ job.salary }}</div>
        </div>
        <div class="job-content">
          <div class="tags">
            <span class="tag" v-for="tag in job.tags" :key="tag">{{ tag }}</span>
          </div>
          <div class="meta">
            <span>发布于 {{ job.publishTime }}</span>
            <span>{{ job.viewCount }}次浏览</span>
            <span>{{ job.applyCount }}人申请</span>
          </div>
        </div>
        <div class="job-footer">
          <div class="status" :class="{ active: job.isActive }">
            {{ job.isActive ? '招聘中' : '已关闭' }}
          </div>
          <div class="actions">
            <t-button size="small" variant="text" @click="viewApply(job)">查看申请</t-button>
            <t-button size="small" variant="text" @click="editJob(job)">编辑</t-button>
            <t-button size="small" variant="text" :theme="job.isActive ? 'danger' : 'primary'" @click="toggleJob(job)">
              {{ job.isActive ? '关闭' : '开启' }}
            </t-button>
          </div>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!jobs.length">
      <FileIcon size="48px" />
      <p>暂无招聘信息</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'
import { FileIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const jobs = ref([
  { id: 1, title: '高级推拿师', salary: '8000-15000', tags: ['五险一金', '包住', '高提成'], publishTime: '2天前', viewCount: 128, applyCount: 5, isActive: true },
  { id: 2, title: '足疗技师', salary: '6000-12000', tags: ['急招', '高提成'], publishTime: '5天前', viewCount: 86, applyCount: 3, isActive: true }
])

const addJob = () => router.push('/merchant/job/edit')
const editJob = (job) => router.push(`/merchant/job/edit/${job.id}`)
const viewApply = (job) => Toast({ message: '申请列表开发中', theme: 'warning' })
const toggleJob = (job) => { job.isActive = !job.isActive; Toast({ message: job.isActive ? '已开启招聘' : '已关闭招聘', theme: 'success' }) }
</script>

<style lang="scss" scoped>
$primary: #07c160;

.job-list-page { min-height: 100vh; background: #f5f5f5; padding: 15px; }
.top-action { margin-bottom: 15px; }
.job-list { display: flex; flex-direction: column; gap: 12px; }

.job-card { background: #fff; border-radius: 12px; padding: 15px; }

.job-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  .title { font-size: 16px; font-weight: 500; color: #1a1a1a; }
  .salary { font-size: 16px; font-weight: 600; color: #f44336; }
}

.job-content {
  .tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 10px;
    .tag { font-size: 11px; padding: 3px 8px; background: #f0fff4; color: $primary; border-radius: 4px; }
  }
  .meta {
    display: flex;
    gap: 15px;
    font-size: 12px;
    color: #999;
  }
}

.job-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #f5f5f5;
  
  .status {
    font-size: 12px;
    padding: 4px 10px;
    background: #f5f5f5;
    color: #999;
    border-radius: 12px;
    &.active { background: #e8f5e9; color: $primary; }
  }
  .actions { display: flex; gap: 8px; }
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
