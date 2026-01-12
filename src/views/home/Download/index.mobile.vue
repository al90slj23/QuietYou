<template>
  <div class="download-mobile">
    <section class="banner">
      <h1>预约渠道</h1>
      <p>BOOKING CHANNELS</p>
    </section>

    <section class="channels">
      <div class="channel-item">
        <div class="icon"><MobileIcon size="36px" /></div>
        <div class="info">
          <h3>轻养到家小程序</h3>
          <p>轻养到家官方出品</p>
        </div>
        <div class="qrcode">小程序码</div>
      </div>
      <div class="channel-item">
        <div class="icon"><LogoWechatStrokeIcon size="36px" /></div>
        <div class="info">
          <h3>轻养到家公众号</h3>
          <p>轻养到家官方出品</p>
        </div>
        <div class="qrcode">公众号码</div>
      </div>
      <div class="channel-item">
        <div class="icon"><InternetIcon size="36px" /></div>
        <div class="info">
          <h3>轻养到家网页版</h3>
          <p>无需下载，直接访问</p>
        </div>
        <router-link to="/user" class="btn btn-primary btn-sm">立即访问</router-link>
      </div>
    </section>

    <section class="service-info">
      <h3>服务说明</h3>
      <ul>
        <li>足不出户的预约上门服务</li>
        <li>在线选择服务项目和技师</li>
        <li>24小时在线预约</li>
      </ul>
    </section>
  </div>
</template>

<script setup>
import { MobileIcon, LogoWechatStrokeIcon, InternetIcon } from 'tdesign-icons-vue-next'
</script>

<style lang="scss" scoped>
$primary: #07c160;

.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }

.channels { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
.channel-item { display: flex; align-items: center; gap: 12px; background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 16px; .icon { color: $primary; flex-shrink: 0; } .info { flex: 1; h3 { font-size: 15px; font-weight: 600; color: #333; margin-bottom: 4px; } p { font-size: 12px; color: #999; } } .qrcode { width: 60px; height: 60px; background: #f5f5f5; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999; } .btn-sm { padding: 8px 16px; font-size: 13px; } }

.service-info { margin: 0 20px 20px; background: #f9fafb; border-radius: 12px; padding: 16px; h3 { font-size: 15px; font-weight: 600; color: #333; margin-bottom: 12px; } ul { list-style: none; padding: 0; margin: 0; li { position: relative; padding-left: 16px; font-size: 13px; color: #555; margin-bottom: 8px; &::before { content: '\2713'; position: absolute; left: 0; color: $primary; font-size: 12px; } } } }
</style>
