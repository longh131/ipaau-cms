import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import VideoModal from './_VideoModal'
import { createMarkup } from '../../helpers/markup'
import Picture from '../helpers/Picture'
import Image from '../helpers/Image'
import TitleBlock from '../helpers/TitleBlock'
import { dataLayerPush } from '../../helpers/thirdparty'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import themeConfig from '../../../theme.config'
import HeroCtaBlock from '../helpers/HeroCtaBlock'
import { ArrowRightIcon } from '@heroicons/react/24/solid'
import PlayButtonDecorator from '../helpers/cards/PlayButtonDecorator'
import { generateDataHash } from '../../helpers/contentHash'

function HeroBanner(props) {
  const [hasCardItems, setHasCardItems] = useState(0)
  const [textAlignment, setTextAlignment] = useState({
    flex: 'center',
    align: 'center',
  })
  const [modalIndex, setModalIndex] = useState()
  const componentPadding = transformPaddingToTailwind(props.componentPadding, true, props.breadcrumbs)

  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  useEffect(() => {
    setHasCardItems(props.cardItems && props.cardItems.length != 0)
  }, [props.cardItems])

  useEffect(() => {
    setTextAlignment((prevState) => {
      const newState = { ...prevState }
      const alignment = props.contentAlignment?.toLowerCase() || 'center'
      if (newState.align !== props.contentAlignment?.toLowerCase()) {
        newState.flex = alignment === 'center' ? 'center' : 'start'
        newState.align = alignment === 'center' ? 'center' : 'left'
      }
      return newState
    })
  }, [])

  const getTitleAlignmentClass = (cardItem) => {
    return cardItem.titleAlignment?.toLowerCase() === 'center' ? 'justify-center gap-5' : 'justify-between'
  }

  const cardInner = (cardItem, href = null) => {
    const titleAlignmentClass = getTitleAlignmentClass(cardItem)
    return (
      <>
        <Picture
          desktopImage={cardItem.desktopImage}
          mobileImage={cardItem.mobileImage}
          className="w-full h-full absolute top-0 left-0 object-cover"
        />
        {cardItem.videoUrl && (
          <div className="grow items-center justify-center md:max-lg:min-h-12 min-h-32 flex">
            <PlayButtonDecorator isButton={false} onClick={() => setModalIndex(i)} theme="none" />
          </div>
        )}

        <div className="relative mt-auto p-4 md:p-6 max-md:break-words w-full uppercase text-white flex flex-col md:max-lg:gap-2 gap-5">
          <div className={`flex ${titleAlignmentClass} items-center`}>
            <div className={`line-clamp-3 ${cardItem.videoUrl ? 'lg:line-clamp-1' : 'lg:line-clamp-3'}`}>
              {cardItem.title}
            </div>
            {href && <ArrowRightIcon role="none" className="h-5 w-5 shrink-0" />}
          </div>
          {cardItem.description && (
            <div
              className={`line-clamp-3 ${cardItem.videoUrl ? 'lg:line-clamp-1' : 'lg:line-clamp-3'} ${titleAlignmentClass}`}
              data-rte="true"
              dangerouslySetInnerHTML={createMarkup(cardItem.description)}
            />
          )}
        </div>
      </>
    )
  }

  const videoCard = (cardItem, outerClassName, idx, videoUrl) => {
    return (
      <button
        key={generateDataHash(cardItem)}
        className={`${outerClassName} aspect-square`}
        onClick={(event) => {
          setModalIndex(idx)
          dataLayerClick(event, videoUrl, undefined)
        }}
      >
        {cardInner(cardItem)}
      </button>
    )
  }

  const linkCard = (cardItem, outerClassName, idx, href) => {
    return (
      <a
        href={cardItem.ctaLinkItem[0].link.url}
        target={cardItem.ctaLinkItem[0].link.target}
        key={generateDataHash(cardItem)}
        className={`${outerClassName} aspect-square`}
        onClick={(event) => dataLayerClick(event, undefined, href)}
      >
        {cardInner(cardItem, cardItem.ctaLinkItem[0].link.url)}
      </a>
    )
  }

  const staticCard = (cardItem, outerClassName, idx) => {
    return (
      <div key={generateDataHash(cardItem)} className={`${outerClassName} aspect-square`}>
        {cardInner(cardItem)}
      </div>
    )
  }

  const dataLayerClick = (event, videoUrl, href) => {
    dataLayerPush(
      {
        event: 'image_link_click',
        click_text: event.target.closest('a, button').innerText,
        destination_path: videoUrl ? undefined : href,
      },
      event.target.closest('section').previousSibling,
    )
  }

  const renderCard = (videoUrl, href, cardItem, outerClassName, idx) => {
    if (videoUrl) {
      return videoCard(cardItem, outerClassName, idx, videoUrl)
    } else if (href) {
      return linkCard(cardItem, outerClassName, idx, href)
    } else {
      return staticCard(cardItem, outerClassName, idx)
    }
  }

  return (
    <>
      <Section
        {...props}
        type="heroBanner"
        sectionTitle={false}
        mobileBackgroundImage={
          themeConfig.settings.heroForegroundImages ? props.mobileBackgroundImage : props.mobileImage
        }
        desktopBackgroundImage={
          themeConfig.settings.heroForegroundImages ? props.desktopBackgroundImage : props.desktopImage
        }
        innerClass={`flex justify-${textAlignment.flex} ${componentPadding.top} ${hasCardItems ? componentPadding.mid : componentPadding.bottom}`}
      >
        {themeConfig.settings.heroForegroundImages && (
          <div className={`heroForeground max-w-full flex justify-center items-center gap-8`}>
            <div className={`basis-full max-w-full ${props.desktopImage ? 'lg:basis-1/2' : ''} shrink-0`}>
              {props.subtitle && (
                <div
                  className="subtitle text-lg mb-4 uppercase"
                  dangerouslySetInnerHTML={createMarkup(props.subtitle)}
                />
              )}
              {(props.title || props.description || props.tagline) && (
                <TitleBlock {...props} lightOrDark={lightOrDarkValue} level="1" />
              )}
              {props.mobileImage && <Image {...props.mobileImage} className="mx-auto lg:hidden" />}
              {props.ctaLinkItem && props.ctaLinkItem.length != 0 && (
                <HeroCtaBlock {...props} basis="full" textAlignment={textAlignment} />
              )}
            </div>
            {props.desktopImage && (
              <div className="basis-full lg:basis-5/12 shrink-0 max-lg:hidden">
                <Image {...props.desktopImage} className="mx-auto max-lg:hidden" />
              </div>
            )}
          </div>
        )}
        {!themeConfig.settings.heroForegroundImages && (props.title || props.description || props.tagline) && (
          <div className={`mx-6 max-w-3xl text-${textAlignment.align}`}>
            <TitleBlock {...props} lightOrDark={lightOrDarkValue} />
            {props.ctaLinkItem && props.ctaLinkItem.length != 0 && (
              <HeroCtaBlock {...props} textAlignment={textAlignment} />
            )}
          </div>
        )}
      </Section>
      {(hasCardItems || props.footnoteText) && (
        <section
          data-index={`${props.idx + 0.5}`}
          className={`${props.backgroundConfiguration === 'Contained' ? 'container mx-auto' : ''}`}
          // the 2.5rem is the gap between the cards, and the 100% is the width of the container.
          // we need to remove the gap from the basis calculation so that the cards fit within the container.
          style={{
            '--ipa-card-basis-sm': `calc(100%)`,
            '--ipa-card-basis-md': 'calc(100% / 2 - 2.5rem)',
            '--ipa-card-basis-lg': `calc(100% / 4 - 2.5rem)`,
          }}
        >
          <div data-type="footnote" className="inner mx-auto px-4 md:px-10">
            {hasCardItems && (
              <div className={`flex flex-wrap align-stretch gap-4 sm:gap-10 justify-center ${componentPadding.card}`}>
                {props.cardItems.map((cardItem, i) => {
                  const videoUrl = cardItem.videoUrl
                  const href = cardItem.ctaLinkItem[0]?.link?.url
                  let outerClassName = `relative aspect-square basis-[var(--ipa-card-basis-sm)] md:basis-[var(--ipa-card-basis-md)] lg:basis-[var(--ipa-card-basis-lg)] flex flex-col bg-electric-blue break-word w-full overflow-hidden rounded-3xl cardItem ${cardItem.titleAlignment === 'Center' ? 'text-center' : 'text-left'}`
                  if (href) {
                    outerClassName += ' hover:scale-[1.05] transition-all duration-300 hover:elevation-4 elevation-6'
                  }

                  return renderCard(videoUrl, href, cardItem, outerClassName, i)
                })}
                {modalIndex !== undefined && props.cardItems[modalIndex]?.videoUrl && (
                  <VideoModal
                    open={true}
                    i={modalIndex}
                    url={props.cardItems[modalIndex].videoUrl}
                    onClose={() => setModalIndex(undefined)}
                  />
                )}
              </div>
            )}

            {props.footnoteText && (
              <div
                className={`${componentPadding.bottom} ${componentPadding.top} text-${textAlignment.align}`}
                data-rte="true"
                dangerouslySetInnerHTML={createMarkup(props.footnoteText)}
              />
            )}
          </div>
        </section>
      )}
    </>
  )
}

HeroBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  contentAlignment: PropTypes.string,
  cardItems: PropTypes.array,
  desktopImage: PropTypes.object,
  mobileImage: PropTypes.object,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  ctaLinkItem: PropTypes.array,
  footnoteText: PropTypes.string,
  footnoteLink: PropTypes.array,
  backgroundConfiguration: PropTypes.string,
  mobileBackgroundImage: PropTypes.object,
  desktopBackgroundImage: PropTypes.object,
  backgroundGradientType: PropTypes.string,
  subtitle: PropTypes.string,
}

export default HeroBanner
