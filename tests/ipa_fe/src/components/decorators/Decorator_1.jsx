import { Colors } from './images/Colors'
import { Lines1 } from './images/Lines1'
import { ParallaxBanner, ParallaxBannerLayer } from 'react-scroll-parallax';

const coloursStyle1 = ` [&_svg]:-translate-x-1/2 blur-[90px] opacity-50`
const coloursStyle2 = ` [&_svg]:translate-y-1/4 [&_svg]:rotate-[65deg] [&_svg]:scale-150 blur-[90px] opacity-50`

const Decorator1 = () => {
  return (
    <ParallaxBanner dataType="decorator1" className="w-full !absolute hidden lg:block min-h-[50vh] -z-[1] !overflow-visible" aria-hidden="true">
      <ParallaxBannerLayer speed={20} className={`left-0 absolute ${coloursStyle1}`}>
          <Colors />
      </ParallaxBannerLayer>
      <ParallaxBannerLayer speed={30} className={`!right-0 !left-[unset] !translate-x-1/2 !w-1/2 absolute ${coloursStyle2}`}>
          <Colors />
      </ParallaxBannerLayer >
      <ParallaxBannerLayer speed={-20} className={`right-0 top-1/2 -translate-y-1/2 absolute opacity-25`}>
        <Lines1 />
      </ParallaxBannerLayer >
    </ParallaxBanner>
  );
};

export default Decorator1
