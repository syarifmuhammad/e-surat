const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */

module.exports = {
  content: [
    'node_modules/preline/dist/*.js',
    './src/**/*.{html,js,vue,ts,jsx,tsx}', 
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
        poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
        quicksand: ['Quicksand', ...defaultTheme.fontFamily.sans]
      },
      colors: {
        neutral: {
          100: 'var(--color-neutral-100)',
          200: 'var(--color-neutral-200)',
          300: 'var(--color-neutral-300)',
          400: 'var(--color-neutral-400)',
          500: 'var(--color-neutral-500)',
          600: 'var(--color-neutral-600)',
          700: 'var(--color-neutral-700)',
          800: 'var(--color-neutral-800)',
          900: 'var(--color-neutral-900)'
        },
        primary: {
          200: '#e69ea6',
          300: '#D3858E',
          400: '#B96972',
          500: '#AF5660'
        },
        secondary: {
          100: '#FFE1C9',
          200: '#FFD5B4',
          300: '#F4C29B'
        }
      }
    }
  },
  plugins: [
    require('preline/plugin'),
    require('@tailwindcss/forms'),
  ]
}
