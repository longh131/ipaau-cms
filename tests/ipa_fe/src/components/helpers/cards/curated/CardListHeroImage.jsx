import PropTypes from 'prop-types'
import Picture from '../../Picture'
import HeroTitle from '../../CardListingHeroTitle'
import { createMarkup } from '../../../../helpers/markup'
import { dataLayerPush } from '../../../../helpers/thirdparty'

const CardListHeroImage = ({ cardItem, colsClass }) => {

  return (
    <div
      className={`relative w-full px-0 rounded-2xl overflow-hidden ${colsClass}`}
    >
      <div className={`relative flex flex-col`}>
        <Picture
          desktopImage={cardItem.desktopImage}
          mobileImage={cardItem.mobileImage}
          className={`mx-auto mb-5 aspect-video rounded-2xl`}
        />
        <div className="col-start-2 col-span-7 md:col-span-3 lg:col-span-5 px-4">
          <HeroTitle
            variant="hero"
            type="curated"
            cardItem={cardItem}
            canLink={true}
            onClick={event => {
              dataLayerPush({
                event: 'tile_link_click',
                click_text: cardItem.title,
                destination_path: link.href,
              }, event.target)
            }}
          />
          {cardItem.description && (
            <div className="mt-2" data-rte="true" dangerouslySetInnerHTML={createMarkup(cardItem.description)} />
          )}
        </div>
      </div>
    </div>
  )
}

CardListHeroImage.propTypes = {
  cardItem: PropTypes.object.isRequired,
  colsClass: PropTypes.string,
}

export default CardListHeroImage
