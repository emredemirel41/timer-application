import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';


export default defineConfig({
    build: {
        rollupOptions: {
            output:{
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return id.toString().split('node_modules/')[1].split('/')[0].toString();
                    }
                }
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