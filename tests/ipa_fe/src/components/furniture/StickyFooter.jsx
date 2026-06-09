import PropTypes from 'prop-types'
import ButtonEl from '../helpers/ctas/Button'
import CtaDropdown from './CTA'
import themeConfig from '../../../theme.config'

function StickyFooter(props) {
  if ( props?.stickyNavPrimary?.length || props?.stickyNavSecondary?.length) {
    return (
      <div data-type="stickyFooterNav" className="block md:hidden sticky bottom-0 z-50">
        <nav
          className={`w-full bg-${themeConfig.settings.nav.sticky.base} text-${themeConfig.settings.nav.sticky.base}-text`}
          aria-label="Sticky mobile navigation"
        >
          <div className="flex justify-center space-evenly w-full py-3 px-3 !text-sm -mb-3">
            {props?.stickyNavPrimary?.[0]?.link?.url && (
              <ButtonEl
                key="sticknavprimary"
                icon={props.stickyNavPrimary[0].linkIconClass}
                position={props.stickyNavPrimary[0].linkPosition}
                iconClass={'mr-[0.5rem] !h-5 !w-5'}
                theme="primary"
                target={props.stickyNavPrimary[0].link.target}
                link={props.stickyNavPrimary[0].link}
                overrideBase={themeConfig.settings.nav.sticky.base}
                className="mr-[0.2rem] ml-0"
              />
            )}
            {props.stickyNavSecondary && (
              <CtaDropdown
                overrideBase={themeConfig.settings.nav.sticky.base}
                position="mobile"
                type="button"
                ctas={props.stickyNavSecondary}
                variant="divider"
                className="ml-[0.2rem] mr-0"
                iconClass="mr-[0.5rem] !h-5 !w-5"
              />
            )}
          </div>
        </nav>
      </div>
    )
  } else {
    return <></>
  }
}

StickyFooter.propTypes = {
  stickyNavPrimary: PropTypes.array,
  stickyNavSecondary: PropTypes.array,
}

export default StickyFooter
