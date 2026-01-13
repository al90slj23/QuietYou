<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索用户昵称/手机号" clearable style="width: 240px">
        <template #prefix-icon><SearchIcon /></template>
      </t-input>
      <t-select v-model="searchForm.status" placeholder="用户状态" clearable style="width: 140px">
        <t-option value="normal" label="正常" />
        <t-option value="disabled" label="已禁用" />
      </t-select>
      <t-date-range-picker v-model="searchForm.dateRange" placeholder="注册时间" style="width: 260px" />
      <t-button theme="primary" @click="handleSearch">
        <template #icon><SearchIcon /></template>
        搜索
      </t-button>
      <t-button variant="outline" @click="handleReset">重置</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="userList" :columns="columns" row-key="id" hover :loading="loading">
        <template #avatar="{ row }">
          <t-avatar :image="row.avatar" size="36px">{{ row.nickname?.charAt(0) }}</t-avatar>
        </template>
        <template #status="{ row }">
          <t-tag :theme="row.status === 'normal' ? 'success' : 'danger'" variant="light">
            {{ row.status === 'normal' ? '正常' : '已禁用' }}
          </t-tag>
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="viewDetail(row)">查看</t-button>
          <t-button 
            variant="text" 
            :theme="row.status === 'normal' ? 'danger' : 'success'"
            @click="toggleStatus(row)"
          >
            {{ row.status === 'normal' ? '禁用' : '启用' }}
          </t-button>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination
          v-model:current="pagination.current"
          v-model:page-size="pagination.pageSize"
          :total="pagination.total"
          show-jumper
          @change="handlePageChange"
        />
      </div>
    </div>

    <!-- 用户详情弹窗 -->
    <t-dialog v-model:visible="detailVisible" header="用户详情" width="600px" :footer="false">
      <div class="user-detail" v-if="currentUser">
        <div class="detail-row">
          <span class="label">头像</span>
          <t-avatar :image="currentUser.avatar" size="64px">{{ currentUser.nickname?.charAt(0) }}</t-avatar>
        </div>
        <div class="detail-row">
          <span class="label">昵称</span>
          <span class="value">{{ currentUser.nickname }}</span>
        </div>
        <div class="detail-row">
          <span class="label">手机号</span>
          <span class="value">{{ currentUser.phone }}</span>
        </div>
        <div class="detail-row">
          <span class="label">性别</span>
          <span class="value">{{ currentUser.gender === 1 ? '男' : currentUser.gender === 2 ? '女' : '未知' }}</span>
        </div>
        <div class="detail-row">
          <span class="label">注册时间</span>
          <span class="value">{{ currentUser.createdAt }}</span>
        </div>
        <div class="detail-row">
          <span class="label">订单数</span>
          <span class="value">{{ currentUser.orderCount }}</span>
        </div>
        <div class="detail-row">
          <span class="label">消费金额</span>
          <span class="value">¥{{ currentUser.totalSpent?.toLocaleString() }}</span>
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
  Avatar as TAvatar, Pagination as TPagination, Dialog as TDialog, MessagePlugin
} from 'tdesign-vue-next'
import { SearchIcon } from 'tdesign-icons-vue-next'

const loading = ref(false)
const detailVisible = ref(false)
const currentUser = ref(null)

const searchForm = reactive({
  keyword: '',
  status: '',
  dateRange: []
})

const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 100
})

const userList = ref([
  { id: 1, avatar: '', nickname: '张三', phone: '138****1234', gender: 1, status: 'normal', orderCount: 12, totalSpent: 3680, createdAt: '2026-01-10 10:30' },
  { id: 2, avatar: '', nickname: '李四', phone: '139****5678', gender: 2, status: 'normal', orderCount: 8, totalSpent: 2450, createdAt: '2026-01-09 14:20' },
  { id: 3, avatar: '', nickname: '王五', phone: '137****9012', gender: 1, status: 'disabled', orderCount: 3, totalSpent: 890, createdAt: '2026-01-08 09:15' },
  { id: 4, avatar: '', nickname: '赵六', phone: '136****3456', gender: 2, status: 'normal', orderCount: 25, totalSpent: 7890, createdAt: '2026-01-07 16:45' },
  { id: 5, avatar: '', nickname: '钱七', phone: '135****7890', gender: 1, status: 'normal', orderCount: 6, totalSpent: 1560, createdAt: '2026-01-06 11:30' }
])

const columns = [
  { colKey: 'avatar', title: '头像', width: 80 },
  { colKey: 'nickname', title: '昵称', width: 120 },
  { colKey: 'phone', title: '手机号', width: 140 },
  { colKey: 'orderCount', title: '订单数', width: 100 },
  { colKey: 'totalSpent', title: '消费金额', width: 120, cell: (_, { row }) => `¥${row.totalSpent?.toLocaleString()}` },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '注册时间', width: 160 },
  { colKey: 'operation', title: '操作', width: 140, fixed: 'right' }
]

const handleSearch = () => {
  pagination.current = 1
  // TODO: 调用搜索 API
}

const handleReset = () => {
  searchForm.keyword = ''
  searchForm.status = ''
  searchForm.dateRange = []
  handleSearch()
}

const handlePageChange = () => {
  // TODO: 调用分页 API
}

const viewDetail = (row) => {
  currentUser.value = row
  detailVisible.value = true
}

const toggleStatus = (row) => {
  const newStatus = row.status === 'normal' ? 'disabled' : 'normal'
  row.status = newStatus
  MessagePlugin.success(`已${newStatus === 'normal' ? '启用' : '禁用'}该用户`)
}
</script>

<style lang="scss" scoped>
.page-container { }

.search-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
}

.table-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
}

.pagination-wrap {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.user-detail {
  .detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
    &:last-child { border-bottom: none; }
    .label { width: 100px; color: #999; }
    .value { flex: 1; color: #1a1a1a; }
  }
}
</style>
