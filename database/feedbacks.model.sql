CREATE TABLE feedback (
    "FeedbackID" uuid PRIMARY KEY DEFAULT gen_random_uuid(),
    "ReviewerID" uuid NOT NULL,
    "VendorID" uuid NOT NULL,
    "Rating" int CHECK ("Rating" BETWEEN 0 AND 5),
    "Comments" text NOT NULL,
    "PostedAt" date NOT NULL
    FOREIGN KEY (ReviewerID) REFERENCES users(UserID),
    FOREIGN KEY (VendorID) REFERENCES users(UserID)
);