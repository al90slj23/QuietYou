<template>
  <div class="page-container">
    <div class="table-card">
      <div class="card-header">
        <h3>管理员列表</h3>
        <t-button theme="primary" @click="handleAdd">
          <template #icon><AddIcon /></template>
          新增管理员
        </t-button>
      </div>
      <t-table :data="adminList" :columns="columns" row-key="id" hover>
        <template #avatar="{ row }">
          <t-avatar size="36px">{{ row.name?.charAt(0) }}</t-avatar>
        </template>
        <template #role="{ row }">
          <t-tag :theme="row.role === 'super' ? 'danger' : 'primary'" variant="light">
            {{ row.role === 'super' ? '超级管理员' : '普通管理员' }}
          </t-tag>
        </template>
        <template #status="{ row }">
          <t-switch v-model="row.isEnabled" :disabled="row.role === 'super'" @change="handleStatusChange(row)" />
        </template>
        <template #operation="{ row }">
          <t-button variant="text" theme="primary" @click="handleEdit(row)">编辑</t-button>
          <t-button variant="text" theme="primary" @click="handleResetPassword(row)">重置密码</t-button>
          <t-popconfirm v-if="row.role !== 'super'" content="确定删除？" @confirm="handleDelete(row)">
            <t-button variant="text" theme="danger">删除</t-button>
          </t-popconfirm>
        </template>
      </t-table>
    </div>

    <!-- 编辑弹窗 -->
    <t-dialog v-model:visible="editVisible" :header="editForm.id ? '编辑管理员' : '新增管理员'" width="500px" @confirm="handleSave">
      <t-form :data="editForm" label-width="80px">
        <t-form-item label="用户名"><t-input v-model="editForm.username" placeholder="请输入用户名" :disabled="!!editForm.id" /></t-form-item>
        <t-form-item label="姓名"><t-input v-model="editForm.name" placeholder="请输入姓名" /></t-form-item>
        <t-form-item label="手机号"><t-input v-model="editForm.phone" placeholder="请输入手机号" /></t-form-item>
        <t-form-item label="角色">
          <t-select v-model="editForm.role" placeholder="请选择角色">
            <t-option value="admin" label="普通管理员" />
            <t-option value="super" label="超级管理员" />
          </t-select>
        </t-form-item>
        <t-form-item v-if="!editForm.id" label="密码"><t-input v-model="editForm.password" type="password" placeholder="请输入密码" /></t-form-item>
      </t-form>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { 
  Button as TButton, Table as TTable, Tag as TTag, Avatar as TAvatar, Switch as TSwitch,
  Dialog as TDialog, Form as TForm, FormItem as TFormItem, Input as TInput,
  Select as TSelect, Option as TOption, Popconfirm as TPopconfirm, MessagePlugin
} from 'tdesign-vue-next'
import { AddIcon } from 'tdesign-icons-vue-next'

const editVisible = ref(false)
const editForm = reactive({ id: null, username: '', name: '', phone: '', role: '', password: '' })

const adminList = ref([
  { id: 1, username: 'admin', name: '超级管理员', phone: '138****1234', role: 'super', isEnabled: true, lastLoginAt: '2026-01-13 10:30' },
  { id: 2, username: 'manager1', name: '张经理', phone: '139****5678', role: 'admin', isEnabled: true, lastLoginAt: '2026-01-13 09:15' },
  { id: 3, username: 'manager2', name: '李经理', phone: '137****9012', role: 'admin', isEnabled: false, lastLoginAt: '2026-01-10 14:20' }
])

const columns = [
  { colKey: 'avatar', title: '头像', width: 80 },
  { colKey: 'username', title: '用户名', width: 120 },
  { colKey: 'name', title: '姓名', width: 120 },
  { colKey: 'phone', title: '手机号', width: 140 },
  { colKey: 'role', title: '角色', width: 120 },
  { colKey: 'status', title: '状态', width: 100 },
  { colKey: 'lastLoginAt', title: '最后登录', width: 160 },
  { colKey: 'operation', title: '操作', width: 200 }
]

const handleAdd = () => { Object.assign(editForm, { id: null, username: '', name: '', phone: '', role: '', password: '' }); editVisible.value = true }
const handleEdit = (row) => { Object.assign(editForm, row); editVisible.value = true }
const handleSave = () => { editVisible.value = false; MessagePlugin.success('保存成功') }
const handleDelete = (row) => { adminList.value = adminList.value.filter(item => item.id !== row.id); MessagePlugin.success('删除成功') }
const handleStatusChange = (row) => { MessagePlugin.success(`已${row.isEnabled ? '启用' : '禁用'}`) }
const handleResetPassword = (row) => { MessagePlugin.success(`已重置 ${row.name} 的密码`) }
</script>

<style lang="scss" scoped>
.table-card { background: #fff; border-radius: 8px; padding: 20px; }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; h3 { font-size: 16px; font-weight: 600; } }
</style>
