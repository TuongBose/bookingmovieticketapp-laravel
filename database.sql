CREATE DATABASE bookingmovieticketapp;
USE bookingmovieticketapp;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phonenumber VARCHAR(20),
    address VARCHAR(255),
    dateofbirth DATE NOT NULL,
    imagename VARCHAR(100) DEFAULT NULL,
    createdat DATETIME,
    isactive BIT DEFAULT 1,
    rolename BIT 
);

CREATE TABLE movies (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT,
    releasedate DATE NOT NULL,
    posterurl VARCHAR(255),
    bannerurl VARCHAR(255),
    agerating VARCHAR(10),
    voteaverage DECIMAL(3, 1) NOT NULL,
    director VARCHAR(255)
);

CREATE TABLE cinemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    coordinates VARCHAR(50), -- Lưu tọa độ (latitude, longitude)
    address VARCHAR(255) NOT NULL,
    phonenumber VARCHAR(20),
    maxroom INT,
    imagename VARCHAR(100),
    isactive BIT DEFAULT 1
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cinemaid INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    seatcolumnmax INT NOT NULL,
    seatrowmax INT NOT NULL,
    FOREIGN KEY (cinemaid) REFERENCES cinemas(id) ON DELETE CASCADE
);

CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roomid INT NOT NULL,
    seatnumber VARCHAR(10) NOT NULL,
    FOREIGN KEY (roomid) REFERENCES rooms(id) ON DELETE CASCADE
);

CREATE TABLE showtimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movieid INT NOT NULL,
    roomid INT NOT NULL,
    showdate DATE NOT NULL,
    starttime DATETIME NOT NULL,
    price INT NOT NULL,    
    isactive BIT DEFAULT 1,
    FOREIGN KEY (movieid) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (roomid) REFERENCES rooms(id) ON DELETE CASCADE
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    userid INT NOT NULL,
    showtimeid INT NOT NULL,
    bookingdate DATETIME NOT NULL,
    totalprice INT NOT NULL,
    paymentmethod VARCHAR(50) NOT NULL,
    paymentstatus VARCHAR(50) NOT NULL,
    isactive BIT DEFAULT 1,
    FOREIGN KEY (userid) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (showtimeid) REFERENCES showtimes(id) ON DELETE CASCADE
);

CREATE TABLE bookingdetails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bookingid INT NOT NULL,
    seatid INT NOT NULL,
    price INT NOT NULL,
    FOREIGN KEY (bookingid) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (seatid) REFERENCES seats(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bookingid INT NOT NULL,
    totalprice INT NOT NULL,
    paymentmethod VARCHAR(50) NOT NULL,
    paymentstatus VARCHAR(50) NOT NULL,
    paymenttime DATETIME NOT NULL,
    FOREIGN KEY (bookingid) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movieid INT NOT NULL,
    userid INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 10),
    comment VARCHAR(255),
    createdat DATETIME,
    FOREIGN KEY (movieid) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (userid) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE movienews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    imageurl VARCHAR(255),
    type VARCHAR(50) NOT NULL,
    publishdate DATE,
    content VARCHAR(255),
    movieid INT,
    FOREIGN KEY (movieid) REFERENCES movies(id) ON DELETE CASCADE
);

CREATE TABLE casts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movieid INT NOT NULL,
    actorname VARCHAR(255) NOT NULL,
    FOREIGN KEY (movieid) REFERENCES movies(id) ON DELETE CASCADE
);

-- Danh sach cac cinema mac dinh
INSERT INTO cinemas (name, city, coordinates, address, phonenumber, maxroom, imagename)
VALUES
    ('Galaxy Nguyễn Du', 'Thành phố Hồ Chí Minh', '10.773307, 106.693373', '116 Nguyễn Du, Quận 1, TP.HCM', '19002224', 4, 'cinema_1_1746214967186.jpg'),
    ('Galaxy Tân Bình', 'Thành phố Hồ Chí Minh', '10.790432, 106.640716', '246 Nguyễn Hồng Đào, Quận Tân Bình, TP.HCM', '19002224', 5, 'cinema_2_1746215174736.jpg'),
    ('Galaxy Quang Trung', 'Thành phố Hồ Chí Minh', '10.834759, 106.662373', 'Lầu 3, TTTM CoopMart Foodcosa số 304A, Quang Trung, P.11, Q. Gò Vấp, Tp.HCM', '19002224', 6, 'cinema_3_1746215224604.jpg'),
    ('Galaxy Long Xuyên', 'Tỉnh An Giang', '10.384155, 105.436843', 'Tầng 1, TTTM Nguyễn Kim, số 01 Trần Hưng Đạo, Phường Mỹ Bình, Thành phố Long Xuyên', '19002224', 5, 'cinema_4_1746375169224.jpg'),
    ('Galaxy Đà Nẵng', 'Thành phố Đà Nẵng', '16.066701, 108.186900', 'Tầng 3, TTTM Coop Mart, 478 Điện Biên Phủ, Quận Thanh Khê, Đà Nẵng', '19002224', 4, 'cinema_5_1746215401993.jpg'),
    ('Galaxy Co.opXtra Linh Trung', 'Thành phố Hồ Chí Minh', '10.867896, 106.776687', 'Tầng trệt, TTTM Co.opXtra Linh Trung, số 934 Quốc Lộ 1A, Phường Linh Trung, Quận Thủ Đức, Thành phố Hồ Chí Minh, Việt Nam', '19002224', 6, 'cinema_6_1746375325231.jpg'),
    ('Galaxy Huỳnh Tấn Phát', 'Thành phố Hồ Chí Minh', '10.712225, 106.736575', 'Lầu 2, TTTM Coopmart, số 1362 Huỳnh Tấn Phát, khu phố 1, Phường Phú Mỹ, Quận 7, Tp.Hồ Chí Minh, Việt Nam.', '19002224', 5, 'cinema_7_1746215524120.jpg'),
    ('Galaxy Sala', 'Thành phố Hồ Chí Minh', '10.771500, 106.721782', 'Tầng 3, Thiso Mall Sala, 10 Mai Chí Thọ, Phường Thủ Thiêm, Thành phố Thủ Đức', '19002224', 7, 'cinema_8_1746215129247.jpg'),
    ('Galaxy Hải Phòng', 'Thành phố Hải Phòng', '20.856159, 106.686521', 'Lầu 7, TTTM Nguyễn Kim - Sài Gòn Mall, số 104 Lương Khánh Thiện', '19002224', 5, 'cinema_9_1746375448482.jpg'),
    ('Galaxy Kinh Dương Vương', 'Thành phố Hồ Chí Minh', '10.749503, 106.628778', '718 Kinh Dương Vương, Quận 6, TP.HCM', '19002224', 5, 'cinema_10_1746375548086.jpg'),
    ('Galaxy Bến Tre', 'Tỉnh Bến Tre', '10.241207, 106.376721', 'Lầu 1, TTTM Sense City 26A Trần Quốc Tuấn, Phường An Hội, TP. Bến Tre', '19002224', 5, 'cinema_11_1746375599036.jpg'),
    ('Galaxy Mipec Long Biên', 'Thành phố Hà Nội', '21.045421, 105.866193', 'Tầng 6, TTTM Mipec Long Biên, Số 2, Phố Long Biên 2, Ngọc Lâm, Long Biên, Hà Nội', '19002224', 5, 'cinema_12_1746375758125.jpg'),
	('Galaxy Cà Mau', 'Tỉnh Cà Mau', '9.177908, 105.154540', 'Lầu 2, TTTM Sense City, số 9, Trần Hưng Đạo, P.5, Tp. Cà Mau', '19002224', 5, 'cinema_13_1746375846069.jpg'),
    ('Galaxy Trung Chánh', 'Thành phố Hồ Chí Minh', '10.855339, 106.608343', 'TTVH Quận 12, Số 09 Quốc Lộ 22, P. Trung Mỹ Tây, Quận 12', '19002224', 5, 'cinema_14_1746375944257.jpg'),
    ('Galaxy Vinh', 'Tỉnh Nghệ An', '18.676724, 105.677608', 'Lầu 5, Trung tâm Giải Trí City HUB – số 1 Lê Hồng Phong, thành phố Vinh', '19002224', 5, 'cinema_15_1746376009746.jpg'),
    ('Galaxy Nguyễn Văn Quá', 'Thành phố Hồ Chí Minh', '10.847156, 106.634100', '119B Nguyễn Văn Quá, Phường Đông Hưng Thuận, Quận 12', '19002224', 5, 'cinema_16_1746376125934.jpg'),
	('Galaxy Buôn Ma Thuột', 'Tỉnh Đắk Lắk', '12.692365, 108.062186', 'Tầng trệt, TTTM Coop Mart Buôn Ma Thuột, số 71 Nguyễn Tất Thành, Phường Tân An, Tp. Buôn Ma Thuột, Tỉnh Đắk Lắk, Việt Nam', '19002224', 5, 'cinema_17_1746376188767.jpg'),
    ('Galaxy Nha Trang Center', 'Tỉnh Khánh Hòa', '12.248043, 109.196326', 'Tầng 3, Trung Tâm Thương Mại Nha Trang Center - 20 Trần Phú, Nha Trang, Khánh Hòa', '19002224', 5, 'cinema_18_1746376268601.jpg'),
    ('Galaxy Trường Chinh', 'Thành phố Hồ Chí Minh', '10.818052, 106.630815', 'Tầng 3 - Co.opMart TTTM Thắng Lợi - Số 2 Trường Chinh, Tây Thạnh, Tân Phú, Thành phố Hồ Chí Minh', '19002224', 5, 'cinema_19_1746376314673.jpg'),
    ('Galaxy GO! Mall Bà Rịa', 'Tỉnh Bà Rịa - Vũng Tàu', '10.492306, 107.169138', 'Tầng 3 TTTM GO! Bà Rịa, Số 2A đường Nguyễn Đình Chiểu, KP1, P. Phước Hiệp, TP. Bà Rịa, Tỉnh Bà Rịa-Vũng Tàu', '19002224', 5, 'cinema_20_1746376394994.jpg'),
    ('Galaxy Cine+ Gold Coast Nha Trang', 'Tỉnh Khánh Hòa', '12.247836, 109.194918', 'Tầng 8, TTTM Gold Coast Nha Trang - Số 1 Trần Hưng Đạo, P. Lộc Thọ, TP. Nha Trang', '19002224', 5, 'cinema_21_1746376448605.jpg'),
    ('Galaxy Cine+ Thiso Phan Huy Ích', 'Thành phố Hồ Chí Minh', '10.841520, 106.637373', 'Tầng 4 TTTM Thiso Mall Trường Chinh - Phan Huy Ích - 385 Phan Huy Ích, Phường 14, Quận Gò Vấp, TP. HCM', '19002224', 5, 'cinema_22_1746376485825.jpg'),
    ('Galaxy Aeon Mall Huế', 'Thành phố Huế', '16.454693, 107.615367', 'Galaxy Aeon Mall Huế - Tầng 4 TTTM Aeon Mall Huế, Cửa số 5 và số 6, Sảnh Đỗ Quyên, ​8 Võ Nguyên Giáp, An Đông, Huế, Thừa Thiên - Huế', '19002224', 5, 'cinema_23_1746376523036.jpg'),
    ('Galaxy Parc Mall Q8', 'Thành phố Hồ Chí Minh', '10.740289, 106.678833', 'Tầng 4 TTTM Parc Mall, 547-549 Tạ Quang Bửu, Phường 4, Quận 8', '19002224', 5, 'cinema_24_1746376581868.jpg');
    
-- Danh sach cac user mac dinh
INSERT INTO users (name, email, password, phonenumber, address, dateofbirth, createdat, isactive, rolename)
VALUES 
	('Nguyen Van A', 'nguyenvana@example.com', 'password123', '0905123456', '123 Đường Láng, Hà Nội', '1990-05-15', '2025-04-21 14:30:00', 1, 0),
	('Bui Teo Eo Lai', 'ntrngtai@example.com', '555', '888', '30/4/1975', '2000-12-12', '2025-04-21 14:30:00', 1, 1),
	('Tran Thi B', 'tranthib@example.com', 'securepass456', '0987654321', '456 Nguyễn Huệ, TP.HCM', '1995-08-20', '2025-04-21 15:00:00', 1, 1);

USE bookingmovieticketapp;
SELECT * FROM users;
SELECT * FROM movies;
SELECT * FROM casts;
SELECT * FROM cinemas;
SELECT * FROM rooms;
SELECT * FROM seats;
SELECT * FROM showtimes;
SELECT * FROM bookings;
SELECT * FROM bookingdetails;
SELECT * FROM payments;
SELECT * FROM ratings;
SELECT * FROM movienews;
