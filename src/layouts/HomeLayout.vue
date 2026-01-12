<template>
  <div class="home-layout">
    <Header />
    <main class="main-content">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script setup>
import Header from '../components/home/Header.vue'
import Footer from '../components/home/Footer.vue'
</script>

<style lang="scss" scoped>
.home-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.main-content {
  flex: 1;
}
</style>
