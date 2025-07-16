CREATE TABLE categories (
    "CategoriesID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "Name" varchar(256) UNIQUE NOT NULL,
    "Description" text NOT NULL
);