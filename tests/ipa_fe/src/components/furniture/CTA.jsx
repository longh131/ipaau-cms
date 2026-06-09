import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import ButtonEl from '../helpers/ctas/Button'
import { ChevronDownIcon, EllipsisVerticalIcon, ArrowLongUpIcon, ArrowRightIcon } from '@heroicons/react/24/solid'

function CtaDropdownTriggerButton(props) {
  return (
    <ButtonEl
      key="header-cta-nav-0-button"
      theme={props.theme}
      //  added workaround to get rounded working correctly here.
      icon={props.ctas[0].linkIconClass}
      position={props.ctas[0].linkPosition}
      onClick={props.onClick}
      ariaHasPopup="true"
      ariaControls="navCtaWrapper"
      ariaExpanded={`${props.ctaActive}`}
      ariaHidden="false"
      overrideBase={props.overrideBase}
      iconClass={`${props.iconClass ? props.iconClass : ''}`}
    >
      <div className="pr-3">{props.ctas[0].link?.name}</div>
      <ChevronDownIcon
        className="h-4 w-4 text-primary text-sm group-[.active]/ctawrapper:-scale-y-100 group-[.inactive]/ctawrapper:scale-y-100 transition-all duration-1000"
        role="none"
      />
    </ButtonEl>
  )
}

function CtaDropdownTriggerMinimal(props) {
  return (
    <ButtonEl
      key="header-cta-nav-0-button"
      theme={props.theme}
      //  added workaround to get rounded working correctly here.
      // icon={props.ctas[0].icon}
      onClick={props.onClick}
      ariaHasPopup="true"
      ariaControls="navCtaWrapper"
      ariaExpanded={`${props.ctaActive}`}
      ariaHidden="false"
    >
      <span className="sr-only">{props.ctas[0].name}</span>
      <div className="h-4 w-4 relative">
        <EllipsisVerticalIcon
          className="absolute top-0 left-0 h-4 w-4 group-[.active]/ctawrapper:opacity-0  group-[.inactive]/ctawrapper:opacity-100 transition-all duration-1000"
          role="none"
        />
        <ArrowLongUpIcon
          className="absolute top-0 left-0 h-4 w-4 group-[.active]/ctawrapper:opacity-100  group-[.inactive]/ctawrapper:opacity-0 transition-all duration-1000"
          role="none"
        />
      </div>
    </ButtonEl>
  )
}

function CtaDropDownTrigger(props) {
  if (props.triggerStyle === 'minimal') {
    // returns the minimal trigger button.
    return <CtaDropdownTriggerMinimal {...props} theme="none" />
  } else if (props.triggerStyle === 'button') {
    // inherits the button style from the first CTA as defined in the CMS
    return <CtaDropdownTriggerButton {...props} theme={props.ctas[0].linkType} />
  }
  // this overrides any theme set on the first button to show the "standard" version of the dropdown.
  return <CtaDropdownTriggerButton {...props} theme="none" />
}

function CtaDropdown(props) {
  const [ctaActive, setCtaActive] = useState(false)
  const [variantClasses, setVariantClasses] = useState('')
  const wrapperClasses = props.position === 'mobile' ? 'ml-3 block md:hidden' : 'basis-1/10 ml-auto hidden md:block'
  const menuClasses = props.position === 'mobile' ? 'origin-bottom translate-y-[-110%] top-0' : 'origin-top'
  useEffect(() => {
    setCtaActive((prevState) => (props.menuActive === true || props.searchActive === true ? false : prevState))
  }, [props.menuActive, props.searchActive])

  document.addEventListener('click', (evt) => {
    if (!evt.target.closest('[data-type="cta-dropdown"]')) {
      setCtaActive(false)
    }
  })

  useEffect(() => {
    setVariantClasses((dd) => {
      if (props.variant === 'divider') {
        return 'divide-y'
      }
      return ''
    })
  }, [props.variant])

  const handleCtaExpandClick = () => {
    setCtaActive((prevState) => {
      const isActive = !prevState
      if (props.position !== 'mobile') {
        props.setCtasActive(isActive)
        props.setSearchActive((prevState) => {
          let newState = prevState
          if (isActive === true) {
            // is search has become active turn the menu off
            newState = false
          }
          // otherwise do nothing.
          props.setMenuActive(newState)
          return newState
        })
      }
      return isActive
    })
  }

  return (
    <div data-type="" className={`${wrapperClasses} ${props.className} self-center`}>
      {props.ctas?.length > 1 && (
        // <p>Multiple Buttons</p>
        // we need to build a drop-down from this, using the first button as a label only
        // any links added to this CTA will be ignored.
        <nav
          data-type="cta-dropdown"
          aria-label="Main CTA navigation"
          className={`group/ctawrapper relative ${ctaActive ? 'active' : 'inactive'}`}
        >
          <ul>
            <li key="header-cta-nav-0" className={`has-submenu `}>
              <CtaDropDownTrigger
                {...props}
                ctaActive={`${ctaActive}`}
                onClick={handleCtaExpandClick}
                triggerStyle={props.type}
              />
              <ul
                id="navCtaWrapper"
                className={`${ctaActive ? 'scale-y-100 z-50' : 'scale-y-0 z-0'} ${menuClasses} shadow-xl transition-all duration-1000 absolute z-50 bg-white rounded min-w-[224px] ${variantClasses} right-0`}
              >
                {props.ctas.map((cta, idx) => {
                  if (idx == 0) {
                    return null
                  }
                  return (
                    <li key={`${cta.link?.url || cta.link?.name || idx}`} className={`my-1`}>
                          <ButtonEl
                            item={cta}
                            theme="none"
                            active={`${ctaActive}`}
                            className={`${props.variant === 'icon' ? 'w-full content-between ' : ''} text-secondary`}
                            ariaHidden={`${ctaActive ? 'false' : 'true'}`}
                            overrideBase={props.overrideBase}
                          >
                            {props.variant !== 'icon' && <span>{cta.link.name}</span>}
                            {props.variant === 'icon' && (
                              <>
                                <div className="basis-3/4">{cta.link.name}</div>
                                <ArrowRightIcon className="w-6 h-4 basis-1/4 shrink-0" />
                              </>
                            )}
                          </ButtonEl>
                    </li>
                  )
                })}
              </ul>
            </li>
          </ul>
        </nav>
      )}

      {props.ctas?.length === 1 &&
        props.ctas.map((cta, i) => {
          return (
            <ButtonEl
              overrideBase={props.overrideBase}
              key={`${cta.link?.url || cta.link?.name || i}`}
              item={cta}
              theme="hollow"
              iconClass={`${props.iconClass ? props.iconClass : ''}`}
            />
          )
        })}
    </div>
  )
}

CtaDropdown.propTypes = {
  ctas: PropTypes.array,
  theme: PropTypes.string,
  onClick: PropTypes.func,
  ctaActive: PropTypes.bool,
  overrideBase: PropTypes.string,
  iconClass: PropTypes.string,
  type: PropTypes.string,
  variant: PropTypes.string,
  position: PropTypes.string,
  className: PropTypes.string,
  menuActive: PropTypes.bool,
  searchActive: PropTypes.bool,
  setCtasActive: PropTypes.func,
  setSearchActive: PropTypes.func,
  setMenuActive: PropTypes.func,
}

CtaDropdownTriggerButton.propTypes = {
  theme: PropTypes.string,
  ctas: PropTypes.array,
  onClick: PropTypes.func,
  ctaActive: PropTypes.bool,
  overrideBase: PropTypes.string,
  iconClass: PropTypes.string,
}

CtaDropdownTriggerMinimal.propTypes = {
  theme: PropTypes.string,
  ctas: PropTypes.array,
  onClick: PropTypes.func,
  ctaActive: PropTypes.bool,
}

CtaDropDownTrigger.propTypes = {
  ctas: PropTypes.array,
  onClick: PropTypes.func,
  ctaActive: PropTypes.bool,
  type: PropTypes.string,
  variant: PropTypes.string,
  triggerStyle: PropTypes.string,
}

export default CtaDropdown
