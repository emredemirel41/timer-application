import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';


export default defineConfig({
    build: {
        rollupOptions: {
            output:{
                manualChunks: {}
            }
        }
      },
    plugins: [
        vue(),
        vuetify({ autoImport: true }), 
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});