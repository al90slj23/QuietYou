<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索技师姓名" clearable style="width: 240px">
        <template #prefix-icon><SearchIcon /></template>
      </t-input>
      <t-select v-model="searchForm.status" placeholder="审核状态" clearable style="width: 140px">
        <t-option value="pending" label="待审核" />
        <t-option value="approved" label="已通过" />
        <t-option value="rejected" label="已拒绝" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="certList" :columns="columns" row-key="id" hover>
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
    <t-dialog v-model:visible="detailVisible" header="认证详情" width="700px" :footer="false">
      <div class="cert-detail" v-if="currentCert">
        <div class="detail-section">
          <h4>基本信息</h4>
          <div class="detail-grid">
            <div class="detail-item"><span class="label">姓名</span><span>{{ currentCert.name }}</span></div>
            <div class="detail-item"><span class="label">手机号</span><span>{{ currentCert.phone }}</span></div>
            <div class="detail-item"><span class="label">申请时间</span><span>{{ currentCert.createdAt }}</span></div>
          </div>
        </div>
        <div class="detail-section">
          <h4>证书照片</h4>
          <div class="cert-images">
            <div class="cert-image" v-for="(img, i) in currentCert.images" :key="i">
              <img :src="img || '/placeholder.png'" />
            </div>
          </div>
        </div>
        <div class="detail-actions" v-if="currentCert.status === 'pending'">
          <t-button theme="success" @click="handleApprove(currentCert)">通过审核</t-button>
          <t-button theme="danger" variant="outline" @click="handleReject(currentCert)">拒绝</t-button>
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
import { SearchIcon } from 'tdesign-icons-vue-next'

const detailVisible = ref(false)
const currentCert = ref(null)

const searchForm = reactive({ keyword: '', status: '' })
const pagination = reactive({ current: 1, total: 50 })

const statusTheme = { pending: 'warning', approved: 'success', rejected: 'danger' }
const statusText = { pending: '待审核', approved: '已通过', rejected: '已拒绝' }

const certList = ref([
  { id: 1, name: '周师傅', phone: '138****1234', certType: '高级按摩师', status: 'pending', images: ['', ''], createdAt: '2026-01-13 10:30' },
  { id: 2, name: '吴师傅', phone: '139****5678', certType: '中医推拿师', status: 'pending', images: ['', ''], createdAt: '2026-01-12 14:20' },
  { id: 3, name: '郑师傅', phone: '137****9012', certType: '足疗师', status: 'approved', images: ['', ''], createdAt: '2026-01-11 09:15' },
  { id: 4, name: '孙师傅', phone: '136****3456', certType: 'SPA理疗师', status: 'rejected', images: ['', ''], createdAt: '2026-01-10 16:45' }
])

const columns = [
  { colKey: 'name', title: '技师姓名', width: 120 },
  { colKey: 'phone', title: '手机号', width: 140 },
  { colKey: 'certType', title: '证书类型', width: 140 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '申请时间', width: 160 },
  { colKey: 'operation', title: '操作', width: 180, fixed: 'right' }
]

const handleSearch = () => { pagination.current = 1 }
const viewDetail = (row) => { currentCert.value = row; detailVisible.value = true }
const handleApprove = (row) => { row.status = 'approved'; detailVisible.value = false; MessagePlugin.success('已通过审核') }
const handleReject = (row) => { row.status = 'rejected'; detailVisible.value = false; MessagePlugin.success('已拒绝') }
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }

.cert-detail {
  .detail-section { margin-bottom: 24px; h4 { font-size: 14px; color: #1a1a1a; margin-bottom: 16px; } }
  .detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
  .detail-item { .label { color: #999; margin-right: 8px; } }
  .cert-images { display: flex; gap: 16px; }
  .cert-image { width: 200px; height: 140px; background: #f5f5f5; border-radius: 8px; overflow: hidden; img { width: 100%; height: 100%; object-fit: cover; } }
  .detail-actions { display: flex; gap: 12px; justify-content: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #f0f0f0; }
}
</style>
