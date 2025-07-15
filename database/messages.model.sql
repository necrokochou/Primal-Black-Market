CREATE TABLE messages (
    "MessageID" int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    "SenderID" int NOT NULL,
    "ReceiverID" int NOT NULL,
    "SentAt" date NOT NULL,
    FOREIGN KEY (SenderID) REFERENCES users(UserID),
    FOREIGN KEY (ReceiverID) REFERENCES users(UserID)
);