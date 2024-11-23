import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true, // Active le rafraîchissement automatique pour Laravel
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    server: {
        host: '0.0.0.0', // Permet au serveur d'écouter toutes les interfaces réseau (nécessaire pour Docker)
        port: 5173, // Port explicite pour éviter les conflits
        strictPort: true, // Empêche le serveur de basculer sur un autre port si celui-ci est occupé
        watch: {
            usePolling: true, // Nécessaire dans certains environnements Docker pour surveiller les fichiers
        },
        hmr: {
            host: 'localhost', // L'hôte à utiliser pour le Hot Module Replacement (HMR)
        },
    },
});
