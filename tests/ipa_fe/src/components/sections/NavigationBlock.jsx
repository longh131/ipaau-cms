import PropTypes from 'prop-types'
import CardListCurated from './CardListCurated'

function NavigationBlock(props) {
  // Navigation Block passes through to CardListCurated
  return <CardListCurated {...props} />
}

NavigationBlock.propTypes = {
  cardVariant: PropTypes.string,
  cardItems: PropTypes.array,
  initialNoOfCards: PropTypes.number,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  cardLinkAlignment: PropTypes.string,
  sectionTitle: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  ctaLinkItem: PropTypes.array,
  viewMoreText: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
}

export default NavigationBlock
