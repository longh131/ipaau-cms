import PropTypes from 'prop-types'
import Logo from './Logo'
import MobileNav from './MobileNav'
import Search from './Search'
import { AuthButton } from '../AuthButton'
import CtaDropDown from './CTA'
import MainNav from './MainNav'

function Header(props) {
  return (
    <header className={` relative container mx-auto` }>
      {props.children}
      <div className="mx-auto h-full flex lg:grid lg:grid-cols-[minmax(0,min-content)_minmax(0,auto)_minmax(0,max-content)_minmax(0,max-content)] justify-between lg:justify-normal lg:gap-x-2 no-wrap items-stretch px-4 xl:px-10">
        <Logo deskLogo={props.deskLogo} mobLogo={props.mobLogo} />
        {!props.res.hideMegamenu && <MainNav {...props} nav={props.nav} headerDataSource={props.headerDataSource} type="desktop" />}
        {props.headerDataSource?.searchBoxActive && <Search {...props} />}
        {props.headerDataSource?.memberPortalDisplay && <AuthButton />}
        {props.ctas && <CtaDropDown {...props} type={'button'} variant="divider" />}
        {!props.res.hideMegamenu && <MobileNav {...props} nav={props.nav} headerDataSource={props.headerDataSource} type="mobile" />}
      </div>
    </header>
  )
}

Header.propTypes = {
  children: PropTypes.node,
  deskLogo: PropTypes.object,
  mobLogo: PropTypes.object,
  res: PropTypes.object,
  nav: PropTypes.object,
  headerDataSource: PropTypes.object,
  ctas: PropTypes.array,
}

export default Header
