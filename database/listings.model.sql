CREATE TABLE listings (
    Listing_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Vendor_ID uuid NOT NULL,
    Categories_ID uuid NOT NULL,
    Title varchar(256) NOT NULL,
    Description_ text NOT NULL,
    Category varchar(100) NOT NULL,
    Price real NOT NULL,
    Quantity int NOT NULL,
    Is_Active boolean DEFAULT TRUE,
    Publish_Date date NOT NULL,
    FOREIGN KEY (Vendor_ID) REFERENCES users(User_ID)
    FOREIGN KEY (Categories_ID) REFERENCES categories(Categories_ID)
);