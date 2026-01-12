import { ref, onMounted, onUnmounted } from 'vue'

const MOBILE_BREAKPOINT = 768

// 主要导出：检测是否为移动端
export function useMediaQuery() {
  const getIsMobile = () => {
    if (typeof window === 'undefined') return true // SSR 默认移动端
    return window.innerWidth < MOBILE_BREAKPOINT
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
