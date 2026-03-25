-- ============================================
-- DINELOCAL · dinelocal.sql
-- ITC 6355 | Arjun & Ayomide
-- Run: mysql -u root -p < dinelocal.sql
-- ============================================

CREATE DATABASE IF NOT EXISTS dinelocal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dinelocal;

-- ── USERS (customers) ──
CREATE TABLE IF NOT EXISTS users (
  id                    INT AUTO_INCREMENT PRIMARY KEY,
  name                  VARCHAR(100) NOT NULL,
  email                 VARCHAR(150) NOT NULL UNIQUE,
  password              VARCHAR(255) NOT NULL,
  phone                 VARCHAR(30) DEFAULT NULL,
  dietary               VARCHAR(200) DEFAULT NULL,
  force_password_change TINYINT(1) DEFAULT 0,
  created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── MENU ITEMS ──
CREATE TABLE IF NOT EXISTS menu_items (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(150) NOT NULL,
  description  TEXT NOT NULL,
  price        DECIMAL(8,2) NOT NULL,
  category     VARCHAR(50) NOT NULL,
  image_url    VARCHAR(500) DEFAULT NULL,
  is_available TINYINT(1) DEFAULT 1,
  is_featured  TINYINT(1) DEFAULT 0,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── RESERVATIONS ──
CREATE TABLE IF NOT EXISTS reservations (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT DEFAULT NULL,
  full_name    VARCHAR(100) NOT NULL,
  email        VARCHAR(150) NOT NULL,
  guests       VARCHAR(20) NOT NULL,
  date         DATE NOT NULL,
  time         VARCHAR(20) NOT NULL,
  special      TEXT DEFAULT NULL,
  status       ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── ADMIN USERS ──
CREATE TABLE IF NOT EXISTS admins (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(50) NOT NULL UNIQUE,
  email      VARCHAR(150) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── PASSWORD RESET REQUESTS ──
CREATE TABLE IF NOT EXISTS password_reset_requests (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT DEFAULT NULL,
  email         VARCHAR(150) NOT NULL,
  status        ENUM('pending','resolved') DEFAULT 'pending',
  temp_password VARCHAR(255) DEFAULT NULL,
  requested_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resolved_at   TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admin accounts (username / password)
-- olusanya   → Admin-olusanya
-- ambalavatta → Admin-Ambalavatta
INSERT IGNORE INTO admins (username, email, password) VALUES
  ('olusanya',    'olusanya.ay@northeastern.edu',             '$2y$12$Yj6bXF5Lt7n17rEH7fGBkuKl1oW2sgoQAkQ3H1EfK6OC/qphviF1e'),
  ('ambalavatta', 'ambalavattakottayi.a@northeastern.edu',    '$2y$12$GhEdP5jc0tG0W6lxJJVYP.gYHRH1pm4KB5914zSVOfKff//JlThOK');

-- ── MENU SEED DATA ──
INSERT IGNORE INTO menu_items (name, description, price, category, image_url, is_available, is_featured) VALUES
('Wood-Fired Flatbread',     'Ontario prosciutto, fig jam, arugula, shaved parmesan, aged balsamic drizzle.',            24.00,'Starters', 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&h=400&fit=crop',1,1),
('Butternut Bisque',         'Roasted Ontario butternut squash, crème fraîche, toasted pumpkin seeds, chive oil.',        14.00,'Starters', 'https://images.unsplash.com/photo-1547592180-85f173990554?w=600&h=400&fit=crop',1,0),
('Charcuterie Board',        'Locally cured meats, artisan cheeses, house pickles, honeycomb, grain mustard, crostini.',  32.00,'Starters', 'https://images.unsplash.com/photo-1555243896-c709bfa0b564?w=600&h=400&fit=crop',1,1),
('Heirloom Tomato Salad',    'Vine-ripened heirlooms, burrata, basil oil, sea salt, 10-year aged balsamic.',              18.00,'Starters', 'https://images.unsplash.com/photo-1592417817098-8fd3d9eb14a5?w=600&h=400&fit=crop',1,0),
('Ontario Beef Striploin',   'Dry-aged 28 days. Roasted marrow, heirloom carrots, red wine reduction, rosemary squash.', 42.00,'Mains',    'https://images.unsplash.com/photo-1558030006-450675393462?w=600&h=400&fit=crop',1,1),
('Wild Mushroom Tagliatelle','Hand-cut pasta, foraged Ontario mushrooms, truffle cream, aged pecorino romano.',           28.00,'Mains',    'https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=600&h=400&fit=crop',1,1),
('Pan-Seared Salmon',        'Atlantic salmon, saffron risotto, crispy capers, lemon beurre blanc, micro herbs.',         38.00,'Mains',    'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&h=400&fit=crop',1,0),
('Duck Confit',              'Slow-cooked Brant County duck, Puy lentils, cherry jus, wilted greens.',                   36.00,'Mains',    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&h=400&fit=crop',1,0),
('Roasted Vegetable Tart',   'Seasonal Ontario vegetables, goat cheese, fresh herbs, puff pastry.',                      26.00,'Mains',    'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&h=400&fit=crop',1,0),
('Dark Cocoa Ganache',       '70% Peruvian cocoa, salted caramel, maple honey gelato, vanilla tuile.',                   14.00,'Desserts', 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&h=400&fit=crop',1,1),
('Crème Brûlée',             'Classic French vanilla custard, torched sugar, Ontario berry compote.',                    12.00,'Desserts', 'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=600&h=400&fit=crop',1,0),
('Sticky Toffee Pudding',    'Medjool date cake, warm toffee sauce, Kawartha Dairy vanilla ice cream.',                  13.00,'Desserts', 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600&h=400&fit=crop',1,0),
('Ontario Red Wine',         'Curated selection from Prince Edward County and Niagara. 6oz pour.',                       16.00,'Drinks',   'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=600&h=400&fit=crop',1,0),
('Craft Beer Flight',        'Four 4oz pours of rotating Ontario craft beers.',                                           18.00,'Drinks',   'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=600&h=400&fit=crop',1,0),
('Seasonal Mocktail',        'House-made shrubs, local herbs, sparkling water. Rotating monthly.',                         9.00,'Drinks',   'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=600&h=400&fit=crop',1,0);

-- ── UPDATE IMAGES FOR EXISTING ROWS (run if already imported) ──
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&h=400&fit=crop' WHERE name='Wood-Fired Flatbread'     AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1547592180-85f173990554?w=600&h=400&fit=crop' WHERE name='Butternut Bisque'         AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1555243896-c709bfa0b564?w=600&h=400&fit=crop' WHERE name='Charcuterie Board'        AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1592417817098-8fd3d9eb14a5?w=600&h=400&fit=crop' WHERE name='Heirloom Tomato Salad'   AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1558030006-450675393462?w=600&h=400&fit=crop' WHERE name='Ontario Beef Striploin'   AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=600&h=400&fit=crop' WHERE name='Wild Mushroom Tagliatelle' AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&h=400&fit=crop' WHERE name='Pan-Seared Salmon'      AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&h=400&fit=crop' WHERE name='Duck Confit'             AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&h=400&fit=crop' WHERE name='Roasted Vegetable Tart'  AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&h=400&fit=crop' WHERE name='Dark Cocoa Ganache'      AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=600&h=400&fit=crop' WHERE name='Crème Brûlée'            AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600&h=400&fit=crop' WHERE name='Sticky Toffee Pudding'   AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=600&h=400&fit=crop' WHERE name='Ontario Red Wine'        AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=600&h=400&fit=crop' WHERE name='Craft Beer Flight'       AND (image_url IS NULL OR image_url='');
UPDATE menu_items SET image_url='https://images.unsplash.com/photo-1544145945-f90425340c7e?w=600&h=400&fit=crop' WHERE name='Seasonal Mocktail'       AND (image_url IS NULL OR image_url='');