import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),

    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),

    // @quasar/plugin-vite options list:
    // https://github.com/quasarframework/quasar/blob/dev/vite-plugin/index.d.ts
    quasar({
      autoImportComponentCase: 'combined',
      // sassVariables: 'resources/scss/quasar-variables.scss'
    })
  ],
  resolve: {
    alias: {
      // '@': path.resolve(__dirname, './resources/js'),
      vue: 'vue/dist/vue.esm-bundler.js',
      '@': fileURLToPath(new URL('./resources/js', import.meta.url))
    }
  }
});
