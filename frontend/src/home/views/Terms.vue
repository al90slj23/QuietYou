<template>
  <div class="terms">
    <!-- Banner -->
    <section class="page-banner">
      <div class="container">
        <h1>服务条款</h1>
        <p class="subtitle">TERMS OF SERVICE</p>
      </div>
    </section>

    <!-- 内容 -->
    <section class="content section">
      <div class="container">
        <div class="article">
          <p class="update-time">更新日期：2024年1月1日</p>
          
          <h2>一、服务说明</h2>
          <p>
            轻养到家是一个上门养生推拿服务预约平台。我们为用户提供在线预约服务，连接用户与专业技师，提供便捷的上门养生服务。
          </p>

          <h2>二、用户注册</h2>
          <p>
            用户在使用本平台服务前需要注册账号。注册时，用户应提供真实、准确、完整的个人信息，并在信息发生变化时及时更新。
          </p>

          <h2>三、服务预约</h2>
          <ul>
            <li>用户可通过平台预约上门服务</li>
            <li>预约成功后，请保持手机畅通，以便技师联系</li>
            <li>如需取消或更改预约，请提前通知</li>
            <li>服务完成后，请及时确认并评价</li>
          </ul>

          <h2>四、用户行为规范</h2>
          <p>用户在使用本平台服务时，应遵守以下规范：</p>
          <ul>
            <li>遵守国家法律法规</li>
            <li>尊重技师，不得有任何骚扰、侮辱行为</li>
            <li>提供真实的服务地址和联系方式</li>
            <li>按时支付服务费用</li>
          </ul>

          <h2>五、免责声明</h2>
          <p>
            本平台仅提供信息撮合服务，不对服务过程中产生的任何纠纷承担直接责任。如遇问题，请及时联系客服处理。
          </p>

          <h2>六、联系方式</h2>
          <p>
            如果您对本服务条款有任何疑问，请联系我们：<br>
            客服热线：400-888-8888<br>
            电子邮箱：service@qingyang.com
          </p>
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

.article {
  max-width: 800px;
  margin: 0 auto;
  
  .update-time {
    color: #999;
    font-size: 14px;
    margin-bottom: 30px;
  }
  
  h2 {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin: 30px 0 15px;
    
    &:first-of-type {
      margin-top: 0;
    }
  }
  
  p {
    font-size: 15px;
    line-height: 1.8;
    color: #555;
    margin-bottom: 15px;
  }
  
  ul {
    margin: 15px 0;
    padding-left: 20px;
    
    li {
      font-size: 15px;
      line-height: 1.8;
      color: #555;
      margin-bottom: 8px;
    }
  }
}
</style>
