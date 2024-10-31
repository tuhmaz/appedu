const purgecss = require('@fullhuman/postcss-purgecss');
const cssnano = require('cssnano');


module.exports = {
  plugins: [
    cssnano({
      preset: 'default',
    }),
    purgecss({
      content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.scss',
      ],
      defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
    }),
  ],
};
