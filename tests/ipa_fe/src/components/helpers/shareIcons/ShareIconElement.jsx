import PropTypes from 'prop-types'

const ShareIconElement = ({ key, platform, url, shareTextTemplate, getSocialIcon, copied, handleShare }) => {
  return (
    <button
      key={key}
      onClick={() => handleShare(platform, url, shareTextTemplate)}
      className={`flex max-lg:w-12 max-lg:h-12 rounded-full items-center justify-center text-white transition-colors duration-200`}
      aria-label={platform.shareTextTemplate ? platform.shareTextTemplate : `Share on ${platform.platform}`}
      title={platform.shareTextTemplate ? platform.shareTextTemplate : `Share on ${platform.platform}`}
    >
      {platform.platform === 'copy' && copied ? (
        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
        </svg>
      ) : (
        getSocialIcon(platform.platform, platform.icon)
      )}
    </button>
  )
}

ShareIconElement.propTypes = {
  key: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  platform: PropTypes.object,
  url: PropTypes.string,
  shareTextTemplate: PropTypes.string,
  getSocialIcon: PropTypes.func,
  copied: PropTypes.bool,
  handleShare: PropTypes.func,
}

export default ShareIconElement
