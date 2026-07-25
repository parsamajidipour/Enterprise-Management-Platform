FROM node:lts-alpine AS builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN NUXT_BUILD_DIR=.nuxt ./node_modules/.bin/nuxt build

FROM node:lts-alpine AS runner

WORKDIR /app

ENV NODE_ENV=production
ENV HOST=0.0.0.0
ENV PORT=3000

COPY --from=builder /app/.output ./.output
COPY --from=builder /app/package.json ./package.json

RUN chown -R node:node /app

USER node

EXPOSE 3000

CMD ["node", ".output/server/index.mjs"]
