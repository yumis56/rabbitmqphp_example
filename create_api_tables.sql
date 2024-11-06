CREATE TABLE hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_code VARCHAR(4),
    hotel_id VARCHAR(50),
    hotel_name VARCHAR(255),
    latitude DECIMAL(9, 6),
    longitude DECIMAL(9, 6),
    country_code VARCHAR(4),
    last_update DATETIME,
    dupe_id VARCHAR(50),
    chain_code VARCHAR(50),
);

/////////////

CREATE TABLE flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin_code VARCHAR(4),
    destination_code VARCHAR(4),
    departure_date DATE,
    return_date DATE,
    price DECIMAL(10, 2),
    flight_dates_link VARCHAR(255),
    flight_offers_link VARCHAR(255),
    create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
