<template>
  <div class="download">
    <!-- Banner -->
    <section class="page-banner">
      <div class="container">
        <h1>预约渠道</h1>
        <p class="subtitle">BOOKING CHANNELS</p>
      </div>
    </section>

    <!-- 下载渠道 -->
    <section class="channels section">
      <div class="container">
        <div class="channels-grid">
          <!-- 微信小程序 -->
          <div class="channel-card">
            <div class="channel-icon">📱</div>
            <h3>轻养到家小程序</h3>
            <p class="channel-desc">轻养到家官方出品</p>
            <div class="qrcode-placeholder">
              <span>小程序码</span>
            </div>
            <p class="channel-tip">微信扫一扫，即可预约服务</p>
          </div>

          <!-- 微信公众号 -->
          <div class="channel-card">
            <div class="channel-icon">💬</div>
            <h3>轻养到家公众号</h3>
            <p class="channel-desc">轻养到家官方出品</p>
            <div class="qrcode-placeholder">
              <span>公众号二维码</span>
            </div>
            <p class="channel-tip">关注公众号，获取更多资讯</p>
          </div>

          <!-- H5网页版 -->
          <div class="channel-card">
            <div class="channel-icon">🌐</div>
            <h3>轻养到家网页版</h3>
            <p class="channel-desc">轻养到家官方出品</p>
            <div class="channel-actions">
              <a href="/pages/user/" class="btn btn-primary">立即访问</a>
            </div>
            <p class="channel-tip">无需下载，浏览器直接访问</p>
          </div>
        </div>

        <!-- 服务说明 -->
        <div class="service-info">
          <h3>服务说明</h3>
          <ul>
            <li>轻养到家为您提供足不出户的预约上门服务</li>
            <li>用户可在线选择服务项目和技师</li>
            <li>为您提供健康、绿色、高效、专业的服务</li>
            <li>24小时在线预约，随时随地享受服务</li>
          </ul>
        </div>
      </div>
    </section>
  </div>
</template>

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

.channels-grid {
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

.channel-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 16px;
  padding: 40px 30px;
  text-align: center;
  transition: all 0.3s;
  
  &:hover {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transform: translateY(-5px);
  }
  
  .channel-icon {
    font-size: 48px;
    margin-bottom: 20px;
  }
  
  h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
  }
  
  .channel-desc {
    font-size: 14px;
    color: #999;
    margin-bottom: 25px;
  }
  
  .qrcode-placeholder {
    width: 160px;
    height: 160px;
    background: #f5f5f5;
    border-radius: 8px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 14px;
  }
  
  .channel-actions {
    margin-bottom: 20px;
    
    .btn {
      padding: 14px 40px;
    }
  }
  
  .channel-tip {
    font-size: 13px;
    color: #999;
  }
}

.service-info {
  background: #f9fafb;
  border-radius: 16px;
  padding: 40px;
  
  h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
  }
  
  ul {
    list-style: none;
    
    li {
      position: relative;
      padding-left: 24px;
      margin-bottom: 12px;
      font-size: 15px;
      color: #555;
      line-height: 1.6;
      
      &::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: $primary-color;
        font-weight: bold;
      }
      
      &:last-child {
        margin-bottom: 0;
      }
    }
  }
}
</style>
