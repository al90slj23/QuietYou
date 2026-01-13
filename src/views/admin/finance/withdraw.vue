<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索申请人" clearable style="width: 180px" />
      <t-select v-model="searchForm.type" placeholder="申请人类型" clearable style="width: 140px">
        <t-option value="tech" label="技师" />
        <t-option value="merchant" label="商户" />
      </t-select>
      <t-select v-model="searchForm.status" placeholder="状态" clearable style="width: 140px">
        <t-option value="pending" label="待审核" />
        <t-option value="approved" label="已打款" />
        <t-option value="rejected" label="已拒绝" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="withdrawList" :columns="columns" row-key="id" hover>
        <template #type="{ row }">
          <t-tag :theme="row.type === 'tech' ? 'primary' : 'warning'" variant="light">
            {{ row.type === 'tech' ? '技师' : '商户' }}
          </t-tag>
        </template>
        <template #status="{ row }">
          <t-tag :theme="statusTheme[row.status]" variant="light">{{ statusText[row.status] }}</t-tag>
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="viewDetail(row)">查看</t-button>
          <template v-if="row.status === 'pending'">
            <t-button variant="text" theme="success" @click="handleApprove(row)">通过</t-button>
            <t-button variant="text" theme="danger" @click="handleReject(row)">拒绝</t-button>
          </template>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>

    <!-- 详情弹窗 -->
    <t-dialog v-model:visible="detailVisible" header="提现详情" width="500px" :footer="false">
      <div class="withdraw-detail" v-if="currentWithdraw">
        <div class="detail-row"><span class="label">申请人</span><span>{{ currentWithdraw.name }}</span></div>
        <div class="detail-row"><span class="label">类型</span><span>{{ currentWithdraw.type === 'tech' ? '技师' : '商户' }}</span></div>
        <div class="detail-row"><span class="label">提现金额</span><span class="price">¥{{ currentWithdraw.amount.toLocaleString() }}</span></div>
        <div class="detail-row"><span class="label">银行卡</span><span>{{ currentWithdraw.bankCard }}</span></div>
        <div class="detail-row"><span class="label">开户行</span><span>{{ currentWithdraw.bankName }}</span></div>
        <div class="detail-row"><span class="label">申请时间</span><span>{{ currentWithdraw.createdAt }}</span></div>
        <div class="detail-actions" v-if="currentWithdraw.status === 'pending'">
          <t-button theme="success" @click="handleApprove(currentWithdraw)">确认打款</t-button>
          <t-button theme="danger" variant="outline" @click="handleReject(currentWithdraw)">拒绝</t-button>
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
const currentWithdraw = ref(null)

const searchForm = reactive({ keyword: '', type: '', status: '' })
const pagination = reactive({ current: 1, total: 50 })

const statusTheme = { pending: 'warning', approved: 'success', rejected: 'danger' }
const statusText = { pending: '待审核', approved: '已打款', rejected: '已拒绝' }

const withdrawList = ref([
  { id: 1, name: '张师傅', type: 'tech', amount: 5680, bankCard: '6222****1234', bankName: '中国银行', status: 'pending', createdAt: '2026-01-13 10:30' },
  { id: 2, name: '悦享养生馆', type: 'merchant', amount: 12800, bankCard: '6225****5678', bankName: '工商银行', status: 'pending', createdAt: '2026-01-13 09:15' },
  { id: 3, name: '李师傅', type: 'tech', amount: 3200, bankCard: '6228****9012', bankName: '建设银行', status: 'approved', createdAt: '2026-01-12 14:20' },
  { id: 4, name: '康乐足道', type: 'merchant', amount: 8600, bankCard: '6217****3456', bankName: '农业银行', status: 'rejected', createdAt: '2026-01-11 16:45' }
])

const columns = [
  { colKey: 'name', title: '申请人', width: 140 },
  { colKey: 'type', title: '类型', width: 100 },
  { colKey: 'amount', title: '提现金额', width: 120, cell: (_, { row }) => `¥${row.amount.toLocaleString()}` },
  { colKey: 'bankCard', title: '银行卡', width: 140 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '申请时间', width: 160 },
  { colKey: 'operation', title: '操作', width: 160, fixed: 'right' }
]

const handleSearch = () => { pagination.current = 1 }
const viewDetail = (row) => { currentWithdraw.value = row; detailVisible.value = true }
const handleApprove = (row) => { row.status = 'approved'; detailVisible.value = false; MessagePlugin.success('已确认打款') }
const handleReject = (row) => { row.status = 'rejected'; detailVisible.value = false; MessagePlugin.success('已拒绝') }
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
.withdraw-detail {
  .detail-row { display: flex; padding: 12px 0; border-bottom: 1px solid #f0f0f0; &:last-child { border-bottom: none; } .label { width: 80px; color: #999; } .price { color: #f44336; font-weight: 600; } }
  .detail-actions { display: flex; gap: 12px; justify-content: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #f0f0f0; }
}
</style>
