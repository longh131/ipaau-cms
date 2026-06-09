import PropTypes from 'prop-types'

const Loader = ({ className }) => {
    return <span data-type="loader" className={`after:border-secondary before:border-warm-plum ${className}`}></span>
}

Loader.propTypes = {
  className: PropTypes.string,
}

export default Loader
