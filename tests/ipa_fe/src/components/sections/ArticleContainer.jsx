import React, { useState, useEffect, useRef } from 'react'
import PropTypes from 'prop-types'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { JumpMenu, JumpMenuDevice } from '../helpers/article/JumpMenu'
import themeConfig from '../../../theme.config'
import { dataLayerPush } from '../../helpers/thirdparty'
import { getFriendlyName } from '../../helpers/markup'
// Import all available components for dynamic rendering
import Accordion from './Accordion'
import FAQ from './FAQ'
import BasicContent from './BasicContent'
import CopyBlock from './CopyBlock'
import CalloutCard from './CalloutCard'
import CardListCurated from './CardListCurated'
import CardListDynamic from './CardListDynamic'
import BlogSection from './BlogSection'
import ContentFiftybyFifty from './ContentFiftybyFifty'
import HeroBanner from './HeroBanner'
import HeroSection from './HeroSection'
import CtaSection from './CtaSection'
import NavigationBlock from './NavigationBlock'
import SearchResult from './SearchResult'
import SubPageBanner from './SubPageBanner'
import ThirdParty from './ThirdParty'
import ProductFiftyFiftyContent from './ProductFiftyFiftyContent'
import TagListing from './TagListing'
import MemberPortalEvents from './MemberPortalEvents'
import TestimonialCarousel from './TestimonialCarousel'
import ImageBlock from './ImageBlock'
import StatsCards from './StatsCards'
import TabbedContent from './TabbedContent'
import BentoBox from './BentoBox'
import FeatureBlock from './FeatureBlock'
import ImageBlockWithCarousel from './ImageBlockWithCarousel'
import { generateDataHash } from '../../helpers/contentHash'

const ArticleContainer = (props) => {
  const [activeSection, setActiveSection] = useState('')
  const [navigationItems, setNavigationItems] = useState([])
  const navigationItemsRef = useRef([])
  const [useSidebar, setUseSidebar] = useState(true)

  // Component mapping for dynamic rendering (same as Page.jsx)
  const components = {
    accordion: Accordion,
    FAQ: FAQ,
    basicContent: BasicContent,
    copyBlock: CopyBlock,
    calloutCard: CalloutCard,
    cardListCurated: CardListCurated,
    cardListDynamic: CardListDynamic,
    blogSection: BlogSection,
    contentFiftybyFifty: ContentFiftybyFifty,
    heroBanner: HeroBanner,
    heroSection: HeroSection,
    ctaSection: CtaSection,
    navigationBlock: NavigationBlock,
    searchResult: SearchResult,
    subPageBanner: SubPageBanner,
    thirdParty: ThirdParty,
    productFiftyFiftyContent: ProductFiftyFiftyContent,
    memberPortalEvents: MemberPortalEvents,
    tagListing: TagListing,
    testimonials: TestimonialCarousel,
    imageBlock: ImageBlock,
    statsCards: StatsCards,
    tabbedContent: TabbedContent,
    bentoBox: BentoBox,
    featureBlock: FeatureBlock,
    imageBlockWithCarousel: ImageBlockWithCarousel,
  }

  useEffect(() => {
    setUseSidebar(props.sidebarOnOff)
  }, [])

  // Get sidebar title from Article Container sidebarTitle field, with fallback
  const sidebarTitle = props.sidebarTitle || 'Jump to a section'
  const [isSmall, setIsSmall] = useState(false)
  const [isMedium, setIsMedium] = useState(false)
  const [isLarge, setIsLarge] = useState(false)
  const resizeTimeoutRef = useRef(null)
  const isResizingRef = useRef(false)
  const resizeCooldownRef = useRef(null)

  const updateBreakpoints = () => {
    setIsSmall(window.matchMedia("(max-width: 767px)").matches)
    setIsMedium(window.matchMedia("(min-width: 768px) and (max-width: 1023px)").matches)
    setIsLarge(window.matchMedia("(min-width: 1024px)").matches)

    // Re-find sidebar container after layout changes using ref to get current value
    const navItems = navigationItemsRef.current
    if (navItems.length > 0) {
      const firstTrigger = document.getElementById(`${navItems[0].id}-trigger`)
      if (firstTrigger) {
        let parent = firstTrigger.closest('.overflow-y-auto')
        if (parent) {
          sidebarContainerRef.current = parent
        }
      }
    }

    // Add a cooldown period after resize to prevent immediate scrolling
    if (resizeCooldownRef.current) {
      clearTimeout(resizeCooldownRef.current)
    }
    resizeCooldownRef.current = setTimeout(() => {
      isResizingRef.current = false
    }, 300) // 300ms cooldown after resize completes
  }

  const handleResize = () => {
    // Set flag to prevent scrolling during resize
    isResizingRef.current = true

    // Clear any pending scroll timeouts to prevent delayed scroll actions
    if (scrollTimeoutRef.current) {
      clearTimeout(scrollTimeoutRef.current)
      scrollTimeoutRef.current = null
    }

    // Clear previous timeout
    if (resizeTimeoutRef.current) {
      clearTimeout(resizeTimeoutRef.current)
    }

    // Set new timeout for debouncing
    resizeTimeoutRef.current = setTimeout(() => {
      updateBreakpoints()
    }, 100)
  }

  useEffect(() => {
    // Set initial values
    updateBreakpoints()
    window.addEventListener('resize', handleResize, { passive: true })
    return () => {
      window.removeEventListener('resize', handleResize)
      if (resizeTimeoutRef.current) {
        clearTimeout(resizeTimeoutRef.current)
      }
      if (resizeCooldownRef.current) {
        clearTimeout(resizeCooldownRef.current)
      }
    }
  }, [navigationItems])

  // Generate navigation items from components using their IDs
  useEffect(() => {
    if (props.articleComponents && props.articleComponents.length > 0) {
      const items = props.articleComponents
        .filter(component => component.jumpToId) // jumpToId now contains the component ID
        .filter(component => component.sidebarMenuTitle) // only show items with sidebar menu titles, as editors may not want every component added to the sidebar
        .map(component => ({
          id: component.jumpToId,
          label: getFriendlyName(component.sidebarMenuTitle),
          componentName: component.componentName
        }))
      setNavigationItems(items)
      // Keep ref in sync with state
      navigationItemsRef.current = items

      // Set first item as active by default
      if (items.length > 0) {
        setActiveSection(items[0].id)
      }
    }
  }, [props.articleComponents])

  // Handle navigation click
  const handleNavigationClick = (event, sectionId) => {
    const element = document.getElementById(sectionId)
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }

    let target = event.target;
    if (!target) return

    if (target.nodeName !== 'BUTTON') {
      target = target.closest('button')
    }

    dataLayerPush({
      event: 'navigation_in_page_click',
      click_text: target.innerText
    })
  }

  // Handle scroll to update active section
  const scrollTimeoutRef = useRef(null)
  const lastScrollYRef = useRef(0)
  const sidebarContainerRef = useRef(null)
  const initTimeoutRef = useRef(null)

  useEffect(() => {
    // Find the sidebar container element by looking for the scrollable container
    // It's the parent of the navigation that has overflow-y-auto
    const findSidebarContainer = () => {
      if (navigationItems.length > 0) {
        const firstTrigger = document.getElementById(`${navigationItems[0].id}-trigger`)
        if (firstTrigger) {
          // Find the scrollable parent container
          let parent = firstTrigger.closest('.overflow-y-auto')
          if (parent) {
            sidebarContainerRef.current = parent
          }
        }
      }
    }

    // Use a small timeout to ensure DOM is ready
    initTimeoutRef.current = setTimeout(findSidebarContainer, 0)

    // Check if element is visible within its container
    const isElementVisible = (element, container) => {
      if (!element || !container) return false

      const elementRect = element.getBoundingClientRect()
      const containerRect = container.getBoundingClientRect()

      // Check if element is within container bounds
      return (
        elementRect.top >= containerRect.top &&
        elementRect.bottom <= containerRect.bottom
      )
    }

    const calculateScrollPosition = (triggerRect, containerRect, container, isScrollingDown) => {
      const triggerTop = triggerRect.top - containerRect.top + container.scrollTop
      const triggerBottom = triggerTop + triggerRect.height
      const containerHeight = container.clientHeight

      if (isScrollingDown) {
        return triggerBottom - containerHeight
      }
      return triggerTop
    }

    const scrollSidebarToTrigger = (trigger, container, isScrollingDown) => {
      if (isElementVisible(trigger, container)) {
        return
      }

      const triggerRect = trigger.getBoundingClientRect()
      const containerRect = container.getBoundingClientRect()
      const scrollPosition = calculateScrollPosition(triggerRect, containerRect, container, isScrollingDown)

      container.scrollTo({
        top: scrollPosition,
        behavior: 'smooth'
      })
    }

    const updateSidebarScroll = (newActiveSection, isScrollingDown) => {
      const isLargeScreen = window.matchMedia("(min-width: 1024px)").matches
      if (!isLargeScreen || isResizingRef.current) {
        return
      }

      const trigger = document.getElementById(`${newActiveSection}-trigger`)
      const container = sidebarContainerRef.current
      if (!trigger || !container) {
        return
      }

      scrollSidebarToTrigger(trigger, container, isScrollingDown)
    }

    const findActiveSectionIndex = (sections) => {
      for (let i = sections.length - 1; i >= 0; i--) {
        const section = sections[i]
        const rect = section.getBoundingClientRect()
        if (rect.top <= 120) {
          return i
        }
      }
      return -1
    }

    const updateActiveSection = (evt) => {
      if (navigationItems.length === 0) {
        return
      }

      if (evt && !evt.srcElement) {
        return
      }

      const sections = navigationItems.map(item => document.getElementById(item.id)).filter(Boolean)
      const currentScrollY = window.scrollY ?? document.documentElement.scrollTop ?? 0
      const isScrollingDown = currentScrollY > lastScrollYRef.current
      lastScrollYRef.current = currentScrollY

      const activeIndex = findActiveSectionIndex(sections)
      if (activeIndex === -1) {
        return
      }

      const newActiveSection = navigationItems[activeIndex].id
      setActiveSection(newActiveSection)
      updateSidebarScroll(newActiveSection, isScrollingDown)
    }

    const handleScroll = evt => {
      if (navigationItems.length === 0) return

      // Clear previous timeout
      if (scrollTimeoutRef.current) {
        clearTimeout(scrollTimeoutRef.current)
      }

      // Set new timeout for debouncing
      scrollTimeoutRef.current = setTimeout(() => {
        updateActiveSection(evt)
      }, 50)
    }

    // Initialize scroll position
    lastScrollYRef.current = window.scrollY ?? document.documentElement.scrollTop ?? 0

    window.addEventListener('scroll', handleScroll, { passive: true })
    // Set initial active section without scrolling
    if (navigationItems.length > 0) {
      const sections = navigationItems.map(item => document.getElementById(item.id)).filter(Boolean)
      for (let i = sections.length - 1; i >= 0; i--) {
        const section = sections[i]
        const rect = section.getBoundingClientRect()
        if (rect.top <= 120) {
          setActiveSection(navigationItems[i].id)
          break
        }
      }
    }

    return () => {
      window.removeEventListener('scroll', handleScroll)
      if (scrollTimeoutRef.current) {
        clearTimeout(scrollTimeoutRef.current)
      }
      if (initTimeoutRef.current) {
        clearTimeout(initTimeoutRef.current)
      }
    }
  }, [navigationItems])
  const [backgroundColor, setBackgroundColor] = useState('transparent')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')

  useEffect(() => {
    setLightOrDarkValue(lightOrDark(backgroundColor))
  }, [backgroundColor])

  useEffect(() => {
    const bgColor = backgroundColor.replace('#', '')
    setBackgroundColor((prevState) => (bgColor ? `#${bgColor}` : prevState))
    setLightOrDarkValue(lightOrDark(bgColor))
  }, [])
  // Render component based on type
  const renderComponent = (component) => {
    const { componentName, componentData, jumpToId } = component

    // Get the component dynamically (same approach as Page.jsx)
    const ComponentToRender = components[componentName]

    if (ComponentToRender) {
      delete componentData.key
    } else {
      console.warn(`Component type ${componentName} was not found in ArticleContainer. Ignoring...`)
    }

    // Create a wrapper div with the component ID for navigation
    return (
      <div id={jumpToId} className="scroll-mt-24">
        {ComponentToRender ? <ComponentToRender {...componentData} fromArticleContainer={true} /> : <div className="prose max-w-none">
          <h2 className="text-2xl font-bold mb-4">Component: {componentName}</h2>
          <pre className="bg-grey-subtle p-4 rounded text-sm overflow-auto">
            {JSON.stringify(componentData, null, 2)}
          </pre>
        </div>}
      </div>
    )
  }

  // Filter out title and description props to prevent Section from rendering them
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  return (
    <section
    data-type="articleContainer"
    data-index={props.idx}
    style={{
      '--mobile-bg-url': props.mobileBackgroundImage ? `url(${props.mobileBackgroundImage.src})` : null,
      '--desktop-bg-url': props.desktopBackgroundImage ? `url(${props.desktopBackgroundImage.src})` : null,
      '--bg-color': backgroundColor ?? 'transparent',
      '--ipa-color-light': themeConfig.textColors.primary,
      '--ipa-color-dark': themeConfig.textColors.white,
      '--light-or-dark': lightOrDarkValue,
      'color': `var(--ipa-color-${lightOrDarkValue})`
    }}
      className={componentPadding}
    >
      <div className="container mx-auto">
        {/* items-start overrides implicit items-stretch for grids, and allows sticky sidebar to work correctly. This should be the first child of the grid container */}
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-20 items-start">
          {useSidebar && navigationItems.length > 0 && (
            <div className="col-span-full lg:col-span-1 lg:sticky lg:top-24">
              {(isSmall || isMedium) && (
                <JumpMenuDevice navigationItems={navigationItems} sidebarTitle={sidebarTitle} activeSection={activeSection} handleNavigationClick={handleNavigationClick} />
              )}
              {isLarge && (
                <JumpMenu navigationItems={navigationItems} sidebarTitle={sidebarTitle} activeSection={activeSection} handleNavigationClick={handleNavigationClick} />
              )}
            </div>
          )}

          {/* Main Content */}
          <div className={`col-span-full ${navigationItems.length > 0 && useSidebar ? 'lg:col-span-3' : 'lg:col-span-4'}`}>
            <div className="space-y-12">
              {props.articleComponents && props.articleComponents.length > 0 ? (
                props.articleComponents.map((component) => (
                  <div key={`article-container-component-${generateDataHash(component.componentName)}`} className="border-b border-primary-border pb-8 last:border-b-0">
                    {renderComponent(component)}
                  </div>
                ))
              ) : (
                <div className="text-center py-12">
                  <p className="text-grey">No article components found.</p>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}

ArticleContainer.propTypes = {
  title: PropTypes.string,
  description: PropTypes.string,
  sidebarTitle: PropTypes.string,
  sidebarOnOff: PropTypes.bool,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.bool,
  mobileBackgroundImage: PropTypes.object,
  desktopBackgroundImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  idx: PropTypes.number,
  articleComponents: PropTypes.arrayOf(
    PropTypes.shape({
      componentName: PropTypes.string.isRequired,
      componentData: PropTypes.object,
      jumpToId: PropTypes.string,
      sidebarMenuTitle: PropTypes.string
    })
  )
}

export default ArticleContainer
