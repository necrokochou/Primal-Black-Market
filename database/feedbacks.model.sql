CREATE TABLE feedbacks (
    "FeedbackID" int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    "ReviewerID" int NOT NULL,
    "VendorID" int NOT NULL,
    "Rating" int CHECK ("Rating" BETWEEN 0 AND 5),
    "Comments" text NOT NULL,
    "PostedAt" date NOT NULL
    FOREIGN KEY (ReviewerID) REFERENCES users(UserID),
    FOREIGN KEY (VendorID) REFERENCES users(UserID)
);