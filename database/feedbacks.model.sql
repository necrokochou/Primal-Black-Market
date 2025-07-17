CREATE TABLE feedbacks (
    Feedback_ID uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    Reviewer_ID uuid NOT NULL,
    Vendor_ID uuid NOT NULL,
    Rating int CHECK (Rating BETWEEN 0 AND 5),
    Comments text NOT NULL,
    Posted_At date NOT NULL,
    FOREIGN KEY (Reviewer_ID) REFERENCES users(User_ID),
    FOREIGN KEY (Vendor_ID) REFERENCES users(User_ID)
);