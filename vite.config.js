import glob from 'glob';
import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import eslint from "vite-plugin-eslint";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                '/resources/js/application.js',
                '/resources/js/inertia.ts',
                ...glob.sync('resources/sass/!(*.example).scss')
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
        eslint({
            fix: true,
            exclude: ['resources/js/*.js', 'vendor/**/*.js', '**/node_modules/**'],
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