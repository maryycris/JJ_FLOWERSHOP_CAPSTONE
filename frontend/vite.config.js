import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    envDir: '../backend',
    build: {
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            publicDirectory: '../backend/public',
            buildDirectory: 'build',
            hotFile: '../backend/public/hot',
            refresh: [
                '../backend/app/**',
                '../backend/routes/**',
                '../backend/config/**',
                'resources/views/**',
            ],
        }),
        tailwindcss(),
    ],
});
