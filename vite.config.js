import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';


export default defineConfig({
    build: {
        minify: false,
    },
    plugins: [
        vue(),
        vuetify({ autoImport: true }), // Enabled by default
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});