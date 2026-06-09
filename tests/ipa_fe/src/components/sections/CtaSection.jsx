import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import TitleBlock from '../helpers/TitleBlock'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'
import { Decorator1 } from '../decorators'

const getLayoutClass = (layoutStyle, bannerVariation) => {
  switch (layoutStyle) {
    case 'image-left-text-right':
      return bannerVariation === 'image' ? 'lg:grid-cols-[40%_1fr]' : ''
    case 'text-left-button-right':
      return 'lg:grid-cols-[60%_1fr]'
    case 'centered':
      return 'lg:grid-cols-1 justify-center'
    case 'text-left-image-right':
      return bannerVariation === 'image' ? 'lg:grid-cols-[1fr_40%]' : ''
    case 'button-left-text-right':
    default:
      return 'lg:grid-cols-[1fr_60%]'
  }
}

const getImageLayoutClass = (layoutStyle) => {
  switch (layoutStyle) {
    case 'image-left-text-right':
    case 'text-left-button-right':
      return 'row-start-1 col-start-1'
    case 'centered':
      return 'col-start-1 row-start-1 lg:max-w-[40%] mx-auto'
    case 'text-left-image-right':
    case 'button-left-text-right':
    default:
      return `row-start-1 col-start-1 lg:col-start-2`
  }
}

const getContentLayoutClass = (layoutStyle, bannerVariation) => {
  switch (layoutStyle) {
    case 'image-left-text-right':
      return `lg:row-start-1 ${bannerVariation === 'image' ? 'lg:col-start-2' : 'lg:col-start-1'}`
    case 'button-left-text-right':
      return `lg:row-start-1 lg:col-start-2`
    case 'centered':
      return 'text-center'
    case 'text-left-image-right':
    case 'text-left-button-right':
    default:
      return ''
  }
}

const getImageShapeClass = (imageShape) => {
  switch (imageShape) {
    case 'organic-curve':
      return 'img-shape-acorn'
    case 'rectangle':
      return 'img-shape-rectangle'
    default:
      return 'rounded-lg'
  }
}

const getButtonAlignment = (layoutStyle, contentAlignment) => {
  switch (layoutStyle) {
    case 'centered':
      return 'justify-center'
    case 'text-left-button-right':
      return 'lg:justify-end'
    case 'button-left-text-right':
      return 'lg:justify-start'
    case 'image-left-text-right':
    case 'text-left-image-right':
    default:
      return contentAlignment === 'left' ? 'lg:justify-start' : 'lg:justify-center'
  }
}

const normalizeBannerVariation = (bannerVariation, layoutStyle, desktopImage, mobileImage) => {
  const isValidImageLayout = ['image-left-text-right', 'text-left-image-right'].includes(layoutStyle)
  const hasImages = desktopImage || mobileImage

  if (!isValidImageLayout || (bannerVariation === 'image' && !hasImages)) {
    return undefined
  }
  return bannerVariation
}

const renderButton = (ctaButtonLink, layoutStyle, contentAlignment) => {
  if (!ctaButtonLink?.length) {
    return null
  }

  const alignment = getButtonAlignment(layoutStyle, contentAlignment?.toLowerCase() || 'center')

  return (
    <div className={`component-cta flex flex-col shrink-0 sm:flex-row gap-4 ${alignment}`}>
      {ctaButtonLink.map((button) => (
        button.link?.url && (
          <ButtonEl key={generateDataHash(button)} item={button}>
            {button.link.name || 'FIND OUT MORE'}
          </ButtonEl>
        )
      ))}
    </div>
  )
}

function CtaSection(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const bannerVariation = normalizeBannerVariation(
    props.bannerVariation,
    props.layoutStyle,
    props.desktopImage,
    props.mobileImage
  )
  const button = renderButton(props.ctaButtonLink, props.layoutStyle, props.contentAlignment)

  const sectionProps = { ...props }
  if (props.backgroundStyle === 'transparent' || props.backgroundStyle === 'none') {
    delete sectionProps.backgroundColor
    delete sectionProps.gradientType
  } else if (props.backgroundStyle !== 'gradient') {
    delete sectionProps.gradientType
  }
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  const getImageUrl = (image) => {
    if (typeof image === 'string') {
      return image
    }
    return image.src
  }
  return (
    <Section
      type="ctaSection"
      outerClass={`${componentPadding} overflow-hidden`}
      sectionTitle={false}
      {...sectionProps}
    >
      <div className="container mx-auto px-4 py-16 lg:py-20">
        <div
          className={`grid grid-cols-1 ${getLayoutClass(props.layoutStyle, bannerVariation)} items-center ${bannerVariation ? 'gap-14 lg:gap-20' : 'gap-12'}`}
        >
          {/* Image Section */}
          {bannerVariation === 'image' && (
            <div className={`content-section content-section-1 content-section-1--image ${getImageLayoutClass(props.layoutStyle)}`}>
              <div
                className={`${getImageShapeClass(props.imageShape)} img-wrapper`}
                style={{
                  '--cta-bg-mobile': props.mobileImage
                    ? `url(${getImageUrl(props.mobileImage)})`
                    : '',
                  '--cta-bg-desktop': props.desktopImage
                    ? `url(${getImageUrl(props.desktopImage)})`
                    : '',
                }}
              />
            </div>
          )}

          {/* Content Section */}
          <div className={`content-section content-section-2 ${getContentLayoutClass(props.layoutStyle, bannerVariation)}`}>
            <TitleBlock {...props} lightOrDark={lightOrDarkValue} />
            {bannerVariation && button}
          </div>

          {!bannerVariation && button}
        </div>
      </div>
    </Section>
  )
}

CtaSection.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  bannerVariation: PropTypes.string,
  layoutStyle: PropTypes.string,
  desktopImage: PropTypes.object,
  mobileImage: PropTypes.object,
  imageShape: PropTypes.string,
  ctaButtonLink: PropTypes.array,
  contentAlignment: PropTypes.string,
  backgroundStyle: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundColor: PropTypes.string,
}

export default CtaSection
