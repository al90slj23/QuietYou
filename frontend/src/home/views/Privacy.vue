<template>
  <div class="privacy">
    <!-- Banner -->
    <section class="page-banner">
      <div class="container">
        <h1>隐私政策</h1>
        <p class="subtitle">PRIVACY POLICY</p>
      </div>
    </section>

    <!-- 内容 -->
    <section class="content section">
      <div class="container">
        <div class="article">
          <p class="update-time">更新日期：2024年1月1日</p>
          
          <h2>引言</h2>
          <p>
            轻养到家（以下简称"我们"）非常重视用户的隐私和个人信息保护。您在使用我们的服务时，我们可能会收集和使用您的相关信息。
            我们希望通过本《隐私政策》向您说明，在使用我们的服务时，我们如何收集、使用、储存和分享这些信息，以及我们为您提供的访问、更新、控制和保护这些信息的方式。
          </p>

          <h2>一、我们收集的信息</h2>
          <p>我们提供服务时，可能会收集、储存和使用下列与您有关的信息：</p>
          <ul>
            <li>您在注册账户时填写的信息，如手机号码、姓名等</li>
            <li>您在使用服务时提供的信息，如预约地址、服务偏好等</li>
            <li>您使用服务时的设备信息、日志信息等</li>
            <li>您的位置信息（仅在您授权后获取）</li>
          </ul>

          <h2>二、我们如何使用信息</h2>
          <p>我们可能将收集的信息用于以下用途：</p>
          <ul>
            <li>向您提供服务</li>
            <li>改进我们的服务质量</li>
            <li>向您发送服务相关的通知</li>
            <li>预防或阻止非法活动</li>
          </ul>

          <h2>三、信息安全</h2>
          <p>
            我们会采取适当的安全措施来保护您的个人信息，防止数据遭到未经授权的访问、公开披露、使用、修改、损坏或丢失。
          </p>

          <h2>四、联系我们</h2>
          <p>
            如果您对本隐私政策有任何疑问、意见或建议，请通过以下方式与我们联系：<br>
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
