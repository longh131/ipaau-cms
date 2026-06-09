import PropTypes from 'prop-types'
import themeConfig from '../../../theme.config'

const heroTitleLink = ({ type, cardItem, titleAlignmentClass, width, className = '', lightOrDark = 'light', link, onClick, variant }) => (
  <a href={link.href} target={link.target} className={`hover:[&>div]:underline ${className}`} onClick={onClick} style={{
    '--ipa-color-light': themeConfig.textColors.secondary,
    '--ipa-color-dark': themeConfig.textColors['secondary-subtle'],
    'color': `var(--ipa-color-${lightOrDark})`
  }}>
    {heroTitleDiv({ type, cardItem, titleAlignmentClass, width, lightOrDark, variant })}
  </a>
)

const heroTitleDiv = ({ type, cardItem, titleAlignmentClass, width, className = '', lightOrDark = 'light', variant }) => {
  const title = type === 'dynamic' ? cardItem.listingTitle : cardItem.title
  const titleFontClass = variant === 'hero' ? 'font-apex-book' : 'font-din'
  const titleWeightClass = type === 'dynamic' ? 'font-normal' : 'font-bold'
  const titleSizeClass = type === 'curated' && cardItem.useSmallTitle ? 'text-lg' : 'text-2xl'
  const titleStyle = {
    '--ipa-color-light': themeConfig.textColors.secondary,
    '--ipa-color-dark': themeConfig.textColors['secondary-subtle'],
    'color': `var(--ipa-color-${lightOrDark})`
  }

  if (title) {
    if (type === 'curated' && cardItem.useSmallTitle) {
      return (
        <h3 className={`${width} mt-auto mb-0 ${titleFontClass} ${titleWeightClass} ${titleSizeClass} ${titleAlignmentClass} ${className}`} style={titleStyle}>
          {title}
        </h3>
      )
    }

    return (
      <h2 className={`${width} mt-auto mb-0 ${titleFontClass} ${titleWeightClass} ${titleSizeClass} ${titleAlignmentClass} ${className}`} style={titleStyle}>
        {title}
      </h2>
    )
  }
}

const getLink = (cardItem) => {
  if (cardItem.listingLink) {
    return {
      href: cardItem.listingLink,
      target: cardItem.listingLinkTarget
    }
  }
  if (cardItem.ctaLinkItem && cardItem.ctaLinkItem?.length !== 0) {
    return {
      href: cardItem.ctaLinkItem[0].link?.url,
      target: cardItem.ctaLinkItem[0].link?.target ?? '_self'
    }
  }
  return null
}

const HeroTitle = ({ type, cardItem, canLink = false, onClick, className = '', lightOrDark = 'light', titleAlignment = null, variant = 'hero' }) => {
  let titleAlignmentClass = ''
  if (titleAlignment) {
    titleAlignmentClass = `text-${titleAlignment.toLowerCase()}`
  } else if (cardItem.titleAlignment) {
    titleAlignmentClass = `text-${cardItem.titleAlignment.toLowerCase()}`
  }
  const width = type === 'curated' ? 'w-full' : ''
  const link = getLink(cardItem)

  if (canLink && link) {
    return heroTitleLink({ type, cardItem, titleAlignmentClass, width, className, lightOrDark, link, onClick, variant })
  } else {
    return heroTitleDiv({ type, cardItem, titleAlignmentClass, width, className, lightOrDark, link, variant })
  }
}

HeroTitle.propTypes = {
  type: PropTypes.string,
  cardItem: PropTypes.object.isRequired,
  canLink: PropTypes.bool,
  onClick: PropTypes.func,
  className: PropTypes.string,
  lightOrDark: PropTypes.string,
  titleAlignment: PropTypes.string,
  variant: PropTypes.string,
}

heroTitleLink.propTypes = {
  type: PropTypes.string,
  cardItem: PropTypes.object,
  titleAlignmentClass: PropTypes.string,
  width: PropTypes.string,
  className: PropTypes.string,
  lightOrDark: PropTypes.string,
  link: PropTypes.object,
  onClick: PropTypes.func,
  variant: PropTypes.string,
}

heroTitleDiv.propTypes = {
  type: PropTypes.string,
  cardItem: PropTypes.object,
  titleAlignmentClass: PropTypes.string,
  width: PropTypes.string,
  className: PropTypes.string,
  lightOrDark: PropTypes.string,
  variant: PropTypes.string,
}

export default HeroTitle
