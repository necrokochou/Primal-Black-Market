CREATE TABLE messages (
    Message_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Sender_ID uuid NOT NULL,
    Receiver_ID uuid NOT NULL,
    Messages_Content text,
    Sent_At date NOT NULL,
    FOREIGN KEY (Sender_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Receiver_ID) REFERENCES users(User_ID)
);