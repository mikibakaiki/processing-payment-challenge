/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.{vue,js}',
    './resources/views/**/*.blade.php',
  ],
  theme: {
    extend: {
      colors: {
        'gop-yellow': '#ffc84b',
        'gop-blue': '#154b64',
        'gop-lightgreen': '#eaf6f0',
        'gop-green': '#7ce1b1',
        'gop-darkgreen': '#199376'
      }
    }
  },
  plugins: [],
}

