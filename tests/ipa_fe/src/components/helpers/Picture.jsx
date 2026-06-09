import PropTypes from 'prop-types'

const Picture = (props) => {
  if (!props.desktopImage && !props.mobileImage) return
  // if either image is provided we still proceed, using what we have.

  // use the mobile as the default image if it's present, otherwise use desktopImage as the default.
  // we know that if mobileImage does not exist desktopImage must, because of the previous check
  const baseImage = props.mobileImage ? props.mobileImage : props.desktopImage
  return (
    <picture>
      {props.desktopImage && (
        <source
          srcSet={props.desktopImage.src}
          media="screen and (min-width: 768px)"
          alt={props.desktopImage.altText ? props.desktopImage.altText : ''}
        />
      )}
      {props.mobileImage && (
        <source
          srcSet={props.mobileImage.src}
          media="screen and (max-width: 767px)"
          alt={props.mobileImage.altText ? props.mobileImage.altText : ''}
        />
      )}
      <img
        loading="lazy"
        className={props.className}
        src={baseImage.src}
        alt={baseImage.altText ? baseImage.altText : ''}
      />
    </picture>
  )
}

Picture.propTypes = {
  desktopImage: PropTypes.object,
  mobileImage: PropTypes.object,
  className: PropTypes.string,
}

export default Picture