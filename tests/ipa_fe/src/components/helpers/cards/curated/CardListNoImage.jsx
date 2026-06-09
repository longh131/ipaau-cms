import PropTypes from 'prop-types'
import { createMarkup } from '../../../../helpers/markup'
import { dataLayerPush } from '../../../../helpers/thirdparty'
import { Arrow } from '../../Icons'
import DynamicHeroIcon from '../../../../helpers/dynamicHeroIcon'
const CardListNoImage = ({ cardItem, colsClass, cardBackgroundColour, cardLinkAlignment, contentAlignment }) => {
  const linkParent = cardItem.ctaLinkItem?.[0]
  const link = linkParent?.link
  let bgClass = '#fff'
  if (cardBackgroundColour) {
    bgClass = `#${cardBackgroundColour.replace('#', '')}`
  }

  const styles = {
    '--bg-color': bgClass,
  }

  // get the content alignment setting from the CMS
  let contentAlignmentClass = 'items-start'
  let contentAlignmentTextClass = 'text-left'
  if (contentAlignment && contentAlignment.toLowerCase() === 'center') {
    contentAlignmentClass = 'items-center'
    contentAlignmentTextClass = 'text-center'
  }

  // if the editor has also added a title alignment to a card, that takes precedence over the component setting.
  if (cardItem.titleAlignment) {
    contentAlignmentClass = cardItem.titleAlignment.toLowerCase() === 'center' ? 'items-center' : 'items-start'
    contentAlignmentTextClass = cardItem.titleAlignment.toLowerCase() === 'center' ? 'text-center' : 'text-left'
  }

  const className = `relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden ${colsClass} bg-[color:var(--bg-color)] border border-[#C6C6C6]} group/card`

  const inner = <>
    {cardItem.tagline && <div className={`eyebrow-xl text-warm-plum mb-2 break-all ${contentAlignmentTextClass}`}>{cardItem.tagline}</div>}
    {cardItem.title && <h2
      className={`mb-0 font-medium font-din text-2xl leading-[1.4] tracking-[.04em] uppercase inline-flex gap-3 items-center group-hover/noImageCard:underline text-link group-hover/noImageCard:text-link-hover ${contentAlignmentTextClass}`}
    >
      {linkParent.linkIcon && linkParent.linkPosition === 'False' && <DynamicHeroIcon icon={linkParent.linkIconClass} iconClass={linkParent.linkIconClass} before={true} />}
      {cardItem.title}
      {linkParent.linkIcon && linkParent.linkPosition === 'True' && <DynamicHeroIcon icon={linkParent.linkIconClass} iconClass={linkParent.linkIconClass} before={false} />}
      {link?.url && <span className="shrink-0 group-hover/noImageCard:translate-x-4 transition-transform duration-300"><Arrow /></span>}
    </h2>}
    {cardItem.description && <div className={`line-clamp-2 ${contentAlignmentTextClass}`} data-rte="true" dangerouslySetInnerHTML={createMarkup(cardItem.description)} />}
  </>

  return link?.url ? <a
    href={link.url}
    target={link.target}
    className={`${className} elevation-0 hover:elevation-3 transition-all duration-300 hover:bg-grey-subtle group/noImageCard ${contentAlignmentClass}`}
    style={styles}
    onClick={event => {
      dataLayerPush({
        event: 'tile_link_click',
        click_text: cardItem.title,
        destination_path: link.url,
      }, event.target)
    }}
  >{inner}</a> : <div {...{className}} style={styles}>{inner}</div>
}

CardListNoImage.propTypes = {
  cardItem: PropTypes.object.isRequired,
  colsClass: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
  cardLinkAlignment: PropTypes.string,
  contentAlignment: PropTypes.string,
}

export default CardListNoImage
