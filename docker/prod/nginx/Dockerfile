#### 1
FROM node:alpine AS node-builder

COPY ./ ./app
WORKDIR /app

RUN yarn install && npm rebuild node-sass

RUN npm run build

#### 2
FROM nginx:1.15-alpine

COPY ./docker/prod/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

COPY ./public ./app/public
COPY --from=node-builder /app/public/build /app/public/build
