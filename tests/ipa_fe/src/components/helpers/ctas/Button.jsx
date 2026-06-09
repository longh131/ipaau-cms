import { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import { dataLayerPush } from '../../../helpers/thirdparty'
import * as HeroIcons from '@heroicons/react/24/solid'

function DynamicHeroIcon({icon, iconClass, before}) {
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
      className={`h-6 ${before ? 'mr-4' : 'ml-4'} shrink-0 w-6 text-current ${iconClass || ''}`}
      role="presentation"
      aria-hidden="true"
    />
  )
}

function ButtonEl(props) {
  const link = props.link || props.item?.link
  const icon = props.icon || props.item?.linkIconClass
  const position = props.position || props.item?.linkPosition
  let theme = props.theme || props.item?.linkType
  if (theme) {
    theme = theme.toLowerCase()
  }
  if (theme === 'text') {
    theme = 'none'
  } else if (!['primary', 'secondary', 'tertiary', 'none', 'hollow'].includes(theme)) {
    theme = 'secondary'
  }

  const [aria, setAria] = useState({})

  let typeClasses = 'rounded-full'
  // if the config is set to not use square buttons, override the class
  // themeConfig.settings.useOnlyPillButtons && (typeClasses = 'rounded-full')

  useEffect(() => {
    setAria((prevState) => {
      let newState = { ...prevState }
      if (!props.ariaHidden || props.ariaHidden === 'false') {
        delete newState['aria-hidden']
        delete newState.tabIndex
      } else {
        newState['aria-hidden'] = 'true'
        newState.tabIndex = -1
      }
      if (props.ariaHasPopup) {
        newState['aria-haspopup'] = props.ariaHasPopup
      }
      if (props.ariaControls) {
        newState['aria-controls'] = props.ariaControls
      } else {
        delete newState['aria-controls']
      }
      if (props.ariaExpanded) {
        newState['aria-expanded'] = props.ariaExpanded
      } else {
        delete newState['aria-expanded']
      }
      return newState
    })
  }, [props.ariaHidden, props.ariaControls, props.ariaExpanded])

  let themeClasses = ''
  switch (theme) {
    case 'primary':
      themeClasses = `border-link bg-link text-white hover:bg-link-hover hover:border-link-hover focus-visible:bg-link-hover focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:bg-disabled disabled:border-disabled disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed`
      break
    case 'secondary':
    case 'tertiary':
      themeClasses = 'border-link bg-white text-link hover:bg-link-hover hover:text-white focus-visible:bg-link-hover focus-visible:text-white focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent focus-visible:no-underline disabled:bg-disabled disabled:border-grey disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed'
      break
    case 'hollow':
      themeClasses = 'border-link bg-transparent text-link hover:bg-link-hover hover:text-white focus-visible:bg-link-hover focus-visible:text-white focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent focus-visible:no-underline disabled:bg-disabled disabled:border-grey disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed'
      break
    case 'text':
    case 'none':
      themeClasses = 'border-transparent text-link hover:text-link-hover hover:underline focus-visible:underline focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:text-grey/50 disabled:cursor-not-allowed disabled:hover:no-underline'
      break
  }

  const className = `cta group font-medium uppercase border-2 ${themeClasses} flex transition-all duration-300`
  let paddingClasses = 'px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px]'
  if (['search', 'mobile', 'login'].includes(props.function)) {
    paddingClasses = 'p-[9px]'
  }

  const inner = (
    <div className="flex flex-wrap items-center w-full">
      <div className="cta-content flex flex-nowrap items-center justify-center w-full uppercase">
        {icon && position === 'False' && <DynamicHeroIcon icon={icon} iconClass={props.iconClass} before={true} />}
        {props.label || props.children || link?.name || link?.label}
        {icon && position === 'True' && <DynamicHeroIcon icon={icon} iconClass={props.iconClass} before={false} />}
      </div>
    </div>
  )

  const dataLayerClick = event => {
    const target = event.target
    dataLayerPush({
      event: (['primary', 'secondary'].includes(theme) ? theme : 'tertiary')+'_cta_click',
      click_text: target.innerText,
      destination_path: link.url
    }, target)
  }
  return link ? (
    <a
      href={link.url}
      target={link.target}
      className={`${props.className || ''} ${className} border uppercase text-lg hover:underline focus-visible:underline ${paddingClasses} ${typeClasses}`}
      tabIndex={props.active === undefined || props.active === 'true' ? 0 : -1}
      onClick={event => {
        if (props.dataLayer !== false) {
          dataLayerClick(event)
        }

        if (props.onClick) {
          props.onClick(event)
        }
      }}
      {...aria}
    >
      {inner}
    </a>
  ) : (
      <button
        type={props.type ?? 'button'}
        className={`${props.className || ''} ${className} border uppercase text-lg hover:underline focus-visible:underline ${paddingClasses} ${typeClasses}`} // had to wrap in {``} to get passed in className to be accepted.
        onClick={event => {
          if (props.onClick) {
            props.onClick(event)
          }
        }}
        {...aria}
        data-idx={props.idx || '0'}
        disabled={props.disabled}
      >
        {inner}
      </button>
  )
}

ButtonEl.propTypes = {
  link: PropTypes.object,
  item: PropTypes.object,
  icon: PropTypes.string,
  position: PropTypes.string,
  theme: PropTypes.string,
  buttonType: PropTypes.string,
  ariaHidden: PropTypes.string,
  ariaHasPopup: PropTypes.string,
  ariaControls: PropTypes.string,
  ariaExpanded: PropTypes.string,
  function: PropTypes.string,
  label: PropTypes.string,
  children: PropTypes.node,
  className: PropTypes.string,
  active: PropTypes.oneOfType([PropTypes.string, PropTypes.bool]),
  dataLayer: PropTypes.bool,
  onClick: PropTypes.func,
  type: PropTypes.string,
  idx: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  disabled: PropTypes.bool,
  iconClass: PropTypes.string,
  outerClass: PropTypes.string,
  showDecorator: PropTypes.bool,
  overrideBase: PropTypes.string,
}

DynamicHeroIcon.propTypes = {
  icon: PropTypes.string,
  iconClass: PropTypes.string,
  before: PropTypes.bool,
}

export default ButtonEl
