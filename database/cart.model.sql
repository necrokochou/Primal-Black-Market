CREATE TABLE cart (
    Cart_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    User_ID uuid NOT NULL,
    Listing_ID uuid NOT NULL,
    Quantity int NOT NULL,
    Added_At timestamp DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (User_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Listing_ID) REFERENCES listings(Listing_ID)
);
