/** @type {import('tailwindcss').Config} */
import themeConfig from '../../theme.config.js'
const plugin = require('tailwindcss/plugin')

const lightOrDark = (color) => {
  // Variables for red, green, blue values
  let r,
    g,
    b,
    hsp,
    type = 'dark'

  // Check the format of the color, HEX or RGB?
  if (color.match(/^rgb/)) {
    // If RGB --> store the red, green, blue values in separate variables
    color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/)

    r = color[1]
    g = color[2]
    b = color[3]
  } else {
    // If hex --> Convert it to RGB: http://gist.github.com/983661
    color = +('0x' + color.slice(1).replace(color.length < 5 && /./g, '$&$&'))

    r = color >> 16
    g = (color >> 8) & 255
    b = color & 255
  }

  // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
  hsp = Math.sqrt(0.299 * (r * r) + 0.587 * (g * g) + 0.114 * (b * b))

  // Using the HSP value, determine whether the color is light or dark
  hsp > 127.5 && (type = 'light')
  return type
}

// const types = themeConfig.tailwind?.types
// const variants = themeConfig.tailwind?.variants
// const modifiers = themeConfig.tailwind?.modifiers

// these are temporary sizes that are not in the designs, but are currently being used somewhere in the theming. Once the themes are updated this can be removed.
const tempFonts = {
  xs: '0.75rem',      // 12px
  sm: '0.875rem',     // 14px
  base: '1rem',       // 16px
  '5xl': '3rem',      // 48px
  '6xl': '3.75rem',   // 60px
  '7xl': '4.5rem',    // 72px
  '8xl': '6rem',      // 96px
  '9xl': '8rem',      // 128px
  '4xl': '2.5rem',    // 40px
}

export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}', '!./src/RTE', './src/App.scss'],
  theme: {
    fontFamily: themeConfig.font.family || {},
    fontSize: {
        ...themeConfig.font.sizes.overrides,
        ...tempFonts,
      },
    extend: {
      fontSize: {
        ...themeConfig.font.sizes.custom,
      },
      screens: themeConfig.settings.breakPoints ? themeConfig.settings.breakPoints : {},
      colors: {...themeConfig.textColors, ...themeConfig.backgroundColors, ...themeConfig.utilityColors},
      textShadow: {
        sm: '0.05em 0.05em 0.05em var(--tw-shadow-color)',
        DEFAULT: '0.05em 0.05em 0.05em var(--tw-shadow-color)',
        off: '0 0 0 transparent',
      },
      transitionProperty: {
        maxHeight: 'max-height',
        underline: 'text-decoration-thickness',
      },
    },
  },
  safelist: [
    // the safe list is for classes that may be applied dynamically and we know we need to load from tailwind at all times.
    // using the safelist will increase the size of the bundle, so use sparingly and only if it can't be avoided.

    // if shades are being set in the theme config file, instead of using base colours and getting an auto-generated palette, we need to modify the "shades" entry below to include all the shade names and variants

    // TODO we need to trim these down once we know what shades we actually need. It makes the CSS file massive.
    // for example, it generates 340 separate instances for a single colour shade with all the below variants being used, as it generates a series of opacity colours along side the named colours.

    'rounded-l-full',
    'rounded-r-full',
    'divide-y',
    'font-normal',
    // the following items are for the card lists in the hero banner.
    'xl:basis-1/6',
    'xl:basis-1/5',
    'xl:basis-3/12',
    'xl:basis-1/3',
    'xl:basis-1/2',
    'xl:basis-full',
    'banner',
    'leading-none',
    'max-w-prose',
    '.features-table_product',
    {
      pattern: /text-display-(xs|sm|md|lg|xl|2xl|3xl|4xl)/,
      variants: ['', 'max-md', 'max-lg', 'lg'],
    },
    // Custom gradient classes defined in SCSS
    'text-gradient-blue',
    'text-gradient-purple',
    'text-gradient-orange',
    'text-gradient-pink',
    'text-gradient-blue-reverse',
    'text-gradient-purple-reverse',
    'text-gradient-orange-reverse',
    'text-gradient-pink-reverse',
    'bg-gradient-blue',
    'bg-gradient-purple',
    'bg-gradient-orange',
    'bg-gradient-pink',
    'bg-gradient-blue-reverse',
    'bg-gradient-purple-reverse',
    'bg-gradient-orange-reverse',
    'bg-gradient-pink-reverse',

    // {
    //   pattern: /^(bg|outline|text|border)-(primary|secondary|tertiary)$/,
    //   variants: ['group-hover', 'hover', 'focus-visible', 'md', 'max-md', 'max-lg', 'lg'],
    // },
    // {
    //   pattern: /^decoration-(primary|secondary|tertiary)$/,
    //   variants: ['hover', 'hover:lg', 'lg', 'lg:hover'],
    // },
    // {
    //   pattern: /^(bg|outline|text|border)-(primary|secondary|tertiary)$/,
    //   variants: ['group-hover', 'hover', 'focus-visible', 'md', 'max-md', 'max-lg'],
    // },
    // {
    //   pattern: /^(bg|text)-(yellow|red|slate|green|blue)-(700|500|50)$/,
    //   variants: ['group-hover', 'hover', 'focus-visible', 'md', 'max-md', 'max-lg', 'lg'],
    // },
    ...themeConfig.tailwind.safelist,
    // ...themePalette,
  ],
  blockList: [...themeConfig.tailwind.blocklist],
  plugins: [
    require('@tailwindcss/container-queries'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    plugin(function ({ matchUtilities, theme }) {
      matchUtilities(
        {
          'text-shadow': (value) => ({
            textShadow: value,
          }),
        },
        { values: theme('textShadow') },
      )
    }),
  ],
}
