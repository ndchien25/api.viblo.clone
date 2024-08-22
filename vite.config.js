import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        react(),  // Add React plugin for Vite
    ],
    server: {
        proxy: {
            '/api': 'http://api.viblo.clone',  // Proxy API requests to Laravel backend
        },
    },
});
