<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索商户名称" clearable style="width: 240px">
        <template #prefix-icon><SearchIcon /></template>
      </t-input>
      <t-select v-model="searchForm.status" placeholder="状态" clearable style="width: 140px">
        <t-option value="normal" label="正常" />
        <t-option value="disabled" label="已禁用" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
      <t-button variant="outline" @click="handleReset">重置</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="merchantList" :columns="columns" row-key="id" hover>
        <template #logo="{ row }">
          <t-avatar :image="row.logo" size="36px" shape="round">{{ row.name?.charAt(0) }}</t-avatar>
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
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>

    <!-- 详情弹窗 -->
    <t-dialog v-model:visible="detailVisible" header="商户详情" width="600px" :footer="false">
      <div class="merchant-detail" v-if="currentMerchant">
        <div class="detail-row"><span class="label">商户名称</span><span>{{ currentMerchant.name }}</span></div>
        <div class="detail-row"><span class="label">联系人</span><span>{{ currentMerchant.contact }}</span></div>
        <div class="detail-row"><span class="label">联系电话</span><span>{{ currentMerchant.phone }}</span></div>
        <div class="detail-row"><span class="label">地址</span><span>{{ currentMerchant.address }}</span></div>
        <div class="detail-row"><span class="label">技师数量</span><span>{{ currentMerchant.techCount }}人</span></div>
        <div class="detail-row"><span class="label">订单数</span><span>{{ currentMerchant.orderCount }}</span></div>
        <div class="detail-row"><span class="label">入驻时间</span><span>{{ currentMerchant.createdAt }}</span></div>
      </div>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Input as TInput, Select as TSelect, Option as TOption, Button as TButton,
  Table as TTable, Tag as TTag, Avatar as TAvatar, Pagination as TPagination, 
  Dialog as TDialog, MessagePlugin
} from 'tdesign-vue-next'
import { SearchIcon } from 'tdesign-icons-vue-next'

const detailVisible = ref(false)
const currentMerchant = ref(null)

const searchForm = reactive({ keyword: '', status: '' })
const pagination = reactive({ current: 1, total: 50 })

const merchantList = ref([
  { id: 1, logo: '', name: '悦享养生馆', contact: '张经理', phone: '028-88881234', address: '成都市武侯区天府大道100号', techCount: 8, orderCount: 1280, status: 'normal', createdAt: '2025-12-01' },
  { id: 2, logo: '', name: '康乐足道', contact: '李经理', phone: '028-88885678', address: '成都市高新区软件园B区', techCount: 6, orderCount: 860, status: 'normal', createdAt: '2025-12-05' },
  { id: 3, logo: '', name: '舒心堂', contact: '王经理', phone: '028-88889012', address: '成都市锦江区春熙路', techCount: 5, orderCount: 520, status: 'disabled', createdAt: '2025-12-10' }
])

const columns = [
  { colKey: 'logo', title: 'Logo', width: 80 },
  { colKey: 'name', title: '商户名称', width: 160 },
  { colKey: 'contact', title: '联系人', width: 100 },
  { colKey: 'phone', title: '联系电话', width: 140 },
  { colKey: 'techCount', title: '技师数', width: 100 },
  { colKey: 'orderCount', title: '订单数', width: 100 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '入驻时间', width: 120 },
  { colKey: 'operation', title: '操作', width: 140, fixed: 'right' }
]

const handleSearch = () => { pagination.current = 1 }
const handleReset = () => { searchForm.keyword = ''; searchForm.status = '' }
const viewDetail = (row) => { currentMerchant.value = row; detailVisible.value = true }
const toggleStatus = (row) => {
  row.status = row.status === 'normal' ? 'disabled' : 'normal'
  MessagePlugin.success(`已${row.status === 'normal' ? '启用' : '禁用'}该商户`)
}
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
.merchant-detail {
  .detail-row { display: flex; padding: 12px 0; border-bottom: 1px solid #f0f0f0; &:last-child { border-bottom: none; } .label { width: 100px; color: #999; } }
}
</style>
