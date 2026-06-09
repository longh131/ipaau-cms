import { ArrowUpCircleIcon } from '@heroicons/react/24/solid'
import ButtonEl from '../helpers/ctas/Button'
import themeConfig from '../../../theme.config'
import { useState, useEffect } from 'react'

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth', // for smoothly scrolling
  })
}

const BackToTop = () => {
  const [showButton, setShowButton] = useState(false)

  useEffect(() => {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        setShowButton(true)
      } else {
        setShowButton(false)
      }
    })
  }, [])

  return (
    <ButtonEl
      theme="text"
      buttonType="none"
      showDecorator={false}
      onClick={scrollToTop}
      className={`fixed !p-0 bottom-3 h-12 w-12 elevation-2 z-[500] transition-all duration-300 left-3 md:left-6 md:bottom-6 hover:scale-110 hover:elevation-6 ${showButton ? 'opacity-1' : 'opacity-0'}`}
    >
      <div className="rounded-full bg-white text-shadow-off">
        <ArrowUpCircleIcon
          role="none"
          className={`w-full text-${themeConfig.settings.backToTop ? themeConfig.settings.backToTop.replace('#', '') : 'light'}`}
        />
        <span className="sr-only">Back to top</span>
      </div>
    </ButtonEl>
  )
}

export default BackToTop
