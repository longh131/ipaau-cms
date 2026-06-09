import { useCallback, useLayoutEffect, useRef } from 'react'

// mostly stolen from https://blog.logrocket.com/create-advanced-scroll-lock-react-hook/

const isiOS = () => /iPad|iPhone|Android/.exec(navigator.userAgent) !== null

const setScrollPosition = (targetScroll) => {
  window.scrollTo({
    top: targetScroll,
    left: 0,
    behavior: 'auto'
  })
  document.documentElement.scrollTop = targetScroll
  document.body.scrollTop = targetScroll
}

const verifyScrollRestoration = (targetScroll) => {
  setTimeout(() => {
    const currentScroll = window.scrollY ?? document.documentElement.scrollTop ?? document.body.scrollTop ?? 0
    if (Math.abs(currentScroll - targetScroll) > 1) {
      setScrollPosition(targetScroll)
    }
  }, 50)
}

export const useScrollLock = () => {
  const scrollOffset = useRef(0)
  const touchMoveHandler = useRef(null)

  const lockScroll = useCallback(
    () => {
      document.body.dataset.scrollLock = 'true';
      document.body.style.overflow = 'hidden';
      document.body.style.paddingRight = 'var(--scrollbar-compensation)';

      if (isiOS()) {
        // CRITICAL: Save scroll position BEFORE any style changes that might affect it
        scrollOffset.current = window.scrollY ?? document.documentElement.scrollTop ?? document.body.scrollTop ?? 0

        // Use a simpler approach: just prevent scrolling with overflow hidden and touch-action
        // Don't change position or height which can cause scroll position to reset
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        document.body.style.touchAction = 'none';
        document.documentElement.style.touchAction = 'none';

        // Prevent default on touchmove to stop scrolling
        touchMoveHandler.current = (e) => {
          // Allow scrolling inside the modal if needed
          const target = e.target;
          const isModalContent = target.closest('[data-type="video-modal-content"]') ||
                                 target.closest('[data-type="filter-panel-content"]') ||
                                 target.closest('.ReactModal__Content') ||
                                 target.closest('[role="dialog"]') ||
                                 target.closest('.ipa-ada-wrapper') ||
                                 target.closest('[data-type="mobile-navigation-content"]'); // Ada chatbot

          if (!isModalContent) {
            e.preventDefault();
          }
        };

        document.addEventListener('touchmove', touchMoveHandler.current, { passive: false });
      }
    }, [])

  const unlockScroll = useCallback(
    () => {
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';

      if (isiOS()) {
        // Remove touch event listener
        if (touchMoveHandler.current) {
          document.removeEventListener('touchmove', touchMoveHandler.current);
          touchMoveHandler.current = null;
        }

        // Restore styles - keep it simple
        document.documentElement.style.overflow = '';
        document.documentElement.style.touchAction = '';
        document.body.style.touchAction = '';

        // Restore scroll position - use requestAnimationFrame with a small delay to ensure styles are applied
        const targetScroll = scrollOffset.current

        // Use multiple requestAnimationFrame calls to ensure it happens after DOM updates
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            setScrollPosition(targetScroll)
            verifyScrollRestoration(targetScroll)
          })
        })
      }
      delete document.body.dataset.scrollLock;
    }, []);

    useLayoutEffect(() => {
      const scrollBarCompensation = window.innerWidth - document.body.offsetWidth;
      document.body.style.setProperty('--scrollbar-compensation', `${scrollBarCompensation}px`);
    }, [])
    return { lockScroll, unlockScroll };
}
