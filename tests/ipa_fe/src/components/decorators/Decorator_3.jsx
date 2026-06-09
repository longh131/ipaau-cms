import { ParallaxBanner, ParallaxBannerLayer } from 'react-scroll-parallax';
import { Colors } from './images/Colors'
import { Lines2 } from './images/Lines2'

const coloursStyle1 = `[&_svg]:rotate-[45deg] [&_svg]:scale-x-[1.8] [&_svg]:scale-y-[1.8] `

const Decorator3 = () => {
  return (
    <ParallaxBanner dataType="decorator3" className="w-full !absolute hidden lg:block min-h-[50vh] -translate-y-1/3 -z-[1] !overflow-visible" aria-hidden="true">
      <ParallaxBannerLayer speed={30} className={`left-0 absolute ${coloursStyle1} blur-[90px] opacity-50`}>
          <Colors />
      </ParallaxBannerLayer>
      <ParallaxBannerLayer speed={-20} className={`!-top-[200%] absolute opacity-25`}>
        <Lines2 />
      </ParallaxBannerLayer>
    </ParallaxBanner>
  )
}

export default Decorator3
