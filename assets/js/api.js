/**
 * 轻养到家 - API 调用工具库
 * 统一处理前后端通信
 */

const API = {
    // API 基础路径
    baseUrl: '/api',
    
    // Token 存储键名
    tokenKey: 'qy_token',
    
    /**
     * 获取存储的 Token
     */
    getToken() {
        return localStorage.getItem(this.tokenKey);
    },
    
    /**
     * 设置 Token
     */
    setToken(token) {
        localStorage.setItem(this.tokenKey, token);
    },
    
    /**
     * 清除 Token
     */
    clearToken() {
        localStorage.removeItem(this.tokenKey);
    },
    
    /**
     * 发起请求
     * @param {string} endpoint - API 端点
     * @param {object} options - 请求选项
     */
    async request(endpoint, options = {}) {
        const url = this.baseUrl + endpoint;
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        // 添加认证头
        const token = this.getToken();
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        const config = {
            method: options.method || 'GET',
            headers,
            ...options
        };
        
        // 处理请求体
        if (options.body && typeof options.body === 'object') {
            config.body = JSON.stringify(options.body);
        }
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            // 处理未授权
            if (response.status === 401) {
                this.clearToken();
                // 可以触发登录弹窗或跳转
                window.dispatchEvent(new CustomEvent('api:unauthorized'));
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            return { code: -1, message: '网络请求失败' };
        }
    },
    
    /**
     * GET 请求
     */
    get(endpoint, params = {}) {
        const query = new URLSearchParams(params).toString();
        const url = query ? `${endpoint}?${query}` : endpoint;
        return this.request(url);
    },
    
    /**
     * POST 请求
     */
    post(endpoint, body = {}) {
        return this.request(endpoint, { method: 'POST', body });
    },
    
    /**
     * PUT 请求
     */
    put(endpoint, body = {}) {
        return this.request(endpoint, { method: 'PUT', body });
    },
    
    /**
     * DELETE 请求
     */
    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

// ============ 用户端 API ============

const UserAPI = {
    // 认证
    auth: {
        sendCode: (phone) => API.post('/user/auth?action=send_code', { phone }),
        login: (phone, code) => API.post('/user/auth?action=login', { phone, code }),
        profile: () => API.get('/user/auth?action=profile'),
        updateProfile: (data) => API.post('/user/auth?action=update_profile', data)
    },
    
    // 服务
    service: {
        categories: () => API.get('/user/service'),
        list: (params) => API.get('/user/service?action=list', params),
        detail: (id) => API.get(`/user/service?action=detail&id=${id}`)
    },
    
    // 技师
    technician: {
        list: (params) => API.get('/user/technician', params),
        detail: (id) => API.get(`/user/technician?id=${id}`),
        services: (id) => API.get(`/user/technician?id=${id}&action=services`),
        reviews: (id, params) => API.get(`/user/technician?id=${id}&action=reviews`, params),
        schedule: (id, date) => API.get(`/user/technician?id=${id}&action=schedule&date=${date}`)
    },
    
    // 订单
    order: {
        create: (data) => API.post('/user/order', data),
        list: (params) => API.get('/user/order', params),
        detail: (id) => API.get(`/user/order?id=${id}`),
        cancel: (id, reason) => API.post(`/user/order?id=${id}&action=cancel`, { reason }),
        pay: (id, method) => API.post(`/user/order?id=${id}&action=pay`, { method })
    },
    
    // 评价
    review: {
        create: (orderId, data) => API.post(`/user/review?order_id=${orderId}`, data),
        list: (techId, params) => API.get(`/user/review?technician_id=${techId}`, params)
    },
    
    // 地址
    address: {
        list: () => API.get('/user/address'),
        create: (data) => API.post('/user/address', data),
        update: (id, data) => API.post(`/user/address?id=${id}`, data),
        delete: (id) => API.delete(`/user/address?id=${id}`),
        setDefault: (id) => API.post(`/user/address?id=${id}&action=default`)
    }
};

// ============ 技师端 API ============

const TechAPI = {
    // 认证
    auth: {
        sendCode: (phone) => API.post('/tech/auth?action=send_code', { phone }),
        login: (phone, code) => API.post('/tech/auth?action=login', { phone, code }),
        profile: () => API.get('/tech/auth?action=profile'),
        updateStatus: (status) => API.post('/tech/auth?action=update_status', { status }),
        updateLocation: (lat, lng) => API.post('/tech/auth?action=update_location', { lat, lng })
    },
    
    // 订单
    order: {
        list: (params) => API.get('/tech/order', params),
        detail: (id) => API.get(`/tech/order?id=${id}`),
        accept: (id) => API.post(`/tech/order?id=${id}&action=accept`),
        reject: (id, reason) => API.post(`/tech/order?id=${id}&action=reject`, { reason }),
        depart: (id) => API.post(`/tech/order?id=${id}&action=depart`),
        arrive: (id) => API.post(`/tech/order?id=${id}&action=arrive`),
        start: (id) => API.post(`/tech/order?id=${id}&action=start`),
        complete: (id) => API.post(`/tech/order?id=${id}&action=complete`)
    },
    
    // 排班
    schedule: {
        list: (params) => API.get('/tech/schedule', params),
        set: (data) => API.post('/tech/schedule', data),
        delete: (id) => API.delete(`/tech/schedule?id=${id}`)
    },
    
    // 钱包
    wallet: {
        overview: () => API.get('/tech/wallet'),
        transactions: (params) => API.get('/tech/wallet?action=transactions', params),
        withdraw: (amount) => API.post('/tech/wallet?action=withdraw', { amount })
    }
};

// ============ 商家端 API ============

const ShopAPI = {
    // 认证
    auth: {
        sendCode: (phone) => API.post('/shop/auth?action=send_code', { phone }),
        login: (phone, code) => API.post('/shop/auth?action=login', { phone, code }),
        profile: () => API.get('/shop/auth?action=profile')
    },
    
    // 技师管理
    technician: {
        list: (params) => API.get('/shop/technician', params),
        detail: (id) => API.get(`/shop/technician?id=${id}`),
        invite: (phone) => API.post('/shop/technician?action=invite', { phone }),
        remove: (id) => API.post(`/shop/technician?id=${id}&action=remove`)
    },
    
    // 订单
    order: {
        list: (params) => API.get('/shop/order', params),
        detail: (id) => API.get(`/shop/order?id=${id}`)
    },
    
    // 财务
    finance: {
        overview: () => API.get('/shop/finance'),
        settlement: (params) => API.get('/shop/finance?action=settlement', params),
        withdraw: (amount) => API.post('/shop/finance?action=withdraw', { amount })
    }
};

// ============ 管理后台 API ============

const AdminAPI = {
    // 认证
    auth: {
        login: (username, password) => API.post('/admin/auth?action=login', { username, password }),
        profile: () => API.get('/admin/auth?action=profile'),
        logout: () => API.post('/admin/auth?action=logout')
    },
    
    // 用户管理
    user: {
        list: (params) => API.get('/admin/user', params),
        detail: (id) => API.get(`/admin/user?id=${id}`),
        enable: (id) => API.post(`/admin/user?id=${id}&action=enable`),
        disable: (id) => API.post(`/admin/user?id=${id}&action=disable`)
    },
    
    // 技师管理
    technician: {
        list: (params) => API.get('/admin/technician', params),
        detail: (id) => API.get(`/admin/technician?id=${id}`),
        approve: (id) => API.post(`/admin/technician?id=${id}&action=approve`),
        reject: (id, reason) => API.post(`/admin/technician?id=${id}&action=reject`, { reason })
    },
    
    // 商家管理
    shop: {
        list: (params) => API.get('/admin/shop', params),
        detail: (id) => API.get(`/admin/shop?id=${id}`),
        approve: (id) => API.post(`/admin/shop?id=${id}&action=approve`),
        reject: (id, reason) => API.post(`/admin/shop?id=${id}&action=reject`, { reason }),
        setCommission: (id, rate) => API.post(`/admin/shop?id=${id}&action=commission`, { rate })
    },
    
    // 服务管理
    service: {
        categories: () => API.get('/admin/service'),
        createCategory: (data) => API.post('/admin/service', data),
        updateCategory: (id, data) => API.post(`/admin/service?id=${id}`, data),
        deleteCategory: (id) => API.delete(`/admin/service?id=${id}`),
        items: (categoryId) => API.get(`/admin/service?action=items&category_id=${categoryId}`),
        createItem: (data) => API.post('/admin/service?action=item', data),
        updateItem: (id, data) => API.post(`/admin/service?action=item&id=${id}`, data),
        deleteItem: (id) => API.delete(`/admin/service?action=item&id=${id}`)
    },
    
    // 订单管理
    order: {
        list: (params) => API.get('/admin/order', params),
        detail: (id) => API.get(`/admin/order?id=${id}`),
        refund: (id, amount, reason) => API.post(`/admin/order?id=${id}&action=refund`, { amount, reason })
    },
    
    // 财务管理
    finance: {
        overview: () => API.get('/admin/finance'),
        withdrawals: (params) => API.get('/admin/finance?action=withdrawals', params),
        approveWithdraw: (id) => API.post(`/admin/finance?action=withdraw&id=${id}&sub_action=approve`),
        rejectWithdraw: (id, reason) => API.post(`/admin/finance?action=withdraw&id=${id}&sub_action=reject`, { reason }),
        settings: () => API.get('/admin/finance?action=settings'),
        updateSettings: (data) => API.post('/admin/finance?action=settings', data)
    }
};

// 导出
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { API, UserAPI, TechAPI, ShopAPI, AdminAPI };
}
