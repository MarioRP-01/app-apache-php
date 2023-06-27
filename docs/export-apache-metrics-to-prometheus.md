# Export Apache Metrics to Prometheus

## Introduction

This documentation provides a step-by-step guide on how to export Apache metrics to Prometheus when running Apache server inside a container. It explains two methods to export the metrics through apache_exporter: adding apache_exporter to the Dockerfile or running apache_exporter in its own container.

## Prerequisites

1. Install Docker

## Exporting Apache Metrics Through Apache Exporter in Different Containers

### Step 1: Publish Apache metrics

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

   If the Apache version is 2.2, use the following lines instead. [More here]([Title](https://httpd.apache.org/docs/2.4/upgrading.html#access))

   ```conf
   <Location "/server-status">
      RewriteRule ^/server-status - [L]
      SetHandler server-status
      Order allow,deny
      Allow from all
   ```

   Both configuration allows access to the `/server-status` URL from any IP address.

### Step 2: Add apache_exporter to the `docker-compose.yml` file.

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

### Step 3: Configure Prometheus

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

## References

- [Apache Exporter Repository](https://github.com/Lusitaniae/apache_exporter)
- [Apache mod_status documentation](https://httpd.apache.org/docs/2.4/mod/mod_status.html)
- [Override RewriteRule to ignore server-status](https://serverfault.com/a/388457)

- [Apache 2.2 to 2.4 upgrade guide- access](https://httpd.apache.org/docs/2.4/upgrading.html#access)
- [Allow all IPs in Apache](https://stackoverflow.com/a/19588786).