<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.orderNo" placeholder="订单号" clearable style="width: 180px" />
      <t-input v-model="searchForm.userName" placeholder="用户" clearable style="width: 120px" />
      <t-select v-model="searchForm.status" placeholder="订单状态" clearable style="width: 140px">
        <t-option value="pending" label="待接单" />
        <t-option value="accepted" label="已接单" />
        <t-option value="serving" label="服务中" />
        <t-option value="completed" label="已完成" />
        <t-option value="cancelled" label="已取消" />
      </t-select>
      <t-date-range-picker v-model="searchForm.dateRange" placeholder="下单时间" style="width: 260px" />
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
      <t-button variant="outline" @click="handleReset">重置</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="orderList" :columns="columns" row-key="id" hover>
        <template #status="{ row }">
          <t-tag :theme="statusTheme[row.status]" variant="light">{{ statusText[row.status] }}</t-tag>
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="viewDetail(row)">详情</t-button>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>

    <!-- 详情弹窗 -->
    <t-dialog v-model:visible="detailVisible" header="订单详情" width="700px" :footer="false">
      <div class="order-detail" v-if="currentOrder">
        <div class="detail-section">
          <h4>订单信息</h4>
          <div class="detail-grid">
            <div class="detail-item"><span class="label">订单号</span><span>{{ currentOrder.orderNo }}</span></div>
            <div class="detail-item"><span class="label">状态</span><t-tag :theme="statusTheme[currentOrder.status]" variant="light">{{ statusText[currentOrder.status] }}</t-tag></div>
            <div class="detail-item"><span class="label">下单时间</span><span>{{ currentOrder.createdAt }}</span></div>
          </div>
        </div>
        <div class="detail-section">
          <h4>服务信息</h4>
          <div class="detail-grid">
            <div class="detail-item"><span class="label">服务项目</span><span>{{ currentOrder.serviceName }}</span></div>
            <div class="detail-item"><span class="label">服务时长</span><span>{{ currentOrder.duration }}分钟</span></div>
            <div class="detail-item"><span class="label">订单金额</span><span class="price">¥{{ currentOrder.amount }}</span></div>
          </div>
        </div>
        <div class="detail-section">
          <h4>用户信息</h4>
          <div class="detail-grid">
            <div class="detail-item"><span class="label">用户</span><span>{{ currentOrder.userName }}</span></div>
            <div class="detail-item"><span class="label">手机号</span><span>{{ currentOrder.userPhone }}</span></div>
            <div class="detail-item"><span class="label">地址</span><span>{{ currentOrder.address }}</span></div>
          </div>
        </div>
        <div class="detail-section">
          <h4>技师信息</h4>
          <div class="detail-grid">
            <div class="detail-item"><span class="label">技师</span><span>{{ currentOrder.techName }}</span></div>
            <div class="detail-item"><span class="label">手机号</span><span>{{ currentOrder.techPhone }}</span></div>
          </div>
        </div>
      </div>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Input as TInput, Select as TSelect, Option as TOption, Button as TButton,
  DateRangePicker as TDateRangePicker, Table as TTable, Tag as TTag, 
  Pagination as TPagination, Dialog as TDialog
} from 'tdesign-vue-next'

const detailVisible = ref(false)
const currentOrder = ref(null)

const searchForm = reactive({ orderNo: '', userName: '', status: '', dateRange: [] })
const pagination = reactive({ current: 1, total: 100 })

const statusTheme = { pending: 'warning', accepted: 'primary', serving: 'success', completed: 'default', cancelled: 'danger' }
const statusText = { pending: '待接单', accepted: '已接单', serving: '服务中', completed: '已完成', cancelled: '已取消' }

const orderList = ref([
  { id: 1, orderNo: 'QY20260113001', userName: '张**', userPhone: '138****1234', techName: '李师傅', techPhone: '139****5678', serviceName: '全身推拿', duration: 60, amount: 298, status: 'completed', address: '成都市武侯区天府大道100号', createdAt: '2026-01-13 14:30' },
  { id: 2, orderNo: 'QY20260113002', userName: '王**', userPhone: '137****9012', techName: '陈师傅', techPhone: '136****3456', serviceName: '肩颈按摩', duration: 45, amount: 198, status: 'serving', address: '成都市高新区软件园B区', createdAt: '2026-01-13 14:15' },
  { id: 3, orderNo: 'QY20260113003', userName: '李**', userPhone: '135****7890', techName: '张师傅', techPhone: '134****1234', serviceName: '足底按摩', duration: 60, amount: 168, status: 'accepted', address: '成都市锦江区春熙路', createdAt: '2026-01-13 13:50' },
  { id: 4, orderNo: 'QY20260113004', userName: '赵**', userPhone: '133****5678', techName: '王师傅', techPhone: '132****9012', serviceName: '全身SPA', duration: 90, amount: 398, status: 'pending', address: '成都市青羊区人民公园', createdAt: '2026-01-13 13:30' },
  { id: 5, orderNo: 'QY20260113005', userName: '刘**', userPhone: '131****3456', techName: '周师傅', techPhone: '130****7890', serviceName: '头部按摩', duration: 30, amount: 128, status: 'cancelled', address: '成都市金牛区万达广场', createdAt: '2026-01-13 12:45' }
])

const columns = [
  { colKey: 'orderNo', title: '订单号', width: 160 },
  { colKey: 'userName', title: '用户', width: 80 },
  { colKey: 'techName', title: '技师', width: 80 },
  { colKey: 'serviceName', title: '服务项目', width: 120 },
  { colKey: 'amount', title: '金额', width: 100, cell: (_, { row }) => `¥${row.amount}` },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '下单时间', width: 160 },
  { colKey: 'operation', title: '操作', width: 80, fixed: 'right' }
]

const handleSearch = () => { pagination.current = 1 }
const handleReset = () => { Object.assign(searchForm, { orderNo: '', userName: '', status: '', dateRange: [] }) }
const viewDetail = (row) => { currentOrder.value = row; detailVisible.value = true }
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; flex-wrap: wrap; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
.order-detail {
  .detail-section { margin-bottom: 24px; h4 { font-size: 14px; color: #1a1a1a; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0; } }
  .detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
  .detail-item { .label { color: #999; margin-right: 8px; } .price { color: #f44336; font-weight: 600; } }
}
</style>
