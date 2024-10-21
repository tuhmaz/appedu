import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import html from '@rollup/plugin-html';
import { terser } from 'rollup-plugin-terser';  // لضغط JavaScript باستخدام Terser
import { visualizer } from 'rollup-plugin-visualizer'; // لإضافة التحليل
import cssnano from 'cssnano'; // لضغط CSS
import purgecss from '@fullhuman/postcss-purgecss'; // لـ PurgeCSS
import { glob } from 'glob'; // لجلب الملفات باستخدام glob

/**
 * Get Files from a directory
 * @param {string} query
 * @returns array
 */
function GetFilesArray(query) {
  return glob.sync(query);
}

/**
 * Js Files
 */
// Page JS Files
const pageJsFiles = GetFilesArray('resources/assets/js/*.js');

// Processing Vendor JS Files
const vendorJsFiles = GetFilesArray('resources/assets/vendor/js/*.js');

// Processing Libs JS Files
const LibsJsFiles = GetFilesArray('resources/assets/vendor/libs/**/*.js');

/**
 * Scss Files
 */
// Processing Core, Themes & Pages Scss Files
const CoreScssFiles = GetFilesArray('resources/assets/vendor/scss/**/!(_)*.scss');

// Processing Libs Scss & Css Files
const LibsScssFiles = GetFilesArray('resources/assets/vendor/libs/**/!(_)*.scss');
const LibsCssFiles = GetFilesArray('resources/assets/vendor/libs/**/*.css');

// Processing Fonts Scss Files
const FontsScssFiles = GetFilesArray('resources/assets/vendor/fonts/!(_)*.scss');

// Processing Window Assignment for Libs like jKanban, pdfMake
function libsWindowAssignment() {
  return {
    name: 'libsWindowAssignment',

    transform(src, id) {
      if (id.includes('jkanban.js')) {
        return src.replace('this.jKanban', 'window.jKanban');
      } else if (id.includes('vfs_fonts')) {
        return src.replaceAll('this.pdfMake', 'window.pdfMake');
      }
    }
  };
}

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/css/custom.css',
        'resources/assets/css/edu.css',
        
        'resources/js/app.js',
        ...pageJsFiles,
        ...vendorJsFiles,
        ...LibsJsFiles,
        ...CoreScssFiles,
        ...LibsScssFiles,
        ...LibsCssFiles,
        ...FontsScssFiles
      ],
      refresh: true
    }),
    html(),
    terser(), // لضغط ملفات JS

    libsWindowAssignment()
  ],

  build: {
    rollupOptions: {
      plugins: [
        terser(), // لضغط ملفات JavaScript
      ]
    }
  },

  css: {
    postcss: {
      plugins: [
        // PurgeCSS يتم استخدامه فقط في بيئة الإنتاج
        process.env.NODE_ENV === 'production'
          ? purgecss({
              content: [
                './resources/**/*.blade.php',
                './resources/**/*.js',
                './resources/**/*.vue',
                './resources/**/*.html',
              ],
              safelist: [/^note-/, 'summernote', 'note-editable','btn-outline-dark', 'bubbly-button', 'd-flex', 'justify-content-center', 'align-items-center'],  // تأكد من إضافة الفئات الخاصة بمحرر النصوص هنا

              defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
            })
          : null, // فقط للإنتاج
        cssnano({ // لضغط CSS
          preset: ['default', {
            discardComments: {
              removeAll: true, // إزالة جميع التعليقات
            },
            reduceIdents: false, // تعطيل تقليل الأسماء المكررة
          }],
        }),
      ].filter(Boolean), // لتجنب أي مكونات غير مفعلة
    }
  }
});
