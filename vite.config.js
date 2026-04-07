import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    const devOrigin = env.VITE_DEV_ORIGIN || 'http://localhost:5173';
    const hmrHost = env.VITE_HMR_HOST || 'localhost';
    const hmrPort = Number(env.VITE_HMR_PORT || 5173);

    let appOrigin = 'http://localhost';
    if (env.APP_URL) {
        try {
            appOrigin = new URL(env.APP_URL).origin;
        } catch {
            /* ignore invalid APP_URL in local tooling */
        }
    }

    const corsOrigins = new Set([devOrigin, appOrigin]);
    if (env.VITE_CORS_EXTRA_ORIGINS) {
        for (const part of env.VITE_CORS_EXTRA_ORIGINS.split(',')) {
            const o = part.trim();
            if (o !== '') {
                corsOrigins.add(o);
            }
        }
    }

    return {
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
            origin: devOrigin,
            cors: {
                origin: [...corsOrigins],
            },
            hmr: {
                host: hmrHost,
                port: hmrPort,
            },
            watch: {
                usePolling: true,
            },
        },
    };
});
