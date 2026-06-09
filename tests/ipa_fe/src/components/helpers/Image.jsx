import PropTypes from 'prop-types'

const Image = (props) => {
  if (!props?.src) return

  return <img loading="lazy" src={props.src} alt={props.altText ? props.altText : ''} className={props.className} />
}

Image.propTypes = {
  src: PropTypes.string,
  altText: PropTypes.string,
  className: PropTypes.string,
}

export default Image
