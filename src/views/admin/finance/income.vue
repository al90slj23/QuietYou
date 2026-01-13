<template>
  <div class="page-container">
    <!-- 统计卡片 -->
    <div class="stat-cards">
      <div class="stat-card">
        <div class="stat-label">今日收入</div>
        <div class="stat-value">¥{{ stats.today.toLocaleString() }}</div>
        <div class="stat-trend up"><ArrowUpIcon /> +15.2%</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">本周收入</div>
        <div class="stat-value">¥{{ stats.week.toLocaleString() }}</div>
        <div class="stat-trend up"><ArrowUpIcon /> +8.6%</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">本月收入</div>
        <div class="stat-value">¥{{ stats.month.toLocaleString() }}</div>
        <div class="stat-trend up"><ArrowUpIcon /> +12.3%</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">累计收入</div>
        <div class="stat-value">¥{{ stats.total.toLocaleString() }}</div>
      </div>
    </div>

    <!-- 筛选 -->
    <div class="search-bar">
      <t-date-range-picker v-model="dateRange" placeholder="选择日期范围" style="width: 280px" />
      <t-select v-model="incomeType" placeholder="收入类型" clearable style="width: 140px">
        <t-option value="order" label="订单收入" />
        <t-option value="commission" label="平台佣金" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">查询</t-button>
      <t-button variant="outline" @click="handleExport">导出</t-button>
    </div>

    <!-- 收入明细 -->
    <div class="table-card">
      <t-table :data="incomeList" :columns="columns" row-key="id" hover>
        <template #type="{ row }">
          <t-tag :theme="row.type === 'order' ? 'primary' : 'success'" variant="light">
            {{ row.type === 'order' ? '订单收入' : '平台佣金' }}
          </t-tag>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  DateRangePicker as TDateRangePicker, Select as TSelect, Option as TOption,
  Button as TButton, Table as TTable, Tag as TTag, Pagination as TPagination, MessagePlugin
} from 'tdesign-vue-next'
import { ArrowUpIcon } from 'tdesign-icons-vue-next'

const dateRange = ref([])
const incomeType = ref('')
const pagination = reactive({ current: 1, total: 100 })

const stats = ref({
  today: 48560,
  week: 286420,
  month: 1256800,
  total: 15680000
})

const incomeList = ref([
  { id: 1, orderNo: 'QY20260113001', type: 'order', amount: 298, commission: 29.8, techName: '李师傅', createdAt: '2026-01-13 14:30' },
  { id: 2, orderNo: 'QY20260113002', type: 'order', amount: 198, commission: 19.8, techName: '陈师傅', createdAt: '2026-01-13 14:15' },
  { id: 3, orderNo: 'QY20260113003', type: 'commission', amount: 168, commission: 16.8, techName: '张师傅', createdAt: '2026-01-13 13:50' },
  { id: 4, orderNo: 'QY20260113004', type: 'order', amount: 398, commission: 39.8, techName: '王师傅', createdAt: '2026-01-13 13:30' }
])

const columns = [
  { colKey: 'orderNo', title: '订单号', width: 160 },
  { colKey: 'type', title: '类型', width: 120 },
  { colKey: 'amount', title: '订单金额', width: 120, cell: (_, { row }) => `¥${row.amount}` },
  { colKey: 'commission', title: '平台佣金', width: 120, cell: (_, { row }) => `¥${row.commission}` },
  { colKey: 'techName', title: '技师', width: 100 },
  { colKey: 'createdAt', title: '时间', width: 160 }
]

const handleSearch = () => { pagination.current = 1 }
const handleExport = () => { MessagePlugin.success('导出成功') }
</script>

<style lang="scss" scoped>
$primary: #07c160;
.stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px; }
.stat-card { background: #fff; border-radius: 8px; padding: 20px; }
.stat-label { font-size: 14px; color: #999; margin-bottom: 8px; }
.stat-value { font-size: 28px; font-weight: 600; color: #1a1a1a; }
.stat-trend { font-size: 13px; margin-top: 8px; display: flex; align-items: center; gap: 4px; &.up { color: $primary; } }
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
</style>
