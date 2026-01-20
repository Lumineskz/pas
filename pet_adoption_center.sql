-- Pet Adoption Center Database Schema
-- This should be run in phpMyAdmin or your MySQL client

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `pet_adoption_center` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pet_adoption_center`;

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create pets table
CREATE TABLE IF NOT EXISTS `pets` (
  `pet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255),
  `status` enum('available','adopted') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create adoption_requests table (UPDATED STRUCTURE)
CREATE TABLE IF NOT EXISTS `adoption_requests` (
  `request_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `adopter_name` varchar(100),
  `adopter_age` int(11),
  `adopter_email` varchar(100),
  `adopter_phone` varchar(15),
  `adoption_reason` text,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add Indexes
ALTER TABLE `adoption_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `owner_id` (`owner_id`);

ALTER TABLE `pets`
  ADD PRIMARY KEY (`pet_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

-- Add AUTO_INCREMENT
ALTER TABLE `adoption_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `pets`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

-- Add Foreign Keys
ALTER TABLE `adoption_requests`
  ADD CONSTRAINT `adoption_requests_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`pet_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adoption_requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adoption_requests_ibfk_3` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
