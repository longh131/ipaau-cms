import PropTypes from 'prop-types'
import Section from './_Section'
import Picture from '../helpers/Picture'
import TitleBlock from '../helpers/TitleBlock'
import { createGradientElement } from '../../helpers/markup'
import { useState, useEffect } from 'react'
import { transformPaddingToTailwind, lightOrDark } from '../../helpers/style'

function FullBanner(props) {
  return (
    <Section
      type="subPageBanner"
      outerClass={props.componentPadding}
      innerClass="px-7"
      {...props}
      mobileBackgroundImage={props.mobileImage}
      desktopBackgroundImage={props.desktopImage}
    ></Section>
  )
}

function SplitBannerContained(props) {
  const sectionGradient = ![null, 'None', 'Text area'].includes(props.gradientType)
  const [gradientColor, setgradientColor] = useState('#fff')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])
  useEffect(() => {
    const fgColor = props.foregroundColor.replace('#', '')
    setgradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
  }, [])

  return (
    <Section
      type="subPageBanner"
      innerClass="lg:flex items-stretch !py-0 !pl-7 !pr-0"
      {...props}
      sectionTitle={false}
      mobileBackgroundImage={null}
      desktopBackgroundImage={null}
    >
      <div className={`z-10 relative max-lg:pr-3 max-lg:w-full lg:basis-[30%] ${props.componentPadding}`}>
        {sectionGradient && (
          <div className="block lg:hidden z-[-1] -ml-7 h-full absolute right-0 top-0 left-0 ">
            {createGradientElement({
              type: props.gradientType,
              fromColor: gradientColor,
              fullMobile: true,
            })}
          </div>
        )}

        {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue} />}
      </div>
      <div className="z-0 max-lg:absolute max-lg:top-0 max-lg:left-0 max-lg:w-full max-lg:h-full lg:basis-[70%] lg:relative">
        <Picture
          desktopImage={props.desktopImage}
          mobileImage={props.mobileImage}
          className={`w-full h-full absolute top-0 object-cover rounded-none`}
        />
      </div>
    </Section>
  )
}

function SplitBannerFull(props) {
  const splitImage = props.variant === 'Split content and image'
  const contained = props.backgroundConfiguration === 'Contained'

  const moveImageClasses = splitImage && !contained ? 'bg-subBanner bg-no-repeat ' : 'test'
  const sectionGradient = ![null, 'None', 'Text area'].includes(props.gradientType)
  const [gradientColor, setgradientColor] = useState('#fff')
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])
  useEffect(() => {
    const fgColor = props.foregroundColor.replace('#', '')
    setgradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
  }, [])
  return (
    <Section
      type="subPageBanner"
      outerClass={`${moveImageClasses} !py-0 split-full`}
      innerClass="md:px-6 lg:px-7 max-md:max-w-none mx-0 sm:mx-auto sm:px-7 !max-w-none"
      {...props}
      sectionTitle={false}
      gradientType={null}
      mobileBackgroundImage={props.mobileImage}
      desktopBackgroundImage={props.desktopImage}
    >
      <div className="container mx-auto">
        {sectionGradient && (
          <div className="block lg:hidden z-[-1] -ml-7 h-full absolute right-0 top-0 left-0 ">
            {createGradientElement({
              type: props.gradientType,
              fromColor: gradientColor,
              fullMobile: true,
            })}{' '}
          </div>
        )}
        <div className={`pr-4 pl-7 lg:max-w-[30%] ${props.componentPadding}`}>
          {(props.title || props.description || props.tagline) && <TitleBlock {...props} lightOrDark={lightOrDarkValue} />}
        </div>
      </div>
    </Section>
  )
}

function SplitBanner(props) {
  const Comp = props.backgroundConfiguration === 'Contained' ? SplitBannerContained : SplitBannerFull

  return <Comp {...props} />
}

function SubPageBanner(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const Comp = props.variant === 'Split content and image' ? SplitBanner : FullBanner

  return <Comp {...props} componentPadding={componentPadding} />
}

SubPageBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  variant: PropTypes.string,
  backgroundGradientType: PropTypes.string,
  mobileBackgroundImage: PropTypes.object,
  desktopBackgroundImage: PropTypes.object,
}

FullBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variant: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
}

SplitBannerContained.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variant: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
}

SplitBannerFull.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variant: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
}

SplitBanner.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  variant: PropTypes.string,
  mobileImage: PropTypes.object,
  desktopImage: PropTypes.object,
}

export default SubPageBanner
