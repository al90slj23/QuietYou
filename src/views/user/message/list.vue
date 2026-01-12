<template>
  <div class="message-page">
    <div class="message-list">
      <div class="message-card" v-for="msg in messages" :key="msg.id" :class="{ unread: !msg.isRead }" @click="readMessage(msg)">
        <div class="icon-wrap" :class="msg.type">
          <component :is="getIcon(msg.type)" size="20px" />
        </div>
        <div class="message-content">
          <div class="title">{{ msg.title }}</div>
          <div class="desc">{{ msg.content }}</div>
          <div class="time">{{ msg.time }}</div>
        </div>
        <div class="dot" v-if="!msg.isRead"></div>
      </div>
    </div>

    <div class="empty" v-if="!messages.length">
      <NotificationIcon size="48px" />
      <p>暂无消息</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Toast } from 'tdesign-mobile-vue'
import { NotificationIcon, FileIcon, DiscountIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const messages = ref([
  { id: 1, type: 'order', title: '订单状态更新', content: '您的订单已完成，感谢您的使用，期待下次光临', time: '10分钟前', isRead: false },
  { id: 2, type: 'coupon', title: '优惠券到账', content: '恭喜您获得30元新人优惠券，快去使用吧', time: '1小时前', isRead: false },
  { id: 3, type: 'system', title: '系统通知', content: '轻养到家新版本已上线，体验更多新功能', time: '昨天', isRead: true },
  { id: 4, type: 'order', title: '订单提醒', content: '您预约的服务将于明天14:00开始，请做好准备', time: '2天前', isRead: true }
])

const getIcon = (type) => {
  const icons = { order: FileIcon, coupon: DiscountIcon, system: InfoCircleIcon }
  return icons[type] || NotificationIcon
}

const readMessage = (msg) => {
  msg.isRead = true
  if (msg.type === 'order') {
    router.push('/user/order/list')
  } else if (msg.type === 'coupon') {
    router.push('/user/coupon/list')
  }
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.message-page { min-height: 100vh; background: #f5f5f5; padding: 15px; }

.message-list { display: flex; flex-direction: column; gap: 12px; }

.message-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  position: relative;
  
  &.unread { background: #fafffe; }
}

.icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  
  &.order { background: #e3f2fd; color: #1976d2; }
  &.coupon { background: #fff3e0; color: #f57c00; }
  &.system { background: #f5f5f5; color: #666; }
}

.message-content {
  flex: 1;
  
  .title { font-size: 15px; font-weight: 500; color: #1a1a1a; margin-bottom: 6px; }
  .desc { font-size: 13px; color: #666; line-height: 1.5; margin-bottom: 6px; }
  .time { font-size: 12px; color: #999; }
}

.dot {
  width: 8px;
  height: 8px;
  background: #f44336;
  border-radius: 50%;
  position: absolute;
  top: 15px;
  right: 15px;
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
