DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM suse_clothing LIMIT 1) THEN
        COPY suse_clothing (uuid, name, brand, gender, price, description, primary_color, label, size, kids)
        FROM '/var/lib/postgresql/data/pg_data/data.csv'
        DELIMITER ',' CSV HEADER;
    END IF;
END 
$$;
