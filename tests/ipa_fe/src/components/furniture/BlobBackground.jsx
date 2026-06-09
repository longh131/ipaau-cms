import { useEffect, useRef, useState } from 'react'
import PropTypes from 'prop-types'

const BlobBackground = (props) => {
  const ref = useRef(null)
  const backgroundRef = useRef(null)
  const interactiveRef = useRef(null)
  const animated = props.type == 'animated'
  const [isIntersecting, setIsIntersecting] = useState(false)

  useEffect(() => {
    if (animated && isIntersecting) {
      let current = { x: 0, y: 0 }
      let target

      const move = () => {
        if (target !== undefined) {
          current = {
            x: current.x + (target.x - current.x) / 20,
            y: current.y + (target.y - current.y) / 20,
          }
          interactiveRef.current.style.transform = 'translate(' + current.x + 'px, ' + current.y + 'px)'
        }
        requestAnimationFrame(move)
      }

      const mousemove = (event) => {
        target = {
          x: event.clientX,
          y: event.clientY - ref.current.offsetTop,
        }
      }
      document.addEventListener('mousemove', mousemove)

      move()

      return () => {
        document.removeEventListener('mousemove', mousemove)
      }
    } else {
      const scroll = () => {
        const rect = backgroundRef.current.getBoundingClientRect()
        const start = rect.top - window.innerHeight
        const height = rect.bottom - start
        backgroundRef.current.style.setProperty('--blobProgress', Math.max(0, Math.min(1, (start * -1) / height)))
      }
      document.addEventListener('scroll', scroll)
      scroll()

      return () => {
        document.removeEventListener('scroll', scroll)
      }
    }
  }, [animated, isIntersecting])

  const blobBackgroundObserver = new IntersectionObserver((entries) => {
    setIsIntersecting(entries[0].isIntersecting)
  })

  useEffect(() => {
    blobBackgroundObserver.observe(document.querySelector('#blobBackground'))
  }, [])

  return (
    <div className="relative" {...{ ref }}>
      <div className={`blobBackground ${props.type} ${isIntersecting ? 'intersecting' : 'not-intersecting'}`} ref={backgroundRef}>
        <svg xmlns="http://www.w3.org/2000/svg" hidden>
          <defs>
            <filter id="blob">
              <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
              <feColorMatrix
                in="blur"
                mode="matrix"
                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -8"
                result="blob"
              />
              <feBlend in="SourceGraphic" in2="blob" />
            </filter>
          </defs>
        </svg>
        <div className="blobContainer" id="blobBackground">
          {animated ? (
            <>
              <div className="purple single lowerRight" />
              <div className="blue single middleRight" />
              <div className="purple single lowerLeft" />
              <div className="blue single lowerLeft" />
              <div className="blue single topRight" />
              <div className="orange double topRight" />
              <div className="interactive" ref={interactiveRef} />
            </>
          ) : (
            <>
              <div className="purple single" />
              <div className="blue single" />
              <div className="orange single" />
            </>
          )}
        </div>
      </div>
    </div>
  )
}

BlobBackground.propTypes = {
  type: PropTypes.string,
}

export default BlobBackground
