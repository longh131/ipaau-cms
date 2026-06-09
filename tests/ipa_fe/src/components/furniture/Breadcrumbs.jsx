import PropTypes from 'prop-types'
import { HomeIcon } from '@heroicons/react/24/solid'
import { dataLayerPush } from '../../helpers/thirdparty'

const Breadcrumbs = ({items}) => {
  if (!items?.length) {
    return
  }

  // we need to treat home differently on mobile, so we need to separate it out from the rest of the breadcrumbs.
  const homeItem = items[0]
  const breadcrumbItems = items.slice(1)

  return (
    // the breadcrumbs need to sit over the top of the content, which may have a background colour. So we need to give it a negative margin to sit over the top.
    <nav aria-label="breadcrumb navigation" className={`container mx-auto h-12 -mb-12 relative z-10 pt-18 flex items-center px-7 md:px-10`}>
      <ul
        className={`bg-transparent  text-primary font-din  w-full flex-nowrap text-md flex gap-3 items-center`}
      >
        {homeItem && (
          <li key={`${homeItem.url}-${homeItem.label}`} className="inline-flex items-center gap-1 md:gap-3">
            <a href={homeItem.url} className={`hover:underline px-0 mx-0 flex items-center gap-1 md:gap-2`} onClick={() => {
              dataLayerPush({
                event: 'navigation_breadcrumb_click',
                click_text: homeItem.label,
                destination_path: homeItem.url
              })
            }}>
              <HomeIcon className="w-4 h-4" />
              <span className="sr-only">{homeItem.label}</span>
            </a>
            {breadcrumbItems.length > 0 && (
              <span>/</span>
            )}
          </li>
        )}
        {breadcrumbItems.map((item, i) => (
          <li key={`${item.url}-${item.label}`} className="inline-flex items-center gap-1 md:gap-3">
            {item.disablePageLinkInMegaMenu && <span className="px-0 mx-0">{item.label}</span>}
            {!item.disablePageLinkInMegaMenu && (i != breadcrumbItems.length - 1) && (
              <a href={item.url} className={`hover:underline px-0 mx-0 inline-block max-md:max-w-16 md:max-lg:max-w-28 truncate text-nowrap ellipsis`} onClick={() => {
                dataLayerPush({
                  event: 'navigation_breadcrumb_click',
                  click_text: item.label,
                  destination_path: item.url
                })
              }}>
                {item.label}
              </a>
            )}
            {!item.disablePageLinkInMegaMenu && (i == breadcrumbItems.length - 1) && (
              <span className="text-link font-medium inline-block max-md:max-w-16 md:max-lg:max-w-28 truncate text-nowrap ellipsis">{item.label}</span>
            )}
            {i != breadcrumbItems.length - 1 && (
              <span>/</span>
            )}
          </li>
        ))}
      </ul>
    </nav>
  )
}

Breadcrumbs.propTypes = {
  items: PropTypes.arrayOf(
    PropTypes.shape({
      url: PropTypes.string,
      label: PropTypes.string,
      disablePageLinkInMegaMenu: PropTypes.bool,
    })
  ),
}

export default Breadcrumbs
