-- Create database
CREATE DATABASE indigo_airlines;
USE indigo_airlines;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Flights table
CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(10) NOT NULL,
    origin VARCHAR(50) NOT NULL,
    destination VARCHAR(50) NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    available_seats INT NOT NULL,
    status ENUM('On Time', 'Delayed', 'Cancelled', 'Landed') DEFAULT 'On Time'
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pnr VARCHAR(6) NOT NULL UNIQUE,
    user_id INT,
    flight_id INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Confirmed', 'Cancelled', 'Checked In') DEFAULT 'Confirmed',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (flight_id) REFERENCES flights(id)
);

-- Passengers table
CREATE TABLE passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    age INT NOT NULL,
    seat_number VARCHAR(5),
    checked_in BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Insert sample flights
INSERT INTO flights (flight_number, origin, destination, departure_time, arrival_time, price, available_seats, status) VALUES
('6E 123', 'Delhi', 'Mumbai', '2023-05-01 08:00:00', '2023-05-01 10:00:00', 3500.00, 120, 'On Time'),
('6E 124', 'Mumbai', 'Delhi', '2023-05-01 11:00:00', '2023-05-01 13:00:00', 3500.00, 120, 'On Time'),
('6E 456', 'Mumbai', 'Bangalore', '2023-05-01 12:00:00', '2023-05-01 14:00:00', 4200.00, 100, 'On Time'),
('6E 457', 'Bangalore', 'Mumbai', '2023-05-01 15:00:00', '2023-05-01 17:00:00', 4200.00, 100, 'On Time'),
('6E 789', 'Bangalore', 'Chennai', '2023-05-01 16:00:00', '2023-05-01 17:30:00', 2800.00, 150, 'On Time'),
('6E 790', 'Chennai', 'Bangalore', '2023-05-01 18:00:00', '2023-05-01 19:30:00', 2800.00, 150, 'On Time'),
('6E 234', 'Chennai', 'Kolkata', '2023-05-01 19:00:00', '2023-05-01 21:00:00', 3900.00, 110, 'On Time'),
('6E 235', 'Kolkata', 'Chennai', '2023-05-02 06:00:00', '2023-05-02 08:00:00', 3900.00, 110, 'On Time'),
('6E 567', 'Kolkata', 'Delhi', '2023-05-02 07:00:00', '2023-05-02 09:00:00', 3700.00, 130, 'On Time'),
('6E 568', 'Delhi', 'Kolkata', '2023-05-02 10:00:00', '2023-05-02 12:00:00', 3700.00, 130, 'On Time'),
('6E 901', 'Delhi', 'Bangalore', '2023-05-03 08:00:00', '2023-05-03 11:00:00', 4500.00, 120, 'On Time'),
('6E 902', 'Bangalore', 'Delhi', '2023-05-03 12:00:00', '2023-05-03 15:00:00', 4500.00, 120, 'On Time'),
('6E 903', 'Mumbai', 'Chennai', '2023-05-04 09:00:00', '2023-05-04 11:30:00', 4000.00, 100, 'On Time'),
('6E 904', 'Chennai', 'Mumbai', '2023-05-04 12:30:00', '2023-05-04 15:00:00', 4000.00, 100, 'On Time'),
('6E 905', 'Hyderabad', 'Delhi', '2023-05-05 07:00:00', '2023-05-05 09:30:00', 3800.00, 110, 'On Time'),
('6E 906', 'Delhi', 'Hyderabad', '2023-05-05 10:30:00', '2023-05-05 13:00:00', 3800.00, 110, 'On Time');
