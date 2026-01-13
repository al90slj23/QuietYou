<template>
  <div class="page-container">
    <div class="table-card">
      <div class="card-header">
        <h3>服务分类</h3>
        <t-button theme="primary" @click="handleAdd">
          <template #icon><AddIcon /></template>
          新增分类
        </t-button>
      </div>
      <t-table :data="categoryList" :columns="columns" row-key="id" hover>
        <template #icon="{ row }">
          <div class="category-icon" :style="{ background: row.color }">
            <component :is="iconMap[row.icon]" v-if="iconMap[row.icon]" />
          </div>
        </template>
        <template #status="{ row }">
          <t-switch v-model="row.isEnabled" @change="handleStatusChange(row)" />
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="handleEdit(row)">编辑</t-button>
          <t-popconfirm content="确定删除该分类？" @confirm="handleDelete(row)">
            <t-button variant="text" theme="danger">删除</t-button>
          </t-popconfirm>
        </template>
      </t-table>
    </div>

    <!-- 编辑弹窗 -->
    <t-dialog v-model:visible="editVisible" :header="editForm.id ? '编辑分类' : '新增分类'" width="500px" @confirm="handleSave">
      <t-form :data="editForm" label-width="80px">
        <t-form-item label="分类名称">
          <t-input v-model="editForm.name" placeholder="请输入分类名称" />
        </t-form-item>
        <t-form-item label="排序">
          <t-input-number v-model="editForm.sort" :min="0" />
        </t-form-item>
        <t-form-item label="主题色">
          <t-color-picker v-model="editForm.color" />
        </t-form-item>
      </t-form>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, markRaw } from 'vue'
import { 
  Button as TButton, Table as TTable, Switch as TSwitch, Dialog as TDialog,
  Form as TForm, FormItem as TFormItem, Input as TInput, InputNumber as TInputNumber,
  ColorPicker as TColorPicker, Popconfirm as TPopconfirm, MessagePlugin
} from 'tdesign-vue-next'
import { AddIcon, HeartIcon, ServiceIcon, ToolsIcon, HelpCircleIcon } from 'tdesign-icons-vue-next'

const editVisible = ref(false)
const editForm = reactive({ id: null, name: '', sort: 0, color: '#07c160' })

const iconMap = {
  massage: markRaw(HeartIcon),
  foot: markRaw(ServiceIcon),
  spa: markRaw(ToolsIcon),
  head: markRaw(HelpCircleIcon)
}

const categoryList = ref([
  { id: 1, name: '推拿按摩', icon: 'massage', color: '#07c160', sort: 1, serviceCount: 12, isEnabled: true },
  { id: 2, name: '足疗保健', icon: 'foot', color: '#3b82f6', sort: 2, serviceCount: 8, isEnabled: true },
  { id: 3, name: 'SPA护理', icon: 'spa', color: '#ec4899', sort: 3, serviceCount: 6, isEnabled: true },
  { id: 4, name: '头部舒缓', icon: 'head', color: '#f59e0b', sort: 4, serviceCount: 4, isEnabled: false }
])

const columns = [
  { colKey: 'icon', title: '图标', width: 80 },
  { colKey: 'name', title: '分类名称', width: 160 },
  { colKey: 'serviceCount', title: '服务数量', width: 120 },
  { colKey: 'sort', title: '排序', width: 100 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'operation', title: '操作', width: 160 }
]

const handleAdd = () => {
  Object.assign(editForm, { id: null, name: '', sort: 0, color: '#07c160' })
  editVisible.value = true
}

const handleEdit = (row) => {
  Object.assign(editForm, row)
  editVisible.value = true
}

const handleSave = () => {
  editVisible.value = false
  MessagePlugin.success('保存成功')
}

const handleDelete = (row) => {
  const index = categoryList.value.findIndex(item => item.id === row.id)
  if (index > -1) categoryList.value.splice(index, 1)
  MessagePlugin.success('删除成功')
}

const handleStatusChange = (row) => {
  MessagePlugin.success(`已${row.isEnabled ? '启用' : '禁用'}`)
}
</script>

<style lang="scss" scoped>
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; h3 { font-size: 16px; font-weight: 600; } }
.category-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; }
</style>
