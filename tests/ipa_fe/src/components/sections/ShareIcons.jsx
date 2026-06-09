import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { getSocialIcon, handleShare } from '../../helpers/sharing'
import { transformPaddingToTailwind } from '../../helpers/style'
import ShareIconsInline from '../helpers/shareIcons/ShareIconsInline'
import ShareIconsSidebar from '../helpers/shareIcons/ShareIconsSidebar'

function ShareIcons(props) {
  const [copied, _] = useState(false)
  const [componentPadding, setComponentPadding] = useState('')
  const isFixed = props.layout?.toLowerCase() === 'sidebar'

  useEffect(() => {
    setComponentPadding(!isFixed ? transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs) : '')
  }, [isFixed, props.componentPadding, props.breadcrumbs])

  const ComponentToRender = isFixed ? ShareIconsSidebar : ShareIconsInline

  if (isFixed) {
    return (
      <ShareIconsSidebar
        backgroundColor={props.backgroundColor}
        label={props.label}
        socialPlatforms={props.socialPlatforms}
        layout={props.layout}
        contentAlignment={props.contentAlignment}
        handleShare={(platform, url, shareTextTemplate) => {
          handleShare(platform, url, shareTextTemplate)
        }}
        getSocialIcon={getSocialIcon}
        copied={copied}
      />
    )
  }

  return (
    <Section
      type="sharingComponent"
      outerClass={`${componentPadding} ${isFixed ? 'z-50' : ''}`}
      sectionTitle={false}
      {...props}
    >
      <ComponentToRender
        backgroundColor={props.backgroundColor}
        label={props.label}
        socialPlatforms={props.socialPlatforms}
        layout={props.layout}
        contentAlignment={props.contentAlignment}
        handleShare={(platform, url, shareTextTemplate) => {
          handleShare(platform, url, shareTextTemplate)
        }}
        getSocialIcon={getSocialIcon}
        copied={copied}
      />

    </Section>
  )
}

ShareIcons.propTypes = {
  layout: PropTypes.string,
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  label: PropTypes.string,
  socialPlatforms: PropTypes.object,
  contentAlignment: PropTypes.string,
}

export default ShareIcons
