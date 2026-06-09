import { useState, useEffect, Suspense } from 'react'
import PropTypes from 'prop-types'
import ButtonEl from '../helpers/ctas/Button'
import { XMarkIcon } from '@heroicons/react/24/solid'
import Modal from 'react-modal'
import Loader from '../helpers/Loader'
import { useScrollLock } from '../../helpers/scrollLock'
Modal.setAppElement('#root')

const customModalStyle = {
  content: {
    top: '50%',
    left: '50%',
    width: '75vw',
    right: 'auto',
    bottom: 'auto',
    marginRight: '-50%',
    transform: 'translate(-50%, -50%)',
    aspectRatio: '16/10',
    maxHeight: '95vh',
    padding: '0',
    overflow: 'hidden',
  },
}

const vimeoEmbed = (videoId) => {
  if (!videoId) return null

  // Clean the video ID (remove any trailing slashes or query params)
  const cleanVideoId = videoId.toString().split('/')[0].split('?')[0]

  return (
    <iframe
      key={cleanVideoId}
      src={`https://player.vimeo.com/video/${cleanVideoId}?badge=0&autopause=0&player_id=0&app_id=58479`}
      allow="autoplay; fullscreen; picture-in-picture"
      referrerPolicy="strict-origin-when-cross-origin"
      title="Vimeo video"
      allowFullScreen
      style={{
        border: 'none',
        display: 'block',
        width: '100%',
        height: '100%',
        position: 'absolute',
        top: 0,
        left: 0
      }}
    ></iframe>
  )
}

const getYouTubeVideoId = (url) => {
  if (!url) return null

  if (url.includes('youtu.be/')) {
    return url.split('youtu.be/')[1]?.split('?')[0]
  } else if (url.includes('youtube.com')) {
    try {
      const urlObj = new URL(url)
      return urlObj.searchParams.get('v') || urlObj.pathname.split('/').pop()
    } catch (err) {
      console.debug('Failed to parse YouTube URL, falling back to regex:', err)
      // Fallback regex extraction
      const match = url.match(/(?:youtube\.com\/watch\?v=|youtube\.com\/embed\/|youtube\.com\/v\/)([^&\n?#]+)/)
      return match ? match[1] : null
    }
  }
  return null
}

const getVideoTitle = async (url) => {
  try {
    const apiUrl = url.includes('youtube.com') || url.includes('youtu.be')
      ? `https://www.youtube.com/oembed?url=${encodeURIComponent(url)}&format=json`
      : `https://vimeo.com/api/oembed.json?url=${encodeURIComponent(url)}`
    const response = await fetch(apiUrl)
    if (!response.ok) return null
    const data = await response.json()
    return data.title || null
  } catch (error) {
    console.error('Error fetching video title:', error)
    return null
  }
}

const youtubeEmbed = (videoId) => {
  if (!videoId) return null

  return (
    <iframe
      key={videoId}
      src={`https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`}
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      referrerPolicy="strict-origin-when-cross-origin"
      title="YouTube video"
      allowFullScreen
      style={{
        border: 'none',
        display: 'block',
        width: '100%',
        height: '100%',
        position: 'absolute',
        top: 0,
        left: 0
      }}
    ></iframe>
  )
}

function VideoModal(props) {
  const [videoType, setVideoType] = useState(null)
  const [youtubeVideoId, setYoutubeVideoId] = useState(null)
  const [vimeoVideoId, setVimeoVideoId] = useState(null)
  const [videoTitle, setVideoTitle] = useState(null)
  const { lockScroll, unlockScroll } = useScrollLock()

  // Load Vimeo player script once
  useEffect(() => {
    if (videoType === 'vimeo' && !document.querySelector('script[src="https://player.vimeo.com/api/player.js"]')) {
      const script = document.createElement('script')
      script.src = 'https://player.vimeo.com/api/player.js'
      script.async = true
      document.body.appendChild(script)
    }
  }, [videoType])

  async function afterOpenModal() {
    if (!props.url) return

    // Determine video type directly from URL instead of relying on state
    const currentVideoType = (props.url.includes('youtube.com') || props.url.includes('youtu.be')) ? 'youtube' : 'vimeo'

    // Ensure videoType state is set
    if (!videoType) {
      setVideoType(currentVideoType)
    }

    if (currentVideoType === 'youtube') {
      const videoId = getYouTubeVideoId(props.url)
      if (videoId) {
        setYoutubeVideoId(videoId)
      } else {
        console.error('Could not extract YouTube video ID from URL:', props.url)
      }
    } else if (currentVideoType === 'vimeo') {
      // Extract Vimeo video ID from various URL formats:
      // https://vimeo.com/123456789
      // https://vimeo.com/123456789?param=value
      // https://player.vimeo.com/video/123456789
      let videoId = null
      if (props.url.includes('player.vimeo.com')) {
        videoId = props.url.match(/\/video\/(\d+)/)?.[1]
      } else if (props.url.includes('vimeo.com/')) {
        videoId = props.url.split('vimeo.com/')[1]?.split('?')[0]?.split('/')[0]
      }

      if (videoId) {
        setVimeoVideoId(videoId)
      } else {
        console.error('Could not extract Vimeo video ID from URL:', props.url)
      }
    }

    // Fetch video title
    const title = await getVideoTitle(props.url)
    if (title) {
      setVideoTitle(title)
    }
    lockScroll()
  }

  useEffect(() => {
    if (props.url) {
      const videoTypeVal = (props.url.includes('youtube.com') || props.url.includes('youtu.be')) ? 'youtube' : 'vimeo'
      setVideoType(videoTypeVal)
    }
  }, [props.url])

  // Load video when modal opens and videoType is set (backup to afterOpenModal)
  useEffect(() => {
    if (props.open && videoType && props.url && !videoTitle) {
      if (videoType === 'youtube' && !youtubeVideoId) {
        const videoId = getYouTubeVideoId(props.url)
        if (videoId) {
          setYoutubeVideoId(videoId)
        }
      } else if (videoType === 'vimeo' && !vimeoVideoId) {
        // Extract Vimeo video ID
        let videoId = null
        if (props.url.includes('player.vimeo.com')) {
          videoId = props.url.match(/\/video\/(\d+)/)?.[1]
        } else if (props.url.includes('vimeo.com/')) {
          videoId = props.url.split('vimeo.com/')[1]?.split('?')[0]?.split('/')[0]
        }
        if (videoId) {
          setVimeoVideoId(videoId)
        }
      }

      // Fetch video title
      getVideoTitle(props.url).then(title => {
        if (title) {
          setVideoTitle(title)
        }
      })
    }
  }, [props.open, videoType, props.url, youtubeVideoId, vimeoVideoId, videoTitle])

  useEffect(() => {
    if (props.open) {
      lockScroll()
    } else {
      unlockScroll()
      // Reset video state when modal closes
      setYoutubeVideoId(null)
      setVimeoVideoId(null)
      setVideoTitle(null)
    }
  }, [props.open])

  useEffect(() => {
    return () => {
      unlockScroll()
    }
  }, [])

  return (
    <Modal
      isOpen={props.open}
      onAfterOpen={afterOpenModal}
      onRequestClose={props.onClose}
      style={customModalStyle}
      contentLabel="Example Modal"
      aria={{
        labelledby: `modal-heading-${props.i}`,
      }}
    >
      <ButtonEl className="right-5 absolute !p-0 z-10" onClick={props.onClose} theme="none">
        <XMarkIcon role="none" className="w-6 h-6 flex-shrink text-current" />
        <span className="sr-only">Close this alert</span>
      </ButtonEl>
      <div data-type="video-modal-content" className="m-0 relative w-full h-full" >
        <div id={`modal-heading-${props.i}`} aria-hidden="true" className="absolute -top-[1000px] opacity-0">
          {videoTitle || (videoType === 'youtube' ? 'YouTube video' : 'Vimeo video')}
        </div>
        <Suspense fallback={<Loader />}>
          {videoType?.toLowerCase() === 'youtube' && youtubeVideoId ? (
            <div
              data-type="video-iframe-wrapper"
              className="w-full h-full"
              style={{ position: 'absolute', top: 0, left: 0, width: '100%', height: '100%' }}
            >
              {youtubeEmbed(youtubeVideoId)}
            </div>
          ) : null}
          {videoType?.toLowerCase() === 'vimeo' && vimeoVideoId ? (
            <div
              data-type="video-iframe-wrapper"
              className="w-full h-full"
              style={{ position: 'absolute', top: 0, left: 0, width: '100%', height: '100%' }}
            >
              {vimeoEmbed(vimeoVideoId)}
            </div>
          ) : null}
        </Suspense>
      </div>
    </Modal>
  )
}

VideoModal.propTypes = {
  url: PropTypes.string.isRequired,
  open: PropTypes.bool.isRequired,
  onClose: PropTypes.func.isRequired,
  i: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
}

export default VideoModal
