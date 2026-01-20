export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
          primary: '#6B0000',   // bordeaux
          secondary: '#E8E8E8', // gris clair
      },
      fontFamily: {
        asset: ['Asset', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
