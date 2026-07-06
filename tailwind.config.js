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
          DEFAULT: '#D51C23',
          light: '#e53e3e',
          dark: '#b91c1c',
        }
      }
    },
  },
  plugins: [],
}
