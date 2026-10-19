-- FeeLedger schema. Load this first, then seed.sql.
SET NAMES utf8mb4;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS installments;
DROP TABLE IF EXISTS guardians;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS batches;

CREATE TABLE batches (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  start_date DATE NOT NULL,
  monthly_fee DECIMAL(10,2) NOT NULL,
  installments TINYINT UNSIGNED NOT NULL,
  status ENUM('active','paused') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE students (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  batch_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  email VARCHAR(150) NULL,
  joined_on DATE NOT NULL,
  discount DECIMAL(10,2) NOT NULL DEFAULT 0,
  KEY idx_students_batch (batch_id),
  CONSTRAINT fk_students_batch FOREIGN KEY (batch_id) REFERENCES batches (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE guardians (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  relation VARCHAR(30) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  KEY idx_guardians_student (student_id),
  CONSTRAINT fk_guardians_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE installments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  batch_id INT UNSIGNED NOT NULL,
  seq TINYINT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  due_date DATE NOT NULL,
  status ENUM('pending','paid') NOT NULL DEFAULT 'pending',
  KEY idx_installments_student (student_id),
  KEY idx_installments_batch_status (batch_id, status, due_date),
  CONSTRAINT fk_installments_student FOREIGN KEY (student_id) REFERENCES students (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  batch_id INT UNSIGNED NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  mode ENUM('counter','online') NOT NULL,
  status ENUM('captured','pending','failed') NOT NULL DEFAULT 'captured',
  gateway_ref VARCHAR(40) NULL,
  remarks VARCHAR(255) NULL,
  paid_at DATETIME NOT NULL,
  KEY idx_payments_student (student_id),
  KEY idx_payments_batch_paid (batch_id, paid_at),
  KEY idx_payments_paid (paid_at),
  CONSTRAINT fk_payments_student FOREIGN KEY (student_id) REFERENCES students (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
