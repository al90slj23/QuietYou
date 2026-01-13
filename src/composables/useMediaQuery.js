import { ref, onMounted, onUnmounted } from 'vue'

const MOBILE_BREAKPOINT = 768

// 检测是否为移动设备（通过 User-Agent）
const isMobileDevice = () => {
  if (typeof navigator === 'undefined') return true
  const ua = navigator.userAgent || navigator.vendor || ''
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile|CriOS/i.test(ua)
}

// 主要导出：检测是否为移动端
export function useMediaQuery() {
  const getIsMobile = () => {
    if (typeof window === 'undefined') return true // SSR 默认移动端
    // 优先使用 User-Agent 检测（更可靠），然后结合屏幕宽度
    const byUserAgent = isMobileDevice()
    const byWidth = window.innerWidth < MOBILE_BREAKPOINT
    // 如果 UA 检测为移动端，直接返回 true
    // 如果 UA 检测为 PC，但宽度小于断点（可能是调试模式），也返回 true
    return byUserAgent || byWidth
  }
  
  const isMobile = ref(getIsMobile())
  
  const updateIsMobile = () => {
    isMobile.value = getIsMobile()
  }

  onMounted(() => {
    updateIsMobile()
    window.addEventListener('resize', updateIsMobile)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', updateIsMobile)
  })

  return { isMobile }
}

// 自定义媒体查询
export function useCustomMediaQuery(query) {
  const matches = ref(false)
  let mediaQuery = null

  const updateMatches = () => {
    matches.value = mediaQuery?.matches ?? false
  }

  onMounted(() => {
    mediaQuery = window.matchMedia(query)
    matches.value = mediaQuery.matches
    mediaQuery.addEventListener('change', updateMatches)
  })

  onUnmounted(() => {
    mediaQuery?.removeEventListener('change', updateMatches)
  })

  return matches
}

// 别名，保持兼容
export const useMobileDetect = useMediaQuery
