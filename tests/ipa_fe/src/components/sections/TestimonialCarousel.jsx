import { useState, useRef } from 'react'
import PropTypes from 'prop-types'
import Section from './_Section'
import Picture from '../helpers/Picture'
import { transformPaddingToTailwind } from '../../helpers/style'
import { Swiper, SwiperSlide } from 'swiper/react'
import { Pagination, Autoplay, A11y } from 'swiper/modules'
// Import Swiper styles
import 'swiper/css'
import 'swiper/css/pagination'
import 'swiper/css/effect-coverflow'
import 'swiper/css/a11y'
import { generateDataHash } from '../../helpers/contentHash'

const arrowIcon = () => <svg width="37" height="31" viewBox="0 0 37 31" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M36 15.057L1 15.057" stroke="#992785" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
<path d="M21.885 0.999923L36.0017 15.0559L21.885 29.1143" stroke="#0D2C6C" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
</svg>

const starIcon = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <g clipPath="url(#clip0_1142_7539)">
    <path d="M11.5383 1.71006C11.7091 1.29942 12.2909 1.29942 12.4617 1.71006L15.0568 7.94943C15.1288 8.12254 15.2916 8.24083 15.4785 8.25581L22.2144 8.79583C22.6577 8.83137 22.8375 9.38462 22.4997 9.67396L17.3676 14.0701C17.2252 14.1921 17.1631 14.3835 17.2066 14.5659L18.7745 21.139C18.8777 21.5716 18.4071 21.9135 18.0275 21.6817L12.2606 18.1593C12.1006 18.0616 11.8994 18.0616 11.7394 18.1593L5.97249 21.6817C5.59294 21.9135 5.12231 21.5716 5.22551 21.139L6.79343 14.5659C6.83694 14.3835 6.77475 14.1921 6.63236 14.0701L1.5003 9.67396C1.16253 9.38462 1.34229 8.83137 1.78562 8.79583L8.52154 8.25581C8.70843 8.24083 8.87124 8.12254 8.94324 7.94943L11.5383 1.71006Z" fill="#FF9F00"/>
  </g>
  <defs>
    <clipPath id="clip0_1142_7539">
      <rect width="24" height="24" fill="white"/>
    </clipPath>
  </defs>
</svg>


function TestimonialCarousel(props) {
  const componentPadding = transformPaddingToTailwind(props.componentPadding, false, props.breadcrumbs)
  let testimonials = props.testimonialItems || []
  const containerRef = useRef(null)
  const swiperRef = useRef(null)

  const [isPlaying, setIsPlaying] = useState(true)

  const renderStars = (rating) => {
    const stars = []
    for (let i = 0; i < rating; i++) {
      stars.push(<span key={i} className="star filled">{starIcon()}</span>)
    }
    return stars
  }

  // TMP: toggle autoplay button
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

  // Only perform navigation if the button's slide is "fully visible"
  function handlePrevClick(e) {
    const btn = e.currentTarget;
    const slideEl = btn?.closest('.swiper-slide');
    if (!slideEl) return;
    swiperRef.current?.slidePrev();
  }

  function handleNextClick(e) {
    const btn = e.currentTarget;
    const slideEl = btn?.closest('.swiper-slide');
    if (!slideEl) return;
    swiperRef.current?.slideNext();
  }

  const testimonialCount = testimonials.length

  const testimonialContent = (testimonial, index, singleTestimonial = false) => (
    <div className={`testimonial-card__wrapper group/testimonial ${index === 0 ? 'swiper-slide-active container' : ''} ${singleTestimonial ? 'testimonial-card_single' : ''}`}>

    {/* Left arrow button */}
    {testimonialCount > 1 && (
    <div className="testimonial-card__navigation testimonial-card__navigation--prev invisible group-[:is(.swiper-slide-active_&)]/testimonial:visible">
      <button
        type="button"
        onClick={handlePrevClick}
        className="testimonial-card__nav-button hover:scale-110 transform transition-transform duration-300 text-secondary"
        aria-label="Previous testimonial"
        title="Previous testimonial"
        aria-controls="testimonial-swiper"
      >
        <span aria-hidden="true" className="rotate-180 inline-block">
          {arrowIcon()}
        </span>        <span className="sr-only">Previous testimonial</span>
      </button>
    </div>
    )}

    {/* Main content */}
    <figure className="testimonial-card__figure" itemScope itemType="https://schema.org/Review">
      <div className="testimonial-card__content">
        <blockquote itemProp="reviewBody" className="testimonial-card__quote font-apex-book text-secondary text-xl md:text-display-xs xl:text-display-lg w-full max-w-full mx-auto">
          "{testimonial.quoteText}"
        </blockquote>
        <figcaption className="testimonial-card__author flex items-center mt-6">
          {(testimonial.desktopProfileImage || testimonial.mobileProfileImage) && (
            <div className="testimonial-card__image max-md:mr-4">
              <Picture
                desktopImage={testimonial.desktopProfileImage}
                mobileImage={testimonial.mobileProfileImage}
                className="testimonial-card__avatar rounded-full object-cover"
                itemProp="image"
                alt={testimonial.desktopProfileImage?.altText || testimonial.mobileProfileImage?.altText || testimonial.authorName}
              />
            </div>
          )}
          <div className="testimonial-card__details" itemProp="author" itemScope itemType="https://schema.org/Person">
            <strong className="testimonial-card__name font-semibold text-lg font-inter text-primary">
              <span itemProp="name">{testimonial.authorName}</span>
            </strong>

            {(testimonial.authorTitle || testimonial.authorCompany) && (
              <div className="testimonial-card__meta text-md font-inter text-primary">
                {testimonial.authorTitle && <span className="testimonial-card__title" itemProp="jobTitle">{testimonial.authorTitle}</span>}
                {testimonial.authorTitle && testimonial.authorCompany && ', '}
                {testimonial.authorCompany && <span className="testimonial-card__company" itemProp="worksFor" itemScope itemType="https://schema.org/Organization">{testimonial.authorCompany}</span>}
              </div>
            )}

            <div className="testimonial-card__rating" itemProp="reviewRating" itemScope itemType="https://schema.org/Rating">
              <span className="testimonial-card__stars" aria-label={`${Math.min(Math.floor(Number(testimonial.starRating)), 5)} out of 5 stars`}>
                {renderStars(Math.min(Math.floor(Number(testimonial.starRating)), 5))}
              </span>
              <meta itemProp="ratingValue" content={testimonial.starRating} />
              <meta itemProp="bestRating" content="5" />
              <meta itemProp="worstRating" content="0" />
            </div>
          </div>
        </figcaption>
      </div>
    </figure>

    {/* Right arrow button */}
    {testimonialCount > 1 && (
    <div className=" testimonial-card__navigation testimonial-card__navigation--next invisible group-[:is(.swiper-slide-active_&)]/testimonial:visible">
      <button
        type="button"
        onClick={handleNextClick}
        className="testimonial-card__nav-button hover:scale-110 transform transition-transform duration-300 text-secondary"
        aria-label="Next testimonial"
        title="Next testimonial"
        aria-controls="testimonial-swiper"
      >
        <span aria-hidden="true">
          {arrowIcon()}
        </span>
        <span className="sr-only">Next testimonial</span>
      </button>
    </div>
    )}

  </div>
  )

  return (
    <Section
    type="testimonialCarousel"
    outerClass={`${componentPadding} overflow-hidden`}
    {...props}
    >
      {testimonialCount > 0 && (
      <div className="testimonial-carousel" id={props.jumpToId} ref={containerRef}>

        {testimonialCount === 1 && (testimonialContent(testimonials[0], 0, true))}
        {testimonialCount > 1 && (

        <Swiper
          modules={[Pagination, Autoplay, A11y]}
          onSwiper={(swiper) => { swiperRef.current = swiper }}
          onAutoplayStop={() => setIsPlaying(false)}
          onAutoplayStart={() => setIsPlaying(true)}
          slidesPerView={1}
          spaceBetween={10}
          centeredSlides={true}
          speed={500}
          a11y={{
            enabled: true,
            prevSlideMessage: 'Previous testimonial',
            nextSlideMessage: 'Next testimonial',
          }}
          rewind={true}
          pagination={{ clickable: true }}

          breakpoints={{
            768: {
              slidesPerView: 1.5,
            },
          }}
          autoplay= {!props.autoPlay ? false : {
              delay: 5000,
              pauseOnMouseEnter: props.pauseOnHover,
              disableOnInteraction: false,
              waitForTransition: true
          }}
        >
          {testimonials.map((testimonial, index) => (
            <SwiperSlide key={generateDataHash(testimonial)}>
              {testimonialContent(testimonial, index)}
            </SwiperSlide>
          ))}
        </Swiper>
        )}
        {/* Play/Pause button */}
        {props.autoPlay &&  testimonialCount > 1 && (
        <section className="flex justify-center mt-6" aria-label="Autoplay controls">
          <button
            type="button"
            onClick={handleToggleAutoplay}
            className="testimonial-card__navigation-autoplay-button"
            aria-pressed={isPlaying}
            aria-label={isPlaying ? 'Pause autoplay' : 'Resume autoplay'}
            title={isPlaying ? 'Pause autoplay' : 'Resume autoplay'}
          >
            <span aria-hidden="true">{isPlaying ? 'Pause Autoplay' : 'Resume Autoplay'}</span>
            <span className="sr-only">{isPlaying ? 'Pause autoplay' : 'Resume autoplay'}</span>
          </button>

          {/* Live region announces status changes to assistive tech */}
          <span className="sr-only" aria-live="polite" aria-atomic="true" style={{position:'absolute', left:'-9999px'}}>
            {isPlaying ? 'Autoplay is running' : 'Autoplay is paused'}
          </span>
        </section>
        )}
      </div>
      )}
    </Section>
  )
}

TestimonialCarousel.propTypes = {
  componentPadding: PropTypes.string,
  breadcrumbs: PropTypes.any,
  testimonialItems: PropTypes.arrayOf(
    PropTypes.shape({
      name: PropTypes.string,
      role: PropTypes.string,
      company: PropTypes.string,
      rating: PropTypes.number,
      testimonial: PropTypes.string,
      image: PropTypes.object,
    })
  ),
  jumpToId: PropTypes.string,
  autoPlay: PropTypes.bool,
  pauseOnHover: PropTypes.bool,
}

export default TestimonialCarousel
