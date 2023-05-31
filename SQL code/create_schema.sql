create database library;

use library;







CREATE TABLE IF NOT EXISTS school(
	school_id INT AUTO_INCREMENT,
	school_name VARCHAR(50) NOT NULL,
	school_address VARCHAR(100) NOT NULL,
	town VARCHAR(50) NOT NULL,
	postal_code VARCHAR(50) NOT NULL,
	email VARCHAR(50) UNIQUE NOT NULL,
	telephone VARCHAR(20) UNIQUE NOT NULL,
	school_principal_firstname VARCHAR(50) NOT NULL,
	school_principal_lastname VARCHAR(50) NOT NULL,
    PRIMARY KEY (school_id)
);




CREATE TABLE IF NOT EXISTS user (
    user_id INT AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(50) UNIQUE NOT NULL,
    date_of_birth DATE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    role ENUM('student', 'teacher', 'admin', 'universal') NOT NULL,
    number_of_rentals INT NOT NULL DEFAULT 0,
    number_of_reservations INT NOT NULL DEFAULT 0,
    school_id INT,
    PRIMARY KEY (user_id),
    FOREIGN KEY (school_id) REFERENCES school(school_id) ON DELETE CASCADE
);





CREATE TABLE IF NOT EXISTS book(
	book_id INT NOT NULL AUTO_INCREMENT,
	ISBN VARCHAR(13) UNIQUE NOT NULL,
	book_title VARCHAR(100) NOT NULL,
	publisher VARCHAR(100) NOT NULL,
	number_of_pages INT(10) UNSIGNED NOT NULL,
	summary TEXT,
	book_language VARCHAR(50),
    image BLOB,
	PRIMARY KEY (book_id)
);


CREATE TABLE IF NOT EXISTS book_in_library(
book_id INT NOT NULL,
school_id INT NOT NULL,
no_of_copies_in_library INT UNSIGNED NOT NULL DEFAULT 0,
last_update DATETIME,
PRIMARY KEY (book_id, school_id),
FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE CASCADE,
FOREIGN KEY (school_id) REFERENCES school(school_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS author(
	author_id INT NOT NULL
	AUTO_INCREMENT,
	author_first_name VARCHAR(50) NOT NULL,
	author_last_name VARCHAR(50) NOT NULL,
	PRIMARY KEY (author_id)
);

CREATE TABLE IF NOT EXISTS book_author(
	author_id INT NOT NULL,
    book_id INT NOT NULL,
	PRIMARY KEY (book_id, author_id),
	FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES author(author_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS keywords(
	keyword_id INT NOT NULL AUTO_INCREMENT,
	keyword VARCHAR(50) NOT NULL,
	PRIMARY KEY (keyword_id)
);

CREATE TABLE IF NOT EXISTS book_keywords(
	keyword_id INT NOT NULL,
    book_id INT NOT NULL,
	PRIMARY KEY (book_id, keyword_id),
	FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE CASCADE,
    FOREIGN KEY (keyword_id) REFERENCES keywords(keyword_id) ON DELETE CASCADE
    
);


CREATE TABLE IF NOT EXISTS category(
	category_id INT NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
	PRIMARY KEY(category_id)
);

CREATE TABLE IF NOT EXISTS book_category(
	category_id INT NOT NULL,
	book_id INT NOT NULL,
	PRIMARY KEY(book_id, category_id),
	FOREIGN KEY(book_id) REFERENCES book(book_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(category_id) ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS book_rent(
rent_id INT AUTO_INCREMENT,
user_id INT,
book_id INT,
rent_request DATE,
rent_date DATE DEFAULT NULL,
returned_at DATE DEFAULT NULL,

PRIMARY KEY (rent_id),
FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE SET NULL,
FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE SET NULL

);


CREATE TABLE IF NOT EXISTS book_reservation(
reservation_id INT NOT NULL AUTO_INCREMENT,
user_id INT NOT NULL,
book_id INT NOT NULL,
reservation_date DATE,
fulfilled_at DATE DEFAULT NULL,

PRIMARY KEY (reservation_id),
FOREIGN KEY (book_id) REFERENCES book(book_id) ON DELETE CASCADE,
FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE

);

CREATE TABLE IF NOT EXISTS review (
  review_id INT NOT NULL AUTO_INCREMENT,
  rent_id INT,
  review_text TEXT,
  review_date DATETIME,
  rating INT,
  PRIMARY KEY (review_id),
  FOREIGN KEY (rent_id) REFERENCES book_rent(rent_id) ON DELETE SET NULL
);


DELIMITER $$

CREATE TRIGGER book_reservation_before_insert BEFORE INSERT ON book_reservation
FOR EACH ROW
BEGIN
    IF NEW.reservation_date > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'reservation_date cannot be in the future';
    END IF;
    IF NEW.fulfilled_at IS NOT NULL AND NEW.fulfilled_at < NEW.reservation_date THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'fulfilled_at cannot be earlier than reservation_date';
    END IF;
END $$

CREATE TRIGGER book_reservation_before_update BEFORE UPDATE ON book_reservation
FOR EACH ROW
BEGIN
    IF NEW.reservation_date > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'reservation_date cannot be in the future';
    END IF;
    IF NEW.fulfilled_at IS NOT NULL AND NEW.fulfilled_at < NEW.reservation_date THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'fulfilled_at cannot be earlier than reservation_date';
    END IF;
END $$

DELIMITER ;


DELIMITER $$

CREATE TRIGGER book_rent_before_insert BEFORE INSERT ON book_rent
FOR EACH ROW
BEGIN
    IF NEW.rent_request > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rent_request cannot be in the future';
    END IF;
    IF NEW.rent_date IS NOT NULL AND NEW.rent_date < NEW.rent_request THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rent_date cannot be earlier than rent_request';
    END IF;
    IF NEW.returned_at IS NOT NULL AND NEW.returned_at < NEW.rent_date THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'returned_at cannot be earlier than rent_date';
    END IF;
END $$

CREATE TRIGGER book_rent_before_update BEFORE UPDATE ON book_rent
FOR EACH ROW
BEGIN
    IF NEW.rent_request > CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rent_request cannot be in the future';
    END IF;
    IF NEW.rent_date IS NOT NULL AND NEW.rent_date < NEW.rent_request THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'rent_date cannot be earlier than rent_request';
    END IF;
    IF NEW.returned_at IS NOT NULL AND NEW.returned_at < NEW.rent_date THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'returned_at cannot be earlier than rent_date';
    END IF;
END $$

DELIMITER ;



