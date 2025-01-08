import {glob} from 'glob';
import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                '/resources/assets/sass/light.scss',
                '/resources/assets/js/application.js',
                '/resources/assets/js/echo.js',
                ...glob.sync('resources/assets/sass/!(*.example).scss')
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    bootstrap: ['bootstrap', '@popperjs/core'],
                    interface: ['easymde', 'swiper', 'signature_pad', 'codethereal-iconpicker']
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
