-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 01, 2024 at 02:28 PM
-- Server version: 8.0.39
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taxi_rental_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `driver_id` int NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `dropoff_location` varchar(255) NOT NULL,
  `booking_status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `booking_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cash` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `driver_id`, `pickup_location`, `dropoff_location`, `booking_status`, `booking_date`, `cash`) VALUES
(5, 46, 2, 'A', 'B', 'pending', '2024-10-20 11:50:45', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `cabs`
--

CREATE TABLE `cabs` (
  `id` int NOT NULL,
  `driver_id` int NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `capacity` int NOT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `price_per_km` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `availability` enum('Available','On Duty','On Leave','Vehicle Under Maintenance') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cabs`
--

INSERT INTO `cabs` (`id`, `driver_id`, `model`, `plate_number`, `capacity`, `fuel_type`, `price_per_km`, `created_at`, `availability`) VALUES
(1, 1, 'Toyota Innova Crysta', 'KL123', 6, 'diesel', 250.00, '2024-10-12 15:05:48', 'Available'),
(2, 2, 'Toyota Etios', 'KL456', 5, 'petrol', 150.00, '2024-10-12 15:05:48', 'Available'),
(3, 7, 'Tata Indica', 'KL644', 5, 'petrol', 100.00, '2024-10-15 14:55:52', 'Available'),
(14, 1, 'Toyota Camry', 'KL567', 5, 'petrol', 200.00, '2024-10-18 05:45:17', 'Available'),
(15, 1, 'Hyundai Elantra', 'KL678', 5, 'diesel', 180.00, '2024-10-18 05:45:17', 'Available'),
(16, 2, 'BMW 530d', 'KL789', 4, 'diesel', 300.00, '2024-10-18 05:45:17', 'Available'),
(23, 7, 'Audi A4', 'KL105', 4, 'hybrid', 250.00, '2024-10-18 05:45:17', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `created_at`, `updated_at`, `username`, `password`) VALUES
(5, 'Anil Mehta', 'anil.mehta@example.com', '9567890123', 'Ahmedabad, India', '2024-10-20 03:54:50', '2024-10-20 03:54:50', 'customer1', 'password123'),
(45, 'Ravi Kumar', 'ravi.kumar@example.com', '9876543210', '123 Street, Bangalore', '2024-10-20 04:15:56', '2024-10-20 04:20:17', 'ravi_kumar', 'password123'),
(46, 'Priya Sharma', 'priya.sharma@example.com', '9876543211', '456 Avenue, Mumbai', '2024-10-20 04:15:56', '2024-10-20 04:20:17', 'priya_sharma', 'password456'),
(53, 'abc Name', 'abc@example.com', '9999999999', 'Unknown Address', '2024-10-20 06:41:16', '2024-10-20 06:41:16', 'abc', 'abc'),
(77, 'Abil Biju', 'abilbiju2004@gmail.com', '09745346436', 'Vanchipurackal(H), Palakara, Kaduthuruthy(P.O)', '2024-10-20 06:41:16', '2024-10-20 06:41:16', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `email`, `phone`, `license_number`, `username`, `password`) VALUES
(1, 'John Doe', 'john.doe@example.com', '9445750231', 'ABC123456', 'johndoe', 'password1'),
(2, 'Jane Smith', 'jane.smith@example.com', '0987654321', 'XYZ987654', 'janesmith', 'password2'),
(7, 'Alice Johnson', 'alice.johnson@example.com', '5551234567', 'LMN543210', 'alicej', 'password3'),
(9, 'MichaelBrown', 'email@example.com', '1234567890', 'LIC123456', 'MichaelBrown', 'password4'),
(30, 'sample', 'abc@gamil.com', '6465488464', 'KX124242', 'sample', 'abc');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','driver','renter','customer') NOT NULL,
  `local_area` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `local_area`, `gender`, `created_at`) VALUES
(1, 'johndoe', 'password1', 'driver', 'Mumbai', 'male', '2024-10-12 15:05:47'),
(2, 'janesmith', 'password2', 'driver', 'Chennai', 'female', '2024-10-12 15:05:47'),
(5, 'customer1', 'password123', 'customer', 'Mumbai', 'male', '2024-10-12 15:05:48'),
(6, 'admin', 'admin123', 'admin', 'Admin Area', 'male', '2024-10-12 15:33:04'),
(7, 'alicej', 'password3', 'driver', 'Bangalore', 'male', '2024-10-15 14:53:22'),
(9, 'MichaelBrown', 'password4', 'driver', 'Delhi', 'male', '2024-10-18 06:11:25'),
(30, 'sample', 'abc', 'driver', 'Delhi', 'male', '2024-10-18 13:24:19'),
(45, 'ravi_kumar', 'password123', 'customer', 'Bangalore', 'male', '2024-10-20 04:15:46'),
(46, 'priya_sharma', 'password456', 'customer', 'Mumbai', 'female', '2024-10-20 04:15:46'),
(77, 'abc', 'abc', 'customer', NULL, 'male', '2024-10-20 06:41:16');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.role = 'driver' THEN
        INSERT INTO drivers (id, name, email, phone, license_number, username, password)
        VALUES (NEW.id, NEW.username, 'email@example.com', '1234567890', 'LIC123456', NEW.username, NEW.password);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `insert_customer_trigger` AFTER INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.role = 'customer' THEN
        INSERT INTO customers (username, password, name, email, phone, address)
        VALUES (NEW.username, NEW.password, CONCAT(NEW.username, ' Name'), CONCAT(NEW.username, '@example.com'), '9999999999', 'Unknown Address');
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `cabs`
--
ALTER TABLE `cabs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cabs`
--
ALTER TABLE `cabs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cabs`
--
ALTER TABLE `cabs`
  ADD CONSTRAINT `cabs_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
