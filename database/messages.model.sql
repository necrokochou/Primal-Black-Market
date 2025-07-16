CREATE TABLE messages (
    "MessageID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "SenderID" uuid NOT NULL,
    "ReceiverID" uuid NOT NULL,
    "MessagesContent" text,
    "SentAt" date NOT NULL,
    FOREIGN KEY (SenderID) REFERENCES users(UserID),
    FOREIGN KEY (ReceiverID) REFERENCES users(UserID)
);