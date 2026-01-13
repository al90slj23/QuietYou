<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.orderNo" placeholder="订单号" clearable style="width: 180px" />
      <t-select v-model="searchForm.status" placeholder="状态" clearable style="width: 140px">
        <t-option value="pending" label="待处理" />
        <t-option value="approved" label="已退款" />
        <t-option value="rejected" label="已拒绝" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="refundList" :columns="columns" row-key="id" hover>
        <template #status="{ row }">
          <t-tag :theme="statusTheme[row.status]" variant="light">{{ statusText[row.status] }}</t-tag>
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="viewDetail(row)">查看</t-button>
          <template v-if="row.status === 'pending'">
            <t-button variant="text" theme="success" @click="handleApprove(row)">同意</t-button>
            <t-button variant="text" theme="danger" @click="handleReject(row)">拒绝</t-button>
          </template>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>

    <!-- 详情弹窗 -->
    <t-dialog v-model:visible="detailVisible" header="退款详情" width="600px" :footer="false">
      <div class="refund-detail" v-if="currentRefund">
        <div class="detail-row"><span class="label">订单号</span><span>{{ currentRefund.orderNo }}</span></div>
        <div class="detail-row"><span class="label">用户</span><span>{{ currentRefund.userName }}</span></div>
        <div class="detail-row"><span class="label">服务项目</span><span>{{ currentRefund.serviceName }}</span></div>
        <div class="detail-row"><span class="label">订单金额</span><span class="price">¥{{ currentRefund.orderAmount }}</span></div>
        <div class="detail-row"><span class="label">退款金额</span><span class="price">¥{{ currentRefund.refundAmount }}</span></div>
        <div class="detail-row"><span class="label">退款原因</span><span>{{ currentRefund.reason }}</span></div>
        <div class="detail-row"><span class="label">申请时间</span><span>{{ currentRefund.createdAt }}</span></div>
        <div class="detail-actions" v-if="currentRefund.status === 'pending'">
          <t-button theme="success" @click="handleApprove(currentRefund)">同意退款</t-button>
          <t-button theme="danger" variant="outline" @click="handleReject(currentRefund)">拒绝</t-button>
        </div>
      </div>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Input as TInput, Select as TSelect, Option as TOption, Button as TButton,
  Table as TTable, Tag as TTag, Pagination as TPagination, Dialog as TDialog, MessagePlugin
} from 'tdesign-vue-next'

const detailVisible = ref(false)
const currentRefund = ref(null)

const searchForm = reactive({ orderNo: '', status: '' })
const pagination = reactive({ current: 1, total: 30 })

const statusTheme = { pending: 'warning', approved: 'success', rejected: 'danger' }
const statusText = { pending: '待处理', approved: '已退款', rejected: '已拒绝' }

const refundList = ref([
  { id: 1, orderNo: 'QY20260112001', userName: '张**', serviceName: '全身推拿', orderAmount: 298, refundAmount: 298, reason: '技师迟到', status: 'pending', createdAt: '2026-01-13 10:30' },
  { id: 2, orderNo: 'QY20260111002', userName: '王**', serviceName: '肩颈按摩', orderAmount: 198, refundAmount: 198, reason: '临时有事', status: 'pending', createdAt: '2026-01-13 09:15' },
  { id: 3, orderNo: 'QY20260110003', userName: '李**', serviceName: '足底按摩', orderAmount: 168, refundAmount: 168, reason: '服务不满意', status: 'approved', createdAt: '2026-01-12 14:20' },
  { id: 4, orderNo: 'QY20260109004', userName: '赵**', serviceName: '全身SPA', orderAmount: 398, refundAmount: 398, reason: '重复下单', status: 'rejected', createdAt: '2026-01-11 16:45' }
])

const columns = [
  { colKey: 'orderNo', title: '订单号', width: 160 },
  { colKey: 'userName', title: '用户', width: 80 },
  { colKey: 'serviceName', title: '服务项目', width: 120 },
  { colKey: 'orderAmount', title: '订单金额', width: 100, cell: (_, { row }) => `¥${row.orderAmount}` },
  { colKey: 'refundAmount', title: '退款金额', width: 100, cell: (_, { row }) => `¥${row.refundAmount}` },
  { colKey: 'reason', title: '退款原因', width: 140 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '申请时间', width: 160 },
  { colKey: 'operation', title: '操作', width: 160, fixed: 'right' }
]

const handleSearch = () => { pagination.current = 1 }
const viewDetail = (row) => { currentRefund.value = row; detailVisible.value = true }
const handleApprove = (row) => { row.status = 'approved'; detailVisible.value = false; MessagePlugin.success('已同意退款') }
const handleReject = (row) => { row.status = 'rejected'; detailVisible.value = false; MessagePlugin.success('已拒绝退款') }
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
.refund-detail {
  .detail-row { display: flex; padding: 12px 0; border-bottom: 1px solid #f0f0f0; &:last-child { border-bottom: none; } .label { width: 100px; color: #999; } .price { color: #f44336; font-weight: 600; } }
  .detail-actions { display: flex; gap: 12px; justify-content: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #f0f0f0; }
}
</style>
