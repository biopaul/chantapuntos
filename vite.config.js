import { readFileSync } from 'fs';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// En producción (document root = public): assets en /build/assets/
const isProduction = process.env.NODE_ENV === 'production';
const env = loadEnv(process.env.NODE_ENV || 'production', process.cwd(), '');
const basePath = env.VITE_BASE_PATH || process.env.VITE_BASE_PATH;
const base = isProduction ? '/build/' : (basePath ? String(basePath).replace(/\/?$/, '') + '/' : '/');

let appVersion = '1.0.0';
try {
    const v = JSON.parse(readFileSync('./version.json', 'utf-8'));
    if (v && v.version) appVersion = v.version;
} catch (_) {}

export default defineConfig({
    base,
    define: {
        __APP_VERSION__: JSON.stringify(appVersion),
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
});
