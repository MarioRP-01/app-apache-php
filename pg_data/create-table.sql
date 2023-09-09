CREATE TABLE IF NOT EXISTS SUSE_CLOTHING (
    uuid UUID,
    name VARCHAR(255),
    brand VARCHAR(255),
    gender VARCHAR(255),
    price NUMERIC,
    description VARCHAR(4095),
    primary_color VARCHAR(255),
    label VARCHAR(255),
    size VARCHAR(10),
    PRIMARY KEY (uuid)
);
