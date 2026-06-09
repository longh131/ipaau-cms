import { useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import Picture from '../helpers/Picture'
import { transformPaddingToTailwind } from '../../helpers/style'
import { PlayIcon } from '@heroicons/react/24/solid'
import { PlayIcon as PlayIconOutline } from '@heroicons/react/24/outline'

function VideoBlock(props) {
  const [isPlaying, setIsPlaying] = useState(false)
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const handlePlayClick = () => {
    setIsPlaying(true)
  }

  const getVideoEmbedUrl = () => {
    if (props.videoUrl) {
      // Handle YouTube URLs
      if (props.videoUrl.includes('youtube.com') || props.videoUrl.includes('youtu.be')) {
        const videoId = props.videoUrl.includes('youtu.be')
          ? props.videoUrl.split('youtu.be/')[1]?.split('?')[0]
          : new URLSearchParams(new URL(props.videoUrl).search).get('v')
        return `https://www.youtube.com/embed/${videoId}?autoplay=1`
      }
      // Handle Vimeo URLs
      if (props.videoUrl.includes('vimeo.com')) {
        const videoId = props.videoUrl.split('vimeo.com/')[1]?.split('?')[0]
        return `https://player.vimeo.com/video/${videoId}?autoplay=1`
      }
      return props.videoUrl
    }
    return null
  }

  const getVideoFileUrl = () => {
    if (props.videoFile) {
      return typeof props.videoFile === 'string' ? props.videoFile : props.videoFile.src
    }
    return null
  }

  const getVideoEmbedBlock = (videoUrl) => {
    return(
      <iframe
        width="100%"
        height="100%"
        src={videoUrl}
        title="Video"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowFullScreen
      />
    )
  }

  const getVideoFileBlock = (videoFile) => {
    return(
      <video
        width="100%"
        height="100%"
        controls
        autoPlay
      >
        <source src={videoFile} />
        {videoFile.caption && <track kind="captions" {...videoFile.caption} />}
        Your browser does not support the video tag.
      </video>    )
  }

  const getVideoBlock = () => {
    const videoUrl = getVideoEmbedUrl()
    if (videoUrl) {
      return getVideoEmbedBlock(videoUrl)
    }
    const videoFile = getVideoFileUrl()
    if (videoFile) {
      return getVideoFileBlock(videoFile)
    }
    return null
  }

  return (
    <Section {...props} outerClass={componentPadding}>
      <div className="container mx-auto">
        <div className="relative rounded-2xl overflow-hidden aspect-video">
          {!isPlaying ? (
            <>
              {props.thumbnailImage && (
                <Picture
                  desktopImage={props.thumbnailImage}
                  mobileImage={props.thumbnailImage}
                  className="w-full h-full object-cover"
                />
              )}
              <button
                onClick={handlePlayClick}
                className="left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 absolute w-24 h-24  bg-white rounded-full flex items-center justify-center bg-black/30 hover:bg-black/40 group/videoButton elevation-3 border-4 border-white transition-all duration-300 hover:border-link-hover "
                aria-label="Play video"
              >
                  <PlayIcon className="absolute top-1/2 left-1/2 text-link group-hover/videoButton:text-link-hover -translate-x-1/2 -translate-y-1/2 text-navy opacity-100 transition-all duration-300 group-hover/videoButton:opacity-0 scale-100  w-16 h-16 ml-1" />
                  <PlayIconOutline className="absolute top-1/2 left-1/2 text-link group-hover/videoButton:text-link-hover -translate-x-1/2 -translate-y-1/2 w-16 h-16 text-navy opacity-0 transition-all duration-300 group-hover/videoButton:opacity-100 scale-100 ml-1" />
              </button>
            </>
          ) : (
            <div className="w-full aspect-video">
              {getVideoBlock()}
            </div>
          )}
        </div>
      </div>
    </Section>
  )
}

VideoBlock.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  videoUrl: PropTypes.string,
  videoFile: PropTypes.oneOfType([PropTypes.string, PropTypes.object]),
  thumbnailImage: PropTypes.object,
}

export default VideoBlock
