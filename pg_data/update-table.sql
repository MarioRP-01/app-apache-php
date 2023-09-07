-- add uuid extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- clothing
ALTER TABLE SUSE_CLOTHING
ALTER COLUMN UUID SET DEFAULT uuid_generate_v4 ();

-- gender
CREATE TABLE suse_gender (
    id SERIAL,
    name VARCHAR(31) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

INSERT INTO suse_gender (name)
SELECT DISTINCT(gender) FROM suse_clothing;

ALTER TABLE suse_clothing ADD gender_id INTEGER;

ALTER TABLE suse_clothing
ADD CONSTRAINT suse_clothing_gender_id_fkey FOREIGN KEY (gender_id)
REFERENCES suse_gender(id)
ON DELETE SET NULL;

UPDATE suse_clothing AS c
SET gender_id = g.id
FROM suse_gender AS g
WHERE
    c.gender = g.name AND
    g.name IS NOT NULL;

ALTER TABLE suse_clothing DROP gender;

-- size
CREATE TABLE suse_size (
    id SERIAL,
    name VARCHAR(31) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

INSERT INTO suse_size (name)
SELECT DISTINCT(size) FROM suse_clothing WHERE size IS NOT NULL;

ALTER TABLE suse_clothing ADD size_id INTEGER;

ALTER TABLE suse_clothing
ADD CONSTRAINT suse_clothing_size_id_fkey FOREIGN KEY (size_id)
REFERENCES suse_size(id)
ON DELETE SET NULL;

UPDATE suse_clothing AS c
SET size_id = s.id
FROM suse_size AS s
WHERE
    c.size = s.name AND
    s.name IS NOT NULL;

ALTER TABLE suse_clothing DROP size;
