

DROP TABLE IF EXISTS `branch_list`;
CREATE TABLE IF NOT EXISTS `branch_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `user_id` int DEFAULT NULL,
  `address` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `branch_list`
--



-- --------------------------------------------------------

--
-- Table structure for table `fee_list`
--

DROP TABLE IF EXISTS `fee_list`;
CREATE TABLE IF NOT EXISTS `fee_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount_from` float NOT NULL DEFAULT '0',
  `amount_to` float NOT NULL DEFAULT '0',
  `fee` float NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `fee_list`
--

INSERT INTO `fee_list` (`id`, `amount_from`, `amount_to`, `fee`, `date_created`) VALUES
(1, 0.01, 500, 10, '2021-10-28 10:51:15'),
(2, 501, 1500, 15, '2021-10-28 10:51:54'),
(3, 1501, 3000, 25, '2021-10-28 10:52:17'),
(4, 3001, 1000000000, 100, '2021-10-28 10:52:54');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

DROP TABLE IF EXISTS `system_info`;
CREATE TABLE IF NOT EXISTS `system_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`)
);

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `user_id`, `meta_field`, `meta_value`) VALUES
(15, 1, 'name', 'Cach Transfer'),
(16, 1, 'short_name', 'Cach Transfer');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_list`
--

DROP TABLE IF EXISTS `transaction_list`;
CREATE TABLE IF NOT EXISTS `transaction_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tracking_code` varchar(50) NOT NULL,
  `branch_id` int DEFAULT NULL,
  `sending_amount` float NOT NULL DEFAULT '0',
  `fee` float NOT NULL DEFAULT '0',
  `purpose` text,
  `user_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  KEY `user_id` (`user_id`)
) ;

--
-- Dumping data for table `transaction_list`
--


DROP TABLE IF EXISTS `transaction_meta`;
CREATE TABLE IF NOT EXISTS `transaction_meta` (
  `transaction_id` int NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `transaction_id` (`transaction_id`)
) ;

--
-- Dumping data for table `transaction_meta`
--


DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `user_id` int DEFAULT NULL,
  `avatar` text,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `branch_id` int DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `location_id` (`branch_id`)
);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `user_id`, `avatar`, `last_login`, `type`, `branch_id`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', NULL, 'uploads/1624240500_avatar.png', NULL, 3, NULL, '2021-01-20 14:02:37', '2024-08-12 21:13:34');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaction_list`
--
ALTER TABLE `transaction_list`
  ADD CONSTRAINT `transaction_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transaction_list_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch_list` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaction_meta`
--
ALTER TABLE `transaction_meta`
  ADD CONSTRAINT `transaction_meta_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transaction_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch_list` (`id`) ON DELETE SET NULL;
COMMIT;
