import { useState } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { getSocialIcon, handleShare } from '../../helpers/sharing'
import Picture from '../helpers/Picture'
import TitleBlock from '../helpers/TitleBlock'
import { transformPaddingToTailwind } from '../../helpers/style'
import ShareIconElement from '../helpers/shareIcons/ShareIconElement'
import { generateDataHash } from '../../helpers/contentHash'

// Helper function to format date
const formatDate = (date) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString('en-AU', {
    year: 'numeric',
    month: 'numeric',
    day: 'numeric'
  })
}

function ArticleHeader(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const alignment = props.contentAlignment?.toLowerCase() || 'center'
  const [copied, _] = useState(false)
  return (
    <Section
      type="articleHeader"
      outerClass={componentPadding}
      sectionTitle={false}
      {...props}
    >
      <div className="flex flex-col lg:flex-row items-center gap-8">
        <div className={`flex flex-col gap-6 lg:py-16 text-${alignment}`}>
          {/* Date and Tags */}
          {(props.publicationDate || props.tags?.length) ? <div className="flex items-center gap-4">
            {props.publicationDate && formatDate(props.publicationDate)}
            {props.tags?.length ? (
              <div className="flex gap-2">
                {props.tags.map((tag) => (
                  <span
                    key={generateDataHash(tag)}
                    className="px-3 h-6 flex items-center text-xs bg-white rounded-full border border-gray-300"
                  >
                    {tag.toUpperCase()}
                  </span>
                ))}
              </div>
            ) : undefined}
          </div> : undefined}

          {/* Article Title */}
          {props.title && (
            <TitleBlock title={props.title} level="1" contentAlignment={alignment} />
          )}

          {/* Share Icons */}
          {props.shareIconsToggle && (
            <div className={`flex justify-${alignment} items-start md:items-center flex-wrap gap-6`}>
              <span className="text-lg">
                {props.socialPlatforms?.label || 'Share:'}
              </span>
              <div className="flex flex-wrap items-start gap-6">

                {Array.isArray(props.socialPlatforms?.socialPlatforms) && props.socialPlatforms.socialPlatforms.map((platform) => {
                  return (
                    <ShareIconElement
                      key={generateDataHash(platform)}
                      platform={platform}
                      getSocialIcon={getSocialIcon}
                      copied={copied}
                      handleShare={(platform, url, shareTextTemplate) => {
                        handleShare(platform, url, shareTextTemplate)
                      }}
                    />
                  )
                })}
                </div>
              </div>
            )}
          </div>
          <Picture
            desktopImage={props.desktopImage}
            mobileImage={props.mobileImage}
            alt={props.title || 'Article image'}
            className="w-full h-auto lg:rounded-3xl lg:rounded-r-none"
          />
      </div>
    </Section>
  )
}

ArticleHeader.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  contentAlignment: PropTypes.string,
  publicationDate: PropTypes.string,
  tags: PropTypes.arrayOf(PropTypes.string),
  title: PropTypes.string,
  shareIconsToggle: PropTypes.bool,
  socialPlatforms: PropTypes.shape({
    label: PropTypes.string,
    socialPlatforms: PropTypes.array,
  }),
  desktopImage: PropTypes.object,
  mobileImage: PropTypes.object,
}

export default ArticleHeader
