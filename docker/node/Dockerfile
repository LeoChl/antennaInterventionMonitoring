FROM node:22-alpine

ARG PNPM_VERSION=12.9.1

RUN npm install -g pnpm@${PNPM_VERSION}

ENV HOME=/tmp \
    npm_config_store_dir=/tmp/.pnpm-store

WORKDIR /app

CMD ["sh", "-c", "pnpm install --ignore-scripts && pnpm run build"]
