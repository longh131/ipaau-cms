import PropTypes from 'prop-types'
import { createMarkup } from '../../../helpers/markup'
import ShareIconElement from './ShareIconElement'

const ShareIconsInline = ({
  label,
  socialPlatforms,
  layout,
  contentAlignment,
  handleShare,
  getSocialIcon,
  copied,
} = {}) => {
  return (
    <div className={`container mx-auto md:px-4`}>
      <div
        className={`flex max-md:flex-col max-md:items-start items-center gap-6 ${contentAlignment?.toLowerCase() === 'left' ? 'max-md:items-start md:justify-start' : 'max-md:items-center md:justify-center'}`}
      >
        {/* Label */}
        {label && (
          <div className="flex-shrink-0">
            <h3 className="mb-0 text-lg" dangerouslySetInnerHTML={createMarkup(label)} />
          </div>
        )}

        {/* Social Icons */}
        {socialPlatforms && socialPlatforms.length > 0 && (
          <div
            className={`flex flex-wrap items-center gap-6 ${layout === 'vertical' ? 'flex-col' : 'flex-row'} ${contentAlignment?.toLowerCase() === 'left' ? 'justify-start' : 'justify-center'}`}
          >
            {socialPlatforms.map((platform) => (
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
      </div>
    </div>
  )
}

ShareIconsInline.propTypes = {
  label: PropTypes.string,
  socialPlatforms: PropTypes.array,
  layout: PropTypes.string,
  contentAlignment: PropTypes.string,
  handleShare: PropTypes.func,
  getSocialIcon: PropTypes.func,
  copied: PropTypes.bool,
}

export default ShareIconsInline
