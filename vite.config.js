import vue from '@vitejs/plugin-vue';
import { glob } from 'glob';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.ts',
                '/resources/assets/sass/light.scss',
                '/resources/assets/js/application.js',
                '/resources/assets/js/echo.js',
                ...glob.sync('resources/assets/sass/!(*.example).scss'),
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    bootstrap: ['bootstrap', '@popperjs/core'],
                    interface: [
                        'easymde',
                        'swiper',
                        'signature_pad',
                        'codethereal-iconpicker',
                    ],
                },
            },
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
