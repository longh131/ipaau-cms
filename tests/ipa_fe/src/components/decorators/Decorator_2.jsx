import { Lines1 } from './images/Lines1'
import { Colors } from './images/Colors'
import { ParallaxBanner, ParallaxBannerLayer } from 'react-scroll-parallax';

const coloursStyle1 = `[&_svg]:rotate-[35deg] [&_svg]:scale-x-[2.2] [&_svg]:scale-y-[-2.2] blur-[90px] opacity-50`

const Decorator2 = () => {
  return (
    <ParallaxBanner datatype='decorator2' className="w-full !absolute hidden lg:block min-h-[50vh] translate-y-1/3 -z-[1] !overflow-visible" aria-hidden="true">
      <ParallaxBannerLayer speed={30} className={`left-0 absolute ${coloursStyle1}`}>
          <Colors />
      </ParallaxBannerLayer>
      <ParallaxBannerLayer speed={-10} className={`right-0 top-1/2 -translate-y-1/2 absolute opacity-25`}>
        <Lines1 />
      </ParallaxBannerLayer>

    </ParallaxBanner>
  )
}

export default Decorator2
