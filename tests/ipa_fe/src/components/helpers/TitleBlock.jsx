import PropTypes from 'prop-types'
import { createMarkup, detectHeadingInContent } from '../../helpers/markup'
import themeConfig from '../../../theme.config'

const TitleBlock = (props) => {
  const alignment = props.contentAlignment?.toLowerCase() || 'center'
  const lightOrDarkValue = props.lightOrDark || 'light'

  let titleRole = null
  let ariaLevel = null

  const titleBlockStyle = {
    '--ipa-color-light': themeConfig.textColors['secondary'],
    '--ipa-color-dark': themeConfig.textColors['secondary-subtle'],
    color: `var(--ipa-color-${lightOrDarkValue})`,
  }

  const generateTitleBlock = (content) => {
    const headingProps = {
      style: titleBlockStyle,
      'data-type': 'section-title',
      'data-rte': 'true',
      className: `${themeConfig.settings.useReadableContentWidth ? 'max-w-prose' : ''} font-apex-book`,
      dangerouslySetInnerHTML: createMarkup(content)
    }

    if (detectHeadingInContent(content)) {
      // the editor has styled the content as a heading, or with a heading, so we just return the content as is.
      return (
        <div {...headingProps} />
      )
    }

        // Render the appropriate heading tag dynamically
    switch (props?.level) {
      case '2':
        return <h2 {...headingProps} />
      case '3':
        return <h3 {...headingProps} />
      case '4':
        return <h4 {...headingProps} />
      case '5':
        return <h5 {...headingProps} />
      case '6':
        return <h6 {...headingProps} />
      case '1':
      default:
        return <h1 {...headingProps} />
    }
  }

  return (
    <>
      {(props.title || props.description || props.tagline) && (
        <div className={`${props.headingClass ?? ''} ${props.textShadow ?? ''} text-${alignment} container mx-auto`}>
          {props.tagline && (
            // the larger tagline is used everywhere except for the first time it is used on a page. That should be the only h1 on the page, so we can use that as our yardstick.
            <span
              style={{
                '--ipa-color-light': themeConfig.textColors['warm-plum'],
                '--ipa-color-dark': themeConfig.textColors['warm-plum-subtle'],
                color: `var(--ipa-color-${lightOrDarkValue})`,
              }}
              className={`${props.level === '1' ? 'eyebrow-md' : 'eyebrow-xl'}`}
            >
              {props.tagline}
            </span>
          )}
          {props.title && generateTitleBlock(props.title)}
          {props.description && (
            <div
              style={{
                '--ipa-color-light': themeConfig.textColors.primary,
                '--ipa-color-dark': themeConfig.textColors['primary-subtle'],
                color: `var(--ipa-color-${lightOrDarkValue})`,
              }}
              className={`text-[color:var(--ipa-color)] ${props.title && 'mt-8'} ${themeConfig.settings.useReadableContentWidth ? 'max-w-prose' : ''} ${props.level === '1' ? 'text-2xl' : 'text-xl'} font-din`}
              data-type="section-description"
              data-rte="true"
              dangerouslySetInnerHTML={createMarkup(props.description)}
            ></div>
          )}
        </div>
      )}
    </>
  )
}

TitleBlock.propTypes = {
  contentAlignment: PropTypes.string,
  lightOrDark: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  headingClass: PropTypes.string,
  textShadow: PropTypes.string,
  level: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
}

export default TitleBlock
