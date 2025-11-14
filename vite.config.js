import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Core chunk (includes Chatbot, Sidebar, Notification, dll)
                    'core': ['./resources/js/core.js'],
                    'main': ['./resources/js/script.js'],
                }
            }
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
    },
    // Enable source maps for debugging
    sourcemap: process.env.NODE_ENV === 'development'
});
