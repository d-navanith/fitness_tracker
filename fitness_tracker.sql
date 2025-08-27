-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 07:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fitness_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `bmi_records`
--

CREATE TABLE `bmi_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `weight` decimal(5,2) NOT NULL COMMENT 'in kg',
  `height` decimal(5,2) NOT NULL COMMENT 'in cm',
  `bmi` decimal(4,1) NOT NULL,
  `category` varchar(20) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bmi_records`
--

INSERT INTO `bmi_records` (`id`, `user_id`, `weight`, `height`, `bmi`, `category`, `date`) VALUES
(1, 2, 55.00, 150.00, 24.4, 'Normal weight', '2025-08-16'),
(2, 2, 60.00, 160.00, 23.4, 'Normal weight', '2025-08-18'),
(3, 2, 100.00, 120.00, 69.4, 'Obese', '2025-08-18'),
(4, 7, 62.00, 172.00, 21.0, 'Normal weight', '2025-08-24');

-- --------------------------------------------------------

--
-- Table structure for table `challenges`
--

CREATE TABLE `challenges` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` enum('workout_count','calories_burned','steps_count','custom') NOT NULL,
  `target_value` int(11) NOT NULL,
  `unit` varchar(20) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenges`
--

INSERT INTO `challenges` (`id`, `name`, `description`, `start_date`, `end_date`, `type`, `target_value`, `unit`, `created_by`, `created_at`) VALUES
(1, '30 Day Plank Challenge', 'Plank exercise', '2025-08-01', '2025-08-31', 'calories_burned', 20, 'workout', 2, '2025-08-18 16:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `challenge_participants`
--

CREATE TABLE `challenge_participants` (
  `id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_value` int(11) DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `challenge_participants`
--

INSERT INTO `challenge_participants` (`id`, `challenge_id`, `user_id`, `current_value`, `joined_at`) VALUES
(1, 1, 2, 0, '2025-08-18 16:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `muscle_group` varchar(50) DEFAULT NULL,
  `equipment` varchar(50) DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `name`, `description`, `instructions`, `muscle_group`, `equipment`, `difficulty`, `image_url`) VALUES
(1, 'Push-ups', 'A classic bodyweight exercise that targets the chest, shoulders, and triceps.', '1. Start in a plank position with hands placed slightly wider than shoulder-width apart.\n2. Lower your body until your chest nearly touches the floor.\n3. Push back up to the starting position.', 'chest', 'none', 'beginner', 'images/exercises/pushups.jpg'),
(2, 'Squats', 'A fundamental lower body exercise that targets the quadriceps, hamstrings, and glutes.', '1. Stand with feet shoulder-width apart.\n2. Lower your body by bending your knees and hips.\n3. Go as low as you can comfortably, then push back up.', 'legs', 'none', 'beginner', 'images/exercises/squats.jpg'),
(3, 'Bench Press', 'A compound exercise that primarily targets the chest muscles.', '1. Lie on a bench with your feet flat on the floor.\n2. Grip the barbell with hands slightly wider than shoulder-width.\n3. Lower the bar to your chest, then press it back up.', 'chest', 'barbell', 'intermediate', 'images/exercises/bench_press.jpg'),
(4, 'Deadlift', 'A compound exercise that works multiple muscle groups including the back, legs, and core.', '1. Stand with feet hip-width apart, toes under the bar.\n2. Bend at the hips and knees to grab the bar.\n3. Stand up by extending your hips and knees, keeping the bar close to your body.', 'back', 'barbell', 'advanced', 'images/exercises/deadlift.jpg'),
(5, 'Pull-ups', 'An upper body exercise that targets the back and biceps.', '1. Hang from a bar with hands slightly wider than shoulder-width.\n2. Pull your body up until your chin clears the bar.\n3. Lower yourself back down with control.', 'back', 'none', 'intermediate', 'images/exercises/pullups.jpg'),
(6, 'Plank', 'An isometric core exercise that strengthens the abdominal muscles.', '1. Start in a push-up position but with your weight on your forearms.\n2. Keep your body in a straight line from head to heels.\n3. Hold this position for the desired time.', 'core', 'none', 'beginner', 'images/exercises/plank.jpg'),
(7, 'Lunges', 'A lower body exercise that targets the quadriceps and glutes.', '1. Stand with feet hip-width apart.\n2. Step forward with one leg and lower your hips until both knees are bent at 90 degrees.\n3. Push back to the starting position and repeat with the other leg.', 'legs', 'none', 'beginner', 'images/exercises/lunges.jpg'),
(8, 'Shoulder Press', 'An exercise that targets the shoulder muscles.', '1. Stand with feet shoulder-width apart, holding dumbbells at shoulder height.\n2. Press the dumbbells overhead until your arms are fully extended.\n3. Lower them back to shoulder height with control.', 'shoulders', 'dumbbells', 'intermediate', 'images/exercises/shoulder_press.jpg'),
(9, 'Bicep Curls', 'An isolation exercise that targets the biceps.', '1. Stand with feet shoulder-width apart, holding dumbbells at your sides.\n2. Curl the weights up by bending your elbows.\n3. Lower them back down with control.', 'arms', 'dumbbells', 'beginner', 'images/exercises/bicep_curls.jpg'),
(10, 'Running', 'A cardiovascular exercise that improves endurance and burns calories.', '1. Start at a comfortable pace.\n2. Maintain good posture with your head up and shoulders relaxed.\n3. Swing your arms naturally and land on your midfoot.', 'cardio', 'none', 'beginner', 'images/exercises/running.jpg'),
(11, 'Dips Standards', 'Dips strength standards help you to compare your one-rep max lift with other lifters at your bodyweight.', 'test1', 'chest', 'other', 'beginner', './uploads/exercises/exercise_1756062744.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `calories_per_serving` int(11) NOT NULL,
  `serving_size` varchar(50) NOT NULL,
  `protein` decimal(5,2) DEFAULT NULL,
  `carbs` decimal(5,2) DEFAULT NULL,
  `fat` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `name`, `calories_per_serving`, `serving_size`, `protein`, `carbs`, `fat`) VALUES
(1, 'Apple', 95, '1 medium (182g)', 0.50, 25.00, 0.30),
(2, 'Banana', 105, '1 medium (118g)', 1.30, 27.00, 0.40),
(3, 'Chicken Breast', 165, '100g', 31.00, 0.00, 3.60),
(4, 'Brown Rice', 216, '1 cup (195g)', 5.00, 45.00, 1.80),
(5, 'Egg', 70, '1 large (50g)', 6.00, 0.60, 5.00),
(6, 'Greek Yogurt', 100, '1 cup (245g)', 17.00, 6.00, 0.70),
(7, 'Broccoli', 25, '1 cup (91g)', 3.00, 5.00, 0.30),
(8, 'Salmon', 206, '100g', 22.00, 0.00, 13.00),
(9, 'Sweet Potato', 103, '1 medium (128g)', 2.30, 24.00, 0.10),
(10, 'Almonds', 160, '1 oz (28g)', 6.00, 6.00, 14.00);

-- --------------------------------------------------------

--
-- Table structure for table `food_logs`
--

CREATE TABLE `food_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `servings` decimal(5,2) NOT NULL,
  `meal_type` enum('breakfast','lunch','dinner','snack') NOT NULL,
  `log_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_logs`
--

INSERT INTO `food_logs` (`id`, `user_id`, `food_id`, `servings`, `meal_type`, `log_date`) VALUES
(1, 2, 10, 1.50, 'breakfast', '2025-08-17'),
(2, 2, 8, 20.00, 'lunch', '2025-08-17'),
(3, 2, 10, 20.00, 'breakfast', '2025-08-18'),
(4, 2, 5, 1.30, 'dinner', '2025-08-21'),
(5, 2, 3, 1.00, 'breakfast', '2025-08-21'),
(6, 4, 10, 1.50, 'lunch', '2025-08-22'),
(7, 7, 10, 2.50, 'breakfast', '2025-08-24');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `status`, `created_at`) VALUES
(1, 3, 2, 'accepted', '2025-08-18 17:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `goal_description` text NOT NULL,
  `target_value` int(11) NOT NULL,
  `current_value` int(11) DEFAULT 0,
  `unit` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','completed','failed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `user_id`, `goal_description`, `target_value`, `current_value`, `unit`, `start_date`, `end_date`, `status`) VALUES
(1, 2, 'Push ups', 20, 4, 'kg', '2025-08-22', '2025-08-23', ''),
(2, 2, 'Push ups', 20, 20, 'kg', '2025-08-22', '2025-08-23', ''),
(3, 2, 'Push ups', 10, 4, 'miles', '2025-08-18', '2025-08-19', ''),
(4, 7, 'Test1', 5, 5, 'miles', '2025-08-25', '2025-08-27', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `progress_photos`
--

CREATE TABLE `progress_photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress_photos`
--

INSERT INTO `progress_photos` (`id`, `user_id`, `photo_url`, `date`, `notes`, `created_at`) VALUES
(1, 2, './uploads/progress/progress_2_1755437507.png', '2025-08-17', 'Now Fitness', '2025-08-17 13:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(2, 'abcd', 'abcd@gmail.com', '$2y$10$11J6SwtxTQzwMzzhmGKpC.lWnypwKlL7Ei9XwGFrKNh4EJWgiUeT.', '2025-08-15 19:55:28'),
(3, 'Gihan', 'gihan@gmail.com', '$2y$10$lu3uW8d8IX.HYcxXrZcSNeDspEOBhZ6sGxOSaPVWt/dGTEAwqaLWm', '2025-08-18 17:33:37'),
(4, 'Test', 'test@gmailcom', '$2y$10$DTTBUmYlFKTCsg/CvFGEfebRfBjwbYbIUkm9UW.oySdQ8P6anWYe2', '2025-08-22 15:25:15'),
(7, 'test1', 'test1@gmail.com', '$2y$10$AEeg0Wwy/GbP8nmWzLb6ZOBO5BsyIztHdEKlxN7dQz350HGJemOze', '2025-08-22 16:22:03'),
(8, 'test2', 'test2@gmail.com', '$2y$10$RSTZn4yxPK8qwrS.NkmRVOaA6BESReiOvjKW0NnRuMj.cm2CxTx9y', '2025-08-22 16:24:00'),
(9, 'Test3', 'test3@gmail.com', '$2y$10$0X/3MHsn/AdXqnVUcJc29OO2WGtK36yoQZn4psrE96wjRoyQoOW1i', '2025-08-25 10:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `water_intake`
--

CREATE TABLE `water_intake` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL COMMENT 'in ml',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `water_intake`
--

INSERT INTO `water_intake` (`id`, `user_id`, `amount`, `date`, `time`, `created_at`) VALUES
(1, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:27'),
(2, 2, 500, '2025-08-15', '22:22:00', '2025-08-15 20:22:28'),
(3, 2, 750, '2025-08-15', '22:22:00', '2025-08-15 20:22:29'),
(4, 2, 500, '2025-08-15', '22:22:00', '2025-08-15 20:22:30'),
(5, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:31'),
(6, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:31'),
(8, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:32'),
(9, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:32'),
(10, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:32'),
(11, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:32'),
(12, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:32'),
(13, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:33'),
(14, 2, 250, '2025-08-15', '22:22:00', '2025-08-15 20:22:33'),
(15, 2, 1000, '2025-08-15', '22:22:00', '2025-08-15 20:22:33'),
(16, 2, 1000, '2025-08-15', '22:22:00', '2025-08-15 20:22:34'),
(17, 2, 1000, '2025-08-15', '22:22:00', '2025-08-15 20:22:34'),
(21, 2, 250, '2025-08-16', '18:25:00', '2025-08-16 16:25:31'),
(22, 2, 250, '2025-08-16', '18:25:00', '2025-08-16 16:25:32'),
(23, 2, 250, '2025-08-16', '18:25:00', '2025-08-16 16:25:32'),
(24, 2, 250, '2025-08-16', '18:25:00', '2025-08-16 16:25:32'),
(25, 2, 250, '2025-08-16', '18:48:00', '2025-08-16 16:48:50'),
(26, 2, 250, '2025-08-16', '18:48:00', '2025-08-16 16:48:50'),
(27, 2, 500, '2025-08-16', '18:48:00', '2025-08-16 16:48:55'),
(28, 2, 55, '2025-08-17', '15:44:00', '2025-08-17 13:44:15'),
(29, 2, 250, '2025-08-18', '18:53:00', '2025-08-18 16:53:51'),
(30, 2, 20, '2025-08-18', '18:53:00', '2025-08-18 16:54:01'),
(31, 2, 250, '2025-08-19', '13:31:00', '2025-08-19 11:31:15'),
(32, 2, 250, '2025-08-21', '21:03:00', '2025-08-21 19:03:35'),
(33, 7, 250, '2025-08-24', '23:10:00', '2025-08-24 21:23:44');

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exercise_name` varchar(100) NOT NULL,
  `sets` int(11) NOT NULL,
  `reps` int(11) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'in minutes',
  `calories` int(11) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workouts`
--

INSERT INTO `workouts` (`id`, `user_id`, `exercise_name`, `sets`, `reps`, `duration`, `calories`, `date`) VALUES
(1, 2, 'Pull Ups', 10, 4, 65, 26, '2025-08-17'),
(2, 2, 'Pull Ups', 10, 4, 65, 26, '2025-08-17'),
(3, 2, 'Push Ups', 10, 4, 20, 10, '2025-08-17'),
(4, 2, 'Bench Press', 3, 3, 25, NULL, '2025-08-18'),
(5, 2, 'Push Ups', 10, 4, 20, 10, '2025-08-18'),
(6, 2, 'Pull Ups', 12, 10, 20, 7, '2025-08-19'),
(7, 2, 'Bicep Curls', 15, 14, 20, 10, '2025-08-19'),
(8, 2, 'Bench Press', 3, 3, 20, NULL, '2025-08-19'),
(9, 3, 'Bicep Curls', 15, 14, 20, 10, '2025-08-22'),
(10, 3, 'Bench Press', 3, 3, 28, NULL, '2025-08-22'),
(11, 4, 'Deadlift', 30, 12, 30, 8, '2025-08-22'),
(12, 7, 'Deadlift', 30, 12, 30, 8, '2025-08-24'),
(13, 7, 'Deadlift', 30, 12, 30, 8, '2025-08-27'),
(14, 7, 'Bicep Curls', 20, 20, 15, NULL, '2025-08-27'),
(15, 7, 'Bench Press', 20, 20, 15, NULL, '2025-08-27');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

CREATE TABLE `workout_plans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`id`, `user_id`, `name`, `description`, `created_at`) VALUES
(1, 2, 'Bench Press', 'Bench Press', '2025-08-18 16:58:33'),
(2, 3, 'Bench Press', 'Bench press', '2025-08-22 08:46:00'),
(3, 7, 'test', 'test', '2025-08-23 23:51:41'),
(4, 7, 'test', 'test description', '2025-08-27 14:43:51');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plan_exercises`
--

CREATE TABLE `workout_plan_exercises` (
  `id` int(11) NOT NULL,
  `workout_plan_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) NOT NULL,
  `reps` int(11) NOT NULL,
  `rest_time` int(11) DEFAULT NULL COMMENT 'in seconds'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_plan_exercises`
--

INSERT INTO `workout_plan_exercises` (`id`, `workout_plan_id`, `exercise_id`, `sets`, `reps`, `rest_time`) VALUES
(1, 1, 3, 3, 3, 10),
(2, 2, 3, 3, 3, 10),
(3, 3, 3, 20, 20, 20),
(4, 4, 9, 20, 20, 20),
(5, 4, 3, 20, 20, 20);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bmi_records`
--
ALTER TABLE `bmi_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `challenges`
--
ALTER TABLE `challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `challenge_participants`
--
ALTER TABLE `challenge_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participation` (`challenge_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_logs`
--
ALTER TABLE `food_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `progress_photos`
--
ALTER TABLE `progress_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `water_intake`
--
ALTER TABLE `water_intake`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `workout_plan_exercises`
--
ALTER TABLE `workout_plan_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_plan_id` (`workout_plan_id`),
  ADD KEY `exercise_id` (`exercise_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bmi_records`
--
ALTER TABLE `bmi_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `challenges`
--
ALTER TABLE `challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `challenge_participants`
--
ALTER TABLE `challenge_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `food_logs`
--
ALTER TABLE `food_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `progress_photos`
--
ALTER TABLE `progress_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `water_intake`
--
ALTER TABLE `water_intake`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `workout_plans`
--
ALTER TABLE `workout_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `workout_plan_exercises`
--
ALTER TABLE `workout_plan_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bmi_records`
--
ALTER TABLE `bmi_records`
  ADD CONSTRAINT `bmi_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenges`
--
ALTER TABLE `challenges`
  ADD CONSTRAINT `challenges_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `challenge_participants`
--
ALTER TABLE `challenge_participants`
  ADD CONSTRAINT `challenge_participants_ibfk_1` FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `challenge_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `food_logs`
--
ALTER TABLE `food_logs`
  ADD CONSTRAINT `food_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `food_logs_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress_photos`
--
ALTER TABLE `progress_photos`
  ADD CONSTRAINT `progress_photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `water_intake`
--
ALTER TABLE `water_intake`
  ADD CONSTRAINT `water_intake_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_plans`
--
ALTER TABLE `workout_plans`
  ADD CONSTRAINT `workout_plans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_plan_exercises`
--
ALTER TABLE `workout_plan_exercises`
  ADD CONSTRAINT `workout_plan_exercises_ibfk_1` FOREIGN KEY (`workout_plan_id`) REFERENCES `workout_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workout_plan_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
