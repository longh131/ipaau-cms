import { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import { XMarkIcon } from '@heroicons/react/24/solid'
import ButtonEl from '../helpers/ctas/Button'
import MainNav from './MainNav'
import { useScrollLock } from '../../helpers/scrollLock'

function MobileNav(props) {
  const [menuActive, setMenuActive] = useState(false)
  const { lockScroll, unlockScroll } = useScrollLock()
  const content = (
    <div
      className={`relative group/menu h-6 w-6 ${menuActive && (props.searchActive === false || props.searchActive === undefined) ? 'active' : 'inactive'}`}
    >
      <div className="absolute top-0 left-0">
        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" className="text-secondary group-[.active]/menu:rotate-90 group-[.active]/menu:opacity-0 group-[.inactive]/menu:rotate-0 group-[.inactive]/menu:opacity-100 transition-all duration-1000">
          <path d="M21.9998 4.98151L1.9998 4.9815" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round"/>
          <path d="M21.9998 12.9815L3.9998 12.9815" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round"/>
          <path d="M21.9998 20.9815L6.9998 20.9815" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round"/>
        </svg>
      </div>
      <div className="absolute top-0 left-0">
        <XMarkIcon
          className="h-6 w-6 text-base-text group-[.active]/menu:rotate-0 group-[.active]/menu:opacity-100 group-[.inactive]/menu:rotate-90 group-[.inactive]/menu:opacity-0 transition-all duration-1000"
          role="none"
        />
      </div>
      <span className="sr-only">Toggle mobile menu</span>
    </div>
  )

  useEffect(() => {
    setMenuActive((prevState) => (props.searchActive === true || props.ctasActive === true ? false : prevState))
  }, [props.searchActive, props.ctasActive, props.menuActive])

  useEffect(() => {
    if (props.type === 'mobile') {
      document.addEventListener('click', (evt) => {
        if (
          !evt.target.closest('[data-type="mobileNavTrigger"]') &&
          !evt.target.closest('[data-type="mobile-navigation"]')
        ) {
          setMenuActive(false)
        }
      })
    }
  }, [])

  useEffect(() => {
    if (menuActive) {
      lockScroll()
    } else {
      unlockScroll()
    }
  }, [menuActive])

  const handleClick = () => {
    // let isActive = false
    setMenuActive((prevState) => {
      const isActive = !prevState
      props.setMenuActive(isActive)
      props.setSearchActive((prevState) => {
        let newState = prevState
        if (isActive === true) {
          // is search has become active turn the menu off
          newState = false
        }
        // otherwise do nothing.
        props.setCtasActive(newState)
        return newState
      })
      return isActive
    })
  }

  return (
    <>
      <div
        data-type="mobileNavTrigger"
        className={`peer/menutrigger self-center ${menuActive && (props.searchActive === false || props.searchActive === undefined) ? 'active' : 'inactive'} xl:hidden ml-3`}
      >
        <ButtonEl theme="none" function="mobile" className="!px-0 !shadow-none h-full items-center" onClick={handleClick}>
          {content}
        </ButtonEl>
      </div>
      <div
        data-type="mobile-navigation-wrapper"
        className={`xl:hidden group/wrapper bg-white shadow-md transition-all duration-500 ${menuActive ? 'active h-[calc(100vh-65px)] md:h-[calc(100vh-85px)]' : 'h-0 overflow-hidden inactive'} flex flex-col w-screen left-1/2 -translate-x-1/2`}
      >
        <div className="basis-auto grow-0 shrink-1 overflow-y-auto" data-type="mobile-navigation-content">
          <MainNav {...props} nav={props.nav} type={props.type} stub="mob" />
        </div>
        <div id="mobile-navigation-footer" className="w-full mt-auto basis-max grow-0 shrink-0 ">
        </div>
      </div>
    </>
  )
}

MobileNav.propTypes = {
  searchActive: PropTypes.bool,
  ctasActive: PropTypes.bool,
  menuActive: PropTypes.bool,
  type: PropTypes.string,
  nav: PropTypes.object,
  headerDataSource: PropTypes.object,
  setMenuActive: PropTypes.func,
  setSearchActive: PropTypes.func,
  setCtasActive: PropTypes.func,
}

export default MobileNav
