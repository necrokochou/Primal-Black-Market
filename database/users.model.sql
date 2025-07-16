CREATE TABLE users (
    "UserID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "Username" varchar(256) UNIQUE NOT NULL,
    "Password" varchar(256) NOT NULL,
    "Email" varchar(256) NOT NULL,
    "Alias" varchar(256) NOT NULL,
    "TrustLevel" real DEFAULT 0,
    "IsVendor" boolean DEFAULT FALSE
);