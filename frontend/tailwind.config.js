module.exports = {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  darkMode: 'media',
  theme: {
    extend: {},
    screens: {
      sm: '600px',
      md: '728px',
      lg: '984px',
      xl: '1130px',
      '2xl': '1330px'
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      inherit: 'inherit',
      'background': '#2A2F3B',
      'item': '#171E29',
      'icon': '#2C3949',
      'primary': '#452b95',
      'secondary': '#7041FF',
      'graywhite': '#FCFEFF',
      'black': '#0a0a0a',
      'white': '#ffffff',
      'purple': '#3f3cbb',
      'green': '#22c55e',
      'midnight': '#121063',
      'metal': '#565584',
      'tahiti': '#3ab7bf',
      'silver': '#ecebff',
      'bubble-gum': '#ff77e9',
      'bermuda': '#78dcca',
      'red': '#be123c'
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
