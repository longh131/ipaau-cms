import PropTypes from 'prop-types'
import CardListDynamic from './CardListDynamic'

function BlogSection(props) {
  // BlogSection reuses CardListDynamic component
  // Pass all props through to CardListDynamic
  return <CardListDynamic {...props} />
}

BlogSection.propTypes = {
  listingContents: PropTypes.array,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  initialNoOfCards: PropTypes.number,
  contentAlignment: PropTypes.string,
  cardVariant: PropTypes.string,
  backgroundColor: PropTypes.string,
  cardBackgroundColour: PropTypes.string,
  imageGradientColour: PropTypes.string,
  imageGradientType: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  tagline: PropTypes.string,
  viewMoreText: PropTypes.string,
}

export default BlogSection
