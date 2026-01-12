import { ref, onMounted, onUnmounted } from 'vue'

const MOBILE_BREAKPOINT = 768

export function useMediaQuery(query) {
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

// 便捷方法：检测是否为移动端（SSR 安全）
export function useMobileDetect() {
  const getIsMobile = () => {
    if (typeof window === 'undefined') return false
    return window.innerWidth < MOBILE_BREAKPOINT
  }
  
  const isMobile = ref(getIsMobile())
  
  const updateIsMobile = () => {
    isMobile.value = getIsMobile()
  }

  onMounted(() => {
    updateIsMobile() // 确保挂载后更新
    window.addEventListener('resize', updateIsMobile)
  })

  onUnmounted(() => {
    window.removeEventListener('resize', updateIsMobile)
  })

  return { isMobile }
}
