module.exports = {
  plugins: [
    require('autoprefixer'),
    process.env.NODE_ENV === 'production' ? require('@fullhuman/postcss-purgecss')({
      content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.html',
      ],
      defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
    }) : null, // تفعيل PurgeCSS فقط في الإنتاج
    require('cssnano')({
      preset: ['default', {
        discardComments: {
          removeAll: true, // إزالة جميع التعليقات
        },
        reduceIdents: false, // تعطيل تقليل الأسماء المكررة
      }],
    }),
  ].filter(Boolean) // تصفية المكونات الفارغة لتجنب الأخطاء
};
