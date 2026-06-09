import { useState, useRef, useEffect } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import { transformPaddingToTailwind } from '../../helpers/style'
import Picture from '../helpers/Picture'
import { Swiper, SwiperSlide } from 'swiper/react'
import { Pagination, Autoplay, A11y } from 'swiper/modules'
import { lightOrDark } from '../../helpers/style'
import TitleBlock from '../helpers/TitleBlock'
import { generateDataHash } from '../../helpers/contentHash'
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/autoplay'
import 'swiper/css/a11y'

function ImageBlockWithCarousel(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  const swiperRef = useRef(null)
  const [isPlaying, setIsPlaying] = useState(true)
  const [lightOrDarkValue, setLightOrDarkValue] = useState('light')
  useEffect(() => {
    setLightOrDarkValue(lightOrDark(props.backgroundColor))
  }, [props.backgroundColor])
  if (!props.carouselSlides || props.carouselSlides.length === 0) {
    return null
  }

  const handleToggleAutoplay = () => {
    if (!swiperRef.current) return
    if (isPlaying) {
      swiperRef.current.autoplay.stop()
      setIsPlaying(false)
    } else {
      swiperRef.current.autoplay.start()
      setIsPlaying(true)
    }
  }

  const carouselContent = (slide, index, contentAlignment) => (
    <SwiperSlide key={generateDataHash(slide)}>
      {(slide.title || slide.description || slide.tagline) && (
        <div className="mb-6">
          <TitleBlock
            title={slide.title}
            description={slide.description}
            tagline={slide.tagline}
            lightOrDark={lightOrDarkValue}
            contentAlignment={contentAlignment}
          />
        </div>
      )}

      {/* Main Image Display */}
      <div className="relative overflow-hidden rounded-xl shadow-lg mb-0 w-full aspect-video">
        <Picture
          desktopImage={slide.desktopImage}
          mobileImage={slide.mobileImage}
          className="w-full h-full object-cover"
        />
      </div>
      {/* Image Caption */}
      {slide.caption && (
        <div className="w-full bg-white/50 rounded-xl text-primary mt-0 mb-2 py-6 text-md font-din">
          <p className="text-sm md:text-base">{slide.caption}</p>
        </div>
      )}
    </SwiperSlide>
  )

  return (
    <Section type="imageBlockWithCarousel" outerClass={componentPadding} {...props}>
      <div className="container imageblock-carousel mx-auto px-4">
        {/* Slide Content */}
        <Swiper
          modules={[Pagination, Autoplay, A11y]}
          onSwiper={(swiper) => {
            swiperRef.current = swiper
          }}
          onAutoplayStop={() => setIsPlaying(false)}
          onAutoplayStart={() => setIsPlaying(true)}
          slidesPerView={1}
          spaceBetween={10}
          centeredSlides={true}
          speed={500}
          a11y={{
            enabled: true,
            prevSlideMessage: 'Previous slide',
            nextSlideMessage: 'Next slide',
          }}
          rewind={true}
          pagination={{ clickable: true }}
          autoplay={
            !props.autoPlay
              ? false
              : {
                  delay: 5000,
                  pauseOnMouseEnter: props.pauseOnHover,
                  disableOnInteraction: false,
                  waitForTransition: true,
                }
          }
        >
          {props.carouselSlides.map((slide, index) => carouselContent(slide, index, props.contentAlignment))}
        </Swiper>
        {/* Play/Pause button */}
        {props.autoPlay && props.carouselSlides.length > 1 && (
          <section className="flex justify-center mt-6" aria-label="Autoplay controls">
            <button
              type="button"
              onClick={handleToggleAutoplay}
              className="image-block-carousel__navigation-autoplay-button"
              aria-pressed={isPlaying}
              aria-label={isPlaying ? 'Pause autoplay' : 'Resume autoplay'}
              title={isPlaying ? 'Pause autoplay' : 'Resume autoplay'}
            >
              <span aria-hidden="true">{isPlaying ? 'Pause Autoplay' : 'Resume Autoplay'}</span>
              <span className="sr-only">{isPlaying ? 'Pause autoplay' : 'Resume autoplay'}</span>
            </button>

            {/* Live region announces status changes to assistive tech */}
            <span
              className="sr-only"
              aria-live="polite"
              aria-atomic="true"
              style={{ position: 'absolute', left: '-9999px' }}
            >
              {isPlaying ? 'Autoplay is running' : 'Autoplay is paused'}
            </span>
          </section>
        )}
      </div>
    </Section>
  )
}

ImageBlockWithCarousel.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  backgroundColor: PropTypes.string,
  carouselSlides: PropTypes.array,
  contentAlignment: PropTypes.string,
  autoPlay: PropTypes.bool,
  pauseOnHover: PropTypes.bool,
}

export default ImageBlockWithCarousel
