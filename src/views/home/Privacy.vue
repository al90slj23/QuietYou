<template>
  <div class="privacy">
    <section class="page-banner">
      <div class="container">
        <h1>隐私政策</h1>
        <p class="subtitle">PRIVACY POLICY</p>
      </div>
    </section>
    <section class="content section">
      <div class="container">
        <div class="article">
          <p class="update-time">更新日期：2024年1月1日</p>
          <h2>引言</h2>
          <p>轻养到家（以下简称"我们"）非常重视用户的隐私和个人信息保护。您在使用我们的服务时，我们可能会收集和使用您的相关信息。</p>
          <h2>一、我们收集的信息</h2>
          <ul>
            <li>您在注册账户时填写的信息，如手机号码、姓名等</li>
            <li>您在使用服务时提供的信息，如预约地址、服务偏好等</li>
            <li>您使用服务时的设备信息、日志信息等</li>
          </ul>
          <h2>二、联系我们</h2>
          <p>客服热线：400-888-8888<br>电子邮箱：service@qingyang.com</p>
        </div>
      </div>
    </section>
  </div>
</template>

<style lang="scss" scoped>
$primary-color: #07c160;
$mobile: 768px;
.page-banner { background: linear-gradient(135deg, $primary-color 0%, #10b981 100%); color: #fff; text-align: center; padding: 140px 0 80px; @media (max-width: $mobile) { padding: 120px 0 60px; } h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; @media (max-width: $mobile) { font-size: 28px; } } .subtitle { font-size: 14px; opacity: 0.8; letter-spacing: 3px; } }
.article { max-width: 800px; margin: 0 auto; .update-time { color: #999; font-size: 14px; margin-bottom: 30px; } h2 { font-size: 20px; font-weight: 600; color: #333; margin: 30px 0 15px; &:first-of-type { margin-top: 0; } } p { font-size: 15px; line-height: 1.8; color: #555; margin-bottom: 15px; } ul { margin: 15px 0; padding-left: 20px; li { font-size: 15px; line-height: 1.8; color: #555; margin-bottom: 8px; } } }
</style>
