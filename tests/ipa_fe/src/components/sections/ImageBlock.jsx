import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import ButtonEl from '../helpers/ctas/Button'
import Picture from '../helpers/Picture'
import { transformPaddingToTailwind } from '../../helpers/style'
import { generateDataHash } from '../../helpers/contentHash'

const ImageBlock = (props) => {
  const {
    desktopImage,
    mobileImage,
    ctaLinkItem,
    ...sectionProps
  } = props
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)

  const alignment = props.contentAlignment?.toLowerCase() || 'center'
  return (
    <Section {...sectionProps} outerClass={`${componentPadding} text-${alignment}`}>
      <div>
        {/* CTA Buttons */}
        {ctaLinkItem && ctaLinkItem.length > 0 && (
          <div className={`flex flex-col sm:flex-row gap-4 justify-${alignment}`}>
            {ctaLinkItem.map((cta) => <ButtonEl key={generateDataHash(cta)} item={cta} />)}
          </div>
        )}

        {/* Image Section */}
        <Picture
          desktopImage={desktopImage}
          mobileImage={mobileImage}
          className="mt-10 sm:mt-20 rounded-3xl inline-block"
        />
      </div>
    </Section>
  )
}

ImageBlock.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string,
  subtitle: PropTypes.string,
  desktopImage: PropTypes.shape({
    src: PropTypes.string,
    altText: PropTypes.string,
    target: PropTypes.string,
  }),
  mobileImage: PropTypes.shape({
    src: PropTypes.string,
    altText: PropTypes.string,
    target: PropTypes.string,
  }),
  ctaLinkItem: PropTypes.arrayOf(
    PropTypes.shape({
      name: PropTypes.string,
      url: PropTypes.string,
      target: PropTypes.string,
    })
  ),
}

export default ImageBlock
