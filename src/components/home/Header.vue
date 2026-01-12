<template>
  <header class="header" :class="{ scrolled: isScrolled, 'menu-open': menuOpen }">
    <div class="container">
      <div class="header-inner">
        <router-link to="/home" class="logo">
          <div class="logo-icon">
            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="20" cy="20" r="18" stroke="currentColor" stroke-width="2.5"/>
              <path d="M12 20C12 15.5817 15.5817 12 20 12C24.4183 12 28 15.5817 28 20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
              <circle cx="20" cy="24" r="4" fill="currentColor"/>
            </svg>
          </div>
          <div class="logo-content">
            <span class="logo-text">轻养到家</span>
            <span class="logo-slogan">QINGYANG</span>
          </div>
        </router-link>
        
        <nav class="nav-desktop">
          <router-link v-for="item in navItems" :key="item.path" :to="item.path" class="nav-link">
            {{ item.name }}
          </router-link>
        </nav>
        
        <button class="menu-toggle" @click="menuOpen = !menuOpen" aria-label="菜单">
          <span class="menu-icon"></span>
        </button>
      </div>
    </div>
    
    <nav class="nav-mobile" v-show="menuOpen">
      <router-link 
        v-for="item in navItems" 
        :key="item.path" 
        :to="item.path" 
        class="nav-link"
        @click="menuOpen = false"
      >
        {{ item.name }}
      </router-link>
    </nav>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const navItems = [
  { name: '首页', path: '/home' },
  { name: '商户合作', path: '/home/recruit' },
  { name: '轻养资讯', path: '/home/news' },
  { name: '党建动态', path: '/home/party' },
  { name: '发展历程', path: '/home/history' },
  { name: '预约渠道', path: '/home/download' },
  { name: '商业计划', path: '/home/whitepaper' },
  { name: '关于我们', path: '/home/about' },
  { name: '联系我们', path: '/home/contact' }
]

const isScrolled = ref(false)
const menuOpen = ref(false)

const handleScroll = () => {
  isScrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$mobile: 768px;

.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: transparent;
  transition: all 0.3s ease;
  
  &.scrolled, &.menu-open {
    background: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    
    .logo-icon { color: $primary-color; }
    .logo-text { color: $primary-color; }
    .logo-slogan { color: #999; }
    .nav-link { 
      color: #333; 
      &::after { background: $primary-color; }
      &:hover, &.router-link-active { color: $primary-color; } 
    }
    .menu-icon, &::before, &::after { background: #333; }
  }
}

.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 70px;
  @media (max-width: $mobile) { height: 60px; }
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon {
  width: 40px;
  height: 40px;
  color: #fff;
  transition: transform 0.3s ease;
  
  svg {
    width: 100%;
    height: 100%;
  }
}

.logo:hover .logo-icon {
  transform: rotate(10deg) scale(1.05);
}

.logo-content {
  display: flex;
  flex-direction: column;
}

.logo-text {
  font-size: 22px;
  font-weight: 700;
  color: #fff;
  transition: color 0.3s;
  line-height: 1.2;
}

.logo-slogan {
  font-size: 10px;
  color: rgba(255, 255, 255, 0.7);
  letter-spacing: 2px;
  transition: color 0.3s;
}

.nav-desktop {
  display: flex;
  gap: 40px;
  @media (max-width: $mobile) { display: none; }
  
  .nav-link {
    font-size: 16px;
    color: #fff;
    transition: color 0.3s;
    position: relative;
    
    &::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: #fff;
      transition: width 0.3s;
    }
    
    &:hover, &.router-link-active {
      &::after { width: 100%; }
    }
    
    &.router-link-active {
      font-weight: 600;
    }
  }
}

.menu-toggle {
  display: none;
  width: 30px;
  height: 24px;
  background: none;
  border: none;
  cursor: pointer;
  position: relative;
  @media (max-width: $mobile) { display: block; }
  
  .menu-icon, &::before, &::after {
    content: '';
    position: absolute;
    left: 0;
    width: 100%;
    height: 3px;
    background: #fff;
    border-radius: 2px;
    transition: all 0.3s;
  }
  
  .menu-icon { top: 50%; transform: translateY(-50%); }
  &::before { top: 0; }
  &::after { bottom: 0; }
}

.nav-mobile {
  display: none;
  @media (max-width: $mobile) {
    display: flex;
    flex-direction: column;
    background: #fff;
    padding: 20px;
    border-top: 1px solid #eee;
    
    .nav-link {
      padding: 15px 0;
      font-size: 16px;
      color: #333;
      border-bottom: 1px solid #eee;
      &:last-child { border-bottom: none; }
      &.router-link-active { color: $primary-color; }
    }
  }
}
</style>
