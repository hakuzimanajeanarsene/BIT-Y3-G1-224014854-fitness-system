USE fitness_db;

INSERT INTO users (username, email, password_hash, role) VALUES
('hakuzimana',  'hakuzimana@gmail.com',  '$2y$10$0Y7YhM0pL5b0uQqYr8N9m.7dklAq3QeH1sV9y3zP7ZqW6eR4tTqTu', 'admin'),
('jeanarsen', 'jeanarsen@gmail.com', '$2y$10$0Y7YhM0pL5b0uQqYr8N9m.7dklAq3QeH1sV9y3zP7ZqW6eR4tTqTu', 'staff');

INSERT INTO members (full_name, phone, email, join_date) VALUES
('Nshimiyimana Patrick',   '0783999980', 'patrick@gmail.com', '2026-11-22'),
('Mushumba John',     '0783780288', 'mushumbajohn@gmail.com',   '2026-11-22'),
('Bazimaziki Fidel', '0789339641', 'bazimaziki@gmail.com', '2026-11-22');

INSERT INTO subscriptions (member_id, plan_name, start_date, end_date, amount, status) VALUES
(1, 'Monthly',   '2026-11-22', '2026-12-21',  30.00, 'active'),
(2, 'Quarterly', '2026-11-22', '2027-03-22',  80.00, 'active'),
(3, 'Yearly',    '2026-11-22', '2027-11-22', 300.00, 'active');

INSERT INTO trainers (full_name, specialization, phone) VALUES
('Coach Mudenge',  'Strength Training', '0788707070'),
('Coach Diane', 'Yoga & Cardio',     '0788808080');
