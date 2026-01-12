<template>
  <div class="merchant-page">
    <div class="placeholder">
      <h1>商户端</h1>
      <p>功能开发中...</p>
      <router-link to="/home" class="back-link">返回首页</router-link>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.merchant-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f5f5; }
.placeholder { text-align: center; h1 { font-size: 32px; color: #333; margin-bottom: 16px; } p { color: #666; margin-bottom: 24px; } .back-link { color: #07c160; } }
</style>
