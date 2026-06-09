  import { useState, useEffect } from 'react'
  import PropTypes from 'prop-types'
  import ShareIconElement from './ShareIconElement'
  import themeConfig from '../../../../theme.config'
  import { ChevronDownIcon } from '@heroicons/react/24/outline'

  const ShareIconsSidebar = ({label, socialPlatforms, backgroundColor, getSocialIcon, copied, handleShare} = {}) => {
    const [backgroundColour, setBackgroundColour] = useState(backgroundColor ?? themeConfig.backgroundColors['1984ff'])

  useEffect(() => {
    const bgColor = backgroundColor?.replace('#', '')
    setBackgroundColour(() => (bgColor ? `#${bgColor}` : themeConfig.backgroundColors['1984ff']))
  }, [])

  return (
    <div
    style={{
      '--bg-color': backgroundColour ?? themeConfig.backgroundColors['1984ff'],
    }}
    className={`max-md:hidden sticky md:top-[10vh] left-0 z-[200] rounded-r-3xl overflow-hidden w-full md:w-12 bg-[color:var(--bg-color)]`} aria-label={`${label ?? 'Share this page'}`}>
      <div className="md:hidden min-h-12 min-w-12 flex items-center justify-center">
        <button aria-controls="social-icons-fixed" aria-expanded="false" onClick={() => {
          const socialIconsFixed = document.getElementById('social-icons-fixed')
          const ariaHidden = socialIconsFixed.getAttribute('aria-hidden')
          if (ariaHidden) {
            socialIconsFixed.removeAttribute('aria-hidden')
          } else {
            socialIconsFixed.setAttribute('aria-hidden', 'true')
          }
        }} className="text-white uppercase text-xs">Share:</button>
      </div>
      {/* Social Icons */}
      {socialPlatforms && socialPlatforms.length > 0 && (
        <div id="social-icons-fixed" aria-hidden="true" className={`not-aria-hidden:max-md:pt-6 lg:px-3 flex flex-col gap-2 lg:gap-6 :not([aria-hidden="true"]):py-10 rounded-r-3xl overflow-hidden h-max md:py-8 aria-hidden:max-md:max-h-0 max-md:max-h-80 max-md:overflow-y-auto transition-all duration-300 peer/social-icons-fixed [&_button]:max-lg:w-12 [&_button]:max-lg:min-h-12`}>
          {socialPlatforms
            .map((platform) => (
              <ShareIconElement
                key={`share-${platform.platform}`}
                platform={platform}
                getSocialIcon={getSocialIcon}
                copied={copied}
                handleShare={handleShare}
                url={platform.url}
                shareTextTemplate={platform.shareTextTemplate}
              />
            ))}
        </div>
      )}
      <div className="md:hidden h-12 w-12 flex items-center justify-center peer-aria-hidden/social-icons-fixed:rotate-0 rotate-180 transition-transform duration-300 text-white">
        <button aria-controls="social-icons-fixed" aria-expanded="false" className="" onClick={() => {
          const socialIconsFixed = document.getElementById('social-icons-fixed')
          const ariaHidden = socialIconsFixed.getAttribute('aria-hidden')
          if (ariaHidden) {
            socialIconsFixed.removeAttribute('aria-hidden')
          } else {
            socialIconsFixed.setAttribute('aria-hidden', 'true')
          }
        }}>
          <ChevronDownIcon className="w-6 h-6 transition-transform duration-300" />
        </button>
      </div>
    </div>
  )
}

ShareIconsSidebar.propTypes = {
  label: PropTypes.string,
  socialPlatforms: PropTypes.array,
  backgroundColor: PropTypes.string,
  getSocialIcon: PropTypes.func,
  copied: PropTypes.bool,
  handleShare: PropTypes.func,
}

export default ShareIconsSidebar
