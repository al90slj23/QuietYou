<template>
  <div class="contact">
    <!-- Banner -->
    <section class="page-banner">
      <div class="container">
        <h1>联系我们</h1>
        <p class="subtitle">CONTACT US</p>
      </div>
    </section>

    <!-- 联系方式 -->
    <section class="contact-info section">
      <div class="container">
        <div class="contact-grid">
          <!-- 客服热线 -->
          <div class="contact-card">
            <div class="contact-icon">
              <CallIcon size="48px" />
            </div>
            <h3>客服热线</h3>
            <p class="contact-value">400-888-8888</p>
            <p class="contact-desc">如您遇到服务过程中的问题想要咨询或投诉，可拨打客服服务热线</p>
          </div>

          <!-- 电子邮箱 -->
          <div class="contact-card">
            <div class="contact-icon">
              <MailIcon size="48px" />
            </div>
            <h3>电子邮箱</h3>
            <p class="contact-value">service@qingyang.com</p>
            <p class="contact-desc">商务合作、意见反馈请发送邮件至此邮箱</p>
          </div>

          <!-- 公众号 -->
          <div class="contact-card">
            <div class="contact-icon">
              <LogoWechatStrokeIcon size="48px" />
            </div>
            <h3>官方公众号</h3>
            <div class="qrcode-placeholder">
              <span>公众号二维码</span>
            </div>
            <p class="contact-desc">关注轻养到家公众号，获取最新资讯</p>
          </div>
        </div>

        <!-- 温馨提示 -->
        <div class="notice">
          <h3>温馨提示</h3>
          <p>
            轻养到家作为合规运营的O2O上门养生服务平台，始终秉持合法经营理念，坚决抵制违规行为。
            非官方渠道暗藏诈骗、非平台商户服务、个人信息泄露等多重风险，且相关服务不受平台保护。
            请广大消费者务必认准官方平台下单，共同维护健康安全的服务环境。
          </p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { CallIcon, MailIcon, LogoWechatStrokeIcon } from 'tdesign-icons-vue-next'
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$mobile: 768px;

.page-banner {
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  color: #fff;
  text-align: center;
  padding: 140px 0 80px;
  
  @media (max-width: $mobile) {
    padding: 120px 0 60px;
  }
  
  h1 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 10px;
    
    @media (max-width: $mobile) {
      font-size: 28px;
    }
  }
  
  .subtitle {
    font-size: 14px;
    opacity: 0.8;
    letter-spacing: 3px;
  }
}

.contact-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  margin-bottom: 60px;
  
  @media (max-width: 1024px) {
    grid-template-columns: repeat(2, 1fr);
  }
  
  @media (max-width: $mobile) {
    grid-template-columns: 1fr;
  }
}

.contact-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 16px;
  padding: 40px 30px;
  text-align: center;
  transition: all 0.3s;
  
  &:hover {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }
  
  .contact-icon {
    color: $primary-color;
    margin-bottom: 20px;
  }
  
  h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
  }
  
  .contact-value {
    font-size: 24px;
    font-weight: 600;
    color: $primary-color;
    margin-bottom: 15px;
  }
  
  .contact-desc {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
  }
  
  .qrcode-placeholder {
    width: 120px;
    height: 120px;
    background: #f5f5f5;
    border-radius: 8px;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 13px;
  }
}

.notice {
  background: #fff9e6;
  border: 1px solid #ffe58f;
  border-radius: 12px;
  padding: 30px;
  
  h3 {
    font-size: 16px;
    font-weight: 600;
    color: #d48806;
    margin-bottom: 12px;
    
    &::before {
      content: '⚠️ ';
    }
  }
  
  p {
    font-size: 14px;
    color: #666;
    line-height: 1.8;
  }
}
</style>
