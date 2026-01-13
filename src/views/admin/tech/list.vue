<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索技师姓名/手机号" clearable style="width: 240px">
        <template #prefix-icon><SearchIcon /></template>
      </t-input>
      <t-select v-model="searchForm.type" placeholder="技师类型" clearable style="width: 140px">
        <t-option value="shop" label="店铺技师" />
        <t-option value="freelance" label="独立技师" />
      </t-select>
      <t-select v-model="searchForm.status" placeholder="状态" clearable style="width: 140px">
        <t-option value="online" label="在线" />
        <t-option value="offline" label="离线" />
        <t-option value="disabled" label="已禁用" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">
        <template #icon><SearchIcon /></template>
        搜索
      </t-button>
      <t-button variant="outline" @click="handleReset">重置</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="techList" :columns="columns" row-key="id" hover :loading="loading">
        <template #avatar="{ row }">
          <t-avatar :image="row.avatar" size="36px">{{ row.name?.charAt(0) }}</t-avatar>
        </template>
        <template #type="{ row }">
          <t-tag :theme="row.type === 'shop' ? 'primary' : 'warning'" variant="light">
            {{ row.type === 'shop' ? '店铺技师' : '独立技师' }}
          </t-tag>
        </template>
        <template #certified="{ row }">
          <t-tag :theme="row.isCertified ? 'success' : 'default'" variant="light">
            {{ row.isCertified ? '已认证' : '未认证' }}
          </t-tag>
        </template>
        <template #status="{ row }">
          <t-tag :theme="statusTheme[row.status]" variant="light">
            {{ statusText[row.status] }}
          </t-tag>
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="viewDetail(row)">查看</t-button>
          <t-button 
            variant="text" 
            :theme="row.status === 'disabled' ? 'success' : 'danger'"
            @click="toggleStatus(row)"
          >
            {{ row.status === 'disabled' ? '启用' : '禁用' }}
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
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Input as TInput, Select as TSelect, Option as TOption, Button as TButton,
  Table as TTable, Tag as TTag, Avatar as TAvatar, Pagination as TPagination, MessagePlugin
} from 'tdesign-vue-next'
import { SearchIcon } from 'tdesign-icons-vue-next'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)

const searchForm = reactive({
  keyword: '',
  type: '',
  status: ''
})

const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 100
})

const statusTheme = { online: 'success', offline: 'default', disabled: 'danger' }
const statusText = { online: '在线', offline: '离线', disabled: '已禁用' }

const techList = ref([
  { id: 1, avatar: '', name: '张师傅', phone: '138****1234', type: 'shop', shopName: '悦享养生馆', isCertified: true, rating: 4.9, orderCount: 328, status: 'online', createdAt: '2026-01-05' },
  { id: 2, avatar: '', name: '李师傅', phone: '139****5678', type: 'freelance', shopName: '-', isCertified: true, rating: 4.8, orderCount: 256, status: 'online', createdAt: '2026-01-04' },
  { id: 3, avatar: '', name: '王师傅', phone: '137****9012', type: 'shop', shopName: '康乐足道', isCertified: false, rating: 4.7, orderCount: 186, status: 'offline', createdAt: '2026-01-03' },
  { id: 4, avatar: '', name: '陈师傅', phone: '136****3456', type: 'freelance', shopName: '-', isCertified: true, rating: 4.6, orderCount: 142, status: 'disabled', createdAt: '2026-01-02' }
])

const columns = [
  { colKey: 'avatar', title: '头像', width: 80 },
  { colKey: 'name', title: '姓名', width: 100 },
  { colKey: 'phone', title: '手机号', width: 140 },
  { colKey: 'type', title: '类型', width: 100 },
  { colKey: 'shopName', title: '所属店铺', width: 140 },
  { colKey: 'certified', title: '认证', width: 100 },
  { colKey: 'rating', title: '评分', width: 80 },
  { colKey: 'orderCount', title: '订单数', width: 100 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'operation', title: '操作', width: 140, fixed: 'right' }
]

const handleSearch = () => { pagination.current = 1 }
const handleReset = () => {
  searchForm.keyword = ''
  searchForm.type = ''
  searchForm.status = ''
  handleSearch()
}
const handlePageChange = () => {}
const viewDetail = (row) => { router.push(`/admin/tech/detail/${row.id}`) }
const toggleStatus = (row) => {
  const newStatus = row.status === 'disabled' ? 'online' : 'disabled'
  row.status = newStatus
  MessagePlugin.success(`已${newStatus === 'disabled' ? '禁用' : '启用'}该技师`)
}
</script>

<style lang="scss" scoped>
.search-bar {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
}
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
</style>
