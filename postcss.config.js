module.exports = {
  plugins: {
    'autoprefixer': {},
    'cssnano': {
      preset: ['default', {
        discardComments: {
          removeAll: true,
        },
        minifyFontValues: {
          removeQuotes: false,
        },
      }],
    },
  },
}
