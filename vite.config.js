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

    // Copies source images referenced directly by templates (via assetUrl('img/...'))
    // into the public docroot, mirroring the production Dockerfile step
    // (docker/php/Dockerfile: `COPY assets/img/ ./public/assets/img/`).
    // Only images imported by the bundle are emitted by Vite on their own.
    function copyPublicImg() {
        const source = path.resolve(__dirname, 'assets/img')
        const target = path.resolve(__dirname, 'public/assets/img')

        return {
            name: 'copy-public-img',
            closeBundle() {
                fs.mkdirSync(target, { recursive: true })
                fs.cpSync(source, target, { recursive: true })
            },
        };
    }

    return {
        plugins: [
            hotFilePlugin(),
            copyPublicImg(),
            liveReload(['templates/**/*', 'src/**/*']),
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
