# Optional SSL Setup for Local Development

By default, the application runs over HTTP. If you want to enable HTTPS locally, follow these steps:

## 1. Generate Self-Signed Certificate

Run this command in your project root to generate a self-signed certificate valid for 365 days:

```bash
mkdir -p docker/nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/server.key \
  -out docker/nginx/ssl/server.crt \
  -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"
```

Or with interactive prompts:

```bash
mkdir -p docker/nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/server.key \
  -out docker/nginx/ssl/server.crt
```

## 2. Update Docker Configuration

### Option A: Use the SSL nginx config

Replace your `docker/nginx/nginx.conf` with `nginx.ssl.conf`:

```bash
cp docker/nginx/nginx.conf docker/nginx/nginx.conf.bak
cp docker/nginx/nginx.ssl.conf docker/nginx/nginx.conf
```

### Option B: Update compose.yml

Add the SSL port and volume mount to the nginx service in `compose.yml`:

```yaml
server:
  container_name: ${APP_ID}-nginx
  image: nginx:alpine
  volumes:
    - ./:/var/www/html
    - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf
    - ./docker/nginx/ssl:/etc/nginx/ssl:ro
  ports:
    - "${NGINX_PORT}:80"
    - "${NGINX_PORT_SSL}:443"
  restart: always
  networks:
    - webifycms-docker
```

Also, update your `.env` file to set the SSL port:

```env
NGINX_PORT_SSL=8443
```

## 3. Rebuild and Restart Containers

```bash
docker compose down
docker compose up -d --build
```

## 4. Access Your Application

- HTTP: `http://localhost:${NGINX_PORT}`
- HTTPS: `https://localhost:${NGINX_PORT_SSL}`

**Note:** Your browser will show a security warning for the self-signed certificate. This is expected in local development. Accept the certificate or add an exception.

## 5. Revert SSL (Return to HTTP)

```bash
cp docker/nginx/nginx.conf.bak docker/nginx/nginx.conf
rm -rf docker/nginx/ssl
docker compose down
docker compose up -d --build
```

## Important Notes

- **Do not commit SSL certificates** to version control. Add `docker/nginx/ssl/` to `.gitignore`.
- Self-signed certificates are only for **local development**.
- For production, use proper certificates from a Certificate Authority (CA).
- Each developer should generate their own certificates if needed.
