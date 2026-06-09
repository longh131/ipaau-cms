import PropTypes from 'prop-types'
import ButtonEl from './ctas/Button'
import { generateDataHash } from '../../helpers/contentHash'

const HeroCtaBlock = (props) => {
  return (
    <div
      className={`basis-${props.basis ? props.basis : 'auto'} flex flex-col sm:flex-row justify-${props.textAlignment.flex} flex-wrap gap-6 mt-12 mb-6`}
    >
      {props?.ctaLinkItem?.map((ctaLinkItem) => (
        <ButtonEl
          className="max-sm:w-full"
          key={generateDataHash(ctaLinkItem)}
          item={ctaLinkItem}
        />
      ))}
    </div>
  )
}

HeroCtaBlock.propTypes = {
  basis: PropTypes.string,
  textAlignment: PropTypes.object,
  ctaLinkItem: PropTypes.array,
}

export default HeroCtaBlock
