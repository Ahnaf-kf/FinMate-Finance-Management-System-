-- User table
CREATE TABLE user (
    userID VARCHAR(50) PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    password VARCHAR(100)
);

-- User emails
CREATE TABLE user_email (
    userID VARCHAR(50),
    email VARCHAR(100),
    PRIMARY KEY (userID, email),
    FOREIGN KEY (userID) REFERENCES User(userID)
);

-- User phone numbers
CREATE TABLE user_phone_no (
    userID VARCHAR(50),
    phone_no VARCHAR(20),
    PRIMARY KEY (userID, phone_no),
    FOREIGN KEY (userID) REFERENCES User(userID)
);

-- Wallets table
CREATE TABLE wallets (
    wallet_id INT PRIMARY KEY AUTO_INCREMENT,
    userID VARCHAR(50),
    organization_name VARCHAR(100),
    category VARCHAR(50),
    amount DECIMAL(10, 2),
    FOREIGN KEY (userID) REFERENCES User(userID)
);

-- wallets transfers (recursive relation)
CREATE TABLE wallet_transfer (
    source_wallet_id INT,
    destination_wallet_id INT,
    -- transfer_amount DECIMAL(10, 2),
    PRIMARY KEY (source_wallet_id, destination_wallet_id),
    FOREIGN KEY (source_wallet_id) REFERENCES wallets(wallet_id),
    FOREIGN KEY (destination_wallet_id) REFERENCES wallets(wallet_id)
);

-- Payments table
CREATE TABLE payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    wallet_id INT,
    category VARCHAR(50),
    payment_status VARCHAR(20),
    tag VARCHAR(50),
    payment_amount DECIMAL(10, 2),
    FOREIGN KEY (wallet_id) REFERENCES wallets(wallet_id)
);

-- Recurring monthly expenses table
CREATE TABLE recurring_monthly_expenses (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    wallet_id INT,
    payment_status VARCHAR(20),
    monthly_payment DECIMAL(10, 2),
    descriptions VARCHAR(100),
    shared_percentage DECIMAL(5, 2),
    media_company VARCHAR(100),
    expense_type BOOLEAN,
    next_payment_date DATE, 
    FOREIGN KEY (wallet_id) REFERENCES wallets(wallet_id)
);


-- Monthly budget plan (weak entity)
CREATE TABLE monthly_budget_plan (
    userID VARCHAR(50),
    category VARCHAR(50),
    categorical_upper_bound VARCHAR(10),
    PRIMARY KEY (userID, category),
    FOREIGN KEY (userID) REFERENCES User(userID)

);
