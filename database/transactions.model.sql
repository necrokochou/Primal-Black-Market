    CREATE TABLE transactions (
        "TransactionID" int NOT NULL PRIMARY KEY AUTO_INCREMENT, --Primary key--
        "BuyerID" int NOT NULL,
        "ListingID" int NOT NULL,
        "Quantity" int NOT NULL,
        "TotalPrice" int NOT NULL,
        "TransactionStatus" varchar(30) NOT NULL,
        "Timestamp" date NOT NULL,
        FOREIGN KEY (BuyerID) REFERENCES users(UserID),
        FOREIGN KEY (ListingID) REFERENCES listings(ListingID)
    );