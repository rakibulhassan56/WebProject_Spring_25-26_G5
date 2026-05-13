-- Default admin: email admin@example.com / password: password
-- (bcrypt hash below matches Laravel's default "password" hash — change in production.)

INSERT INTO users (name, email, password_hash, role, is_active)
SELECT 'Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@example.com');
