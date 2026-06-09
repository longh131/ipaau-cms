import PropTypes from 'prop-types'
import { createMarkup } from '../../helpers/markup'
import StickyFooter from './StickyFooter'
import themeConfig from '../../../theme.config'
import BackToTop from './BackToTop'
import { AdaTrigger } from './Ada'
import { lightOrDark } from '../../helpers/style'
import { dataLayerPush } from '../../helpers/thirdparty'
import lockIcon from '../../assets/icons/lock.svg';
import { generateDataHash } from '../../helpers/contentHash';

function FooterTextAndIcon({item}) {
  const icon = item.linkIconClass === 'LockClosed' && (
    <img
      className={`lock-icon inline-block ${item.linkIconClass}`}
      src={lockIcon}
      alt="locked icon"
      title="locked content"
    />
  )
  return <div className="flex items-center gap-1">
    {item?.linkPosition == 'True' ? <>
      {item.link?.name}
      {icon}
    </> : <>
      {icon}
      {item.link?.name}
    </>}
  </div>
}

function Footer(props) {
  return (
    <>
      <footer
        style={{
          '--bg-color': themeConfig.settings.nav.footer.base ?? '#fff',
          '--light-or-dark': lightOrDark(themeConfig.settings.nav.footer.base),
        }}
        className={`footer-main w-full bg-[color:var(--bg-color)] text-${lightOrDark(themeConfig.settings.nav.footer.base)}`}
      >

        {((props.footerLogo && props.footerDisclaimerText && props.footerNavigationItems?.length > 0) || props.footerNavigationItems?.length > 0 || props.copyrightText) && <div className="container mx-auto py-16">
          {((props.footerLogo && props.footerDisclaimerText && props.footerNavigationItems?.length > 0) || props.footerNavigationItems?.length > 0) && <div className="grid grid-cols-4 md:grid-cols-8 xl:grid-cols-12 gap-x-8 gap-y-2">

            {props.footerLogo && props.footerDisclaimerText && props.footerNavigationItems?.length > 0 && <div id="footer-row-1" className="footer-row footer-row-1 col-span-4 md:col-span-8 xl:col-span-4 p-4 order-1 md:order-2 xl:order-1">

              <div id="footer-logo">
                {props.footerLogo && (
                  props.footerLogo.target ? (
                    <a href={props.footerLogo.target}>
                      <img
                        src={props.footerLogo.src}
                        alt={props.footerLogo.altText || "Footer logo"}
                        className="w-[86px] h-[70px]"
                      />
                    </a>
                  ) : (
                    <img
                      src={props.footerLogo.src}
                      alt={props.footerLogo.altText || "Footer logo"}
                      className="w-[86px] h-[70px]"
                    />
                  )
                )}
              </div>

              {props.footerDisclaimerText && (
                <div id="footer-disclaimer" className="pt-2">
                  <div data-rte="true" dangerouslySetInnerHTML={createMarkup(props.footerDisclaimerText)} />
                </div>
              )}

              {props.socialMediaLinks?.length ? (
                <ul className="footer-social-media-links">
                  {props.socialMediaLinks?.map((link, j) => (
                    <li
                      key={`footer-social-media-link--${link.link?.url || link.link?.name || generateDataHash(link)}`}
                    >
                      <a
                        href={link.link?.url}
                        target={link.link?.target}
                        title={link.link?.name}
                        className="block w-[24px] transition-all duration-500 border-transparent group"
                        onClick={() => {
                          dataLayerPush({
                            event: 'social_outbound_click',
                            outbound_domain: link.link?.url,
                            click_text: link.link?.name
                          })
                        }}
                      >
                        <img
                          className="w-full h-auto group-hover:scale-[1.1] transition-all duration-500"
                          alt={link.linkImage?.altText}
                          src={link.linkImage?.src}
                        />
                        <span className="sr-only">{link.link?.name}</span>
                      </a>
                    </li>
                  ))}
                </ul>
              ) : null}

            </div>} {/* end #footer-row-1 */}


            {props.footerNavigationItems?.length > 0 && (
                <div className="col-span-4 md:col-span-8 xl:col-span-8 p-4 order-2 md:order-1 xl:order-2 font-inter">
                  <div id="footer-right-section" className="footer-nav grid grid-cols-2 md:flex md:flex-wrap mb-8 justify-around gap-12">
                    {props.footerNavigationItems.map((col, idx) => (
                      <nav
                        key={`footer-nav--${generateDataHash(col)}`}
                        className="footer-nav-column basis-full flex-auto md:basis-[calc(50%-3rem)] xl:basis-[min-content]"
                        aria-label={col.title ?? `Footer nav column ${idx + 1}`}
                      >
                        {col.mainMenuLink?.map((item, i) => (
                          item.link?.url ? (
                            <a
                              key={`main-menu-link--${generateDataHash(item)}`}
                              href={item.link.url}
                              target={item.link.target || "_self"}
                              rel={item.link.target === "_blank" ? "noopener noreferrer" : undefined}
                              tabIndex="0"
                              className={`footer-nav-item footer-nav-item-title footer-nav-item-title--link
                                    ${themeConfig.settings.nav.footer.inheritLinkStyle
                                      ? ''
                                      : `hover:underline hover:text-${lightOrDark(
                                          themeConfig.settings.nav.footer.base
                                        )}-500-hover`}
                                  `}
                              title={item.link.name}
                              onClick={() => {
                                dataLayerPush({
                                  event: 'navigation_footer_click',
                                  category: col.mainMenuLink?.[0]?.link.name,
                                  click_text: item.link?.name
                                })
                              }}
                            >
                              <FooterTextAndIcon item={item} />
                            </a>
                          ) : (
                            <span key={`main-menu-link--${generateDataHash(item)}`} className={`footer-nav-item footer-nav-item-title footer-nav-item-title--no-link`}>
                              <FooterTextAndIcon item={item} />
                            </span>
                          )
                        ))}

                        <ul className="footer-nav-ul">
                          {col.menuLinksL2?.map((item, j) => {
                            return <li
                              key={`footer-nav--${generateDataHash(col)}-menuLinksL2-${generateDataHash(item)}`}
                              data-rte={themeConfig.settings.nav.footer.inheritLinkStyle.toString()}
                            >
                              {item.link?.url ? (
                                <a
                                  className={`footer-nav-item footer-nav-item-sub
                                    ${themeConfig.settings.nav.footer.inheritLinkStyle
                                      ? ''
                                      : `hover:underline hover:text-${lightOrDark(
                                          themeConfig.settings.nav.footer.base
                                        )}-500-hover`}
                                  `}
                                  href={item.link.url}
                                  target={item.link.target ?? '_self'}
                                  tabIndex="0"
                                  onClick={() => {
                                    dataLayerPush({
                                      event: 'navigation_footer_click',
                                      category: col.mainMenuLink?.[0]?.link.name,
                                      click_text: item.link?.name
                                    })
                                  }}
                                >
                                  <FooterTextAndIcon item={item} />
                                </a>
                              ) : (
                                <span className={`footer-nav-text font-normal text-light ${align}`}>
                                  <FooterTextAndIcon item={item} />
                                </span>
                              )}
                            </li>
                          })}
                        </ul>
                      </nav>
                    ))}
                  </div>
                </div>
              )}
          </div>}


          {props.copyrightText && (
            <div className="footer-copyright-text col-span-12 p-4 flex">
              &copy; {new Date().getFullYear()}&nbsp;<div data-rte="true" dangerouslySetInnerHTML={createMarkup(props.copyrightText)} />
            </div>
          )}


        </div>}


        <BackToTop />
        <AdaTrigger />
      </footer>
      {!props.disableSticky && (props.stickyNavPrimary || props.stickyNavSecondary) && <StickyFooter {...props} />}
    </>
  )
}

Footer.propTypes = {
  footerLogo: PropTypes.object,
  footerDisclaimerText: PropTypes.string,
  footerNavigationItems: PropTypes.array,
  copyrightText: PropTypes.string,
  disableSticky: PropTypes.bool,
  stickyNavPrimary: PropTypes.array,
  stickyNavSecondary: PropTypes.array,
  socialMediaLinks: PropTypes.arrayOf(
    PropTypes.shape({
      link: PropTypes.shape({
        url: PropTypes.string,
        name: PropTypes.string,
        target: PropTypes.string,
      }),
      linkImage: PropTypes.shape({
        src: PropTypes.string,
        altText: PropTypes.string,
      }),
    })
  ),
}

FooterTextAndIcon.propTypes = {
  item: PropTypes.object,
}

export default Footer
