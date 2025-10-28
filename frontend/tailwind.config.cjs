/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './components/**/*.{vue,js,ts}',
    './layouts/**/*.{vue,js,ts}',
    './pages/**/*.{vue,js,ts}',
    './app.vue',
    './scripts/**/*.{js,ts}',
    './nuxt.config.{js,ts}',
  ],
  theme: {
    extend: {
      colors: {
        'color-scale': '#26CFBF',
        'color-scale-hover': '#a4ece5ff', 
        'white-scale': '#F3F3F3',
        'muted':'#959AA3',
        'success-scale': '#47B34F',
        'surface': '#F9FAFB',
        'surface-dark': '#0F151F',
        
        'surface-secondary': '#FFFFFF',
        'surface-secondary-dark': '#151F2C',

        'input-dark': '#0e1133ff',

        'border-color': '#E5E7EB',
        'border-color-dark': '#283941ff',
      },
    },
  },
  plugins: [],
}
