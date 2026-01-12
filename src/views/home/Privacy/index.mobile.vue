<template>
  <div class="privacy-mobile">
    <section class="banner">
      <h1>隐私政策</h1>
      <p>PRIVACY POLICY</p>
    </section>
    <section class="content">
      <p class="update-time">更新日期：2024年1月1日</p>
      <h2>引言</h2>
      <p>轻养到家非常重视用户的隐私和个人信息保护。</p>
      <h2>一、我们收集的信息</h2>
      <ul>
        <li>注册时填写的信息（手机号码、姓名等）</li>
        <li>使用服务时提供的信息（预约地址、服务偏好等）</li>
        <li>设备信息、日志信息等</li>
      </ul>
      <h2>二、联系我们</h2>
      <p>客服热线：400-888-8888<br>电子邮箱：service@qingyang.com</p>
    </section>
  </div>
</template>

<style lang="scss" scoped>
$primary: #07c160;
.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }
.content { padding: 20px; .update-time { color: #999; font-size: 12px; margin-bottom: 20px; } h2 { font-size: 16px; font-weight: 600; color: #333; margin: 20px 0 10px; &:first-of-type { margin-top: 0; } } p { font-size: 14px; line-height: 1.8; color: #555; margin-bottom: 10px; } ul { margin: 10px 0; padding-left: 16px; li { font-size: 14px; line-height: 1.8; color: #555; margin-bottom: 6px; } } }
</style>
