import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup, createGradientElement } from '../../helpers/markup'
import { addBgImageClass, lightOrDark, transformPaddingToTailwind } from '../../helpers/style'
import ButtonEl from '../helpers/ctas/Button'
import { ArrowRightIcon } from '@heroicons/react/24/solid'
import HeroTitle from '../helpers/CardListingHeroTitle'
import TitleBlock from '../helpers/TitleBlock'
import Picture from '../helpers/Picture'
import { generateDataHash } from '../../helpers/contentHash'

function TagListing(props) {
  let cardItems = (props.items || []).filter((cardItem) => !cardItem.hideInListing)
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const initialNumber = props.initialNoOfCards || 3
  const [viewMore, setViewMore] = useState(cardItems.length <= initialNumber)
  const [alignment, setAlignment] = useState('center')
  const [textAlignment, setTextAlignment] = useState({
    flex: 'center',
    align: 'center',
  })
  const variant = props.cardVariant
  const [backgroundColor, setBackgroundColor] = useState('transparent')
  const [gradientColor, setgradientColor] = useState('#fff')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])
  useEffect(() => {
    setTextAlignment((prevState) => {
      const newState = { ...prevState }
      const alignment = props.contentAlignment?.toLowerCase() || 'center'
      if (newState.align !== props.contentAlignment?.toLowerCase()) {
        newState.flex = alignment === 'center' ? 'center' : 'start'
        newState.align = alignment === 'center' ? 'center' : 'left'
      }
      setAlignment(alignment)
      return newState
    })
    const bgColor = props.cardBackgroundColour?.replace('#', '') ?? ''
    const fgColor = props.imageGradientColour?.replace('#', '') ?? ''
    setBackgroundColor((prevState) => (bgColor ? `#${bgColor}` : prevState))
    setgradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
    // Only set colors and alignment on initial mount to prevent SSR hydration mismatches
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  if (!viewMore) {
    cardItems = cardItems.slice(0, initialNumber)
  }

  return (
    <Section
      type="tagListing"
      sectionTitle={false}
      innerClass={`px-7 text-${textAlignment.align} flex ${componentPadding}`}
      {...props}
    >
      <div className="w-full">
        {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue}/>}

        {cardItems.length != 0 && (
          <div
            className={`grid grid-cols-1 md:grid-cols-6 items-start ${props.title?.length || props.description?.length ? 'pt-10' : 'pt-0'} gap-8`}
          >
            {cardItems.map((cardItem) => {
              const tags = (cardItem.tagsConfig && JSON.parse(cardItem.tagsConfig).tags) || []

              const innerHero = (
                <div key={generateDataHash(cardItem)} data-type="hero">
                  <div className={`relative flex flex-col`}>
                    <Picture
                      desktopImage={cardItem.listingDesktopImage}
                      mobileImage={cardItem.listingMobileImage}
                      className={`mx-auto mb-5 aspect-video rounded-2xl`}
                    />
                    {cardItem.dateTimeFormatted}
                    {cardItem.listingTitle && <HeroTitle type="dynamic" variant="hero" cardItem={cardItem} idx={generateDataHash(cardItem)} />}
                    {cardItem.listingDescription && (
                      <div
                        className="mt-2"
                        data-rte="true"
                        dangerouslySetInnerHTML={createMarkup(cardItem.listingDescription)}
                      />
                    )}
                  </div>
                </div>
              )

              const innerBG = (
                <>
                  {props.imageGradientColour &&
                    createGradientElement({
                      type: props.imageGradientType,
                      fromColor: gradientColor,
                    })}
                  <div data-type="bg" className="relative min-h-96 h-full mt-auto flex flex-col justify-end">
                    <span className="text-sm">{cardItem.dateTimeFormatted}</span>
                    {cardItem.listingTitle && <strong className="text-lg">{cardItem.listingTitle}</strong>}
                    {tags.length != 0 && <small>{tags.join(', ')}</small>}
                    {cardItem.listingDescription && <div className="mt-2">{cardItem.listingDescription}</div>}
                    {cardItem.dynamicCardLinkText && (
                      <div className={`mt-2 flex justify-${alignment !== 'center' ? 'start' : 'center'}`}>
                        <ButtonEl>{cardItem.dynamicCardLinkText}</ButtonEl>
                      </div>
                    )}
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
                md:[&:last-child:nth-child(3n+2)]:col-start-4 col-span-2 relative w-full h-full bg-[color:var(--bg-color)] pt-4 px-8 pb-8 rounded-2xl overflow-hidden text-${lightOrDark(backgroundColor)} ${addBgImageClass(cardItem.listingDesktopImage?.src, cardItem.listingMobileImage?.src)}`,
              }

              const heroProps = {
                className: 'relative w-full pt-4 pb-8 rounded-2xl overflow-hidden',
              }

              const inner = variant !== 'Hero image' ? innerBG : innerHero
              if (variant !== 'Hero image') {
                return cardItem.listingLink ? (
                  <a href={cardItem.listingLink} key={generateDataHash(cardItem)} {...bgProps}>
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

TagListing.propTypes = {
  items: PropTypes.array,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  initialNoOfCards: PropTypes.number,
  cardVariant: PropTypes.string,
  backgroundColor: PropTypes.string,
  contentAlignment: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
  imageGradientColour: PropTypes.string,
  imageGradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  viewMoreText: PropTypes.string,
}

export default TagListing
