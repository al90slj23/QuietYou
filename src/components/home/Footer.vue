<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-links">
        <router-link to="/home/terms">服务条款</router-link>
        <span class="divider">|</span>
        <router-link to="/home/privacy">隐私政策</router-link>
      </div>
      
      <div class="footer-copyright">
        <p>Copyright 2024-{{ currentYear }} 轻养到家 版权所有</p>
        <p class="icp">
          <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener">蜀ICP备XXXXXXXX号-1</a>
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>

<style lang="scss" scoped>
$primary-color: #07c160;

.footer {
  background: #2c2c2c;
  color: #999;
  padding: 40px 0;
  text-align: center;
}

.footer-links {
  margin-bottom: 20px;
  
  a {
    color: #ccc;
    font-size: 14px;
    transition: color 0.3s;
    &:hover { color: $primary-color; }
  }
  
  .divider {
    margin: 0 15px;
    color: #666;
  }
}

.footer-copyright {
  font-size: 13px;
  line-height: 1.8;
  
  .icp {
    margin-top: 5px;
    a {
      color: #999;
      &:hover { color: $primary-color; }
    }
  }
}
</style>
