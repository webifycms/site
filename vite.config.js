import {defineConfig, loadEnv} from 'vite';
import liveReload from 'vite-plugin-live-reload';
import fs from 'fs';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const hotFile = path.resolve(__dirname, 'public/hot');

    function hotFilePlugin() {
        return {
            name: 'hot-file-plugin',
            configureServer(server) {
                const url = env.VITE_DEV_SERVER_URL

                fs.writeFileSync(hotFile, url)

                const cleanup = () => {
                    if (fs.existsSync(hotFile)) fs.unlinkSync(hotFile)
                    process.exit()
                }

                process.on('exit', cleanup)
                process.on('SIGINT', cleanup)
                process.on('SIGTERM', cleanup)
            },
        };
    }

    return {
        plugins: [
            liveReload(['templates/**/*', 'src/**/*']),
            hotFilePlugin(),
        ],
        server: {
            host: true,
            port: Number(env.VITE_PORT) || 5173,
            strictPort: true,
            cors: {
                origin: env.APP_BASE_URL,
            },
            origins: env.VITE_DEV_SERVER_URL,
        },
        build: {
            manifest: true,
            outDir: 'public',
            publicDir: false,
            emptyOutDir: false,
            rollupOptions: {
                input: {
                    app: 'assets/js/app.js',
                },
            }
        }
    }
});
