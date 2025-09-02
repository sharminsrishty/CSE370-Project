-- Database schema for User, Student, Alumni, Admin, Chat, Event system
-- Make sure to create the database first:
-- CREATE DATABASE university_portal;
-- USE university_portal;

-- ========================
-- Table: User
-- ========================
CREATE TABLE User (
    User_id INT AUTO_INCREMENT,
    User_mail VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Approve BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (User_id)
);

-- ========================
-- Table: Student
-- ========================
CREATE TABLE Student (
    User_id INT PRIMARY KEY,
    User_name VARCHAR(100),
    Department VARCHAR(100),
    FOREIGN KEY (User_id) REFERENCES User(User_id)
);

-- ========================
-- Table: Student Verification
-- ========================
CREATE TABLE Student_Verification (
    Verified_id INT AUTO_INCREMENT PRIMARY KEY,
    Std_id INT NOT NULL,
    Admin_Username VARCHAR(100),
    Verification_date DATE,
    Student_username VARCHAR(100),
    Approve_state BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (Std_id) REFERENCES Student(User_id)
);

-- ========================
-- Table: Alumni
-- ========================
CREATE TABLE Alumni (
    User_id INT PRIMARY KEY,
    User_name VARCHAR(100),
    Passing_year YEAR,
    Date_of_birth DATE,
    Session VARCHAR(50),
    Designation VARCHAR(100),
    Job_location VARCHAR(150),
    FOREIGN KEY (User_id) REFERENCES User(User_id)
);

-- ========================
-- Table: Alumni Verification
-- ========================
CREATE TABLE Alumni_Verification (
    Verified_id INT AUTO_INCREMENT PRIMARY KEY,
    Alm_id INT NOT NULL,
    Verification_date DATE,
    Alumni_username VARCHAR(100),
    Approve_state BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (Alm_id) REFERENCES Alumni(User_id)
);

-- ========================
-- Table: Admin
-- ========================
CREATE TABLE Admin (
    Admin_id INT AUTO_INCREMENT PRIMARY KEY,
    Admin_mail VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Username VARCHAR(100) NOT NULL
);

INSERT INTO Admin (Admin_mail, Password, Username) VALUES
('admin1@bracu.com', '1', 'Admin1'),
('admin2@bracu.com', '2', 'Admin2'),
('admin3@bracu.com', '3', 'Admin3'),
('admin4@bracu.com', '4', 'Admin4'),
('admin5@bracu.com', '5', 'Admin5'),
('admin6@bracu.com', '6', 'Admin6');

-- ========================
-- Table: Admin Verify
-- ========================
CREATE TABLE Admin_verify (
    Admin_id INT,
    Alm_verified_id INT NULL,
    Std_verified_id INT NULL,
    FOREIGN KEY (Admin_id) REFERENCES Admin(Admin_id),
    FOREIGN KEY (Alm_verified_id) REFERENCES Alumni_Verification(Verified_id),
    FOREIGN KEY (Std_verified_id) REFERENCES Student_Verification(Verified_id)
);

-- ========================
-- Table: Chat_history
-- ========================
CREATE TABLE Chat_history (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Message TEXT NOT NULL,
    Sender_username VARCHAR(100) NOT NULL,
    Receiver_username VARCHAR(100) NOT NULL,
    Time_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================
-- Table: Access
-- ========================
CREATE TABLE Access (
    Chat_id INT,
    User_id INT,
    FOREIGN KEY (Chat_id) REFERENCES Chat_history(Id),
    FOREIGN KEY (User_id) REFERENCES User(User_id),
    PRIMARY KEY (Chat_id, User_id)
);

-- ========================
-- Table: Event
-- ========================
CREATE TABLE Event (
    Id INT AUTO_INCREMENT UNIQUE,
    User_id INT NOT NULL,
    User_mail VARCHAR(100) NOT NULL,
    Event_creator VARCHAR(100),
    Event_description TEXT,
    Event_name VARCHAR(150),
    Start_time DATETIME,
    End_time DATETIME,
    PRIMARY KEY (Id),
    FOREIGN KEY (User_id) REFERENCES User(User_id),
    FOREIGN KEY (User_mail) REFERENCES User(User_mail)
);

-- ========================
-- Table: Event Verification
-- ========================
CREATE TABLE Event_verification (
    Event_verification_id INT AUTO_INCREMENT PRIMARY KEY,
    Event_id INT NOT NULL,
    Admin_username VARCHAR(100),
    Verification_date DATE,
    FOREIGN KEY (Event_id) REFERENCES Event(Id)
);

-- ========================
-- Table: Event Verification Request
-- ========================
CREATE TABLE Event_verification_request (
    Admin_id INT,
    Event_verification_id INT,
    FOREIGN KEY (Admin_id) REFERENCES Admin(Admin_id),
    FOREIGN KEY (Event_verification_id) REFERENCES Event_verification(Event_verification_id),
    PRIMARY KEY (Admin_id, Event_verification_id)
);
