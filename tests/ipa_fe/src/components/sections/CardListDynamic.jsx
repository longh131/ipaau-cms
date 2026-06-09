import { useState, useEffect, useMemo } from 'react'
import PropTypes from 'prop-types'
import themeConfig from '../../../theme.config'
import Section from './_Section'
import { createGradientElement } from '../../helpers/markup'
import { addBgImageClass, lightOrDark, transformPaddingToTailwind } from '../../helpers/style'
import { dataLayerPush } from '../../helpers/thirdparty'
import ButtonEl from '../helpers/ctas/Button'
import { ArrowRightIcon } from '@heroicons/react/24/solid'
import HeroTitle from '../helpers/CardListingHeroTitle'
import TitleBlock from '../helpers/TitleBlock'
import Picture from '../helpers/Picture'
import Pill from '../helpers/Pill'
import { generateDataHash } from '../../helpers/contentHash'

function CardListDynamic(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const initialNumber = props.initialNoOfCards || 3

  // Memoize filtered items to avoid duplication and improve performance
  const filteredCardItems = useMemo(
    () => (props.listingContents || []).filter((cardItem) => !cardItem.hideInListing && cardItem.listingTitle),
    [props.listingContents]
  )

  const [viewMore, setViewMore] = useState(filteredCardItems.length <= initialNumber)
  const alignment = props.contentAlignment?.toLowerCase() || 'center'
  const variant = props.cardVariant
  const [backgroundColor, setBackgroundColor] = useState('transparent')
  const [gradientColor, setgradientColor] = useState('#fff')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')

  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  // Sync viewMore state with actual item count (fixes SSR hydration mismatch)
  useEffect(() => {
    setViewMore(filteredCardItems.length <= initialNumber)
  }, [filteredCardItems.length, initialNumber])

  useEffect(() => {
    const bgColor = props.cardBackgroundColour?.replace('#', '')
    const fgColor = props.imageGradientColour?.replace('#', '')
    setBackgroundColor((prevState) => (bgColor ? `#${bgColor}` : prevState))
    setgradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
  }, [])

  // Determine which items to display based on viewMore state
  const displayedCardItems = viewMore ? filteredCardItems : filteredCardItems.slice(0, initialNumber)

  return (
    <Section
      type="cardListDynamic"
      sectionTitle={false}
      innerClass={`px-7 text-${alignment} flex ${componentPadding}`}
      {...props}
    >
      <div className="w-full">
        {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue}/>}

        {props.ctaLinkItem && (
          <div className={`my-6 flex flex-col gap-4 justify-${alignment} xl:flex-row items-${alignment}`}>
            {props.ctaLinkItem.map((ctaLinkItem) => (
              <ButtonEl
                className="max-sm:w-full"
                key={generateDataHash(ctaLinkItem)}
                item={ctaLinkItem}
              />
            ))}
          </div>
        )}
        {displayedCardItems.length != 0 && (
          <div
            className={`grid grid-cols-1 md:grid-cols-6 items-stretch ${props.title?.length || props.description?.length ? 'pt-10' : 'pt-0'} gap-8`}
          >
            {displayedCardItems.map((cardItem) => {
              const cmsTags = cardItem.tagsConfig ? JSON.parse(cardItem.tagsConfig).tags : null
              const tags = cmsTags && cmsTags.length > 0 ? cmsTags : []

              const innerHero = (
                <div key={generateDataHash(cardItem)} data-type="hero" className="h-full">
                  <div className={`relative flex flex-col h-full`}>
                    <Picture
                      desktopImage={cardItem.listingDesktopImage}
                      mobileImage={cardItem.listingMobileImage}
                      className={`mx-auto mb-5 aspect-video rounded-2xl object-cover`}
                    />
                    {(cardItem.dateTimeFormatted || cardItem.membersOnly) && (
                      <div className={`flags flex flex-row md:flex-col md:max-lg:items-start lg:flex-row gap-2 mb-2 items-center justify-start`}>
                        {cardItem.dateTimeFormatted && <span className="text-md inline-block">{cardItem.dateTimeFormatted}</span>}
                        {cardItem.membersOnly&& <Pill theme="members">Members only</Pill>}
                      </div>
                    )}
                    {cardItem.listingTitle && (
                      <div className="title line-clamp-2">
                        <HeroTitle type="dynamic" variant="hero" cardItem={cardItem} canLink={true} titleAlignment="left" className="text-secondary" lightOrDark={lightOrDarkValue} />
                      </div>
                    )}
                    {cardItem.listingDescription && (
                      <div className="mt-2 text-lg line-clamp-3 text-left">{cardItem.listingDescription}</div>
                    )}
                    {tags && tags.length > 0 && (
                      <div className="tags flex gap-2 mb-2 pt-4 mt-auto items-center  flex-wrap">
                        {tags.map((tag) => (
                          <Pill key={generateDataHash(tag)} theme="primary">{tag}</Pill>
                        ))}
                      </div>
                    )}
                  </div>
                </div>
              )

              const innerBG = (
                <>
                  {props.imageGradientColour && props.imageGradientType?.toLowerCase() !== 'text area' &&
                    createGradientElement({
                      type: props.imageGradientType,
                      fromColor: gradientColor,
                    })}
                  <div data-type="bg" className="relative min-h-96 h-full mt-auto flex flex-col justify-end"
                    style={{
                      '--ipa-color-light': themeConfig.textColors.primary,
                      '--ipa-color-dark': themeConfig.textColors['primary-subtle'],
                      'color': `var(--ipa-color-${lightOrDark(backgroundColor)})`
                    }}
                  >
                    <div className={`text-area h-min relative ${props.imageGradientColour && props.imageGradientType?.toLowerCase() === 'text area' ? 'p-4 rounded-2xl overflow-hidden ' : ''}`}>
                      {props.imageGradientColour && props.imageGradientType?.toLowerCase() === 'text area' &&
                        createGradientElement({
                          type: props.imageGradientType,
                          fromColor: gradientColor,
                      })}
                      {(cardItem.dateTimeFormatted || cardItem.membersOnly) && (
                        <div className={`flags flex flex-row md:flex-col md:max-lg:items-start lg:flex-row gap-2 mb-2 items-center relative z-10 justify-start`}>
                          {cardItem.dateTimeFormatted && <span className="text-md inline-block">{cardItem.dateTimeFormatted}</span>}
                          {cardItem.membersOnly&& <Pill theme="members">Members only</Pill>}
                        </div>
                      )}
                      {cardItem.listingTitle && <HeroTitle type="dynamic" cardItem={cardItem} canLink={false} className="group-hover/card:underline relative line-clamp-2" titleAlignment="left" lightOrDark={lightOrDark(backgroundColor)}/>}
                      {tags && tags.length > 0 && <small>{tags.join(', ')}</small>}
                      {cardItem.listingDescription && <div className="mt-2 text-lg relative z-10 line-clamp-3 text-left">{cardItem.listingDescription}</div>}
                      {cardItem.dynamicCardLinkText && (
                        <div className={`mt-2 flex justify-start relative z-10`}>
                          <ButtonEl>{cardItem.dynamicCardLinkText}</ButtonEl>
                        </div>
                      )}
                    </div>
                  </div>
                </>
              )
              const bgProps = {
                style: {
                  '--mobile-bg-url': cardItem.listingMobileImage ? `url(${cardItem.listingMobileImage.src})` : null,
                  '--desktop-bg-url': cardItem.listingDesktopImage ? `url(${cardItem.listingDesktopImage.src})` : null,
                  '--bg-color': backgroundColor,
                  '--light-or-dark': lightOrDark(backgroundColor),
                },
                className: `md:[&:nth-child(3n+3)]:col-start-5
                md:[&:nth-child(3n+2)]:col-start-3
                md:[&:nth-child(3n+1)]:col-start-1
                md:[&:last-child:first-child]:col-start-3
                md:[&:last-child:nth-child(3n+1)]:col-start-3
                md:[&:nth-last-child(2):first-child]:col-start-2
                md:[&:nth-last-child(2):nth-child(3n+1)]:col-start-2
                md:[&:nth-child(2):last-child]:col-start-4
                md:[&:last-child:nth-child(3n+2)]:col-start-4 col-span-2 relative w-full h-full bg-[color:var(--bg-color)] pt-4 px-8 pb-8 rounded-2xl overflow-hidden ${addBgImageClass(cardItem.listingDesktopImage?.src, cardItem.listingMobileImage?.src)} ${cardItem.listingLink ? 'elevation-0 hover:elevation-6 transition-all duration-300 scale-100 hover:scale-105 group/card' : ''}`,
              }

              const heroProps = {
                className: 'relative w-full pt-4 pb-8 rounded-2xl overflow-hidden',
              }

              const inner = variant !== 'Hero image' ? innerBG : innerHero
              if (variant !== 'Hero image') {
                return cardItem.listingLink ? (
                  <a href={cardItem.listingLink} key={generateDataHash(cardItem)} {...bgProps} onClick={(e) => {
                    dataLayerPush({
                      event: 'article_click',
                      click_text: cardItem.listingTitle,
                      category: tags ? tags.join(', ') : undefined
                    }, e.target)
                  }}>
                    {inner}
                  </a>
                ) : (
                  <div key={generateDataHash(cardItem)} {...bgProps}>
                    {inner}
                  </div>
                )
              } else {
                return (
                  <div
                    key={generateDataHash(cardItem)}
                    {...heroProps}
                    className="md:[&:nth-child(3n+3)]:col-start-5
    md:[&:nth-child(3n+2)]:col-start-3
    md:[&:nth-child(3n+1)]:col-start-1
    md:[&:last-child:first-child]:col-start-3
    md:[&:last-child:nth-child(3n+1)]:col-start-3
    md:[&:nth-last-child(2):first-child]:col-start-2
    md:[&:nth-last-child(2):nth-child(3n+1)]:col-start-2
    md:[&:nth-child(2):last-child]:col-start-4
    md:[&:last-child:nth-child(3n+2)]:col-start-4 col-span-2"
                  >
                    {inner}
                  </div>
                )
              }
            })}
          </div>
        )}
        {!viewMore && (
          <div className="flex justify-center mt-20">
            <ButtonEl
              onClick={() => {
                setViewMore(true)
              }}
            >
              {props.viewMoreText || 'View more'}
              <ArrowRightIcon role="none" className="h-6 w-6 ml-2" />
            </ButtonEl>
          </div>
        )}
      </div>
    </Section>
  )
}

CardListDynamic.propTypes = {
  listingContents: PropTypes.array,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  initialNoOfCards: PropTypes.number,
  contentAlignment: PropTypes.string,
  cardVariant: PropTypes.string,
  backgroundColor: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
  imageGradientColour: PropTypes.string,
  imageGradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  viewMoreText: PropTypes.string,
  ctaLinkItem: PropTypes.array,
}

export default CardListDynamic
