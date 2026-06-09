import { useEffect, useState, useMemo } from 'react'
import PropTypes from 'prop-types'
import SectionContext from '../helpers/Context'
import TitleBlock from '../helpers/TitleBlock'
import { createGradientElement } from '../../helpers/markup'
import { lightOrDark, addBgImageClass } from '../../helpers/style'
import themeConfig from '../../../theme.config'

const Section = (props) => {
  // the api is sending out un-hashed hex colour strings, so we either make it the hashed version, or default to the base colour setting from the theme config.
  const [contained, setContained] = useState(false)
  const [backgroundColor, setBackgroundColor] = useState('transparent')
  const [gradientColor, setGradientColor] = useState('#fff')
  const [textShadow, setTextShadow] = useState(null)
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  const sectionGradient = ![null, 'None', 'Text area'].includes(props.gradientType)
  const contentGradient = ['Text area'].includes(props.gradientType)

  useEffect(() => {
    setContained(() => props.backgroundConfiguration === 'Contained')
  }, [props.backgroundConfiguration])

  useEffect(() => {
    // we strip the '#' from the front by default because some sources of the data have it included, and some do not.
    // this sets everything to the same format, even though we add it back immediately after.
    const bgColor = props.backgroundColor?.replace('#', '')
    const fgColor = props.foregroundColor?.replace('#', '')
    setBackgroundColor((prevState) => (bgColor ? `#${bgColor}` : prevState))
    setGradientColor((prevState) => (fgColor ? `#${fgColor}` : prevState))
    setLightOrDarkValue(lightOrDark(bgColor))
  }, [])

  useEffect(() => {
    setTextShadow(() => {
      if (!['always', 'images'].includes(themeConfig.settings.useTextShadow)) {
        return null
      }
      if (
        themeConfig.settings.useTextShadow === 'images' &&
        !props.mobileBackgroundImage &&
        !props.desktopBackgroundImage
      ) {
        return null
      }
      const theme = lightOrDark(backgroundColor)
      return `text-shadow shadow-${theme === 'dark' ? 'black' : 'white'}`
    })
  }, [backgroundColor])


  const getFromArticleClass = (fromArticleContainer) => {
    return fromArticleContainer ? 'article-container' : 'container'
  }

  const getContainedClass = (contained) => {
    return contained ? ` mx-auto rounded ${getFromArticleClass(props.fromArticleContainer)}` : ''
  }

  return (
    <section
      data-type={props.type}
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
      className={`${props.outerClass || ''} ${getContainedClass(contained)} ${addBgImageClass(props.desktopBackgroundImage?.src, props.mobileBackgroundImage?.src)} bg-[color:var(--bg-color)]`}
    >
      <SectionContext.Provider value={useMemo(() => ({ base: backgroundColor }), [backgroundColor])}>
        {sectionGradient &&
          createGradientElement({
            type: props.gradientType,
            fromColor: gradientColor,
            fullMobile: true,
          })}
        <div
          className={`inner ${!props.fromArticleContainer ? 'container' : 'article-container'} px-4 md:px-10 mx-auto ${props.innerClass || ''} flex flex-col gap-12 ${contentGradient ? 'has-gradient' : ''}`}
        >
          {contentGradient &&
            createGradientElement({
              type: props.gradientType,
              fromColor: gradientColor,
              fullMobile: false,
            })}
          {props.sectionTitle !== false && (props.title || props.description || props.tagline) && <TitleBlock {...props} textShadow={textShadow} lightOrDark={lightOrDarkValue}/>}
          {props.children}
        </div>
      </SectionContext.Provider>
    </section>
  )
}

Section.propTypes = {
  type: PropTypes.string,
  idx: PropTypes.number,
  outerClass: PropTypes.string,
  innerClass: PropTypes.string,
  backgroundConfiguration: PropTypes.string,
  backgroundColor: PropTypes.string,
  foregroundColor: PropTypes.string,
  gradientType: PropTypes.string,
  mobileBackgroundImage: PropTypes.object,
  desktopBackgroundImage: PropTypes.object,
  fromArticleContainer: PropTypes.bool,
  sectionTitle: PropTypes.oneOfType([PropTypes.bool, PropTypes.string]),
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  children: PropTypes.node,
}

export default Section
