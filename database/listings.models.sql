CREATE TABLE listings (
    "ListingID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "VendorID" uuid NOT NULL,
    "CategoriesID" uuid NOT NULL,
    "Title" varchar(256) NOT NULL,
    "Description" text NOT NULL,
    "Category" varchar(100) NOT NULL,
    "Price" real NOT NULL,
    "Quantity" int NOT NULL,
    "IsActive" boolean DEFAULT TRUE,
    "PublishDate" date NOT NULL,
    FOREIGN KEY ("VendorID") REFERENCES users("UserID")
    FOREIGN KEY ("CategoriesID") REFERENCES categories("CategoriesID")
);