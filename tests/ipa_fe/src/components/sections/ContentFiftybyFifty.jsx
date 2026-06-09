import PropTypes from 'prop-types'
import Section from './_Section'
import { createMarkup, createGradientElement } from '../../helpers/markup'
import { addBgImageClass, lightOrDark, transformPaddingToTailwind } from '../../helpers/style'
import ButtonEl from '../helpers/ctas/Button'
import { useState, useEffect } from 'react'
import { generateDataHash } from '../../helpers/contentHash'

function ContentFiftybyFifty(props) {
  const [backgroundColor, setBackgroundColor] = useState('transparent')
  const [gradientColor, setgradientColor] = useState('#fff')
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  useEffect(() => {
    const bgColor = props.cardBackgroundColour?.replace('#', '') ?? ''
    const fgColor = props.imageGradientColour?.replace('#', '') ?? ''
    setBackgroundColor((prevState) => (bgColor ? `#${bgColor}` : prevState))
    setgradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
    // Only set colors on initial mount to prevent SSR hydration mismatches
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const getCardItemTitleAlignmentClass = (cardItem) => {
    if (cardItem.titleAlignment) {
      return `text-${cardItem.titleAlignment.toLowerCase()}`
    }
    return null
  }
  return (
    <Section type="contentFiftybyFifty" outerClass={componentPadding} {...props}>
      <div className="mx-6">
        <div className="grid md:grid-cols-2 gap-2 md:gap-16 mt-12">
          {props.cardItems.map((cardItem) => (
            <div
              style={{
                '--mobile-bg-url': cardItem.mobileImage ? `url(${cardItem.mobileImage.src})` : null,
                '--desktop-bg-url': cardItem.desktopImage ? `url(${cardItem.desktopImage.src})` : null,
                '--bg-color': backgroundColor,
                '--light-or-dark': lightOrDark(backgroundColor),
              }}
              key={generateDataHash(cardItem)}
              className={`p-8 pb-10 rounded-2xl relative text-${lightOrDark(backgroundColor)} ${addBgImageClass(cardItem.desktopImage?.src, cardItem.mobileImage?.src)} bg-[color:var(--bg-color)]`}
            >
              {props.imageGradientColour &&
                createGradientElement({
                  type: props.imageGradientType,
                  fromColor: gradientColor,
                  classes: 'rounded-2xl',
                })}
              <div className="min-h-72 relative">
                {cardItem.title && !cardItem.useSmallTitle && (
                  <h2
                    className={`block mt-auto font-bold ${cardItem.useSmallTitle ? 'text-lg' : 'text-2xl'} ${getCardItemTitleAlignmentClass(cardItem)}`}
                  >
                    {cardItem.title}
                  </h2>
                )}
                {cardItem.title && cardItem.useSmallTitle && (
                  <h3
                    className={`block mt-auto font-bold ${cardItem.useSmallTitle ? 'text-lg' : 'text-2xl'} ${getCardItemTitleAlignmentClass(cardItem)}`}
                  >
                    {cardItem.title}
                  </h3>
                )}
                {cardItem.description && (
                  <div className="mt-2" data-rte="true" dangerouslySetInnerHTML={createMarkup(cardItem.description)} />
                )}

                {cardItem.ctaLinkItem.length != 0 && (
                  <div className="flex flex-col items-start gap-6 mt-3">
                    {cardItem.ctaLinkItem.map((ctaLinkItem) => <ButtonEl key={generateDataHash(ctaLinkItem)} item={ctaLinkItem} />)}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </Section>
  )
}

ContentFiftybyFifty.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  cardBackgroundColour: PropTypes.string,
  imageGradientColour: PropTypes.string,
  imageGradientType: PropTypes.string,
  cardItems: PropTypes.arrayOf(
    PropTypes.shape({
      mobileImage: PropTypes.object,
      desktopImage: PropTypes.object,
      title: PropTypes.string,
      useSmallTitle: PropTypes.bool,
      titleAlignment: PropTypes.string,
      description: PropTypes.string,
      ctaLinkItem: PropTypes.array,
    })
  ),
}

export default ContentFiftybyFifty
