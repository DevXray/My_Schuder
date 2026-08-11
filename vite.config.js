import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
        server: {
        host: true, // bind ke 0.0.0.0
        port: 5173,
        hmr: {
        protocol: 'ws',
        host: 'localhost', // atau IP yang Live Share forward gunakan
        clientPort: 5173
        }
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 
                    'resources/js/app.js',
                    'resources/css/dashboard.css',
                    'resources/css/pages.css',
                    'resources/css/jadwal.css',
                    'resources/css/materi.css',
                    'resources/css/tugas.css',
                    'resources/css/profile.css'
                
                
                ],
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
