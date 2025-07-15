CREATE TABLE categories (
    "CategoriesID" int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    "Name" varchar(256) UNIQUE NOT NULL,
    "Description" text NOT NULL
);