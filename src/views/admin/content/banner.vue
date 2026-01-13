<template>
  <div class="page-container">
    <div class="table-card">
      <div class="card-header">
        <h3>轮播图管理</h3>
        <t-button theme="primary" @click="handleAdd">
          <template #icon><AddIcon /></template>
          新增轮播图
        </t-button>
      </div>
      <t-table :data="bannerList" :columns="columns" row-key="id" hover>
        <template #image="{ row }">
          <div class="banner-image">
            <img :src="row.image || '/placeholder.png'" />
          </div>
        </template>
        <template #position="{ row }">
          <t-tag variant="light">{{ positionText[row.position] }}</t-tag>
        </template>
        <template #status="{ row }">
          <t-switch v-model="row.isEnabled" @change="handleStatusChange(row)" />
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="handleEdit(row)">编辑</t-button>
          <t-popconfirm content="确定删除？" @confirm="handleDelete(row)">
            <t-button variant="text" theme="danger">删除</t-button>
          </t-popconfirm>
        </template>
      </t-table>
    </div>

    <!-- 编辑弹窗 -->
    <t-dialog v-model:visible="editVisible" :header="editForm.id ? '编辑轮播图' : '新增轮播图'" width="600px" @confirm="handleSave">
      <t-form :data="editForm" label-width="80px">
        <t-form-item label="标题"><t-input v-model="editForm.title" placeholder="请输入标题" /></t-form-item>
        <t-form-item label="位置">
          <t-select v-model="editForm.position" placeholder="请选择位置">
            <t-option value="home" label="官网首页" />
            <t-option value="user" label="用户端首页" />
          </t-select>
        </t-form-item>
        <t-form-item label="链接"><t-input v-model="editForm.link" placeholder="点击跳转链接" /></t-form-item>
        <t-form-item label="排序"><t-input-number v-model="editForm.sort" :min="0" /></t-form-item>
        <t-form-item label="图片">
          <t-upload v-model="editForm.imageList" action="/api/upload" theme="image" accept="image/*" />
        </t-form-item>
      </t-form>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Button as TButton, Table as TTable, Tag as TTag, Switch as TSwitch, Dialog as TDialog,
  Form as TForm, FormItem as TFormItem, Input as TInput, Select as TSelect, Option as TOption,
  InputNumber as TInputNumber, Upload as TUpload, Popconfirm as TPopconfirm, MessagePlugin
} from 'tdesign-vue-next'
import { AddIcon } from 'tdesign-icons-vue-next'

const editVisible = ref(false)
const editForm = reactive({ id: null, title: '', position: '', link: '', sort: 0, imageList: [] })

const positionText = { home: '官网首页', user: '用户端首页' }

const bannerList = ref([
  { id: 1, title: '新年特惠', image: '', position: 'home', link: '/home/news', sort: 1, isEnabled: true },
  { id: 2, title: '技师招募', image: '', position: 'home', link: '/home/recruit', sort: 2, isEnabled: true },
  { id: 3, title: '首单立减', image: '', position: 'user', link: '/user/service/list', sort: 1, isEnabled: true }
])

const columns = [
  { colKey: 'image', title: '图片', width: 160 },
  { colKey: 'title', title: '标题', width: 160 },
  { colKey: 'position', title: '位置', width: 120 },
  { colKey: 'link', title: '链接', width: 200 },
  { colKey: 'sort', title: '排序', width: 80 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'operation', title: '操作', width: 140 }
]

const handleAdd = () => { Object.assign(editForm, { id: null, title: '', position: '', link: '', sort: 0, imageList: [] }); editVisible.value = true }
const handleEdit = (row) => { Object.assign(editForm, row); editVisible.value = true }
const handleSave = () => { editVisible.value = false; MessagePlugin.success('保存成功') }
const handleDelete = (row) => { bannerList.value = bannerList.value.filter(item => item.id !== row.id); MessagePlugin.success('删除成功') }
const handleStatusChange = (row) => { MessagePlugin.success(`已${row.isEnabled ? '启用' : '禁用'}`) }
</script>

<style lang="scss" scoped>
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; h3 { font-size: 16px; font-weight: 600; } }
.banner-image { width: 120px; height: 60px; border-radius: 4px; overflow: hidden; background: #f5f5f5; img { width: 100%; height: 100%; object-fit: cover; } }
</style>
