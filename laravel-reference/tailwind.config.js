module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/wire-elements/modal/resources/views/*.blade.php',
    './storage/framework/views/*.php',
  ],
  safelist: [
    'sm:max-w-sm',
    'sm:max-w-md',
    'sm:max-w-lg',
    'sm:max-w-xl',
    'sm:max-w-2xl',
    'sm:max-w-3xl',
    'sm:max-w-4xl',
    'sm:max-w-5xl',
    'sm:max-w-6xl',
    'sm:max-w-7xl',
  ],
  theme: {
    screens: {
      sm: '1000px',
      md: '1280px',
      lg: '1680px',
      '2xl': '2060px',
    },
    extend: {
      transitionDelay: {
        400: '400ms',
        600: '600ms',
        800: '800ms',
        900: '900ms',
      },
      lineHeight: {
        14: '4rem',
        16: '6rem',
        18: '8rem',
      },
      rotate: {
        225: '225deg',
      },
      boxShadow: {
        DEFAULT: '0px 20px 30px rgba(0,0,0,0.15);',
      },
      transitionDuration: {
        3500: '3500ms',
      },
      colors: {
        green: {
          light: '#59c6c6',
          DEFAULT: '#04575f',
          dark: '#04575f',
        },
        yellow: {
          DEFAULT: '#ffab01',
        },
        purple: {
          DEFAULT: '#6610f1',
        },
        gray: {
          light: '#f5f5f5',
          dark: '#121212',
        },
      },
      fontFamily: {
        sans: ['Calibri', 'sans-serif'],
        lores: ['LoRes', 'sans-serif'],
        oswald: ['Oswald', 'sans-serif'],
      },
      fontSize: {
        '10xl': '8rem',
        '11xl': '9.5rem',
      },
    },
  },
  variants: {
    extend: {
      fontWeight: ['group-hover'],
      scale: ['group-hover'],
      backgroundOpacity: ['group-hover'],
      blur: ['group-hover'],
    },
  },
  plugins: [require('@tailwindcss/forms')],
};
