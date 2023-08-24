ARG NODE_VERSION=20
ARG ALPINE_VERSION=3.17

FROM node:${NODE_VERSION}-alpine${ALPINE_VERSION}

ARG USER_ID
ARG GROUP_ID

WORKDIR /app

## Add user and group
RUN deluser --remove-home node
RUN addgroup -S user -g $GROUP_ID
RUN adduser -S -g user -u $USER_ID user

USER user

## If you need to keep the container running use CMD ["tail", "-f", "/dev/null"]
CMD ["npm", "run", "dev"]
