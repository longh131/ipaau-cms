import { Suspense, useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Header from './furniture/Header'
import Breadcrumbs from './furniture/Breadcrumbs'
import Footer from './furniture/Footer'
import Loader from './helpers/Loader'
import Alert from './furniture/Alert'
import BlobBackground from './furniture/BlobBackground'
import Skip from './furniture/Skip'

import Accordion from './sections/Accordion'
import ArticleContainer from './sections/ArticleContainer'
import FAQ from './sections/FAQ'
import BasicContent from './sections/BasicContent'
import CopyBlock from './sections/CopyBlock'
import CalloutCard from './sections/CalloutCard'
import CardListCurated from './sections/CardListCurated'
import CardListDynamic from './sections/CardListDynamic'
import BlogSection from './sections/BlogSection'
import ContentFiftybyFifty from './sections/ContentFiftybyFifty'
import HeroBanner from './sections/HeroBanner'
import HeroSection from './sections/HeroSection'
import CtaSection from './sections/CtaSection'
import NavigationBlock from './sections/NavigationBlock'
import SearchResult from './sections/SearchResult'
import SubPageBanner from './sections/SubPageBanner'
import ThirdParty from './sections/ThirdParty'
import { showExternalLinkIcons } from '../helpers/style'
import { generateDataHash } from '../helpers/contentHash'
import ProductFiftyFiftyContent from './sections/ProductFiftyFiftyContent'
import TagListing from './sections/TagListing'
import MemberPortalEvents from './sections/MemberPortalEvents'
import TestimonialCarousel from './sections/TestimonialCarousel'
import BentoBox from './sections/BentoBox'
import FeatureBlock from './sections/FeatureBlock'
import ImageBlock from './sections/ImageBlock'
import ImageBlockWithCarousel from './sections/ImageBlockWithCarousel'
import StatsCards from './sections/StatsCards'
import TabbedContent from './sections/TabbedContent'
import EventHeader from './sections/EventHeader'
import EventDetailsBlock from './sections/EventDetailsBlock'
import ArticleHeader from './sections/ArticleHeader'
import ShareIcons from './sections/ShareIcons'
import VideoBlock from './sections/VideoBlock'
import RegistrationBanner from './sections/RegistrationBanner'
import FindAnAccountant from './sections/FindAnAccountant'
import Newsletter from './sections/Newsletter'
import OneStepApplicationForm from './sections/OneStepApplicationForm'
import { Decorator1, Decorator2, Decorator3 } from './decorators'
import { AdaContainer, AdaProvider } from './furniture/Ada'

function Page(props) {
  const [componentsList, setComponentsList] = useState([])
  const [menuActive, setMenuActive] = useState(false)
  const [searchActive, setSearchActive] = useState(false)
  const [ctasActive, setCtasActive] = useState(false)
  const [thirdPartyAdded, setThirdPartyAdded] = useState(false)

  // footer override from public data.json (optional)
  const [publicFooter, setPublicFooter] = useState(null)

  const components = {
    accordion: Accordion,
    articleContainer: ArticleContainer,
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
    bentoBox: BentoBox,
    featureBlock: FeatureBlock,
    imageBlock: ImageBlock,
    imageBlockWithCarousel: ImageBlockWithCarousel,
    statsCards: StatsCards,
    tabbedContent: TabbedContent,
    eventHeader: EventHeader,
    eventDetailsBlock: EventDetailsBlock,
    articleHeader: ArticleHeader,
    shareIcons: ShareIcons,
    video: VideoBlock,
    registrationBanner: RegistrationBanner,
    findAnAccountantComponent: FindAnAccountant,
    newsletter: Newsletter,
    oneStepApplicationForm: OneStepApplicationForm,
    decorator1: Decorator1,
    decorator2: Decorator2,
    decorator3: Decorator3,
  }

  const noDataExcludeList = [
    // a list of items that don't require data, and will not trigger the warning and be ignored if the data object does not exist.
    // there should not be too many of these.
    'thirdParty',
    'decorator1',
    'decorator2',
    'decorator3',
  ]

  useEffect(() => {
    showExternalLinkIcons()
  }, [])

  // Handle deep link scrolling after content has loaded
  useEffect(() => {
    if (componentsList.length > 0 && window.location.hash) {
      // Small delay to ensure DOM has fully rendered
      const timeoutId = setTimeout(() => {
        const hash = window.location.hash.substring(1) // Remove the '#'
        const element = document.getElementById(hash)
        if (element) {
          element.scrollIntoView({ behavior: 'smooth' })
        }
      }, 100)

      return () => clearTimeout(timeoutId)
    }
  }, [componentsList])

  useEffect(() => {
    if (props.content?.length) {
      setComponentsList(() => {
        // This only loads once, on page load, so we don't need to worry about the content of prevState
        const newState = []

        const decoratorList = [
          { componentName: 'decorator1', componentData: {} },
          { componentName: 'decorator2', componentData: {} },
          { componentName: 'decorator3', componentData: {} },
        ]
        let decoratorIndex = 0

        // Insert decorators every 3rd component
        const contentWithDecorators = []
        props.content.forEach((comp, index) => {
          contentWithDecorators.push(comp)
          // Insert decorator after every 3rd component (after indices 2, 5, 8, etc.)
          if ((index + 1) % 3 === 0) {
            contentWithDecorators.push(decoratorList[decoratorIndex])
            decoratorIndex++
            if (decoratorIndex >= decoratorList.length) {
              decoratorIndex = 0
            }
          }
        })

        // Create a new array instead of mutating props.content
        const contentWithThirdParty = !thirdPartyAdded
          ? [...contentWithDecorators, { componentName: 'thirdParty', componentData: {} }]
          : contentWithDecorators

        setThirdPartyAdded(true)

        contentWithThirdParty.forEach((comp) => {
          if (comp.componentData) {
            // Check for key property first - if present, use it directly
            if (comp.componentData.key) {
              newState.push({
                name: comp.componentName,
                data: comp.componentData,
                uniqueKey: comp.componentData.key
              })
              return
            }

            // Try to find a unique identifier in the component data
            const uniqueId = comp.componentData.id ||
              comp.componentData.itemId ||
              comp.componentData.nodeId ||
              comp.componentData.uniqueId

            newState.push({
              name: comp.componentName,
              data: comp.componentData,
              // Store a unique key for React - prefer data identifier, fallback to data hash
              uniqueKey: uniqueId
            ? `${comp.componentName}-${uniqueId}`
            : `${comp.componentName}-${generateDataHash(comp.componentData)}`
            })
          } else {
            console.warn(`No data sent for component ${comp.componentName}`)
          }
        })
        return [...newState]
      })
    }
  }, [props.content])

  useEffect(() => {
    // Attempt to load public/data.json from the dev server public folder.
    // Public assets are typically served at the site root (e.g. /data.json).
    const loadPublicData = async () => {
      try {
        const response = await fetch('/data.json')
        if (!response.ok) {
          throw new Error(`Failed to fetch public data.json: ${response.status} ${response.statusText}`)
        }
        const json = await response.json()
        if (json?.result?.footer) {
          setPublicFooter(json.result.footer)
        }
      } catch (error) {
        // silent fallback to server-provided props.footer
        if (process.env.NODE_ENV === 'development') {
          const errorMessage = error instanceof Error ? error.message : 'Unknown error'
          console.warn('Could not load public data.json:', errorMessage)
        }
      }
    }
    loadPublicData()
  }, [])

  return (
    <AdaProvider openByDefault={props.res.openAdaByDefault}>
      <Skip />
      {props.res.globalEnablePageAlert &&
        (props.res.globalPageAlertTitle || props.res.globalPageAlertDescription) && (
          <Alert
            title={props.res.globalPageAlertTitle}
            type={props.res.globalPageAlertType}
            description={props.res.globalPageAlertDescription}
          />
        )}
      {props.res.enablePageAlert && (props.res.pageAlertTitle || props.res.pageAlertDescription) && (
        <Alert
          title={props.res.pageAlertTitle}
          type={props.res.pageAlertType}
          description={props.res.pageAlertDescription}
        />
      )}
      <Header
        deskLogo={props.nav?.siteLogo ?? ''}
        mobLogo={props.nav?.siteLogoMobile ?? ''}
        ctas={props.nav?.utilityLinks}
        menuActive={menuActive}
        setMenuActive={setMenuActive}
        searchActive={searchActive}
        setSearchActive={setSearchActive}
        ctasActive={ctasActive}
        setCtasActive={setCtasActive}
        nav={props.nav?.items}
        headerDataSource={props.nav?.headerDataSource}
        res={props.res}
      >
        {process.env.DISABLE_BLOB_EFFECT !== 'true' && (
          <BlobBackground type={props.breadcrumbs?.length ? "left" : "animated"} />
        )}
      </Header>
      <main id="main">
        <Breadcrumbs items={props.breadcrumbs} />
        <Suspense key={'pageSuspender'} fallback={<Loader />}>
          {componentsList &&
            componentsList.length > 0 &&
            componentsList.map((comp, idx) => {
              const TagName = components[comp.name]
              if (!TagName) {
                if (process.env.NODE_ENV === 'development') {
                  console.warn(`Component type ${comp.name} was not found. Ignoring...`)
                }
                return null
              }
              if (!comp.data || !Object.keys(comp.data).length && !noDataExcludeList.includes(comp.name)) {
                if (process.env.NODE_ENV === 'development') {
                  console.warn(`No data for component type ${comp.name} was found. Ignoring...`)
                }
                return null
              }
              const data = {...comp.data}
              // Delete key property - it's used as React key, not as component prop
              delete data.key
              const showBreadcrumbs = props.breadcrumbs && idx === 0
              // Use uniqueKey generated when building componentsList
              // It prefers key property, then other data identifiers (id, itemId, etc.), or falls back to data hash
              return <TagName key={comp.uniqueKey} {...data} allData={props.allData} idx={idx} breadcrumbs={showBreadcrumbs} />
            })}
        </Suspense>
      </main>
      {!props.res.disablePrimaryFooter && (
        <Footer
          {...(publicFooter || props.footer)}
          deskLogo={props.nav?.siteLogo ?? ''}
          mobLogo={props.nav?.siteLogoMobile ?? ''}
          disableSticky={props.res.disableUtilityFooter}
          stickyNavPrimary={props.nav?.primaryLink}
          stickyNavSecondary={props.nav?.mobileStickyNavigationItems}
        />
      )}
      <div id="portal-wrapper" className="fixed bottom-0 left-0 right-0 z-50 flex items-end justify-start" />
      <div id="portal-ada">
        <AdaContainer logo={props.nav?.siteLogo} />
      </div>
      <svg className="decorator-svg">
        <clipPath id="invertshallowconvex-path" clipPathUnits="objectBoundingBox"><path d="M1,0.739 C0.623,1,0.283,1,0,0.836 V0 H1 V0.739"></path></clipPath>
      </svg>
    </AdaProvider>
  )
}

Page.propTypes = {
  content: PropTypes.arrayOf(
    PropTypes.shape({
      componentName: PropTypes.string.isRequired,
      componentData: PropTypes.object,
    })
  ),
  nav: PropTypes.shape({
    siteLogo: PropTypes.string,
    siteLogoMobile: PropTypes.string,
    utilityLinks: PropTypes.array,
    items: PropTypes.array,
    headerDataSource: PropTypes.object,
    primaryLink: PropTypes.object,
    mobileStickyNavigationItems: PropTypes.array,
  }),
  res: PropTypes.shape({
    globalEnablePageAlert: PropTypes.bool,
    globalPageAlertTitle: PropTypes.string,
    globalPageAlertDescription: PropTypes.string,
    globalPageAlertType: PropTypes.string,
    enablePageAlert: PropTypes.bool,
    pageAlertTitle: PropTypes.string,
    pageAlertDescription: PropTypes.string,
    pageAlertType: PropTypes.string,
    disablePrimaryFooter: PropTypes.bool,
    disableUtilityFooter: PropTypes.bool,
    openAdaByDefault: PropTypes.bool,
  }),
  breadcrumbs: PropTypes.array,
  allData: PropTypes.object,
  footer: PropTypes.object,
}

export default Page
