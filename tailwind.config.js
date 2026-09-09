/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#C51517',
          light: '#d62426',
          dark: '#a51012',
        },
        brand: {
          DEFAULT: '#C51517',
          light: '#d62426',
          dark: '#a51012',
        },
        pressmatik: {
          DEFAULT: '#C51517',
          light: '#d62426',
          dark: '#a51012',
        }
      }
    },
  },
  plugins: [],
}
