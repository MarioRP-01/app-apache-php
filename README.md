# App Apache PHP

## Overview

This project is a basic application with Apache and PHP made to test apache's metric exports methods.

Besides it contains multiple documentation about different topics related to the project. You can find the documentation in the `docs` folder.

- [Export Apache Metrics to Prometheus](docs/export-apache-metrics-to-prometheus.md)
- [Create Laminas Skeleton](docs/create-laminas-skeleton.md)
- [Create ZF Skeleton](docs/create-zf-skeleton.md)
- [Set Up Data](docs/set-up-data.md)

## Pre-requisites

- Have Docker and Docker Compose installed (Tested on version 20.10.23).

- Create .env file with the following variables:

    ```shell
    UID=...
    GID=...
    POSTGRES_USER=...
    POSTGRES_PASSWORD=...
    POSTGRES_DB=...
    ```

    Add your user and group id to the `UID` and `GID` variables respectively (you can use `id -u` to get your user id and `id -g` to get your group id).

- Create docker volume for postgres data:

    ```shell
    docker volume create postgres-data
    ```

## First Steps

After cloning the repository, you need to install the dependencies. For this you have two options:

### Install Dependencies With PHP on Your System

```shell
php composer.phar install
```

### Install Dependencies With PHP on Docker

1. Start docker-compose

    ```shell
    docker compose up -d
    ```

2. Access the container

    ```shell
    docker exec -it -u user:user app-php bash
    ```

3. Install dependencies

    ```shell
    php composer.phar install
    ```

### Set up Data Storage

Refer to the [Set up Data Storage](docs/set-up-data-storage.md) documentation for detailed instructions on configuring and managing data storage for your project. It covers PostgreSQL initialization and other details.

## Development

### Run The Application

For this use docker-compose:

```shell
docker compose up -d
```

After this, you can access the application on `http://localhost:8080`

### Install or Update Dependencies

```shell
# install dependencies based in composer.lock
php composer.phar install

# Or

# update dependencies based in composer.json
php composer.phar update 
```

### Install Node Dependencies

```shell
cd frontend
npm ci
```

### Make Changes to the Dockerfile

If you make any changes to the Dockerfile, you need to rebuild the image. To do it and start the containers, use the following command:

```shell
docker compose up -d --build
```

### Access the Container

To access the container, use the following command:

```shell
docker exec -it -u user:user app-php bash
```

This command will start an interactive shell session (bash) in the container named app-php with the user user:user. The -u option specifies the user and group IDs that the container process should run as.

If you want to access the container as root, use the following command:

```shell
docker exec -it app-php bash
```

### Execute psql Commands From Running Container

```shell
docker exec -it postgres psql -U postgres
```

Replace `-U postgres` with the user configured for the database.

Alternatively, you can access the container and execute the command from there:

```shell
docker exec -it postgres bash
```

### Configure Apache

The apache configuration is located in `httpd.conf` file. Reload the container to apply the changes:

```shell
docker compose restart app-php
```

### Laminas Development Mode

This functionality comes with [`laminas-development-mode`](https://github.com/laminas/laminas-development-mode)

```shell
php composer.phar development-enable  # enable development mode
php composer.phar development-disable # disable development mode
php composer.phar development-status  # whether or not development mode is enabled
```

You may provide development-only modules and bootstrap-level configuration in `config/development.config.php.dist`, and development-only application configuration in `config/autoload/development.local.php.dist`. Enabling development mode will copy these files to versions removing the `.dist` suffix, while disabling development mode will remove those copies.

After making changes to one of the above-mentioned `.dist` configuration files you will either need to disable then enable development mode for the changes to take effect, or manually make matching updates to the `.dist`-less copies of those files.

## References

- [how to avoid permission errors](https://vsupalov.com/docker-shared-permissions/)
- [laminas-skeleton repository](https://github.com/laminas/laminas-mvc-skeleton#readme)
- [docker exec documentation](https://docs.docker.com/engine/reference/commandline/exec/)

### Laminas Service Manager

- [Configure service manager](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/#aliases).

### Database Config

- [laminas-db documentation](https://docs.laminas.dev/tutorials/db-adapter/)
- [laminas-db adapter documentation](https://docs.laminas.dev/laminas-db/adapter/).
- [Configure adapter](https://docs.laminas.dev/tutorials/db-adapter/#configuring-the-default-adapter).
- [Create models in laminas](https://docs.laminas.dev/tutorials/getting-started/database-and-models/).
- [Store data with postgres](https://github.com/docker-library/docs/blob/master/postgres/README.md#where-to-store-data).

### Apache Config

- [Apache Exporter Repository](https://github.com/Lusitaniae/apache_exporter).
- [Apache mod_status documentation](https://httpd.apache.org/docs/2.4/mod/mod_status.html).
- [Apache Access Control](https://httpd.apache.org/docs/2.4/en/howto/access.html).
- [Upgrade Apache access from 2.2 to 2.4](https://httpd.apache.org/docs/2.4/upgrading.html#access).

- [Prometheus: Apache Exporter – Install and Config – Ubuntu, CentOS](https://www.shellhacks.com/prometheus-apache-exporter-install-config-ubuntu-centos/).
- [Monitor Apache Web Server with Prometheus and Grafana in 5 minutes](https://computingforgeeks.com/monitor-apache-web-server-prometheus-grafana/).
- [One exporter for each Apache instance](https://github.com/Lusitaniae/apache_exporter/issues/37#issuecomment-357419581).
- [Information about Kubernetes use case](https://github.com/Lusitaniae/apache_exporter/issues/37#issuecomment-1224380326)

- [Run Multiple Services in a Container](https://docs.docker.com/config/containers/multi-service_container/).
- [php-apache Dockerfile](https://github.com/docker-library/php/blob/master/8.2/bullseye/apache/Dockerfile)

- [RewriteRule to ignore server-status](https://serverfault.com/a/388457).
- [Allow all IPs in Apache](https://stackoverflow.com/a/19588786).

### Node

- [Use custom non-root user](https://github.com/nodejs/docker-node/blob/main/docs/BestPractices.md#non-root-user).
- [Webpack sass-loader](https://webpack.js.org/loaders/sass-loader/)
- [Dart Sass](https://sass-lang.com/dart-sass/)
- [Relative SASS routes in Webpack](https://github.com/bholloway/resolve-url-loader/blob/v5/packages/resolve-url-loader/README.md#configure-webpack)
- [Import Boostrap with Webpack](https://getbootstrap.com/docs/5.2/getting-started/webpack/)
- [Sass directory structure](https://www.webdesignerdepot.com/2020/12/2-smartest-ways-to-structure-sass/)
