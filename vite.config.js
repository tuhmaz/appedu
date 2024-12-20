import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import html from '@rollup/plugin-html';
import { glob } from 'glob';
import terser from '@rollup/plugin-terser';
import { visualizer } from 'rollup-plugin-visualizer';
import viteCompression from 'vite-plugin-compression';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';
import PurgeIcons from 'vite-plugin-purge-icons';

/**
 * Get Files from a directory
 * @param {string} query
 * @returns array
 */
function GetFilesArray(query) {
  return Array.from(new Set(glob.sync(query))); // Remove duplicate files by using Set initially
}

// File paths to be collected
const fileQueries = {
  pageJsFiles: 'resources/assets/js/*.js',
  vendorJsFiles: 'resources/assets/vendor/js/*.js',
  libsJsFiles: 'resources/assets/vendor/libs/**/*.js',
  coreScssFiles: 'resources/assets/vendor/scss/**/!(_)*.scss',
  libsScssFiles: 'resources/assets/vendor/libs/**/!(_)*.scss',
  libsCssFiles: 'resources/assets/vendor/libs/**/*.css',
  fontsScssFiles: 'resources/assets/vendor/fonts/!(_)*.scss',
  customScssFiles: 'resources/assets/vendor/scss/_*.scss',
};

// Collect all files
const files = Object.entries(fileQueries).reduce((acc, [key, query]) => {
  acc[key] = GetFilesArray(query);
  return acc;
}, {});

function collectInputFiles() {
  return [
    'resources/css/app.css',
    'resources/assets/css/edu.css',
    'resources/js/app.js',
    ...Object.values(files).flat(), // Flatten all arrays into one
  ];
}

// Processing Window Assignment for Libs like jKanban, pdfMake
function libsWindowAssignment() {
  return {
    name: 'libsWindowAssignment',
    transform(src, id) {
      const replacements = {
        'jkanban.js': ['this.jKanban', 'window.jKanban'],
        'vfs_fonts': ['this.pdfMake', 'window.pdfMake'],
      };
      for (const [key, [searchValue, replaceValue]] of Object.entries(replacements)) {
        if (id.includes(key)) {
          return src.replaceAll(searchValue, replaceValue);
        }
      }
    },
  };
}

export default defineConfig({
  plugins: [
    laravel({
      input: collectInputFiles(),
      refresh: true,
    }),
    html(),
    libsWindowAssignment(),
    PurgeIcons({
      content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
      ],
    }),
    terser({
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
      format: {
        comments: false,
      },
    }),
    viteCompression({
      algorithm: 'gzip',
      ext: '.gz',
      threshold: 10240, // Only compress files larger than 10KB
      deleteOriginFile: false,
    }),
    viteCompression({
      algorithm: 'brotliCompress',
      ext: '.br',
      threshold: 10240,
      deleteOriginFile: false,
    }),
    visualizer({
      filename: 'stats.html',
      gzipSize: true,
      brotliSize: true,
    }),
  ],
  css: {
    postcss: './postcss.config.mjs',
  },
  build: {
    cssMinify: 'cssnano',
    rollupOptions: {
      external: ['summernote'],
      output: {
        globals: {
          onesignal: 'OneSignal'
        },
        manualChunks: {
          vendor: [
            '@yaireo/tagify',
            '@popperjs/core',
          ],
          utils: [
            'lodash-es',
            'moment',
          ],
        },
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
        assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
      },
    },
    assetsInlineLimit: 4096, // 4KB
    sourcemap: false,
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
    },
    chunkSizeWarningLimit: 600,
    cssCodeSplit: true,
    emptyOutDir: true,
    modulePreload: {
      polyfill: true
    }
  },
  resolve: {
    alias: {
      '@': '/resources',
      '~': '/resources/assets',
    },
  },
  server: {
    hmr: {
      overlay: false
    },
    watch: {
      usePolling: true
    }
  }
});
