/*
*	Request
*	https://git.jeyserver.com/abedi/request
*/

CREATE TABLE `request_processes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user` int(11) DEFAULT NULL,
 `title` varchar(255) COLLATE utf8_persian_ci NOT NULL,
 `operator` int(11) DEFAULT NULL,
 `create_at` int(11) NOT NULL,
 `done_at` int(11) DEFAULT NULL,
 `type` varchar(255) COLLATE utf8_persian_ci NOT NULL,
 `parameters` text COLLATE utf8_persian_ci,
 `status` tinyint(4) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `user` (`user`),
 KEY `operator` (`operator`),
 CONSTRAINT `request_processes_ibfk_1` FOREIGN KEY (`user`) REFERENCES `userpanel_users` (`id`) ON DELETE CASCADE,
 CONSTRAINT `request_processes_ibfk_2` FOREIGN KEY (`operator`) REFERENCES `userpanel_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

CREATE TABLE `request_processes_params` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `process` int(11) NOT NULL,
 `name` varchar(255) NOT NULL,
 `value` varchar(255) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `process` (`process`),
 CONSTRAINT `request_processes_params_ibfk_1` FOREIGN KEY (`process`) REFERENCES `request_processes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `request_base` (
 `request` int(11) NOT NULL,
 `process` int(11) NOT NULL,
 `type` int(11) NOT NULL,
 PRIMARY KEY (`request`,`process`),
 KEY `process` (`process`),
 CONSTRAINT `request_base_ibfk_1` FOREIGN KEY (`request`) REFERENCES `request_processes` (`id`) ON DELETE CASCADE,
 CONSTRAINT `request_base_ibfk_2` FOREIGN KEY (`process`) REFERENCES `base_processes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
