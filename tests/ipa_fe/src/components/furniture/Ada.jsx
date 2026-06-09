import ButtonEl from '../helpers/ctas/Button'
import { useState, useEffect, createContext, useContext, useRef } from 'react'
import adaImage from '../../assets/ada-small.png'
import adaLogo from '../../assets/ada--large.png'
import adaBg from '../../assets/ada--bg.png'
import { ChevronDownIcon } from '@heroicons/react/24/solid'
import { useScrollLock } from '../../helpers/scrollLock'

const DESKTOP_BREAKPOINT = '(min-width: 1024px)'

// Context for managing Ada active state
const AdaContext = createContext({
  isActive: false,
  setIsActive: () => {},
  openByDefault: false,
})

export const useAda = () => useContext(AdaContext)

// Provider component to manage Ada state
export const AdaProvider = ({ children, openByDefault = false }) => {
  const [isActive, setIsActive] = useState(false)

  return (
    <AdaContext.Provider value={{ isActive, setIsActive, openByDefault }}>
      {children}
    </AdaContext.Provider>
  )
}

const adaInfoHtml = (logoUrl) => `
    <div class='hero-image'>
      <img class="!max-w-36 !max-h-36" alt='Ada virtual assistant' src="${logoUrl}" width='600' height='600' />
    </div>
    <h3 class="block">Hi 👋 I'm Ada - IPA's Intelligent Assistant</h3>
    <div class="answerRow">
      <div class="chatBubble">
        <div class="chatBubbleText" dir="auto"><p>How can I help you today?</p></div>
      </div>
    </div>
    <div class="answerRow">
      <div class="chatBubble">
        <div class="chatBubbleText" dir="auto"><p>Choose a quick reply below or enter a short, simple phrase.</p></div>
      </div>
    </div>
    <div class="my-4 text-left">
      <div class='static-question'>Become a member?</div>
      <div class='static-question'>IPA Certification</div>
      <div class='static-question'>Where is the next IPA event?</div>
    </div>
`

export const AdaContainer = (props) => {
  const { isActive, setIsActive } = useAda()
  const { lockScroll, unlockScroll } = useScrollLock()
  const resizeTimeoutRef = useRef(null)

  // Lock/unlock scroll when Ada opens/closes on mobile devices
  useEffect(() => {
    // Only lock scroll on mobile (when window width is less than 1024px)
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
      if (isActive) {
        lockScroll()
      } else {
        unlockScroll()
      }
    } else {
      // Always unlock on desktop (>= 1024px)
      unlockScroll()
    }

    // Cleanup on unmount
    return () => {
      if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        unlockScroll()
      }
    }
  }, [isActive, lockScroll, unlockScroll])

  // Handle window resize to automatically unlock scroll when crossing 1024px threshold
  useEffect(() => {
    if (typeof window === 'undefined') return

    const handleResize = () => {
      // Clear existing timeout
      if (resizeTimeoutRef.current) {
        clearTimeout(resizeTimeoutRef.current)
      }

      // Debounce resize handling
      resizeTimeoutRef.current = setTimeout(() => {
        const currentWidth = window.innerWidth

        // If crossing from mobile to desktop (>= 1024px), unlock scroll
        if (currentWidth >= 1024) {
          unlockScroll()
        }
        // If crossing from desktop to mobile (< 1024px) and Ada is active, lock scroll
        else if (currentWidth < 1024 && isActive) {
          lockScroll()
        }
      }, 150) // 150ms debounce
    }

    window.addEventListener('resize', handleResize)

    return () => {
      window.removeEventListener('resize', handleResize)
      if (resizeTimeoutRef.current) {
        clearTimeout(resizeTimeoutRef.current)
      }
    }
  }, [isActive, lockScroll, unlockScroll])

  return (
    <div
    style={{
      '--ada-bg-image': `url(${adaBg})`
    }}
    className={`ipa-ada-wrapper  ${isActive ? ' ada-active h-full lg:max-h-[90ch]' : 'max-h-0 overflow-hidden'}`}>
      <div data-type="ada-header">
        {props.logo && (
          <img
            src={props.logo.src}
            alt={props.logo.altText || "Footer logo"}
            className="w-auto h-[68px]"
          />
        )}
        <div>Ask Ada</div>
        <button onClick={() => setIsActive(false)} aria-label="Close Ada"><ChevronDownIcon className="w-8 h-8" /></button>
      </div>
      <betty-bot
        api-url="https://betty-api.tasio.co/"
        api-endpoint-token="D666D1E6-C0B8-4AAA-BCDF-D82BD6D9BDFE"
        stream="1"
        user-id=""
        assistant-image={adaLogo}
        user-image=""
        terms-url="https://bettybot.ai/betty-bot-terms-of-use/"
        info-html={adaInfoHtml(adaLogo)}
      />
    </div>
  )
}
export const AdaTrigger = () => {
  const [showButton, setShowButton] = useState(false)
  const [scriptLoaded, setScriptLoaded] = useState(false)
  const { isActive, setIsActive, openByDefault } = useAda()
  const hasAutoOpened = useRef(false)

  // Check if betty-bot script has loaded by checking if the custom element is registered
  // The script https://files.bettybot.ai/ui-widget/betty-bot-ui.js registers the 'betty-bot' custom element
  // We check the CustomElementRegistry, not the DOM - the JSX element in AdaContainer is just markup
  // until the script registers it
  const checkScriptLoaded = () => {
    if (typeof window === 'undefined' || !window.customElements) {
      return false
    }

    try {
      // Check if the custom element is registered (this only happens when the script has fully loaded and executed)
      return customElements.get('betty-bot') !== undefined
    } catch (err) {
      console.debug('Failed to check custom element registry:', err)
      return false
    }
  }

  // Check for script on mount and periodically (since it may load after component mounts)
  useEffect(() => {
    // Initial check
    if (checkScriptLoaded()) {
      setScriptLoaded(true)
      return
    }

    // Poll for script to load (check every 500ms, stop after 10 seconds)
    let attempts = 0
    const maxAttempts = 20
    const checkInterval = setInterval(() => {
      attempts++
      if (checkScriptLoaded()) {
        setScriptLoaded(true)
        clearInterval(checkInterval)
      } else if (attempts >= maxAttempts) {
        // Stop checking after 10 seconds
        clearInterval(checkInterval)
      }
    }, 500)

    return () => clearInterval(checkInterval)
  }, [])

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 300) {
        setShowButton(true)
      } else {
        setShowButton(false)
      }
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  // Auto-open Ada on desktop when openByDefault is enabled
  useEffect(() => {
    if (typeof window === 'undefined') return
    if (!scriptLoaded || !openByDefault || hasAutoOpened.current) return

    if (window.matchMedia(DESKTOP_BREAKPOINT).matches) {
      hasAutoOpened.current = true
      setIsActive(true)
    }
  }, [scriptLoaded, openByDefault, setIsActive])

  // Close Ada if it was auto-opened and viewport drops below desktop breakpoint
  useEffect(() => {
    if (typeof window === 'undefined') return

    const mediaQuery = window.matchMedia(DESKTOP_BREAKPOINT)
    const handleBreakpointChange = (e) => {
      if (hasAutoOpened.current && !e.matches) {
        setIsActive(false)
      }
    }

    mediaQuery.addEventListener('change', handleBreakpointChange)
    return () => mediaQuery.removeEventListener('change', handleBreakpointChange)
  }, [setIsActive])

  const toggleAda = () => {
    setIsActive(!isActive)
  }

  // Return nothing if script is not loaded
  if (!scriptLoaded) {
    return null
  }

  return (
    <ButtonEl
      theme="text"
      buttonType="none"
      showDecorator={false}
      onClick={toggleAda}
      className={`fixed bottom-3 elevation-2 z-[500] transition-all duration-300 right-3 md:right-6 md:bottom-6 hover:scale-110 hover:elevation-6 rounded-full border !border-white !p-1 bg-secondary ${showButton && !isActive ? 'opacity-1' : 'opacity-0'}`}
    >
      <img src={adaImage} alt="" className="w-full h-full max-w-10 max-h-10 rounded-full" />
      <span className="sr-only">Load Ada - IPA's intelligent assistant</span>
    </ButtonEl>
  )
}
