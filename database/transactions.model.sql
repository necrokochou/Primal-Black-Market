    CREATE TABLE transactions (
        Transaction_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(), --Primary key--
        Buyer_ID uuid NOT NULL,
        Listing_ID uuid NOT NULL,
        Quantity int NOT NULL,
        Total_Price int NOT NULL,
        Transaction_Status varchar(30) NOT NULL,
        Timestamp date NOT NULL,
        FOREIGN KEY (Buyer_ID) REFERENCES users(User_ID),
        FOREIGN KEY (Listing_ID) REFERENCES listings(Listing_ID)
    );