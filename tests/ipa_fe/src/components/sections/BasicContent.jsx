import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import { transformPaddingToTailwind } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

function BasicContent(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  return (
    <Section type="basicContent" outerClass={componentPadding} innerClass="px-7" {...props}>
      {props.ctaLink?.length != 0 && (
        <div className={`flex justify-${props.ctaLinkAlignment !== 'Center' ? 'start' : 'center'} space-x-6 mt-10`}>
          {props.ctaLink.map((ctaLinkItem) => <ButtonEl key={generateDataHash(ctaLinkItem)} item={ctaLinkItem} />)}
        </div>
      )}
    </Section>
  )
}

BasicContent.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  ctaLink: PropTypes.array,
  ctaLinkAlignment: PropTypes.string,
}

export default BasicContent
