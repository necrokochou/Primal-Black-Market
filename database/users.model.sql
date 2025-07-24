CREATE TABLE users (
    User_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Username varchar(256) UNIQUE NOT NULL,
    Password varchar(256) NOT NULL,
    Email varchar(256) NOT NULL,
    Alias varchar(256) NOT NULL,
    TrustLevel real DEFAULT 0,
    Created_At date NOT NULL,
    Is_Vendor boolean DEFAULT FALSE,
    Is_Admin boolean DEFAULT FALSE,
    Is_Banned boolean DEFAULT FALSE
);