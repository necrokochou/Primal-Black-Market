CREATE TABLE users (
    "UserID" int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    "Username" varchar(256) UNIQUE NOT NULL,
    "Password" varchar(256) NOT NULL,
    "Alias" varchar(256) NOT NULL,
    "TrustLevel" real DEFAULT 0,
    "IsVendor" boolean DEFAULT FALSE
);