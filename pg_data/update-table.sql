-- add uuid extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- clothing
ALTER TABLE suse_clothing
ALTER COLUMN UUID SET DEFAULT uuid_generate_v4 ();

ALTER TABLE suse_clothing
DROP COLUMN IF EXISTS kids;

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

-- images

CREATE TABLE suse_image (
    id SERIAL,
    filename VARCHAR(255) NOT NULL UNIQUE,
    imageable_type VARCHAR(255) NOT NULL,
    imageable_id UUID NOT NULL REFERENCES suse_clothing(uuid) ON DELETE CASCADE,
    PRIMARY KEY (id)
);

INSERT INTO suse_image (filename, imageable_type, imageable_id)
SELECT 
    'images/' || uuid || '.jpg' AS filename,
    CAST('suse_clothing' AS text) AS imageable_type,
    uuid AS imageable_id
FROM suse_clothing;
