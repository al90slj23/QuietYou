<template>
  <div class="contact-mobile">
    <section class="banner">
      <h1>联系我们</h1>
      <p>CONTACT US</p>
    </section>

    <section class="contact-list">
      <div class="contact-item">
        <div class="icon"><CallIcon size="32px" /></div>
        <div class="info">
          <h3>客服热线</h3>
          <p class="value">400-888-8888</p>
          <p class="desc">服务咨询或投诉</p>
        </div>
      </div>
      <div class="contact-item">
        <div class="icon"><MailIcon size="32px" /></div>
        <div class="info">
          <h3>电子邮箱</h3>
          <p class="value">service@qingyang.com</p>
          <p class="desc">商务合作、意见反馈</p>
        </div>
      </div>
      <div class="contact-item">
        <div class="icon"><LogoWechatStrokeIcon size="32px" /></div>
        <div class="info">
          <h3>官方公众号</h3>
          <div class="qrcode">公众号二维码</div>
        </div>
      </div>
    </section>

    <section class="notice">
      <h3>温馨提示</h3>
      <p>请认准官方平台下单，非官方渠道暗藏诈骗风险，相关服务不受平台保护。</p>
    </section>
  </div>
</template>

<script setup>
import { CallIcon, MailIcon, LogoWechatStrokeIcon } from 'tdesign-icons-vue-next'
</script>

<style lang="scss" scoped>
$primary: #07c160;

.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }

.contact-list { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
.contact-item { display: flex; align-items: flex-start; gap: 15px; background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 16px; .icon { color: $primary; flex-shrink: 0; } .info { h3 { font-size: 15px; font-weight: 600; color: #333; margin-bottom: 6px; } .value { font-size: 16px; font-weight: 600; color: $primary; margin-bottom: 4px; } .desc { font-size: 12px; color: #999; } .qrcode { width: 80px; height: 80px; background: #f5f5f5; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #999; margin-top: 8px; } } }

.notice { margin: 0 20px 20px; background: #fff9e6; border: 1px solid #ffe58f; border-radius: 10px; padding: 16px; h3 { font-size: 14px; font-weight: 600; color: #d48806; margin-bottom: 8px; } p { font-size: 13px; color: #666; line-height: 1.6; } }
</style>
