CREATE TABLE messages (
    "MessageID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "SenderID" int NOT NULL,
    "ReceiverID" int NOT NULL,
    "MessagesContent" text,
    "SentAt" date NOT NULL,
    FOREIGN KEY (SenderID) REFERENCES users(UserID),
    FOREIGN KEY (ReceiverID) REFERENCES users(UserID)
);