import PropTypes from 'prop-types'

function Logo(props) {
  return (
    <div
      data-type="siteLogo"
      className="basis-1/4 md:basis-1/5 flex max-h-full items-center py-3  pr-3 h-[65px] md:h-[85px]"
    >
      <a href="/" title="Go to Homepage" className="h-full flex items-center justify-start">
        {(props.mobLogo || props.deskLogo) && (
          <picture className="max-h-full h-full w-auto">
            {props.mobLogo && <source media="(max-width: 768px)" srcSet={props.mobLogo?.src} />}
            {props.deskLogo && <source media="(min-width: 769px)" srcSet={props.deskLogo?.src} />}
            {props.deskLogo && (
              <img
                className="w-auto max-w-none h-full"
                src={props.deskLogo.src}
                alt={props.deskLogo?.altText || props.mobLogo?.altText || 'Site Logo'}
              />
            )}
            {!props.deskLogo && props.mobLogo && (
              <span>{props.deskLogo?.altText || props.mobLogo?.altText || 'Site Logo'}</span>
            )}
          </picture>
        )}
        {!props.mobLogo && !props.deskLogo && <span>Home</span>}
      </a>
    </div>
  )
}

Logo.propTypes = {
  mobLogo: PropTypes.object,
  deskLogo: PropTypes.object,
}

export default Logo
