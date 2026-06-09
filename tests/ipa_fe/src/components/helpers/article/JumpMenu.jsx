import PropTypes from 'prop-types'
import { createMarkup, stripTitleHTML } from '../../../helpers/markup'
import { Arrow } from '../Icons'
import { ChevronDownIcon } from '@heroicons/react/24/solid'

const JumpMenuItem = ({ item, activeSection, handleNavigationClick }) => {
  return (
    <button
      key={item.id}
      id={`${item.id}-trigger`}
      onClick={(evt) => handleNavigationClick(evt, item.id)}
      className={`text-left w-full text-lg border-t border-primary-border transition-colors font-medium ${
        activeSection === item.id ? 'bg-grey-subtle text-secondary' : 'text-primary hover:bg-grey-subtle'
      }`}
    >
      <span
        className={`flex gap-2 items-center justify-between px-6 py-3.5 border-l-4 transition-all duration-300 ${activeSection === item.id ? 'border-l-secondary' : 'border-l-transparent'}`}
      >
        <span>{stripTitleHTML(createMarkup(item.label))}</span>
        <span
          className={`shrink-0 ${activeSection === item.id ? 'opacity-100' : 'opacity-0'} transition-opacity duration-300`}
        >
          <Arrow />
        </span>
      </span>
    </button>
  )
}

const JumpNav = ({ navigationItems, activeSection, handleNavigationClick }) => {
  return (
    <nav>
      {navigationItems.map((item) => (
        <JumpMenuItem
          key={item.id}
          item={item}
          activeSection={activeSection}
          handleNavigationClick={handleNavigationClick}
        />
      ))}
    </nav>
  )
}

const handleDeviceNavToggle = evt => {
  const trigger = document.getElementById('jump-menu-device-nav-trigger')
  const nav = document.getElementById('jump-menu-device-nav')
  if (trigger.getAttribute('aria-expanded') === 'false') {
    trigger.setAttribute('aria-expanded', 'true')
    nav.setAttribute('aria-hidden', 'false')
  } else {
    trigger.setAttribute('aria-expanded', 'false')
    nav.setAttribute('aria-hidden', 'true')
  }
}

const JumpMenuDevice = ({ navigationItems, sidebarTitle, activeSection, handleNavigationClick }) => {
  return (
    <>
      {navigationItems.length > 0 && (
        <div className="col-span-1 w-full px-4 md:px-10">
          <div className="max-h-[calc(100vh-8rem)] overflow-y-auto">
            <button id="jump-menu-device-nav-trigger" aria-controls="jump-menu-device-nav" aria-expanded="false" className="flex items-center justify-between w-full text-left text-lg border-y border-t-primary-border border-b-primary-border aria-expanded:border-b-transparent transition-colors font-medium group/jumpTrigger " onClick={(evt) => {
              handleDeviceNavToggle(evt)
            }}>
              <span className="text-2xl my-2 text-secondary">{sidebarTitle}</span>
              <ChevronDownIcon className="w-6 h-6 group-aria-expanded/jumpTrigger:rotate-180 transition-transform duration-300" />
            </button>
          </div>
          <div id="jump-menu-device-nav" aria-hidden="true" className="aria-hidden:max-h-0 overflow-y-auto aria-hidden:overflow-hidden max-h-[calc(100vh-8rem)] transition-all duration-300">
            <JumpNav
              navigationItems={navigationItems}
              activeSection={activeSection}
              handleNavigationClick={handleNavigationClick}
              />
          </div>
        </div>
      )}
    </>
  )
}

const JumpMenu = ({ navigationItems, sidebarTitle, activeSection, handleNavigationClick }) => {
  return (
    <>
      {/* Left Navigation */}
      {navigationItems.length > 0 && (
        <div className="lg:col-span-1">
          <div className="sticky top-24 -scale-x-100 max-h-[calc(100vh-8rem)] overflow-y-auto">
            <div className="-scale-x-100 pl-2">
              <h3 className="text-2xl mb-4 text-secondary">{sidebarTitle}</h3>
              <JumpNav
                navigationItems={navigationItems}
                sidebarTitle={sidebarTitle}
                activeSection={activeSection}
                handleNavigationClick={handleNavigationClick}
              />
            </div>
          </div>
        </div>
      )}
    </>
  )
}

JumpMenuItem.propTypes = {
  item: PropTypes.shape({
    id: PropTypes.string,
    label: PropTypes.string,
  }).isRequired,
  activeSection: PropTypes.string,
  handleNavigationClick: PropTypes.func.isRequired,
}

JumpNav.propTypes = {
  navigationItems: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      label: PropTypes.string,
    })
  ).isRequired,
  activeSection: PropTypes.string,
  handleNavigationClick: PropTypes.func.isRequired,
}

JumpMenuDevice.propTypes = {
  navigationItems: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      label: PropTypes.string,
    })
  ).isRequired,
  sidebarTitle: PropTypes.string,
  activeSection: PropTypes.string,
  handleNavigationClick: PropTypes.func.isRequired,
}

JumpMenu.propTypes = {
  navigationItems: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.string,
      label: PropTypes.string,
    })
  ).isRequired,
  sidebarTitle: PropTypes.string,
  activeSection: PropTypes.string,
  handleNavigationClick: PropTypes.func.isRequired,
}

export { JumpMenu, JumpMenuDevice, JumpNav }
