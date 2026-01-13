<template>
  <div class="dashboard-page">
    <div class="stat-cards">
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #07c160, #10b981)">
          <UserIcon />
        </div>
        <div class="stat-info">
          <div class="stat-value">{{ stats.userCount.toLocaleString() }}</div>
          <div class="stat-label">注册用户</div>
        </div>
        <div class="stat-trend up"><ArrowUpIcon /> +12.5%</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #6366f1)">
          <UserBusinessIcon />
        </div>
        <div class="stat-info">
          <div class="stat-value">{{ stats.techCount.toLocaleString() }}</div>
          <div class="stat-label">认证技师</div>
        </div>
        <div class="stat-trend up"><ArrowUpIcon /> +8.3%</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #f97316)">
          <ShopIcon />
        </div>
        <div class="stat-info">
          <div class="stat-value">{{ stats.merchantCount.toLocaleString() }}</div>
          <div class="stat-label">入驻商户</div>
        </div>
        <div class="stat-trend up"><ArrowUpIcon /> +5.2%</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #f43f5e)">
          <FileIcon />
        </div>
        <div class="stat-info">
          <div class="stat-value">{{ stats.orderCount.toLocaleString() }}</div>
          <div class="stat-label">总订单数</div>
        </div>
        <div class="stat-trend up"><ArrowUpIcon /> +18.6%</div>
      </div>
    </div>

    <div class="section-row">
      <div class="section-card">
        <div class="card-header"><h3>今日数据</h3><span class="date">{{ todayDate }}</span></div>
        <div class="today-grid">
          <div class="today-item"><div class="today-value">{{ today.newUsers }}</div><div class="today-label">新增用户</div></div>
          <div class="today-item"><div class="today-value">{{ today.newOrders }}</div><div class="today-label">新增订单</div></div>
          <div class="today-item"><div class="today-value">¥{{ today.income.toLocaleString() }}</div><div class="today-label">订单金额</div></div>
          <div class="today-item"><div class="today-value">{{ today.completedOrders }}</div><div class="today-label">完成订单</div></div>
        </div>
      </div>
      <div class="section-card">
        <div class="card-header"><h3>待处理事项</h3></div>
        <div class="task-list">
          <div class="task-item" @click="goTo('/admin/tech/certification')"><div class="task-info"><span class="task-name">技师认证审核</span><span class="task-count">{{ pending.certification }}</span></div><ChevronRightIcon /></div>
          <div class="task-item" @click="goTo('/admin/order/refund')"><div class="task-info"><span class="task-name">退款申请处理</span><span class="task-count">{{ pending.refund }}</span></div><ChevronRightIcon /></div>
          <div class="task-item" @click="goTo('/admin/finance/withdraw')"><div class="task-info"><span class="task-name">提现申请审核</span><span class="task-count">{{ pending.withdraw }}</span></div><ChevronRightIcon /></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { UserIcon, UserBusinessIcon, ShopIcon, FileIcon, ArrowUpIcon, ChevronRightIcon } from 'tdesign-icons-vue-next'

const router = useRouter()
const stats = ref({ userCount: 12580, techCount: 386, merchantCount: 128, orderCount: 45620 })
const today = ref({ newUsers: 86, newOrders: 234, income: 48560, completedOrders: 198 })
const pending = ref({ certification: 12, refund: 5, withdraw: 8 })
const todayDate = computed(() => { const d = new Date(); return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}` })
const goTo = (path) => router.push(path)
</script>

<style lang="scss" scoped>
$primary: #07c160;
.dashboard-page { }
.stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 24px; }
.stat-card { background: #fff; border-radius: 8px; padding: 24px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; }
.stat-info { flex: 1; }
.stat-value { font-size: 28px; font-weight: 600; color: #1a1a1a; }
.stat-label { font-size: 14px; color: #999; margin-top: 4px; }
.stat-trend { font-size: 13px; display: flex; align-items: center; gap: 4px; &.up { color: $primary; } }
.section-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
.section-card { background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; h3 { font-size: 16px; font-weight: 600; color: #1a1a1a; } .date { font-size: 14px; color: #999; } }
.today-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.today-item { padding: 16px; background: #f8f9fa; border-radius: 8px; text-align: center; }
.today-value { font-size: 24px; font-weight: 600; color: #1a1a1a; }
.today-label { font-size: 13px; color: #999; margin-top: 6px; }
.task-list { display: flex; flex-direction: column; }
.task-item { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f0f0f0; cursor: pointer; color: #999; &:last-child { border-bottom: none; } &:hover { color: $primary; .task-name { color: $primary; } } }
.task-info { display: flex; align-items: center; gap: 12px; }
.task-name { font-size: 14px; color: #1a1a1a; }
.task-count { font-size: 12px; padding: 2px 8px; background: #ff4d4f; color: #fff; border-radius: 10px; }
</style>
