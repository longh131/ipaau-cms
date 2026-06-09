import PropTypes from 'prop-types'
import Picture from '../../Picture'
import HeroTitle from '../../CardListingHeroTitle'
import { createMarkup } from '../../../../helpers/markup'
import { dataLayerPush } from '../../../../helpers/thirdparty'
import { Arrow } from '../../Icons'
import DynamicHeroIcon from '../../../../helpers/dynamicHeroIcon'

const CardListIcon = ({ cardItem, linkAlignment }) => {
  const linkParent = cardItem.ctaLinkItem?.[0]
  const link = linkParent?.link

  let alignClass = 'mx-0'
  let imageClass = 'text-left'
  if (cardItem.titleAlignment && cardItem.titleAlignment.toLowerCase() === 'center') {
    alignClass = `mx-auto`
    imageClass = 'text-center'
  }
  return (
    <div className={`relative w-full rounded-lg p-8 flex flex-col items-start overflow-hidden gap-4
      md:[&:nth-child(2n+2)]:col-start-4
      md:[&:nth-child(2n+1)]:col-start-1
      col-span-3`}>
      <div className={`relative ${alignClass} ${imageClass}`}>
        <Picture
          desktopImage={cardItem.desktopImage}
          mobileImage={cardItem.mobileImage}
        />
      </div>
      {cardItem.title && <HeroTitle type="curated" cardItem={cardItem} variant="hero" canLink={false} />}
      {cardItem.description && <div className={`line-clamp-2 ${alignClass}`} data-rte="true" dangerouslySetInnerHTML={createMarkup(cardItem.description)} />}
      {link?.url && <a
        href={link.url}
        target={link.target}
        className={`mt-4 flex gap-[6px] font-medium text-link hover:text-link-hover transition-all duration-300 group/iconCard text-lg ${alignClass} items-center`}
        onClick={event => {
          dataLayerPush({
            event: 'tile_link_click',
            click_text: cardItem.title,
            destination_path: link.url,
          }, event.target)
        }}
      >
        {linkParent.linkIcon && linkParent.linkPosition === 'False' && <DynamicHeroIcon icon={linkParent.linkIconClass} iconClass={linkParent.linkIconClass} before={true} />}
        {link.name}
        {linkParent.linkIcon && linkParent.linkPosition === 'True' && <DynamicHeroIcon icon={linkParent.linkIconClass} iconClass={linkParent.linkIconClass} before={false} />}
        <span className="shrink-0 group-hover/iconCard:translate-x-2 transition-transform duration-300"><Arrow /></span>
      </a>}
    </div>
  )
}

CardListIcon.propTypes = {
  cardItem: PropTypes.object.isRequired,
  linkAlignment: PropTypes.string,
}

export default CardListIcon
