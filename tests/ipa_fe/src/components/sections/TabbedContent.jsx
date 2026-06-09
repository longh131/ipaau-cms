import React, { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import TitleBlock from '../helpers/TitleBlock'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { dataLayerPush } from '../../helpers/thirdparty'
import ButtonEl from '../helpers/ctas/Button'
import { generateDataHash } from '../../helpers/contentHash'

function TabbedContent(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  // Determine initial active tab
  const getInitialActiveTab = () => {
    if (props.defaultActiveTab && props.tabs) {
      const tabIndex = props.tabs.findIndex(tab =>
        tab.tabLabel === props.defaultActiveTab ||
        tab.tabLabel?.toLowerCase() === props.defaultActiveTab?.toLowerCase()
      )
      return tabIndex >= 0 ? tabIndex : 0
    }
    return 0
  }

  const [activeTab, setActiveTab] = useState(getInitialActiveTab)

  if (!props.tabs || props.tabs.length === 0) {
    return null
  }

  const currentTab = props.tabs[activeTab]
  const currentTabHasImage = currentTab && (currentTab.desktopImage || currentTab.mobileImage)

  const tabListing = () => (
    <div className="flex flex-wrap gap-4 mb-8 xl:mb-auto mt-8 shrink grow-0 ">
      {props.tabs.map((tab, index) => (
        <button
          key={generateDataHash(tab)}
          onClick={() => {
            dataLayerPush({
              event: 'tab_content_click',
              click_text: tab.tabLabel || `Tab ${index + 1}`
            })
            setActiveTab(index)
          }}
          className={`text-secondary  tab label-md lg:label-xl transition-colors duration-200 grid ${tab.tabIcon ? 'grid-cols-[minmax(0,max-content),auto]' : 'grid-cols-1'} items-center gap-2 ${
            activeTab === index
              ? 'active '
              : 'hover:text-primary'
          }`}
        >
          {tab.tabIcon && <img src={tab.tabIcon.src} alt={tab.tabIcon.altText ?? ''} className="w-8 h-8 block" />}
          <span className={`max-w-none md:max-w-56 truncate text-nowrap ellipsis whitespace-nowrap block text-left ${tab.tabIcon ? 'col-start-2' : 'col-start-1'}`}>
            {tab.tabLabel || `Tab ${index + 1}`}
          </span>
        </button>
      ))}
    </div>
  )

  const getImageUrl = (image) => {
    return typeof image === 'string' ? image : image?.src || ''
  }

  const tabImage = () => (
    <div>
      <div
        className="img-shape-acorn img-wrapper"
        style={{
          '--cta-bg-mobile': currentTab.mobileImage ? `url(${getImageUrl(currentTab.mobileImage)})` : '',
          '--cta-bg-desktop': currentTab.desktopImage ? `url(${getImageUrl(currentTab.desktopImage)})` : '',
        }}
      />
    </div>
  )

  return (
    <Section
      type="tabbedContent"
      outerClass={componentPadding}
      {...props}
    >
      <div className="container">

        <div className={`grid grid-cols-1 gap-8 lg:gap-16 items-center ${currentTabHasImage ? 'lg:grid-cols-2' : ''}`}>
          <div className='lg:hidden'>
            {tabListing()}
          </div>
          {/* Left Side - Image */}
          {props.layoutStyle === 'image-left-text-right' && currentTabHasImage && tabImage()}

          <div className={`flex h-full flex-col ${props.contentAlignment.toLowerCase() === 'left' ? 'items-start' : 'items-center'}`}>
            {/* Tab Navigation */}
            <div className='hidden lg:block'>
              {tabListing()}
            </div>

            {/* Tab Content */}
            {currentTab && (
              <div className="space-y-8 grow flex flex-col justify-center">
                <TitleBlock
                  {...props}
                  tagline={currentTab.tagline}
                  title={currentTab.title}
                  description={currentTab.description}
                  lightOrDark={lightOrDarkValue}
                />

                {/* CTA Button */}
                {currentTab.ctaLinks && currentTab.ctaLinks.length > 0 && (
                  <div className={`pt-4 ${props.contentAlignment.toLowerCase() === 'left' ? 'text-left' : 'text-center'}`}>
                    {currentTab.ctaLinks.map((link) => (
                      <ButtonEl
                        key={generateDataHash(link)}
                        link={link.link}
                        dataLayer={false}
                        className="max-sm:w-full"
                        onClick={event => {
                          dataLayerPush({
                            event: 'tab_cta_click',
                            click_text: link.link.name,
                            destination_path: link.link.url,
                            component_name: 'tab content'
                          }, event.target)
                        }}
                      />
                    ))}
                  </div>
                )}
              </div>
            )}
          </div>
          {props.layoutStyle === 'text-left-image-right' && currentTabHasImage && tabImage()}

        </div>
      </div>
    </Section>
  )
}

TabbedContent.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  defaultActiveTab: PropTypes.string,
  tabs: PropTypes.arrayOf(
    PropTypes.shape({
      tabLabel: PropTypes.string,
      title: PropTypes.string,
      description: PropTypes.string,
      tagline: PropTypes.string,
      desktopImage: PropTypes.object,
      mobileImage: PropTypes.object,
      ctaLink: PropTypes.array,
    })
  ),
  layoutStyle: PropTypes.string,
  contentAlignment: PropTypes.string,
  tagline: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  ctaLinks: PropTypes.arrayOf(
    PropTypes.shape({
      link: PropTypes.object,
      name: PropTypes.string,
      url: PropTypes.string,
      target: PropTypes.string,
    })
  ),
}

export default TabbedContent
