# SSL Setup

## Production (Recommended): Terminate TLS at the Reverse Proxy

In production, SSL is terminated by a reverse proxy on the host, outside this
project. The Docker Nginx only listens internally and does not need SSL
configuration.

See [DEPLOYMENT.md](./DEPLOYMENT.md#5-reverse-proxy-setup) for the proxy setup.

## Local Development: Self-Signed Certificate (Optional)

By default, the application runs over HTTP. If you want to enable HTTPS locally, follow these steps:

### 1. Generate Self-Signed Certificate

Run this command in your project root to generate a self-signed certificate valid for 365 days:

```bash
mkdir -p docker/nginx/ssl
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout docker/nginx/ssl/server.key \
  -out docker/nginx/ssl/server.crt \
  -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost"
```

### 2. Add SSL to the Nginx Config

Add an SSL server block to `docker/nginx/nginx.caddy.conf` inside the `server` directive:

```nginx
server {
    charset utf-8;
    client_max_body_size 10M;

    listen 80;
    listen 443 ssl;

    ssl_certificate     /etc/nginx/ssl/server.crt;
    ssl_certificate_key /etc/nginx/ssl/server.key;

    # ... rest of the config remains the same
}
```

### 3. Update compose.override.yml

Add the SSL volume mount and port to the `server` service in `compose.override.yml`:

```yaml
server:
  volumes:
    - ./:/var/www/html
    - ./docker/nginx/nginx.caddy.conf:/etc/nginx/conf.d/default.conf
    - ./docker/nginx/ssl:/etc/nginx/ssl:ro
  ports:
    - "${NGINX_PORT}:80"
    - "${NGINX_PORT_SSL}:443"
```

Also, update your `.env` file to set the SSL port:

```env
NGINX_PORT_SSL=8443
```

### 4. Rebuild and Restart Containers

```bash
docker compose down
docker compose up -d --build
```

### 5. Access Your Application

- HTTP: `http://localhost:${NGINX_PORT}`
- HTTPS: `https://localhost:${NGINX_PORT_SSL}`

**Note:** Your browser will show a security warning for the self-signed certificate.
This is expected in local development. Accept the certificate or add an exception.

## Important Notes

- **Do not commit SSL certificates** to version control. `docker/nginx/ssl/` is already in `.gitignore`.
- Self-signed certificates are only for **local development**.
- For production, TLS is handled by the reverse proxy.
- Each developer should generate their own certificates if needed.
