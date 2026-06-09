import PropTypes from 'prop-types'
import themeConfig from '../../../theme.config'

const InlinePercent = (props) => {
  return (
    <>
      <strong
        className={`${themeConfig.settings.rates?.useAlternateSize ? themeConfig.settings.rates?.useAlternateSize : 'text-3xl'} ${themeConfig.settings.rates?.useHeroFont ? 'font-highlight' : ''}`}
      >
        {props.rate?.replace('%', '')}
        <sup>%</sup>
      </strong>
      <span className="text-sm font-semibold ml-2">p.a.</span>
    </>
  )
}

const StackedPercent = (props) => {
  return (
    <div
      className={`flex flex-nowrap items-end justify-center gap-1 ${themeConfig.settings.rates?.useHeroFont ? 'font-highlight' : ''}`}
    >
      <strong
        className={`${themeConfig.settings.rates.useAlternateSize ? themeConfig.settings.rates?.useAlternateSize : 'text-3xl leading-7'}`}
      >
        {props.rate?.replace('%', '')}
      </strong>
      {props.rate !== 'n/a' && (
        <div className="flex flex-col text-sm leading-4 font-semibold items-start justify-end">
          <span>%</span>
          <span>p.a.</span>
        </div>
      )}
      {themeConfig.settings.ratesCard.showDisclaimersFromCMS && props.disclaimer && (
        <div className="self-start">
          {
            themeConfig.settings.ratesCard.disclaimerIcons[
              ['interest', 'comparison'].indexOf(props.rateType.toLowerCase())
            ]
          }
        </div>
      )}
    </div>
  )
}

const PercentType = (props) => {
  return <span className="block text-sm text-nowrap text-center">{props?.rateType ?? 'Rate'}</span>
}

const PercentValue = (props) => {
  return themeConfig.settings.rates?.stackPercent ? (
    <>
      <StackedPercent {...props} />
      {props.rateType && <PercentType {...props} />}
    </>
  ) : (
    <>
      <InlinePercent {...props} />
      {props.rateType && <PercentType {...props} />}
    </>
  )
}

PercentValue.propTypes = {
  rate: PropTypes.string,
  rateType: PropTypes.string,
  disclaimer: PropTypes.string,
}

InlinePercent.propTypes = {
  rate: PropTypes.string,
}

StackedPercent.propTypes = {
  rate: PropTypes.string,
  rateType: PropTypes.string,
  disclaimer: PropTypes.string,
}

PercentType.propTypes = {
  rateType: PropTypes.string,
}

export default PercentValue
