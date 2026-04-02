import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

/** Browser must load dev assets from a host it can reach (Docker publishes 5173 → localhost). */
const devOrigin = 'http://localhost:5173';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // Asset URLs in the browser (must stay reachable from the host).
        origin: devOrigin,
        // Page is served from nginx (e.g. :9090); modules/fonts load from :5173 → cross-origin.
        // Without this, Allow-Origin stays tied to `origin` above and the browser blocks scripts.
        cors: {
            origin: true,
        },
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        watch: {
            usePolling: true,
        },
    },
});
