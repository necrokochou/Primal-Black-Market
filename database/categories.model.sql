CREATE TABLE categories (
    Categories_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Name_ varchar(256) UNIQUE NOT NULL,
    Description_ text NOT NULL
);