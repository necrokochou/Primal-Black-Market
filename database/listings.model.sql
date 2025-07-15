CREATE TABLE listings (
    "ListingID" int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    "VendorID" int NOT NULL,
    "Title" varchar(256) NOT NULL,
    "Description" text NOT NULL,
    "Category" varchar(100) NOT NULL,
    "Price" real NOT NULL,
    "Quantity" int NOT NULL,
    "IsActive" boolean DEFAULT TRUE,
    "PublishDate" date NOT NULL,
    FOREIGN KEY (VendorID) REFERENCES users(UserID)
);