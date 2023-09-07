#!/bin/bash

LOCAL_PROYECT_PATH=$(dirname $0)/..

LOCAL_PG_DATA=$LOCAL_PROYECT_PATH/pg_data 

CONTAINER_NAME=postgres

CONTAINER_PG_DATA=/var/lib/postgresql/data/pg_data
CONTAINER_CREATE_TABLE_SCRIPT=$CONTAINER_PG_DATA/create-table.sql
CONTAINER_LOAD_DATA_SCRIPT=$CONTAINER_PG_DATA/load-data.sql
CONTAINER_UPDATE_TABLE_SCRIPT=$CONTAINER_PG_DATA/update-table.sql

source $LOCAL_PROYECT_PATH/.env

if [ "$(docker ps -q -f name=$CONTAINER_NAME)" ]; then

    docker cp $LOCAL_PG_DATA $CONTAINER_NAME:$CONTAINER_PG_DATA

    docker exec $CONTAINER_NAME \
        psql -U $POSTGRES_USER \
        -d $POSTGRES_DB \
        -f $CONTAINER_CREATE_TABLE_SCRIPT

    docker exec $CONTAINER_NAME \
        psql -U $POSTGRES_USER \
        -d $POSTGRES_DB \
        -f $CONTAINER_LOAD_DATA_SCRIPT

    docker exec $CONTAINER_NAME \
        psql -U $POSTGRES_USER \
        -d $POSTGRES_DB \
        -f $CONTAINER_UPDATE_TABLE_SCRIPT

else
    echo "Container $CONTAINER_NAME is not running"
fi
