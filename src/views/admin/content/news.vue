<template>
  <div class="page-container">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <t-input v-model="searchForm.keyword" placeholder="搜索标题" clearable style="width: 200px" />
      <t-select v-model="searchForm.category" placeholder="分类" clearable style="width: 140px">
        <t-option value="news" label="资讯" />
        <t-option value="notice" label="公告" />
      </t-select>
      <t-button theme="primary" @click="handleSearch">搜索</t-button>
      <div style="flex: 1"></div>
      <t-button theme="primary" @click="handleAdd">
        <template #icon><AddIcon /></template>
        发布资讯
      </t-button>
    </div>

    <!-- 数据表格 -->
    <div class="table-card">
      <t-table :data="newsList" :columns="columns" row-key="id" hover>
        <template #category="{ row }">
          <t-tag :theme="row.category === 'news' ? 'primary' : 'warning'" variant="light">
            {{ row.category === 'news' ? '资讯' : '公告' }}
          </t-tag>
        </template>
        <template #status="{ row }">
          <t-tag :theme="row.status === 'published' ? 'success' : 'default'" variant="light">
            {{ row.status === 'published' ? '已发布' : '草稿' }}
          </t-tag>
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="handleEdit(row)">编辑</t-button>
          <t-button variant="text" :theme="row.status === 'published' ? 'warning' : 'success'" @click="togglePublish(row)">
            {{ row.status === 'published' ? '下架' : '发布' }}
          </t-button>
          <t-popconfirm content="确定删除？" @confirm="handleDelete(row)">
            <t-button variant="text" theme="danger">删除</t-button>
          </t-popconfirm>
        </template>
      </t-table>
      <div class="pagination-wrap">
        <t-pagination v-model:current="pagination.current" :total="pagination.total" />
      </div>
    </div>

    <!-- 编辑弹窗 -->
    <t-dialog v-model:visible="editVisible" :header="editForm.id ? '编辑资讯' : '发布资讯'" width="800px" @confirm="handleSave">
      <t-form :data="editForm" label-width="80px">
        <t-form-item label="标题"><t-input v-model="editForm.title" placeholder="请输入标题" /></t-form-item>
        <t-form-item label="分类">
          <t-select v-model="editForm.category" placeholder="请选择分类">
            <t-option value="news" label="资讯" />
            <t-option value="notice" label="公告" />
          </t-select>
        </t-form-item>
        <t-form-item label="摘要"><t-textarea v-model="editForm.summary" placeholder="请输入摘要" :maxlength="200" /></t-form-item>
        <t-form-item label="内容"><t-textarea v-model="editForm.content" placeholder="请输入内容" :autosize="{ minRows: 6 }" /></t-form-item>
      </t-form>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Input as TInput, Select as TSelect, Option as TOption, Button as TButton,
  Table as TTable, Tag as TTag, Pagination as TPagination, Dialog as TDialog,
  Form as TForm, FormItem as TFormItem, Textarea as TTextarea, Popconfirm as TPopconfirm, MessagePlugin
} from 'tdesign-vue-next'
import { AddIcon } from 'tdesign-icons-vue-next'

const editVisible = ref(false)
const editForm = reactive({ id: null, title: '', category: '', summary: '', content: '' })

const searchForm = reactive({ keyword: '', category: '' })
const pagination = reactive({ current: 1, total: 30 })

const newsList = ref([
  { id: 1, title: '轻养到家2026新年特惠活动', category: 'news', viewCount: 1280, status: 'published', createdAt: '2026-01-10' },
  { id: 2, title: '平台服务升级公告', category: 'notice', viewCount: 860, status: 'published', createdAt: '2026-01-08' },
  { id: 3, title: '技师招募计划启动', category: 'news', viewCount: 520, status: 'draft', createdAt: '2026-01-05' }
])

const columns = [
  { colKey: 'title', title: '标题', width: 280 },
  { colKey: 'category', title: '分类', width: 100 },
  { colKey: 'viewCount', title: '浏览量', width: 100 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'createdAt', title: '创建时间', width: 120 },
  { colKey: 'operation', title: '操作', width: 180 }
]

const handleSearch = () => { pagination.current = 1 }
const handleAdd = () => { Object.assign(editForm, { id: null, title: '', category: '', summary: '', content: '' }); editVisible.value = true }
const handleEdit = (row) => { Object.assign(editForm, row); editVisible.value = true }
const handleSave = () => { editVisible.value = false; MessagePlugin.success('保存成功') }
const handleDelete = (row) => { newsList.value = newsList.value.filter(item => item.id !== row.id); MessagePlugin.success('删除成功') }
const togglePublish = (row) => { row.status = row.status === 'published' ? 'draft' : 'published'; MessagePlugin.success(row.status === 'published' ? '已发布' : '已下架') }
</script>

<style lang="scss" scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 8px; }
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.pagination-wrap { display: flex; justify-content: flex-end; margin-top: 20px; }
</style>
