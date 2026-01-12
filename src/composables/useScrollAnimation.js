import { onMounted, onUnmounted } from 'vue'

/**
 * 滚动动画 composable
 * 为元素添加进入视口时的淡入动画
 */
export function useScrollAnimation() {
  let observer = null

  const initScrollAnimation = () => {
    const elements = document.querySelectorAll('.animate-on-scroll')
    
    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry, index) => {
          if (entry.isIntersecting) {
            // 添加延迟，让元素依次出现
            const delay = entry.target.dataset.delay || index * 100
            setTimeout(() => {
              entry.target.classList.add('animated')
            }, Math.min(delay, 400))
            observer.unobserve(entry.target)
          }
        })
      },
      {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      }
    )

    elements.forEach((el) => {
      observer.observe(el)
    })
  }

  onMounted(() => {
    // 延迟初始化，确保 DOM 已渲染
    setTimeout(initScrollAnimation, 100)
  })

  onUnmounted(() => {
    if (observer) {
      observer.disconnect()
    }
  })

  return { initScrollAnimation }
}
