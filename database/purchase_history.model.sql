CREATE TABLE purchase_history (
    Purchase_History_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    User_ID uuid NOT NULL,
    Listing_ID uuid NOT NULL,
    Transaction_ID uuid NOT NULL,
    Quantity integer NOT NULL CHECK (Quantity > 0),
    Price_Each decimal(10,2) NOT NULL CHECK (Price_Each >= 0),
    Total_Price decimal(10,2) NOT NULL CHECK (Total_Price >= 0),
    Purchase_Date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Payment_Method varchar(50) NOT NULL,
    Delivery_Status varchar(50),
    Notes text,
    FOREIGN KEY (User_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Listing_ID) REFERENCES listings(Listing_ID),
    FOREIGN KEY (Transaction_ID) REFERENCES transactions(Transaction_ID)
);
