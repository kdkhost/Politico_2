import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        react(),
        tailwindcss(),
        laravel({
            input: [
                'resources/css/admin/admin.css',
                'resources/js/admin/admin.js',
                'resources/css/site/site.css',
                'resources/css/site/premium.css',
                'resources/js/site/site.js',
                'resources/js/site/premium-app.jsx',
            ],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: 'manifest.json',
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
