# Export Apache Metrics to Prometheus

## Introduction

This documentation provides a step-by-step guide on how to export Apache metrics to Prometheus when running Apache server inside a container. It explains two methods to export the metrics through apache_exporter: adding apache_exporter to the Dockerfile or running apache_exporter in its own container.

## Prerequisites

1. Install Docker

## Exporting Apache Metrics Through Apache Exporter in Different Containers

### Step 1: Publish Apache metrics (Allow all sources)

1. Add volume to modify the Apache configuration file in the `docker-compose.yml` file.

   ```yml
   services:
      app:
         ...
         volumes:
            ...
            - ./httpd.conf:/etc/apache2/sites-available/000-default.conf:ro
         ...
   ```

2. Add the following lines to the Apache configuration file `httpd.conf` to publish the metrics.

   ```conf
   <Location "/server-status">
      RewriteRule ^/server-status - [L]
      SetHandler server-status
      Require all granted
   </Location>
   ```

   - `RewriteRule ^/server-status - [L]`: tells the server to stop processing any rules for requests to `/server-status`. [More here](https://serverfault.com/a/388457).
   - `SetHandler`: tells the server to use a specific handler to process requests to the `/server-status` URL. [More here](https://httpd.apache.org/docs/2.4/mod/mod_status.html#sethandler).
   - `Require all granted`: allows unrestricted access to the specified resource for all users or clients.

   If the Apache version is 2.2, use the following lines instead. [More here](https://httpd.apache.org/docs/2.4/upgrading.html#access)

   ```conf
   <Location "/server-status">
      RewriteRule ^/server-status - [L]
      SetHandler server-status
      Order allow,deny
      Allow from all
   ```

   Both configuration allows access to the `/server-status` URL from any IP address.

### Step 2: Add apache_exporter to the `docker-compose.yml` file

   ```yml
   services:
      ...
      apache_exporter:
         image: lusotycoon/apache-exporter
         container_name: apache_exporter
         restart: always
         ports:
         - '9117:9117/tcp'
         command:
            - '--scrape_uri=http://app_php/server-status?auto'
         depends_on:
         - app_php
   ```

   This configuration tells docker-compose to run apache_exporter in its own container. The `command` option tells apache_exporter to scrape the metrics from the `/server-status?auto` URL.

### Step 3: Configure Prometheus (scrape apache_exporter)

1. Add Prometheus to the `docker-compose.yml` file.

   ```yml
   prometheus:
      image: 'prom/prometheus:v2.43.0'
      container_name: prometheus
      restart: always
      ports:
         - '9090:9090'
      command:
         - '--config.file=/etc/prometheus/prometheus.yml'
      volumes:
         - './prometheus.yml:/etc/prometheus/prometheus.yml'
      depends_on:
         - apache_exporter
   ```

2. Create a `prometheus.yml` file in the same directory as the `docker-compose.yml` file.

   ```yml
   global:
      scrape_interval: 15s

   scrape_configs:
   - job_name: "apache_exporter"
      honor_labels: true
      static_configs:
         - targets: ["apache_exporter:9117"]
         labels:
            alias: 'apache_exporter'
   ```

   This configuration creates a job named `apache_exporter` that scrapes the metrics from the `apache_exporter` container at port `9117`.

## Exporting Apache Metrics Through Apache Exporter in the Same Containers

### Step 1: Publish Apache Metrics (Allow only localhost)

1. Add volume to modify the Apache configuration file in the `docker-compose.yml` file.

   ```yml
   services:
      app:
         ...
         volumes:
            ...
            - ./httpd.conf:/etc/apache2/sites-available/000-default.conf:ro
         ...
   ```

2. Add the following lines to the Apache configuration file `httpd.conf` to publish the metrics.

   ```conf
   <Location "/server-status">
      RewriteRule ^/server-status - [L]
      SetHandler server-status
      Require host localhost
   </Location>
   ```

   - `RewriteRule ^/server-status - [L]`: tells the server to stop processing any rules for requests to `/server-status`. [More here](https://serverfault.com/a/388457).
   - `SetHandler`: tells the server to use a specific handler to process requests to the `/server-status` URL. [More here](https://httpd.apache.org/docs/2.4/mod/mod_status.html#sethandler).
   - `Require host localhost`: allows access to the resources inside the container.

   If the Apache version is 2.2, use the following lines instead. [More here](https://httpd.apache.org/docs/2.4/upgrading.html#access)

   ```conf
   <Location "/server-status">
      RewriteRule ^/server-status - [L]
      SetHandler server-status
      Order deny,allow
      Deny from all
      Allow from localhost
   ```

### Step 2: Add apache_exporter installation to the `Dockerfile`

Anywhere after the `apt-get update` command, add the following lines.

```dockerfile
## Apache Exporter
RUN apt-get install -y wget
RUN curl -s https://api.github.com/repos/Lusitaniae/apache_exporter/releases/latest \
    | grep browser_download_url \
    | grep linux-amd64 \
    | cut -d '"' -f 4 \
    | wget -qi -
RUN tar xvf apache_exporter-*.linux-amd64.tar.gz
RUN rm apache_exporter-*.linux-amd64.tar.gz
RUN mv apache_exporter-*.linux-amd64/apache_exporter /usr/bin
RUN chmod +x /usr/bin/apache_exporter
```

This commands download the latest version of apache_exporter binaries from github and install it in the `/usr/bin` directory.

### Step 3: Create script to run apache_exporter and Apache in the same container

1. Create a `docker-entrypoint.sh` file in the same directory as the `Dockerfile`.

   ```shell
   # Start Apache
   /usr/local/bin/apache2-foreground &

   # Start apache_exporter
   /usr/bin/apache_exporter --scrape_uri=http://localhost/server-status &

   # Wait for any process to exit
   wait -n

   # Exit with status of process that exited first
   exit $?
   ```

   `apache2-foreground` is the script that starts Apache in the `php-apache` image. [More here](https://github.com/docker-library/php/blob/master/8.2/bullseye/apache/Dockerfile)

2. Change the EntryPoint in the `Dockerfile` to run the script.

   ```dockerfile
   ## Execute both apache and apache exporter
   COPY ./docker-entrypoint.sh /usr/local/bin/

   ENTRYPOINT ["docker-entrypoint.sh"]
   ```

Although it's best to separate areas of concern by using one service per container, it's also possible to run multiple services in a single container. The code above it's one way to do it. You can check another options [here](https://docs.docker.com/config/containers/multi-service_container/).

### Step 4: Configure Prometheus (scrape php-apache container)

1. Add Prometheus to the `docker-compose.yml` file.

   ```yml
      services:
         ...
         prometheus:
            image: 'prom/prometheus:v2.43.0'
            container_name: prometheus
            restart: always
            ports:
               - '9090:9090'
            command:
               - '--config.file=/etc/prometheus/prometheus.yml'
            volumes:
               - './prometheus.yml:/etc/prometheus/prometheus.yml'
            ...
   ```

2. Create a `prometheus.yml` file in the same directory as the `docker-compose.yml` file.

   ```yml
   global:
      scrape_interval: 15s

   scrape_configs:
   - job_name: "apache"
      honor_labels: true
      static_configs:
         - targets: ["app_php:9117"]
         labels:
            alias: 'apache'
   ```

   This configuration creates a job named `apache_exporter` that scrapes the metrics from the `apache_exporter` container at port `9117`.

## References

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
