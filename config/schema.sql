-- 1
CREATE TABLE bus_companies (
    company_id int(11) NOT NULL,
    company_name varchar(100) NOT NULL,
    created_at timestamp NULL DEFAULT current_timestamp()
);

--2
CREATE TABLE users (
    user_id int(11) NOT NULL,
    name varchar(100) NOT NULL,
    email varchar(255) NOT NULL,
    hashed_password varchar(255) NOT NULL,
    company_id int(11) NOT NULL,
    created_at datetime DEFAULT current_timestamp(),
    token text DEFAULT NULL,
    role enum('operator','conductor') NOT NULL,
    foreign key (company_id) REFERENCES bus_companies(company_id) ON DELETE CASCADE ON UPDATE CASCADE
);

--3
CREATE TABLE schedule (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    first_trip TIME,
    last_trip TIME,
    time_interval INT
);

--4
CREATE TABLE route (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    route_name VARCHAR(100),
    schedule_id INT,
    FOREIGN KEY (schedule_id) REFERENCES schedule(schedule_id)
);

--5
CREATE TABLE driver (
    driver_id INT PRIMARY KEY,
    license_number VARCHAR(50),
    full_name VARCHAR(50),
    contact_info VARCHAR(100),
    company_id INT,
    FOREIGN KEY (company_id) REFERENCES bus_companies(company_id)
);

--6
CREATE table conductor (
    conductor_id INT PRIMARY KEY,
    full_name VARCHAR(50),
    contact_info VARCHAR(100),
    company_id INT,
    FOREIGN KEY (company_id) REFERENCES bus_companies(company_id)
);

--7
CREATE TABLE bus (
    bus_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT DEFAULT NULL,
    driver_id INT DEFAULT NULL,
    conductor_id INT DEFAULT NULL,
    company_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (route_id) REFERENCES route(route_id),
    FOREIGN KEY (driver_id) REFERENCES driver(driver_id),
    FOREIGN KEY (conductor_id) REFERENCES conductor(conductor_id),
    FOREIGN KEY (company_id) REFERENCES bus_companies(company_id)
);

--8
CREATE TABLE trip (
    trip_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT,
    bus_id INT,
    driver_id INT,
    conductor_id INT,
    boarding_time DATETIME,
    arrival_time DATETIME,
    total_passenger INT,
    total_revenue INT,
    FOREIGN KEY (route_id) REFERENCES route(route_id),
    FOREIGN KEY (bus_id) REFERENCES bus(bus_id),
    FOREIGN KEY (driver_id) REFERENCES driver(driver_id),
    FOREIGN KEY (conductor_id) REFERENCES conductor(conductor_id)
);

--9
CREATE TABLE ticket (
    trip_id INT,
    ticket_id INT,
    passenger_category ENUM('regular', 'student', 'senior', 'pwd'),
    fare_amount DECIMAL(10, 2),
    payment_mode VARCHAR(50),
    company_id INT,
    PRIMARY KEY (trip_id, ticket_id),
    FOREIGN KEY (trip_id) REFERENCES trip(trip_id),
    FOREIGN KEY (company_id) REFERENCES bus_companies(company_id)
);