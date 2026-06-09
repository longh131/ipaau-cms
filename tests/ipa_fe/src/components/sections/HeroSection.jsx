import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import Picture from '../helpers/Picture'
import TitleBlock from '../helpers/TitleBlock'
import { createGradientElement } from '../../helpers/markup'
import { useState, useEffect } from 'react'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

function Buttons(props) {
  const links = props.links || []
  if (!links.length) {
    return
  }
  let alignmentClasses = 'justify-stretch lg;justify-center lg:mx-auto'
  if (props.contentAlignment?.toLowerCase() === 'left') {
    alignmentClasses = 'justify-start lg:mx-0'
  }

  return <div className={`flex flex-col md:flex-row ${alignmentClasses} flex-wrap gap-6 `}>
    {links.map((link, i) => <ButtonEl className="w-full md:w-auto" key={generateDataHash(link)} item={link} />)}
  </div>
}

function FullBanner(props) {
  return (
    <Section
      type="subPageBanner"
      outerClass={props.componentPadding}
      innerClass="px-7"
      {...props}
      mobileBackgroundImage={props.mobileImage || props.mobileBackgroundImage}
      desktopBackgroundImage={props.desktopImage || props.desktopBackgroundImage}
    >
      <Buttons {...props} />
    </Section>
  )
}

function SplitBannerContained(props) {
  const sectionGradient = ![null, 'None', 'Text area'].includes(props.gradientType)
  const lightOrDarkValue = lightOrDark(props.backgroundColor)
  const [gradientColor, setGradientColor] = useState('#fff')

  useEffect(() => {
    const fgColor = props.foregroundColor?.replace('#', '')
    setGradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
  }, [])

  return (
    <Section
      type="subPageBanner"
      innerClass="lg:flex items-stretch"
      outerClass={props.componentPadding}
      {...props}
      sectionTitle={false}
    >
      <div className={`relative ${props.componentPadding}`}>
        {sectionGradient === true && (
          <div className="block z-[-1] -ml-7 h-full absolute right-0 top-0 left-0 ">
            {createGradientElement({
              type: props.gradientType,
              fromColor: gradientColor,
              fullMobile: true,
            })}
          </div>
        )}
        <div className="flex flex-wrap gap-6 h-full w-full items-center justify-between lg:flex-nowrap">
          <div className="flex flex-col gap-6 lg:basis-1/2 basis-full relative">
            {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue}/>}
            <Buttons {...props} />
          </div>

          <div className="h-full lg:basis-1/2 basis-full">
            <Picture
              desktopImage={props.desktopImage}
              mobileImage={props.mobileImage}
              />
          </div>
        </div>
      </div>
    </Section>
  )
}

function SplitBannerFull(props) {
  const splitImage = props.variant === 'Split content and image'
  const contained = props.backgroundConfiguration === 'Contained'

  const moveImageClasses = splitImage && !contained ? 'bg-subBanner bg-no-repeat ' : 'test'
  const sectionGradient = ![null, 'None', 'Text area'].includes(props.gradientType)
  const [gradientColor, setGradientColor] = useState('#fff')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])

  useEffect(() => {
    const fgColor = props.foregroundColor?.replace('#', '')
    setGradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
  }, [])

  const alignment = props.contentAlignment?.toLowerCase() || 'center'
  const textAlignment = alignment === 'center' ? 'center' : 'left'
  const getUrl = (image) => {
    return typeof image === 'string' ? image : image?.src || ''
  }

  return (
    <Section
      type="subPageBanner"
      outerClass={`${moveImageClasses} ${props.componentPadding} split-full`}
      innerClass="md:px-6 lg:px-7 max-md:max-w-none mx-0 sm:mx-auto sm:px-7 !max-w-none"
      {...props}
      sectionTitle={false}
      gradientType={props.backgroundGradientType}
      foregroundColor={props.backgroundGradientType == 'left' ? '#e0bbda' : '#87cbe6'}
    >
      <div className="container mx-auto flex flex-col lg:flex-row-reverse justify-between items-center">
        {sectionGradient && (
          <div className="block lg:hidden z-[-1] -ml-7 h-full absolute right-0 top-0 left-0">
            {createGradientElement({
              type: props.gradientType,
              fromColor: gradientColor,
              fullMobile: true,
            })}{' '}
          </div>
        )}
        {(props.desktopImage || props.mobileImage) && <div
          className="img-shape-acorn lg:max-w-[40%]"
          style={{
            '--cta-bg-mobile': props.mobileImage ? `url(${getUrl(props.mobileImage)})` : '',
            '--cta-bg-desktop': props.desktopImage ? `url(${getUrl(props.desktopImage)})` : '',
          }}
        />}
        <div className={`${(props.desktopImage || props.mobileImage) ? 'lg:max-w-[60%] pr-6' : 'w-full'} ${props.componentPadding} flex flex-col gap-12 text-${textAlignment}`}>
          {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue}/>}
          <Buttons {...props} />
        </div>
      </div>
    </Section>
  )
}

function SplitBanner(props) {
  const Comp = props.backgroundConfiguration === 'Contained' ? SplitBannerContained : SplitBannerFull

  return <Comp {...props} />
}

function HeroSection(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const Comp = props.variantSelect === 'full-width-image' ? FullBanner : SplitBanner

  return <Comp {...props} componentPadding={componentPadding} />
}

HeroSection.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variantSelect: PropTypes.string,
  links: PropTypes.array,
  contentAlignment: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
  mobileBackgroundImage: PropTypes.object,
  desktopBackgroundImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundGradientType: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  variant: PropTypes.string,
}

SplitBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variantSelect: PropTypes.string,
  links: PropTypes.array,
  contentAlignment: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundGradientType: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  variant: PropTypes.string,
}

FullBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variantSelect: PropTypes.string,
  links: PropTypes.array,
  contentAlignment: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundGradientType: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  variant: PropTypes.string,
}

SplitBannerContained.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variantSelect: PropTypes.string,
  links: PropTypes.array,
  contentAlignment: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundGradientType: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  variant: PropTypes.string,
}
export default HeroSection
