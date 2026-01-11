/**
 * 轻养到家 - 管理后台 SPA 路由
 * ABCD 布局
 */

(function() {
  'use strict';

  // 菜单配置
  const menuConfig = [
    { route: 'dashboard', icon: '📊', text: '数据概览' },
    { route: 'user/list', icon: '👤', text: '用户管理' },
    { route: 'technician/list', icon: '💆', text: '技师管理' },
    { route: 'shop/list', icon: '🏪', text: '商家管理' },
    { route: 'service/category', icon: '📋', text: '服务管理' },
    { route: 'order/list', icon: '📦', text: '订单管理' },
    { route: 'finance', icon: '💰', text: '财务管理' }
  ];

  // 路由配置
  const routes = {
    'dashboard': {
      title: '数据概览',
      content: 'dashboard/content.html',
      breadcrumb: ['管理后台', '数据概览']
    },
    'user/list': {
      title: '用户列表',
      content: 'user/list/content.html',
      breadcrumb: ['管理后台', '用户管理', '用户列表']
    },
    'user/detail': {
      title: '用户详情',
      content: 'user/detail/content.html',
      breadcrumb: ['管理后台', '用户管理', '用户详情']
    },
    'technician/list': {
      title: '技师列表',
      content: 'technician/list/content.html',
      breadcrumb: ['管理后台', '技师管理', '技师列表']
    },
    'technician/pending': {
      title: '待审核技师',
      content: 'technician/pending/content.html',
      breadcrumb: ['管理后台', '技师管理', '待审核']
    },
    'technician/detail': {
      title: '技师详情',
      content: 'technician/detail/content.html',
      breadcrumb: ['管理后台', '技师管理', '技师详情']
    },
    'shop/list': {
      title: '商家列表',
      content: 'shop/list/content.html',
      breadcrumb: ['管理后台', '商家管理', '商家列表']
    },
    'shop/pending': {
      title: '待审核商家',
      content: 'shop/pending/content.html',
      breadcrumb: ['管理后台', '商家管理', '待审核']
    },
    'shop/detail': {
      title: '商家详情',
      content: 'shop/detail/content.html',
      breadcrumb: ['管理后台', '商家管理', '商家详情']
    },
    'service/category': {
      title: '服务分类',
      content: 'service/category/content.html',
      breadcrumb: ['管理后台', '服务管理', '服务分类']
    },
    'service/item': {
      title: '服务项目',
      content: 'service/item/content.html',
      breadcrumb: ['管理后台', '服务管理', '服务项目']
    },
    'order/list': {
      title: '订单列表',
      content: 'order/list/content.html',
      breadcrumb: ['管理后台', '订单管理', '订单列表']
    },
    'order/detail': {
      title: '订单详情',
      content: 'order/detail/content.html',
      breadcrumb: ['管理后台', '订单管理', '订单详情']
    },
    'finance': {
      title: '财务概览',
      content: 'finance/content.html',
      breadcrumb: ['管理后台', '财务管理', '财务概览']
    },
    'finance/withdraw': {
      title: '提现管理',
      content: 'finance/withdraw/content.html',
      breadcrumb: ['管理后台', '财务管理', '提现管理']
    }
  };

  // 当前状态
  let currentRoute = null;
  let routeParams = {};
  let sidebarCollapsed = false;

  // DOM 元素
  const sidebarArea = document.getElementById('sidebar-area');
  const sidebarMenu = document.getElementById('sidebarMenu');
  const breadcrumbPath = document.getElementById('breadcrumbPath');
  const contentArea = document.getElementById('content-area');

  /**
   * 解析 URL hash
   */
  function parseHash() {
    const hash = window.location.hash.slice(1) || 'dashboard';
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
   * 初始化侧边栏菜单
   */
  function initSidebar() {
    sidebarMenu.innerHTML = menuConfig.map(item => `
      <a class="menu-item" data-route="${item.route}" onclick="ADMIN.navigateTo('${item.route}')">
        <span class="menu-icon">${item.icon}</span>
        <span class="menu-text">${item.text}</span>
      </a>
    `).join('');
  }

  /**
   * 更新侧边栏激活状态
   */
  function updateSidebarActive(route) {
    const menuItems = sidebarMenu.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
      const itemRoute = item.dataset.route;
      if (route === itemRoute || route.startsWith(itemRoute.split('/')[0] + '/')) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });
  }

  /**
   * 切换侧边栏展开/收起
   */
  function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    sidebarArea.classList.toggle('collapsed', sidebarCollapsed);
  }

  /**
   * 更新面包屑
   */
  function updateBreadcrumb(route) {
    const config = routes[route];
    const breadcrumb = config ? config.breadcrumb : ['管理后台'];
    
    breadcrumbPath.innerHTML = breadcrumb.map((item, index) => {
      const isLast = index === breadcrumb.length - 1;
      return `
        <span class="breadcrumb-item ${isLast ? 'current' : ''}">${item}</span>
        ${!isLast ? '<span class="breadcrumb-separator">/</span>' : ''}
      `;
    }).join('');
  }

  /**
   * 复制当前路径
   */
  function copyPath() {
    const path = window.location.href;
    navigator.clipboard.writeText(path).then(() => {
      ADMIN.showToast('路径已复制');
    });
  }

  /**
   * 刷新页面
   */
  function refreshPage() {
    loadContent(currentRoute, routeParams);
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

    contentArea.innerHTML = '<div class="loading">加载中...</div>';

    try {
      const response = await fetch(config.content);
      if (!response.ok) {
        throw new Error('页面加载失败');
      }
      
      const html = await response.text();
      contentArea.innerHTML = html;
      
      // 执行页面脚本
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
      
      // 触发页面加载事件
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
    
    currentRoute = path;
    routeParams = params;
    
    updateSidebarActive(path);
    updateBreadcrumb(path);
    loadContent(path, params);
  }

  /**
   * 暴露全局 API
   */
  window.ADMIN = {
    navigateTo,
    getParams: () => routeParams,
    getCurrentRoute: () => currentRoute,
    
    // 格式化工具
    formatPrice: (price) => parseFloat(price).toFixed(2),
    
    formatDate: (date) => {
      const d = new Date(date);
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    },
    
    formatDateTime: (date) => {
      const d = new Date(date);
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')} ${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
    },
    
    // Toast 提示
    showToast: (message, duration = 2000) => {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 12px 24px;
        border-radius: 4px;
        font-size: 14px;
        z-index: 9999;
      `;
      toast.textContent = message;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), duration);
    },

    // 确认弹窗
    showConfirm: (message) => {
      return new Promise((resolve) => {
        resolve(window.confirm(message));
      });
    },

    // 显示模态框
    showModal: (title, content, onConfirm) => {
      const overlay = document.createElement('div');
      overlay.className = 'modal-overlay';
      overlay.innerHTML = `
        <div class="modal">
          <div class="modal-header">
            <span class="modal-title">${title}</span>
            <span class="modal-close" onclick="this.closest('.modal-overlay').remove()">×</span>
          </div>
          <div class="modal-body">${content}</div>
          <div class="modal-footer">
            <button class="btn btn-default" onclick="this.closest('.modal-overlay').remove()">取消</button>
            <button class="btn btn-primary" id="modalConfirmBtn">确定</button>
          </div>
        </div>
      `;
      document.body.appendChild(overlay);
      
      overlay.querySelector('#modalConfirmBtn').onclick = () => {
        if (onConfirm) onConfirm();
        overlay.remove();
      };
    }
  };

  // 暴露全局函数
  window.toggleSidebar = toggleSidebar;
  window.copyPath = copyPath;
  window.refreshPage = refreshPage;

  // 监听路由变化
  window.addEventListener('hashchange', handleRouteChange);

  // 初始化
  document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    handleRouteChange();
  });

})();
