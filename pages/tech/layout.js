/**
 * 轻养到家 - 技师端 SPA 路由
 * 遵循 ZERO 框架规范
 */

(function() {
  'use strict';

  // 路由配置
  const routes = {
    'home': {
      title: '技师工作台',
      content: 'home/content.html',
      showTabbar: true,
      showHeader: true
    },
    'order/list': {
      title: '订单管理',
      content: 'order/list/content.html',
      showTabbar: true,
      showHeader: true
    },
    'order/detail': {
      title: '订单详情',
      content: 'order/detail/content.html',
      showTabbar: false,
      showHeader: true,
      showBack: true
    },
    'schedule': {
      title: '排班管理',
      content: 'schedule/content.html',
      showTabbar: true,
      showHeader: true
    },
    'wallet': {
      title: '我的钱包',
      content: 'wallet/content.html',
      showTabbar: true,
      showHeader: true
    },
    'wallet/withdraw': {
      title: '申请提现',
      content: 'wallet/withdraw/content.html',
      showTabbar: false,
      showHeader: true,
      showBack: true
    },
    'wallet/records': {
      title: '交易记录',
      content: 'wallet/records/content.html',
      showTabbar: false,
      showHeader: true,
      showBack: true
    },
    'profile': {
      title: '个人中心',
      content: 'profile/content.html',
      showTabbar: true,
      showHeader: true
    },
    'profile/settings': {
      title: '设置',
      content: 'profile/settings/content.html',
      showTabbar: false,
      showHeader: true,
      showBack: true
    }
  };

  // 当前路由状态
  let currentRoute = null;
  let routeParams = {};
  let routeHistory = [];

  // DOM 元素
  const app = document.getElementById('app');
  const headerArea = document.getElementById('header-area');
  const contentArea = document.getElementById('content-area');
  const tabbarArea = document.getElementById('tabbar-area');

  /**
   * 解析 URL hash
   */
  function parseHash() {
    const hash = window.location.hash.slice(1) || 'home';
    const [path, queryString] = hash.split('?');
    const params = {};
    
    if (queryString) {
      queryString.split('&').forEach(pair => {
        const [key, value] = pair.split('=');
        params[decodeURIComponent(key)] = decodeURIComponent(value || '');
      });
    }
    
    return { path, params };
  }

  /**
   * 导航到指定路由
   */
  function navigateTo(path, params = {}) {
    let hash = path;
    const queryParts = [];
    
    Object.keys(params).forEach(key => {
      queryParts.push(`${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`);
    });
    
    if (queryParts.length > 0) {
      hash += '?' + queryParts.join('&');
    }
    
    window.location.hash = hash;
  }

  /**
   * 返回上一页
   */
  function goBack() {
    if (routeHistory.length > 1) {
      routeHistory.pop();
      const prev = routeHistory[routeHistory.length - 1];
      navigateTo(prev.path, prev.params);
    } else {
      navigateTo('home');
    }
  }

  /**
   * 更新 Header
   */
  function updateHeader(route, config) {
    if (!config.showHeader) {
      app.classList.add('hide-header');
      return;
    }
    
    app.classList.remove('hide-header');
    
    let headerHTML = '<div class="header-content">';
    
    if (config.showBack) {
      headerHTML += `<span class="header-back" onclick="window.TECH.goBack()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
        </svg>
      </span>`;
    }
    
    headerHTML += `<span class="header-title">${config.title}</span>`;
    headerHTML += '</div>';
    
    headerArea.innerHTML = headerHTML;
  }

  /**
   * 更新 Tabbar
   */
  function updateTabbar(route) {
    const config = routes[route];
    
    if (!config || !config.showTabbar) {
      app.classList.add('hide-tabbar');
      return;
    }
    
    app.classList.remove('hide-tabbar');
    
    // 更新 active 状态
    const tabbarItems = tabbarArea.querySelectorAll('.tabbar-item');
    tabbarItems.forEach(item => {
      const itemRoute = item.dataset.route;
      if (route === itemRoute || route.startsWith(itemRoute + '/')) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });
  }

  /**
   * 加载页面内容
   */
  async function loadContent(route, params) {
    const config = routes[route];
    
    if (!config) {
      contentArea.innerHTML = `
        <div class="empty">
          <div class="empty-icon">😕</div>
          <div class="empty-text">页面不存在</div>
        </div>
      `;
      return;
    }

    // 显示加载状态
    contentArea.innerHTML = '<div class="loading">加载中...</div>';

    try {
      const response = await fetch(config.content);
      if (!response.ok) {
        throw new Error('页面加载失败');
      }
      
      const html = await response.text();
      contentArea.innerHTML = html;
      
      // 执行页面初始化脚本
      const scripts = contentArea.querySelectorAll('script');
      scripts.forEach(script => {
        const newScript = document.createElement('script');
        if (script.src) {
          newScript.src = script.src;
        } else {
          newScript.textContent = script.textContent;
        }
        script.parentNode.replaceChild(newScript, script);
      });
      
      // 触发页面加载完成事件
      window.dispatchEvent(new CustomEvent('pageLoaded', { 
        detail: { route, params } 
      }));
      
    } catch (error) {
      console.error('加载页面失败:', error);
      contentArea.innerHTML = `
        <div class="empty">
          <div class="empty-icon">😕</div>
          <div class="empty-text">页面加载失败，请重试</div>
        </div>
      `;
    }
  }

  /**
   * 处理路由变化
   */
  function handleRouteChange() {
    const { path, params } = parseHash();
    const config = routes[path];
    
    if (!config) {
      const parentPath = path.split('/').slice(0, -1).join('/');
      if (routes[parentPath]) {
        navigateTo(parentPath, params);
        return;
      }
    }
    
    // 记录历史
    if (currentRoute !== path) {
      routeHistory.push({ path, params });
      if (routeHistory.length > 20) {
        routeHistory.shift();
      }
    }
    
    currentRoute = path;
    routeParams = params;
    
    const routeConfig = config || { 
      title: '技师工作台', 
      showHeader: true, 
      showTabbar: false,
      showBack: true
    };
    
    updateHeader(path, routeConfig);
    updateTabbar(path);
    loadContent(path, params);
  }

  /**
   * 初始化 Tabbar 点击事件
   */
  function initTabbar() {
    const tabbarItems = tabbarArea.querySelectorAll('.tabbar-item');
    tabbarItems.forEach(item => {
      item.addEventListener('click', () => {
        const route = item.dataset.route;
        navigateTo(route);
      });
    });
  }

  /**
   * 暴露全局 API
   */
  window.TECH = {
    navigateTo,
    goBack,
    getParams: () => routeParams,
    getCurrentRoute: () => currentRoute,
    
    // 工具方法
    formatPrice: (price) => {
      return parseFloat(price).toFixed(2);
    },
    
    formatDate: (date) => {
      const d = new Date(date);
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    },
    
    formatTime: (date) => {
      const d = new Date(date);
      return `${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
    },
    
    formatDateTime: (date) => {
      const d = new Date(date);
      return `${d.getMonth() + 1}月${d.getDate()}日 ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
    },
    
    showToast: (message, duration = 2000) => {
      const toast = document.createElement('div');
      toast.className = 'toast';
      toast.textContent = message;
      toast.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        z-index: 9999;
      `;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), duration);
    },

    showConfirm: (message) => {
      return new Promise((resolve) => {
        resolve(window.confirm(message));
      });
    }
  };

  // 监听 hash 变化
  window.addEventListener('hashchange', handleRouteChange);

  // 初始化
  document.addEventListener('DOMContentLoaded', () => {
    initTabbar();
    handleRouteChange();
  });

})();
