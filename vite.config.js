import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // CSS Files
                'resources/css/app.css',
                'resources/css/materi.css',
                'resources/css/tugas.css',
                'resources/css/jadwal.css',
                
                // JS Files
                'resources/js/script.js',
                'resources/js/materi.js',
                'resources/js/tugas.js',
                'resources/js/jadwal.js',
            ],
            refresh: true,
        }),
    ],
});