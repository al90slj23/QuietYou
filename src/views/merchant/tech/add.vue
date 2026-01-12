<template>
  <div class="add-tech-page">
    <div class="section">
      <div class="section-title">邀请方式</div>
      <div class="invite-options">
        <div class="option" :class="{ active: inviteType === 'phone' }" @click="inviteType = 'phone'">
          <MobileIcon size="24px" />
          <span>手机号邀请</span>
        </div>
        <div class="option" :class="{ active: inviteType === 'qrcode' }" @click="inviteType = 'qrcode'">
          <QrcodeIcon size="24px" />
          <span>二维码邀请</span>
        </div>
      </div>
    </div>

    <div class="section" v-if="inviteType === 'phone'">
      <div class="section-title">输入技师手机号</div>
      <div class="form-item">
        <input v-model="phone" placeholder="请输入技师注册手机号" type="tel" />
      </div>
      <t-button block theme="primary" @click="searchTech" :disabled="!phone">搜索技师</t-button>
    </div>

    <div class="section" v-if="inviteType === 'qrcode'">
      <div class="section-title">扫码加入</div>
      <div class="qrcode-area">
        <div class="qrcode-placeholder">
          <QrcodeIcon size="120px" />
        </div>
        <p>让技师扫描此二维码加入店铺</p>
        <t-button variant="outline" @click="saveQrcode">保存二维码</t-button>
      </div>
    </div>

    <!-- 搜索结果 -->
    <div class="section" v-if="searchResult">
      <div class="section-title">搜索结果</div>
      <div class="tech-card">
        <t-avatar :image="searchResult.avatar" size="56px" />
        <div class="info">
          <div class="name">{{ searchResult.name }}</div>
          <div class="meta">{{ searchResult.skills }} · {{ searchResult.experience }}年经验</div>
          <div class="rating">评分 {{ searchResult.rating }} · {{ searchResult.orderCount }}单</div>
        </div>
        <t-button size="small" theme="primary" @click="inviteTech">邀请加入</t-button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { MobileIcon, QrcodeIcon } from 'tdesign-icons-vue-next'

const inviteType = ref('phone')
const phone = ref('')
const searchResult = ref(null)

const searchTech = () => {
  // 模拟搜索结果
  searchResult.value = {
    id: 1,
    avatar: '',
    name: '陈师傅',
    skills: '推拿、足疗、刮痧',
    experience: 6,
    rating: 4.8,
    orderCount: 420
  }
}

const inviteTech = () => {
  Toast({ message: '邀请已发送，等待技师确认', theme: 'success' })
}

const saveQrcode = () => {
  Toast({ message: '二维码已保存', theme: 'success' })
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.add-tech-page { min-height: 100vh; background: #f5f5f5; padding: 15px; }

.section { background: #fff; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
.section-title { font-size: 14px; color: #999; margin-bottom: 15px; }

.invite-options {
  display: flex;
  gap: 15px;
  
  .option {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 12px;
    border: 2px solid transparent;
    cursor: pointer;
    color: #666;
    
    &.active {
      border-color: $primary;
      background: #f0fff4;
      color: $primary;
    }
    
    span { font-size: 14px; }
  }
}

.form-item {
  margin-bottom: 15px;
  
  input {
    width: 100%;
    padding: 12px;
    border: 1px solid #eee;
    border-radius: 8px;
    font-size: 15px;
    outline: none;
    
    &:focus { border-color: $primary; }
    &::placeholder { color: #ccc; }
  }
}

.qrcode-area {
  text-align: center;
  padding: 20px 0;
  
  .qrcode-placeholder {
    width: 180px;
    height: 180px;
    background: #f9f9f9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: #ddd;
  }
  
  p { font-size: 14px; color: #999; margin-bottom: 15px; }
}

.tech-card {
  display: flex;
  align-items: center;
  gap: 12px;
  
  .info {
    flex: 1;
    .name { font-size: 16px; font-weight: 500; color: #1a1a1a; }
    .meta { font-size: 13px; color: #666; margin-top: 4px; }
    .rating { font-size: 12px; color: #999; margin-top: 4px; }
  }
}
</style>
