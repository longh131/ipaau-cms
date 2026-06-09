/*
    This file is the base theming configuration file for the Sitbank cua instance.
    This file is part of the theming config and should only be updated inside the /theme/ branches in the repository.
    When you make changes to this file, please update the below string so we can always stay +1 to the base repo version of the file.

    Iteration: 1

*/
const textColors = {
  'white': 'oklch(1 0 0)',
  'black': 'oklch(0 0 0)',
  'primary': 'oklch(0.464 0 0)',
  'secondary': 'oklch(0.3152 0.1176 262.41)',
  'grey': 'oklch(0.5452 0 0)',
  'warm-plum': 'oklch(0.4867 0.1803 336.11)',
  'warm-plum-subtle': 'oklch(0.8944 0.0357 331.62)',
  'electric-blue': 'oklch(0.6258 0.2041 256.27)',
  'primary-subtle': 'oklch(0.9612 0 0)',
  'secondary-subtle': 'oklch(0.9011 0.0552 218.07)',
  'flamingo': 'oklch(0.6651 0.1915 40.22)',
}

const utilityColors = {
  'link': 'oklch(0.3152 0.1176 262.41)',
  'link-hover': 'oklch(0.2305 0.0781 262.98)',
  'link-focused': 'oklch(0.6209 0.0502 239.85)',
  'link-active':  'oklch(0.1402 0.0316 258.8)',
  'disabled': 'oklch(0.9612 0 0)',
  'error': 'oklch(0.5057 0.1874 25.19)',
  'error-bold': 'oklch(0.3518 0.1252 24.18)',
  'error-subtle': 'oklch(0.8902 0.0348 13.4)',
  'warning': 'oklch(0.7822 0.172155 67.583)',
  'warning-bold': 'oklch(0.5373 0.1172 68.91)',
  'warning-subtle': 'oklch(0.9501 0.0466 80.81)',
  'success': 'oklch(0.6817 0.2246 143.48)',
  'success-bold': 'oklch(0.5784 0.189659 143.6337)',
  'success-subtle': 'oklch(0.9234 0.0567 149.8)',
  'grey-subtle': 'oklch(0.9612 0 0)',
  'navy-subtle': 'oklch(0.8723 0.0191 265.97)',
}

const backgroundColors = {
  // these colours are explicitly redefined in case of duplication as they are tied to the hex code.
  // theme colours may change, so we can't rely on them to match.
  'ffffff': 'oklch(1 0 0)',
  'c6c6c6': 'oklch(0.8266 0 0)',
  '0d2c6c': 'oklch(0.3152 0.1176 262.41)',
  'cfd5e2': 'oklch(0.8723 0.0191 265.97)',
  '1984ff': 'oklch(0.6258 0.2041 256.27)',
  '081a41': 'oklch(0.2305 0.0781 262.98)',
  '6e80a7': 'oklch(0.6209 0.0502 239.85)',
  '030916': 'oklch(0.1402 0.0316 258.8)',
  'b81e26': 'oklch(0.5057 0.1874 25.19)',
  'f1d2d4': 'oklch(0.8902 0.0348 13.4)',
  'ff9f00': 'oklch(0.7822 0.172155 67.583)',
  'ffeccc': 'oklch(0.9501 0.0466 80.81)',
  '00b91e': 'oklch(0.6817 0.2246 143.48)',
  'ccf1d2': 'oklch(0.9234 0.0567 149.8)',
  'f2f2f2': 'oklch(0.9612 0 0)',
}

const borderColors = {
  'primary-border': 'oklch(0.8266 0 0)',
  'secondary-border': 'oklch(0.3152 0.1176 262.41)',
  'inverse-border': 'oklch(1 0 0)',
  'error-border': 'oklch(0.5057 0.1874 25.19)',
  'warning-border': 'oklch(0.7822 0.172155 67.583)',
  'success-border': 'oklch(0.6817 0.2246 143.48)',
}

const thirdParty = {
  // this is left as a placeholder for future use, so that the structure for including third party elements is not lost.
  // typekit: {
  //   test: {
  //     selector: 'body',
  //     attributes: [],
  //     elements: [],
  //   },
  //   script: {
  //     src: <link rel="stylesheet" href="https://use.typekit.net/itp8aqm.css"></link>
  //     src: 'https://use.typekit.net/af/e0ec02/00000000000000003b9aee0c/27/l?primer=7cdcb44be4a7db8877ffa5c0007b8dd865b3bbc383831fe2ea177f62257a9191&fvd=n3&v=3',
  //     id: 'typekitScript',
  //     callback: null,
  //     location: 'head'
  //   }
  // },
  // calculators: {
  //   test: {
  //     // we test against this selector to see if we need to load the third party script.
  //     // selector: 'iframe[src*="gbst.com"]',
  //     selector: 'iframe',
  //     // list the attributes we need to get from the selector to make it work.
  //     attributes: ['id'],
  //     // list any subelements that we need to get from the selected element.
  //     // note that in this instance because we are loading from an iframe we cannot access any DOM elements within, so anything here will return and empty NodeList.
  //     elements: [],
  //   },
  //   script: {
  //     // if we do need to run the script, here's the src and an ID so that we only load it once.
  //     src: 'https://calculators.gbst.com/clients/standard_suite/lib/iframeResizer.min.js',
  //     id: 'iframeResizerScript',
  //     // we add a callback function to be run once that loaded script's onload event fires.
  //     callback: null,
  //   },
  //   callback: {
  //     attributes: function ({ id }) {
  //       if (typeof iFrameResize === 'function') {
  //         iFrameResize({}, `#${id}`)
  //       }
  //     },
  //     elements: function (elements) {
  //       // any function that requires subelements of the selected parents elements goes in here.
  //     },
  //   },
  // },
}

/* if you are adding a local font for a client, you will also need to edit fe-src\src\scss\base\_localFonts.scss to add the font-family details */
const viteFontSetup = {
  google: {
    families: [
      {
        name: 'Inter',
        styles: 'ital,wght@0,400;0,700;0,900;1,400;1,700;1,900',
        defer: true,
      },
      {
        name: 'Roboto Slab',
        styles: 'ital,wght@0,400;0,700;0,900;1,400;1,700;1,900',
        defer: true,
      },
    ],
  },
  // custom: {
  //   families: [
  //     {
  //       name: 'PPEditorialNew',
  //       local: 'PPEditorialNew',
  //       src: './public/fonts/PPEditorialNew-Regular.woff',
  //       transform(font) {
  //         if (font.basename === 'PPEditorialNew') {
  //           // update the font weight
  //           font.weight = 400
  //         }
  //         // we can also return null to skip the font
  //         return font
  //       },
  //     },
  //   ],
  // },
}

const themeConfig = {
  thirdParty: thirdParty,
  textColors,
  backgroundColors,
  utilityColors,
  borderColors,
  settings: {
    // this will close all other accordions in the same component when a new one is open
    onlyOneAccordionOpen: true,
    // this setting will resetting any/all breakpoints for your site.
    // this is mostly useful for locking the max-content width to a certain size.
    // the default tailwind setting for this is 1536px.
    // values: false, or an object something like:
    // {
    //   '2xl': '1440px',
    // },
    breakPoints: {
      '2xl': '1440px',
    },
    // this setting will set a more 'readable' width constraint to various components.
    // it's probably a good idea to turn it on, but the default will be off, so as not to break existing cient designs.
    useReadableContentWidth: false,
    // there are two types of button available, rounded rectangles and pill-shaped.
    // set this to true to lock all buttons to pill-shaped, or false to default to rounded-rectangles
    useOnlyPillButtons: true,
    // if a button colour selection matches the colour of a button in the component
    // true: switch to a white button
    // false: switch to a lighter shadew of the same theme colour.
    useWhiteButtonsOnCollision: true,
    showExternalLinkIcons: false,
    heroForegroundImages: true,
    // useTextShadow: ['images', 'always'] | false
    // images is for sections with background images
    // always is for... always
    // false is don't use shadows at all.
    useTextShadow: 'images',
    buttonShades: {
      standard: {
        base: backgroundColors['0d2c6c'],
        hover: backgroundColors['0d2c6c'],
      },
      muted: {
        base: backgroundColors['cfd5e2'],
        hover: backgroundColors['cfd5e2'],
      },
    },
    backToTop: backgroundColors['0d2c6c'],
    searchPageStub: 'search',
    useCMSMenuAccents: true,
    hero: {
      card: {
        shadow: {
          base: 'inherit',
          // it is unlikely that you wil want this to be the full colour, as it will be quite stark, so it's recommended that you specifically add the class `shadow-[color-value]/50` to the safelist below to ensure that the colour choice is loaded (unless you use 'inherit' of course).
          hover: 'inherit',
        },
      },
    },
    nav: {
      desktop: {
        base: backgroundColors['ffffff'],
        active: backgroundColors['ffffff'],
        highlight: utilityColors['border-secondary'],
      },
      mobile: {
        base: backgroundColors['ffffff'],
        active: backgroundColors['ffffff'],
      },
      footer: {
        base: backgroundColors['ffffff'],
        disclaimers: textColors['primary'],
        inheritLinkStyle: false,
      },
      sticky: {
        base: backgroundColors['cfd5e2'],
      },
      breadcrumbs: {
        base: backgroundColors['ffffff'],
        height: '3rem',
      },
      search: {
        base: backgroundColors['ffffff'],
      },
    },
    alerts: {
      success: {
        bg: utilityColors['success-subtle'],
        text: utilityColors['success-bold'],
      },
      primary: {
        bg: backgroundColors['ffffff'],
        text: textColors['primary'],
      },
      danger: {
        bg: utilityColors['error-subtle'],
        text: utilityColors['error-bold'],
      },
      secondary: {
        bg: backgroundColors['secondary'],
        text: textColors['secondary'],
      },
      warning: {
        bg: utilityColors['warning-subtle'],
        text: utilityColors['warning'],
      },
      light: {
        bg: backgroundColors['c6c6c6'],
        text: textColors['primary'],
      },
      dark: {
        bg: backgroundColors['0d2c6c'],
        text: textColors['white'],
      },
    },
  },
  // this flag will tell the system to generate text-colors for each base colour that is present.
  detectInverseText: true,
  // these colours can be presented in either HEX or RBG notation, either will work for the inverse text detection
  colors: {
    // if the client just has a set of base colours they can be defined here, and it will generate a tailwind-compatible set of shades based on these as the central (-500 value) colour.
    text: {
      // the color for dark text on a light background, not the color that goes over dark
      light: textColors['primary'],
      // the color for light text on a dark background, not the color that goes over light
      dark: textColors['white'],
      highlight: textColors['primary']
    },
    link: {
      default: {
        // the color for dark text on a light background, not the color that goes over dark
        light: utilityColors['link'],
        // the color for light text on a dark background, not the color that goes over light
        dark: textColors['white'],
      },
      hover: {
        // the color for dark text on a light background, not the color that goes over dark
        light: utilityColors['link-hover'],
        // the color for light text on a dark background, not the color that goes over light
        dark: textColors['white'],
      },
      focused: {
        light: utilityColors['link-focused'],
        dark: textColors['white'],
      },
      active: {
        light: utilityColors['link-active'],
        dark: textColors['white'],
      },
    },
  },
  font: {
    vite: viteFontSetup,
    family: {
      // not limited to just 'sans' and 'serif', can be any arbitrary name

      // Instructions for adding a font from Google.
      /*
        - near the top of this file is a const viteFontSetup.
        - this is an object that will load a series of one or more google fonts, with selectable weights.
        - once this is filled in using the options that can be found at https://github.com/cssninjaStudio/unplugin-fonts you need to:
          - run the build script
          - open the resulting index.html file
          - look for lines that look like
              <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="anonymous">
              <link rel="preload" as="style" onload="this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?family=Playfair Display:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&display=swap">
            - they will be different depending on the fonts you are installing, so be careful. DO NOT COPY AND PASTE FROM HERE
        - Copy those lines and paste them into the Header Scripts area of the site in the CMS
          - the plugin adds them to the index.html, but we are not using that as our launchpad for the site, so need to work around it.
        - Save, and test locally.
        - The same piece of code will need to be added to each environment, per client.
        - add the font details as below, using the display name of the font you are trying to load, per Google's naming in the related font page.
        - profit!
      */

      // Font names with spaces can be surrounded with "" inside the string.
      sans: ['"din-2014"', 'sans-serif'],
      highlight: ['"ApexSerif Book"', '"Roboto Slab"', 'serif'],
    },
    sizes: {
      custom: {
        event: `${(31 / 16)}rem`,
        'display-xs': [`${20/16}rem`, 1.4],
        'display-sm': [`${25/16}rem`, 1.4],
        'display-md': [`${31/16}rem`, 1.4],
        'display-lg': [`${39/16}rem`, 1.4],
        'display-xl': [`${49/16}rem`, 1.2],
        'display-2xl': [`${61/16}rem`, 1.2],
        'display-3xl': [`${76/16}rem`, 1.2],
        'display-4xl': [`${95/16}rem`, 1.15],
        'eyebrow-md': [`${18/16}rem`, 1.3],
        'eyebrow-xl': [`${20/16}rem`, 1.3],
        'label-xl': [`${25/16}rem`, 1.4],
        'label-md': [`${20/16}rem`, 1.4],
        'label-sm': [`${18/16}rem`, 1.4],
        'label-xs': [`${16/16}rem`, 1.4],
      },
      overrides: {
        // Your custom sizes
        xs: [`${(12/16)}rem`, 1.5],
        sm: [`${(14/16)}rem`, 1.5],
        md: [`${(16/16)}rem`, 1.5],
        lg: [`${(18/16)}rem`, 1.5],
        xl: [`${(22/16)}rem`, 1.5],
        '2xl': [`${(25/16)}rem`, 1.5],
        '3xl': [`${(24/16)}rem`, 1.5],
      }
    }
  },
  // these settings will change the colour related classes that are added to the tailwind safelist.
  // because many of our classes are dynamically added they do not get picked up as being in use when tailwind does its internal tree-shaking.
  // adding classes to the safelist forces them to be included, at the cost of potentially unneccessary rules being added.
  // tailwind colour classes can get pretty special, from simple items like "bg-red-500" or "text-black-500" to "max-lg:hover:text-jazzberry-300-text".
  tailwind: {
    // this is the list of shades that will be included in the final build.
    // these are the variants of the classes used to cover the "state" or "breakpoint" options of the classes
    variants: ['', 'group-hover:', 'hover:', 'focus-visible:', 'md:', 'max-md:', 'max-lg:', 'lg:', 'hover:lg:'],
    // this is the current list of safelist tailwind classes required for the theme to function.
    // this can be a string, or an object that matches a pattern. This object may include a variants key that is similar to the above variants key, but customised for that pattern.
    // there are currently other patterns described in the base tailwind.config.js, the one here is a small example of usage (that we also need, so please don't remove it)
    safelist: [
      'rounded-l-full',
      'rounded-r-full',
      'divide-y',
      'font-normal',
      'shadow-white',
      'shadow-black',
      'banner',


      {
        pattern: /bg-gradient-(t|r|l)/,
      },
      {
        pattern: /grid-cols-(1|2|3|4)/,
        variants: ['md', 'lg', 'xl', '2xl'],
      },
      {
        pattern: /prose/,
        variants: ['md', 'lg', 'xl', '2xl'],
      },
      {
        pattern: /divide-(x|y)-(2|4|6)/,
        variants: ['', 'md', 'lg', 'xl', '2xl', 'max-xl', 'max-md'],
      },
      {
        pattern: /divide-(x|y)/,

        variants: ['md', 'lg', 'xl', '2xl', 'max-xl', 'max-md'],
      },
      {
        pattern: /(m|p)(x|y|t|b|r|l)-(2|4|6|8)/,
      },
      {
        pattern: /^text-(lg|xl|2xl|3xl|4xl|5xl|6xl)$/,
        variants: ['', 'max-md', 'max-lg', 'lg:@sm/callout'],
      },
      {
        pattern: /border-(solid|dashed|dotted|double|groove|ridge|inset|outset)/,
        variants: ['', 'hover', 'focus-visible', 'md', 'max-md', 'max-lg', 'lg'],
      },
      {
        pattern: /border-(l|r|t|b)-(0|1|2|4)/,
        variants: ['', 'hover', 'focus-visible', 'md', 'max-md', 'max-lg', 'lg'],
      },
      {
        pattern: /^(border|bg|text)-(primary|secondary|grey|warm-plum|electric-blue|white|black|link|link-hover|link-focused|link-active|disabled|error|error-bold|warning|warning-bold|success|success-bold|transparent)$/,
        variants: ['', 'hover', 'focus-visible'],
      },
    ],
    // this is a list of specific classes that we do not want to include in the build.
    // this cannot be a pattern, like the safelist, and must be a discrete array of strings.
    // this is probably not going to be necessary due to configuration optimisations that have already taken place.
    blocklist: [],
  },
}
// if we want to push custom classes to the safelist, but also need settings from the config, we can do it like this
themeConfig.tailwind.safelist.push(`h-[${themeConfig.settings.nav.breadcrumbs.height}]`)
themeConfig.tailwind.safelist.push(`-mb-[${themeConfig.settings.nav.breadcrumbs.height}]`)
themeConfig.tailwind.safelist.push(`pt-[${themeConfig.settings.nav.breadcrumbs.height}]`)
themeConfig.tailwind.safelist.push(`shadow-${themeConfig.settings.hero.card.shadow.base}/50`)
themeConfig.tailwind.safelist.push(`hover:shadow-${themeConfig.settings.hero.card.shadow.hover}/50`)

export default themeConfig
