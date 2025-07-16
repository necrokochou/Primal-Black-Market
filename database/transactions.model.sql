    CREATE TABLE transactions (
        "TransactionID" uuid PRIMARY KEY DEFAULT gen_random_uuid(), --Primary key--
        "BuyerID" int NOT NULL,
        "ListingID" int NOT NULL,
        "Quantity" int NOT NULL,
        "TotalPrice" int NOT NULL,
        "TransactionStatus" varchar(30) NOT NULL,
        "Timestamp" date NOT NULL,
        FOREIGN KEY (BuyerID) REFERENCES users(UserID),
        FOREIGN KEY (ListingID) REFERENCES listings(ListingID)
    );