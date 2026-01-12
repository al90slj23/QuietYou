<template>
  <div class="address-page">
    <div class="address-list">
      <div class="address-card" v-for="addr in addresses" :key="addr.id">
        <div class="address-info" @click="selectAddress(addr)">
          <div class="header">
            <span class="name">{{ addr.name }}</span>
            <span class="phone">{{ addr.phone }}</span>
            <span class="tag default" v-if="addr.isDefault">默认</span>
          </div>
          <div class="detail">{{ addr.fullAddress }}</div>
        </div>
        <div class="address-actions">
          <span @click="setDefault(addr)" v-if="!addr.isDefault">设为默认</span>
          <span @click="editAddress(addr)">编辑</span>
          <span class="danger" @click="deleteAddress(addr)">删除</span>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!addresses.length">
      <LocationIcon size="48px" />
      <p>暂无收货地址</p>
    </div>

    <div class="add-btn">
      <t-button block theme="primary" size="large" @click="addAddress">添加新地址</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'
import { LocationIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const addresses = ref([
  { id: 1, name: '李女士', phone: '138****8888', fullAddress: '四川省成都市武侯区天府大道100号 锦绣花园3栋1单元1001', isDefault: true },
  { id: 2, name: '李先生', phone: '139****9999', fullAddress: '四川省成都市高新区软件园B区 科技大厦A座2001', isDefault: false }
])

const selectAddress = (addr) => {
  // 如果是从订单页面跳转来的，选择后返回
  Toast({ message: '已选择该地址', theme: 'success' })
}

const setDefault = (addr) => {
  addresses.value.forEach(a => a.isDefault = false)
  addr.isDefault = true
  Toast({ message: '已设为默认地址', theme: 'success' })
}

const editAddress = (addr) => {
  router.push(`/user/address/edit/${addr.id}`)
}

const deleteAddress = (addr) => {
  Toast({ message: '确认删除该地址？', theme: 'warning' })
}

const addAddress = () => {
  router.push('/user/address/edit')
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.address-page { min-height: 100vh; background: #f5f5f5; padding: 15px; padding-bottom: 100px; }

.address-list { display: flex; flex-direction: column; gap: 12px; }

.address-card { background: #fff; border-radius: 12px; overflow: hidden; }

.address-info {
  padding: 15px;
  
  .header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
    
    .name { font-size: 16px; font-weight: 500; color: #1a1a1a; }
    .phone { font-size: 14px; color: #666; }
    .tag {
      font-size: 10px;
      padding: 2px 6px;
      border-radius: 4px;
      &.default { background: #e8f5e9; color: $primary; }
    }
  }
  
  .detail { font-size: 14px; color: #666; line-height: 1.5; }
}

.address-actions {
  display: flex;
  justify-content: flex-end;
  gap: 20px;
  padding: 12px 15px;
  border-top: 1px solid #f5f5f5;
  
  span {
    font-size: 13px;
    color: #666;
    cursor: pointer;
    &.danger { color: #f44336; }
  }
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }

.add-btn { position: fixed; bottom: 20px; left: 15px; right: 15px; }
</style>
