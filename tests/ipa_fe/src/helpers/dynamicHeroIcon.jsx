import * as HeroIcons from '@heroicons/react/24/solid'
import PropTypes from 'prop-types'

const DynamicHeroIcon = ({ icon, iconClass, before }) => {
  const { ...icons } = HeroIcons

  if (icon && !icon.endsWith('Icon')) {
    icon += 'Icon'
  }

  const TheIcon = icon ? icons[icon] : null
  if (!TheIcon) {
    return null
  }

  return (
    <TheIcon
      className={`h-6 ${before ? 'mr-2' : 'ml-2'} shrink-0 w-6 text-current ${iconClass || ''}`}
      aria-hidden="true"
    />
  )
}

DynamicHeroIcon.propTypes = {
  icon: PropTypes.string.isRequired,
  iconClass: PropTypes.string,
  before: PropTypes.bool,
}

export default DynamicHeroIcon
