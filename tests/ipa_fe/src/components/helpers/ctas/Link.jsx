import PropTypes from 'prop-types';

const LinkEl = ({link, theme, value, classes}) => {
  return (
    <a
      href={link}
      data-theme={theme}
      className={classes}
    >
      {value}
    </a>
  )
}

LinkEl.propTypes = {
  link: PropTypes.string,
  theme: PropTypes.string,
  value: PropTypes.string,
  classes: PropTypes.string
}


export default LinkEl
