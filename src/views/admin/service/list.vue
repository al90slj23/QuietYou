<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索服务名称" clearable style="width: 200px" />
      <t-select v-model="searchForm.category" placeholder="服务分类" clearable style="width: 140px">
        <t-option value="1" label="推拿按摩" />
        <t-option value="2" label="足疗保健" />
        <t-option value="3" label="SPA护理" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
      <div style="flex: 1"></div>
      <t-button theme="primary" @click="handleAdd">
        <template #icon><AddIcon /></template>
        新增服务
      </t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="serviceList" :columns="columns" row-key="id" hover>
        <template #status="{ row }">
          <t-switch v-model="row.isEnabled" @change="handleStatusChange(row)" />
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="handleEdit(row)">编辑</t-button>
          <t-popconfirm content="确定删除该服务？" @confirm="handleDelete(row)">
            <t-button variant="text" theme="danger">删除</t-button>
          </t-popconfirm>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>

    <!-- 编辑弹窗 -->
    <t-dialog v-model:visible="editVisible" :header="editForm.id ? '编辑服务' : '新增服务'" width="600px" @confirm="handleSave">
      <t-form :data="editForm" label-width="100px">
        <t-form-item label="服务名称"><t-input v-model="editForm.name" placeholder="请输入服务名称" /></t-form-item>
        <t-form-item label="服务分类">
          <t-select v-model="editForm.categoryId" placeholder="请选择分类">
            <t-option value="1" label="推拿按摩" />
            <t-option value="2" label="足疗保健" />
            <t-option value="3" label="SPA护理" />
          </t-select>
        </t-form-item>
        <t-form-item label="服务时长"><t-input-number v-model="editForm.duration" :min="15" :step="15" suffix="分钟" /></t-form-item>
        <t-form-item label="参考价格"><t-input-number v-model="editForm.price" :min="0" prefix="¥" /></t-form-item>
        <t-form-item label="服务描述"><t-textarea v-model="editForm.description" placeholder="请输入服务描述" /></t-form-item>
      </t-form>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Input as TInput, Select as TSelect, Option as TOption, Button as TButton,
  Table as TTable, Switch as TSwitch, Pagination as TPagination, Dialog as TDialog,
  Form as TForm, FormItem as TFormItem, InputNumber as TInputNumber, Textarea as TTextarea,
  Popconfirm as TPopconfirm, MessagePlugin
} from 'tdesign-vue-next'
import { AddIcon } from 'tdesign-icons-vue-next'

const editVisible = ref(false)
const editForm = reactive({ id: null, name: '', categoryId: '', duration: 60, price: 0, description: '' })

const searchForm = reactive({ keyword: '', category: '' })
const pagination = reactive({ current: 1, total: 50 })

const serviceList = ref([
  { id: 1, name: '全身中式推拿', categoryName: '推拿按摩', duration: 60, price: 298, orderCount: 1280, isEnabled: true },
  { id: 2, name: '肩颈深度调理', categoryName: '推拿按摩', duration: 45, price: 198, orderCount: 860, isEnabled: true },
  { id: 3, name: '足底穴位按摩', categoryName: '足疗保健', duration: 60, price: 168, orderCount: 520, isEnabled: true },
  { id: 4, name: '精油SPA护理', categoryName: 'SPA护理', duration: 90, price: 398, orderCount: 320, isEnabled: false }
])

const columns = [
  { colKey: 'name', title: '服务名称', width: 180 },
  { colKey: 'categoryName', title: '分类', width: 120 },
  { colKey: 'duration', title: '时长', width: 100, cell: (_, { row }) => `${row.duration}分钟` },
  { colKey: 'price', title: '参考价格', width: 120, cell: (_, { row }) => `¥${row.price}` },
  { colKey: 'orderCount', title: '订单数', width: 100 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'operation', title: '操作', width: 140 }
]

const handleSearch = () => { pagination.current = 1 }
const handleAdd = () => { Object.assign(editForm, { id: null, name: '', categoryId: '', duration: 60, price: 0, description: '' }); editVisible.value = true }
const handleEdit = (row) => { Object.assign(editForm, row); editVisible.value = true }
const handleSave = () => { editVisible.value = false; MessagePlugin.success('保存成功') }
const handleDelete = (row) => { serviceList.value = serviceList.value.filter(item => item.id !== row.id); MessagePlugin.success('删除成功') }
const handleStatusChange = (row) => { MessagePlugin.success(`已${row.isEnabled ? '启用' : '禁用'}`) }
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
</style>
