# Set up Data Storage

This guide will help you to set up the data storage for the project. It is meant to emulate a real environment, so it will use different strategies for different types of data.

## Dataset

The [clothing-dataset-small](https://github.com/alexeygrigorev/clothing-dataset-small) is the dataset we will be working with in this project. It contains structured data related to clothing items, such as their names, categories, prices, and other relevant information.

You can download the dataset from the repository and place it in the `resources` directory.

```shell
git clone https://github.com/alexeygrigorev/clothing-dataset-small
```

Once you have downloaded the dataset, you can use the following command to get the data into an usable format:

```shell
mkdir data
docker run -it --rm \
    -v $(pwd)/clothing-dataset-small:/clothing-dataset-small:ro \
    -v $(pwd)/data:/data:rw \
    dataset-manager:latest \
    dataset-manager -o clothing-dataset-small -d /data
```

`dataset-manager` is a tool to manage [clothing-dataset-small](https://github.com/alexeygrigorev/clothing-dataset-small). For more details about the usage check the [image](https://hub.docker.com/r/mariorp01/clothing-dataset-small-manager) and the [repository](https://github.com/MarioRP-01/clothing-dataset-small-manager).

Now just move `data/data.csv` to `pg_data` directory and `data/images` to `nfs_server`.

```shell
mv data/data.csv pg_data
mv data/images/* resources
```

And finally, remove the `data` and `clothing-dataset-small` directory.

```shell
rm -rf data clothing-dataset-small
```

## PostgreSQL

PostgreSQL is a powerful open-source relational database management system that is widely used for storing structured data. It provides robust features for data integrity, transaction management, and efficient querying.

To dump the data into the database, we will follow the steps below:ç

1. Create docker volume to persist the data:

    ```shell
    docker volume create postgres-data
    ```

2. Start docker-compose

    ```shell
    docker compose up -d
    ```

3. Run the `init-db.sh` script

    ```shell
    ./bin/init-db.sh
    ```

## Volumes for General File Storage

In this example, we will use a volume for general file storage, although in a real use case, we would prefer an NFS (Network File System) volume for general file storage. NFS is a distributed file system protocol that allows you to share files and directories across a network. It provides a convenient way to store and access files from multiple machines.

In our case, we will create a regular volume changing the `docker-compose.yml` file as follows:

```yaml
services:
    app_php:
        ...
        volumes:
            ...
            - ./resources:/data-nfs
```

## References

- [clothing-dataset-small](https://github.com/alexeygrigorev/clothing-dataset-small).
- [clothing-dataset-small-manager (Repository)](https://github.com/MarioRP-01/clothing-dataset-small-manager).
- [clothing-dataset-small-manager (Image)](https://hub.docker.com/r/mariorp01/clothing-dataset-small-manager).
