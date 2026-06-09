import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import { createPortal } from 'react-dom'
import ButtonEl from '../helpers/ctas/Button'
import { dataLayerPush } from '../../helpers/thirdparty'
import { ArrowRightIcon, ChevronDownIcon } from '@heroicons/react/24/solid'
import { PlusIcon, MinusIcon } from '@heroicons/react/24/outline'
import themeConfig from '../../../theme.config'
import { generateDataHash } from '../../helpers/contentHash'

const NavLevel2 = (props) => {
  // Use mobile-specific active state for second level menus
  const isMobileActive = props.secondLevelMenuActive === props.menuItemIndex
  const isDesktopActive = props.active
  const [desktopActiveClass, setDesktopActiveClass] = useState('inactive max-xl:hidden')
  const [mobileActiveClass, setMobileActiveClass] = useState('max-xl:hidden')

  useEffect(() => {
    setDesktopActiveClass(isDesktopActive ? 'active block' : 'inactive max-xl:hidden')
    setMobileActiveClass(isMobileActive ? 'max-xl:block' : 'max-xl:hidden')
  }, [isDesktopActive, isMobileActive])

  return (
    <ul
      data-type="megamenu-level-2"
      id={props.id}
      className={`${desktopActiveClass} ${mobileActiveClass}`}
    >
      {props.child.children.map((subchild) => {
        return !subchild?.disablePageLinkInMegaMenu ? (
          <li key={`${generateDataHash(subchild)}`} data-level="2">
            <a
              href={subchild.url}
              target={subchild.target ?? '_self'}
              className={`
                font-normal
                m-0
                p-2
                xl:py-3
                flex
                justify-between
                xl:border-b-4
                text-primary!
                hover:underline
                border-transparent
                transition-all
                duration-300
                focus:-outline-offset-4
              `}
              tabIndex={props.active ? 0 : -1}
              aria-hidden={!props.active}
              onClick={() => {
                dataLayerPush({
                  event: 'navigation_menu_click',
                  category: props.child.label,
                  click_text: subchild.label
                })
              }}
            >
              {subchild.label}
            </a>
          </li>
        ) : (
          <></>
        )
      })}
    </ul>
  )
}

const NavLevel1 = (props) => {
  const [subActive, setSubActive] = useState(-1)
  const [menuImage, setMenuImage] = useState()
  const [, setHasChildActive] = useState(false)
  const [secondLevelMenuActive, setSecondLevelMenuActive] = useState(-1)
  const [megamenuHeight, setMegamenuHeight] = useState(null)

  const handleNavClick = ({ target }) => {
    let clickTarget = ['BUTTON', 'A'].includes(target.nodeName) ? target : target.closest('a, button')
    // Convert dataset.idx to number for proper comparison
    const idx = clickTarget?.dataset?.idx !== undefined ? Number(clickTarget.dataset.idx) : null
    props.handleNavClick(idx)

    // Only auto-open first child if clicking the anchor tag (not the button)
    if (idx !== null && props.navItem?.children?.length > 0 && target.nodeName === 'A') {
      setSecondLevelMenuActive(0)
    }
  }

  const handleSecondLevelMenuClick = (idx) => {
    setSecondLevelMenuActive((prevState) => {
      // If clicking the same item, toggle it off
      if (prevState === idx) {
        return -1
      }
      // Otherwise, set the new active item (this automatically deactivates others)
      return idx
    })
  }

  useEffect(() => {
    if (!props.active) {
      // if we close the parent, close the active second level nav too.
      setSubActive(-1)
      setSecondLevelMenuActive(-1)
      setMegamenuHeight({})
    }
  }, [props.active])

  useEffect(() => {
    if (props.active) {
      const imageOpts = {
        src: props.navItem?.image?.src ?? null,
        alt: props.navItem?.image?.alt ?? '',
        url: props.navItem?.image?.url ?? null,
        target: props.navItem?.image?.target ?? null,
        description: props.navItem?.imageDescription ?? null
      }
      setMenuImage(imageOpts)
    } else {
      setMenuImage(null)
    }
  }, [props.active, props.navItem?.image, props.navItem?.imageDescription])

  const menuDecorator = ({menuImage, imageDescription}) => {
    if (!menuImage?.src && !imageDescription) {
      return null
    }
    return (
      <div
      data-type="menu-decorator"
      className="empty:hidden"
    >
      {menuImage?.src && (
        <div>
          <img src={menuImage.src} alt={menuImage.alt} />
        </div>
      )}
      {menuImage?.description && (
        <div>
          {menuImage.url ? (
            <a
              href={menuImage.url}
              target={menuImage.target}
              className="flex items-center gap-6 px-0 group/navlink"
              onClick={() => {
                dataLayerPush({
                  event: 'navigation_menu_click',
                  category: imageDescription,
                  click_text: imageDescription
                })
              }}
            >
              {imageDescription}
              <ArrowRightIcon role="none" className="h-6 w-6 shrink-0 group-hover/navlink:translate-x-1 transition-all duration-300" />
            </a>
          ) : (
            <>
              {imageDescription}
            </>
          )}
        </div>
      )}
    </div>
    )
  }

  return (
    <li
      data-children="true"
      data-level="0"
      className={`${props.active && (props.menuActive === true || props.menuActive === undefined) ? 'active' : 'inactive'} group/navlink`}
    >
      <div
        data-type="menu-wrapper-0"
      >
        {!props.navItem?.disablePageLinkInMegaMenu ? (
          <>
          <a
            href={props.navItem?.url}
            target={props.navItem?.target ?? '_self'}
            data-img-description={props.navItem?.imageDescription}
            data-img-alt={props.navItem?.image?.alt}
            data-img={props.navItem?.image?.src}
            className={`h-full flex font-inter font-semibold items-center hover:underline focus:-outline-offset-4 underline-offset-8 text-secondary max-xl:border-b max-xl:border-b-grey-subtle max-xl:py-8`}
            data-idx={props.idx}
            onClick={handleNavClick}
          >
            {props.navItem?.label}
          </a>
            <button
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                // Create a synthetic target with the required dataset.idx
                const syntheticTarget = {
                  nodeName: 'BUTTON',
                  dataset: { idx: String(props.idx) }
                };
                handleNavClick({ target: syntheticTarget });
              }}
              className=" w-12 h-12 ml-auto xl:hidden relative flex items-center justify-center mr-4">
              <div className="w-6 h-6 flex items-center justify-center rounded-full  border-2 border-secondary text-warm-plum" role="none">
              {props.active ? (
                <MinusIcon className="w-4 h-4" role="none" />
              ) : (
                <PlusIcon className="w-4 h-4" role="none" />
              )}
              </div>
            </button>
            </>
        ) : (
          <ButtonEl
            theme="none"
            idx={props.idx}
            className={`h-full w-full pr-2 pl-6 py-3 flex font-inter font-semibold items-center hover:underline focus:!-outline-offset-4  underline-offset-8 text-secondary max-xl:border-b max-xl:border-b-subtle-grey max-xl:py-8`}
            ariaHasPopup="true"
            ariaControls={`megamenu-${generateDataHash(props.navItem)}`}
            ariaExpanded={`${props.active ? 'true' : 'false'}`}
            onClick={handleNavClick}
            buttonType="default"
          >
            <span
              data-img-description={props.navItem?.imageDescription}
              data-img-alt={props.navItem?.image?.alt}
              data-img={props.navItem?.image?.src}
              className="font-normal inline-block mr-8"
            >
              {props.navItem?.label}
            </span>
            <ChevronDownIcon
              className={`item-level-0-icon text-current h-4 w-4 text-base-text ml-auto text-sm transition-all duration-1000`}
            />
            <div className="sr-only">{`Expand menu for item ${props.navItem?.label}`}</div>
          </ButtonEl>
        )}
      </div>
      {props.navItem?.children.length > 0 && (
      <div
        data-type="megamenu-panel"
        id={`megamenu-${generateDataHash(props.navItem)}`}
        style={megamenuHeight}
        className={`megamenu-gradient xl:elevation-bottom-4 xl:w-screen xl:left-1/2 xl:-translate-x-1/2 ${props.active && (props.menuActive === true || props.menuActive === undefined) ? 'block' : 'hidden'}`}
      >
        <div data-type="megamenu-container" className="container w-full flex items-stretch mx-auto">
          <ul data-type="megamenu-level-1" className="grow max-xl:py-6">
            {props.navItem?.children.map((child, idx) => {
              return (
                <li
                  key={generateDataHash(child)}
                  data-idx={idx}
                  data-sub={subActive?.toString()}
                  className={`
                    ${subActive?.toString() === idx.toString() ? `active max-xl:bg-${themeConfig.settings.nav[props.type].active}` : `inactive`}
                    xl:text-${themeConfig.settings.nav[props.type].base}-text
                    hover:bg-${themeConfig.settings.nav[props.type].active}
                    hover:text-${themeConfig.settings.nav[props.type].active}-text
                  `}
                >
                  <div
                    data-type="menu-wrapper-1"
                    className={`pr-4 ${child?.children?.length ? 'max-xl:flex max-xl:justify-between max-xl:items-center' : ''}`}
                  >
                    {child.url ? (
                      <a
                        href={child.url}
                        data-idx={idx}
                        data-children="true"
                        target={child.target ?? '_self'}
                        tabIndex={props.active ? 0 : -1}
                        aria-hidden={!props.active}
                        className={`py-0 xl:py-3 max-xl:h-12 max-xl:flex max-xl:items-center w-full  hover:underline text-primary max-xl:font-semibold xl:text-secondary`}
                        onClick={() => {
                          dataLayerPush({
                            event: 'navigation_menu_click',
                            category: child.label,
                            click_text: child.label
                          })
                        }}
                      >
                        {child.label}
                      </a>
                    ) : <strong>{child.label}</strong>}
                    {child?.children?.length && (
                      <button
                        onClick={(e) => {
                          e.preventDefault();
                          e.stopPropagation();
                          handleSecondLevelMenuClick(idx);
                        }}
                        className="flex items-center justify-center min-w-12 h-12 ml-auto xl:hidden relative">
                        <div className="w-6 h-6 flex items-center justify-center rounded-full  border-2 border-secondary text-warm-plum" role="none">
                          {secondLevelMenuActive === idx ? (
                            <MinusIcon className="w-4 h-4" />
                          ) : (
                            <PlusIcon className="w-4 h-4" />
                          )}
                        </div>
                      </button>
                    )}
                  </div>

                  {child.children && child.children?.length > 0 && <NavLevel2
                    {...props}
                    id={`megamenu-lv2-${generateDataHash(child)}`}
                    setHasChildActive={setHasChildActive}
                    active={props.active}
                    child={child}
                    secondLevelMenuActive={secondLevelMenuActive}
                    handleSecondLevelMenuClick={handleSecondLevelMenuClick}
                    menuItemIndex={idx}
                  />}
                </li>
              )
            })}
          </ul>
          {props.type && props.type === 'desktop' && (
            menuDecorator({menuImage, imageDescription: props.imageDescription})
          )}
          {props.type && props.type !== 'desktop'&& document.getElementById('mobile-navigation-footer') && createPortal(
            menuDecorator({menuImage, imageDescription: props.imageDescription}),
              document.getElementById('mobile-navigation-footer')
            )}
        </div>
      </div>
      )}
    </li>
  )
}

const MainNav = (props) => {
  const [activeIndex, setActiveIndex] = useState(null)
  const useMenuAccents = themeConfig.settings.useCMSMenuAccents ?? false
  const themeHighlight = themeConfig.settings.nav.desktop.highlight

  // Reset active menu when parent mobile nav closes
  useEffect(() => {
    if (props.type && props.type !== 'desktop' && !props.menuActive) {
      setActiveIndex(null)
    }
  }, [props.menuActive, props.type])

  // test the settings to see if we want to use CMS-based link highlights.
  // if we do not, use the colour in the settings
  // if we do, test to see if it is set
  // if it is, use that colour, or fall back to the settings colour.
  let baseHighlightColour = themeHighlight
  if (useMenuAccents && props.res?.navigationAccentColour?.length > 1) {
    baseHighlightColour = props.res.navigationAccentColour.toLowerCase()
  }

  const setActive = (isActive) => {
    // global menu active should be the same as local menu active
    props.setMenuActive(isActive)
    props.setSearchActive((prevState) => {
      let newState = prevState
      if (isActive === true) {
        // is menu has become active turn the search off
        return false
      }
      // otherwise do nothing.
      props.setCtasActive(newState)

      return newState
    })
  }

  const handleNavClick = (idx = null) => {
    if (idx === null || idx === undefined) {
      return false
    }
    setActiveIndex((prevState) => {
      let isActive = true

      if (idx === prevState) {
        isActive = false
      }
      setActive(isActive)
      return isActive ? idx : null
    })
  }

  const generateHighlight = (colour) =>
    colour?.length > 0 ? `${colour.toLowerCase()}-500` : `${baseHighlightColour}`

  document.addEventListener('keydown', (evt) => {
    if (evt.key !== 'Escape') return
    props.setMenuActive(false)
    setActiveIndex(null)
  })

  useEffect(() => {
    if (props.type === 'desktop') {
      document.addEventListener('click', (evt) => {
        if (
          !evt.target.closest('[data-type="desktop-navigation"]') &&
          !evt.target.closest('[data-type="mobile-navigation"]')
        ) {
          props.setMenuActive(false)
          setActiveIndex(null)
        }
      })
    }
  }, [])

  // Use header datasource if available, otherwise fall back to page-based navigation
  const navigationData = props.headerDataSource?.megaMenuColumns || props.nav;

  return (
    <nav data-type={`${props.type}-navigation`}>
      <ul data-type="menu-level-0">
        {navigationData?.map((navItem, idx) => {
          // Handle header datasource navigation sections
          if (props.headerDataSource?.megaMenuColumns) {
            return (
              <NavLevel1
                key={generateDataHash(navItem)}
                {...props}
                handleNavClick={handleNavClick}
                active={activeIndex !== null && activeIndex === idx}
                idx={idx}
                navItem={{
                  label: navItem.title,
                  url: '#',
                  image: navItem.megaMenuCtaImage ? {
                    src: navItem.megaMenuCtaImage.src ?? null,
                    alt: navItem.megaMenuCtaImage.alt ?? '',
                    url: navItem.navigationImageLink?.link?.url ?? null,
                    target: navItem.navigationImageLink?.link?.target ?? null
                  } : null,
                  imageDescription: navItem.navigationImageDescription,
                  children: navItem.sections?.map(item => ({
                    label: item.mainMenuLink?.[0]?.link?.name,
                    url: item.mainMenuLink?.[0]?.link?.url,
                    target: item.mainMenuLink?.[0]?.link?.target,
                    children: item.menuLinksL2?.map(subitem => ({
                      label: subitem.link?.name,
                      url: subitem.link?.url,
                      target: subitem.link?.target
                    }))
                  })) || []
                }}
                themeHighlight={themeHighlight}
                baseHighlightColour={baseHighlightColour}
                decorationColor={`${baseHighlightColour}`}
                generateHighlight={generateHighlight}
                imageDescription={navItem.navigationImageDescription}
              />
            );
          }

          // Handle page-based navigation (existing logic)
          if (navItem?.children?.length) {
            return (
              <NavLevel1
                key={`${generateDataHash(navItem)}-level-1`}
                {...props}
                handleNavClick={handleNavClick}
                active={activeIndex !== null && activeIndex === idx}
                idx={idx}
                navItem={navItem}
                themeHighlight={themeHighlight}
                baseHighlightColour={baseHighlightColour}
                decorationColor={`${generateHighlight(navItem?.navigationAccentColour)}`}
                generateHighlight={generateHighlight}
                imageDescription={navItem?.imageDescription}
              />
            )
          }

          if (!navItem?.disablePageLinkInMegaMenu) {
            return (
            <li key={`${generateDataHash(navItem)}-level-1`} data-children="false">
              <a
                className={`h-full flex font-inter items-center hover:underline focus:-outline-offset-4 hover:decoration-4 hover:decoration-${generateHighlight(navItem?.navigationAccentColour)} underline-offset-8 `}
                href={navItem?.url}
                target={navItem?.target}
                onClick={() => {
                  dataLayerPush({
                    event: 'navigation_menu_click',
                    category: navItem?.label,
                    click_text: navItem?.label
                  })
                }}
              >
                {navItem?.label}
              </a>
            </li>
          )}

          return null
        })}
      </ul>
    </nav>
  )
}

MainNav.propTypes = {
  nav: PropTypes.object,
  headerDataSource: PropTypes.object,
  type: PropTypes.string,
  menuActive: PropTypes.bool,
  setMenuActive: PropTypes.func,
  setSearchActive: PropTypes.func,
  setCtasActive: PropTypes.func,
  res: PropTypes.shape({
    navigationAccentColour: PropTypes.string,
  }),
}

NavLevel2.propTypes = {
  secondLevelMenuActive: PropTypes.number,
  menuItemIndex: PropTypes.number,
  active: PropTypes.bool,
  id: PropTypes.string,
  child: PropTypes.object,
}

NavLevel1.propTypes = {
  navItem: PropTypes.object,
  active: PropTypes.bool,
  handleNavClick: PropTypes.func,
  menuItemIndex: PropTypes.number,
  secondLevelMenuActive: PropTypes.number,
  setSecondLevelMenuActive: PropTypes.func,
  menuActive: PropTypes.bool,
  idx: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  type: PropTypes.string,
  imageDescription: PropTypes.string,
}

export default MainNav
