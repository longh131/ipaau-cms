import PropTypes from 'prop-types'

function MapPinIcon({className}) {
  return <svg {...{className}} fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
    <g strokeLinecap="round" strokeLinejoin="round" strokeWidth="2">
      <path d="m12 22c4-4 8-7.5817 8-12 0-4.41828-3.5817-8-8-8-4.41828 0-8 3.58172-8 8 0 4.4183 4 8 8 12z" stroke="#0d2c6c"/>
      <path d="m12 13c1.6569 0 3-1.3431 3-3 0-1.65685-1.3431-3-3-3s-3 1.34315-3 3c0 1.6569 1.3431 3 3 3z" stroke="#000"/>
      <path d="m12 13c1.6569 0 3-1.3431 3-3 0-1.65685-1.3431-3-3-3s-3 1.34315-3 3c0 1.6569 1.3431 3 3 3z" stroke="#992785"/>
    </g>
  </svg>
}

MapPinIcon.propTypes = {
  className: PropTypes.string,
}

export default MapPinIcon
