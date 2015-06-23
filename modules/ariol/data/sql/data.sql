INSERT INTO `users` 
	(`email`, `password`, `roles`, `name`, `created_at`, `updated_at`)
VALUES
	('admin@smartdesign.by', 'fac9c6bc237e456f4124d0c0ceb477218d6bfedd41f38ac021c190ecbc4234f1', 1, 'admin', UNIX_TIMESTAMP(NOW()), 0);