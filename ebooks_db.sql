-- Create database
CREATE DATABASE IF NOT EXISTS ebooks_db;
USE ebooks_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin BOOLEAN DEFAULT FALSE
);

-- Genres table
CREATE TABLE IF NOT EXISTS genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    book_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    genre_id INT,
    description TEXT,
    cover_image VARCHAR(255),
    pdf_file VARCHAR(255),
    price DECIMAL(10,2),
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id) ON DELETE SET NULL
);

-- User_downloads table (track user downloads)
CREATE TABLE IF NOT EXISTS user_downloads (
    download_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    book_id INT,
    download_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE SET NULL
);

-- Orders table (for purchase tracking)
CREATE TABLE IF NOT EXISTS orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    book_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_details (
    order_id VARCHAR(20) PRIMARY KEY,
    user_id INT,
    book_id INT,
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    payment_details TEXT,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    status VARCHAR(20),
    created_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (book_id) REFERENCES books(book_id)
);

-- Insert default genres
INSERT INTO genres (name, description) VALUES
('Fiction', 'Imaginative and creative literature'),
('Non-Fiction', 'Factual and informative books'),
('Science', 'Scientific literature and research'),
('History', 'Historical accounts and analysis'),
('Philosophy', 'Philosophical works and thoughts'),
('Poetry', 'Poetic works and collections'),
('Drama', 'Theatrical works and plays'),
('Biography', 'Life stories and memoirs'),
('Technology', 'Books about technology and computing'),
('Romance', 'Love stories and romantic literature');

--inserting books
INSERT INTO books (title, author, genre_id, description, cover_image, pdf_file, price, is_featured, created_at) VALUES
('Pride and Prejudice', 'Jane Austen', 1, 'A romantic novel of manners.', 'covers/pride_and_prejudice.jpg', 'pdfs/pride_and_prejudice.pdf', 0.00, 1, NOW()),
('Dracula', 'Bram Stoker', 2, 'Gothic horror introducing Count Dracula.', 'covers/dracula.jpg', 'pdfs/dracula.pdf', 2.99, 0, NOW()),
('The Adventures of Sherlock Holmes', 'Arthur Conan Doyle', 3, 'Detective stories with Sherlock Holmes.', 'covers/sherlock_holmes.jpg', 'pdfs/sherlock_holmes.pdf', 3.49, 1, NOW()),
('Frankenstein', 'Mary Shelley', 2, 'A scientist creates a sentient being.', 'covers/frankenstein.jpg', 'pdfs/frankenstein.pdf', 1.99, 0, NOW()),
('The Art of War', 'Sun Tzu', 4, 'Ancient Chinese military strategies.', 'covers/art_of_war.jpg', 'pdfs/art_of_war.pdf', 1.50, 1, NOW()),
('Moby-Dick', 'Herman Melville', 5, 'Captain Ahab hunts the white whale.', 'covers/moby_dick.jpg', 'pdfs/moby_dick.pdf', 2.75, 0, NOW()),
('A Tale of Two Cities', 'Charles Dickens', 1, 'Set in London and Paris.', 'covers/tale_of_two_cities.jpg', 'pdfs/tale_of_two_cities.pdf', 2.25, 0, NOW()),
('The Time Machine', 'H.G. Wells', 6, 'Inventing the idea of time travel.', 'covers/time_machine.jpg', 'pdfs/time_machine.pdf', 2.00, 1, NOW()),
('Treasure Island', 'Robert Louis Stevenson', 3, 'Pirates and buried treasure.', 'covers/treasure_island.jpg', 'pdfs/treasure_island.pdf', 1.80, 0, NOW()),
('Alice in Wonderland', 'Lewis Carroll', 7, 'Alice falls through a rabbit hole.', 'covers/alice_in_wonderland.jpg', 'pdfs/alice_in_wonderland.pdf', 0.00, 1, NOW()),

('Wuthering Heights', 'Emily Brontë', 1, 'A story of love and revenge.', 'covers/wuthering_heights.jpg', 'pdfs/wuthering_heights.pdf', 2.10, 0, NOW()),
('Jane Eyre', 'Charlotte Brontë', 1, 'A novel about personal growth.', 'covers/jane_eyre.jpg', 'pdfs/jane_eyre.pdf', 2.60, 1, NOW()),
('The Call of the Wild', 'Jack London', 5, 'A dog becomes wild in the Yukon.', 'covers/call_of_the_wild.jpg', 'pdfs/call_of_the_wild.pdf', 1.95, 0, NOW()),
('Little Women', 'Louisa May Alcott', 1, 'Coming of age story.', 'covers/little_women.jpg', 'pdfs/little_women.pdf', 2.30, 0, NOW()),
('The War of the Worlds', 'H.G. Wells', 6, 'Aliens invade Earth.', 'covers/war_of_worlds.jpg', 'pdfs/war_of_worlds.pdf', 1.70, 0, NOW()),
('The Picture of Dorian Gray', 'Oscar Wilde', 2, 'A man who never ages.', 'covers/dorian_gray.jpg', 'pdfs/dorian_gray.pdf', 1.99, 0, NOW()),
('The Brothers Karamazov', 'Fyodor Dostoevsky', 5, 'Philosophical novel.', 'covers/karamazov.jpg', 'pdfs/karamazov.pdf', 3.20, 0, NOW()),
('Les Misérables', 'Victor Hugo', 1, 'A tale of justice and redemption.', 'covers/les_miserables.jpg', 'pdfs/les_miserables.pdf', 3.99, 1, NOW()),
('Crime and Punishment', 'Fyodor Dostoevsky', 5, 'A psychological drama.', 'covers/crime_and_punishment.jpg', 'pdfs/crime_and_punishment.pdf', 2.80, 0, NOW()),
('The Prince', 'Niccolò Machiavelli', 4, 'Political treatise.', 'covers/the_prince.jpg', 'pdfs/the_prince.pdf', 1.25, 0, NOW()),

('Heart of Darkness', 'Joseph Conrad', 5, 'Journey into the Congo.', 'covers/heart_of_darkness.jpg', 'pdfs/heart_of_darkness.pdf', 2.00, 0, NOW()),
('The Jungle Book', 'Rudyard Kipling', 7, 'Animal stories in India.', 'covers/jungle_book.jpg', 'pdfs/jungle_book.pdf', 1.60, 1, NOW()),
('Don Quixote', 'Miguel de Cervantes', 5, 'Adventures of a delusional knight.', 'covers/don_quixote.jpg', 'pdfs/don_quixote.pdf', 2.95, 0, NOW()),
('Ulysses', 'James Joyce', 5, 'Modernist novel.', 'covers/ulysses.jpg', 'pdfs/ulysses.pdf', 3.80, 0, NOW()),
('Candide', 'Voltaire', 5, 'A satirical novella.', 'covers/candide.jpg', 'pdfs/candide.pdf', 1.10, 0, NOW()),
('Beowulf', 'Unknown', 6, 'Epic Old English poem.', 'covers/beowulf.jpg', 'pdfs/beowulf.pdf', 0.00, 0, NOW()),
('Faust', 'Johann Wolfgang von Goethe', 5, 'Deal with the devil.', 'covers/faust.jpg', 'pdfs/faust.pdf', 1.95, 0, NOW()),
('The Count of Monte Cristo', 'Alexandre Dumas', 5, 'Revenge and redemption.', 'covers/monte_cristo.jpg', 'pdfs/monte_cristo.pdf', 3.50, 1, NOW()),
('The Scarlet Letter', 'Nathaniel Hawthorne', 1, 'Sin and redemption.', 'covers/scarlet_letter.jpg', 'pdfs/scarlet_letter.pdf', 2.20, 0, NOW()),
('Gulliver’s Travels', 'Jonathan Swift', 7, 'Satirical travel novel.', 'covers/gullivers_travels.jpg', 'pdfs/gullivers_travels.pdf', 1.75, 1, NOW()),

('The Hound of the Baskervilles', 'Arthur Conan Doyle', 3, 'Holmes investigates a supernatural hound.', 'covers/hound_baskervilles.jpg', 'pdfs/hound_baskervilles.pdf', 2.60, 0, NOW()),
('The Invisible Man', 'H.G. Wells', 6, 'Man becomes invisible.', 'covers/invisible_man.jpg', 'pdfs/invisible_man.pdf', 2.30, 0, NOW()),
('Around the World in 80 Days', 'Jules Verne', 6, 'Phileas Fogg’s journey.', 'covers/80_days.jpg', 'pdfs/80_days.pdf', 1.80, 0, NOW()),
('Northanger Abbey', 'Jane Austen', 1, 'A satire of Gothic novels.', 'covers/northanger_abbey.jpg', 'pdfs/northanger_abbey.pdf', 1.90, 0, NOW()),
('Emma', 'Jane Austen', 1, 'A novel of matchmaking and manners.', 'covers/emma.jpg', 'pdfs/emma.pdf', 2.10, 1, NOW()),
('Persuasion', 'Jane Austen', 1, 'A mature love story.', 'covers/persuasion.jpg', 'pdfs/persuasion.pdf', 1.99, 0, NOW()),
('The Odyssey', 'Homer', 5, 'Greek epic poem.', 'covers/odyssey.jpg', 'pdfs/odyssey.pdf', 2.45, 1, NOW()),
('The Iliad', 'Homer', 5, 'Epic of the Trojan War.', 'covers/iliad.jpg', 'pdfs/iliad.pdf', 2.40, 0, NOW()),
('The Republic', 'Plato', 4, 'Philosophical text.', 'covers/republic.jpg', 'pdfs/republic.pdf', 2.00, 0, NOW()),
('Meditations', 'Marcus Aurelius', 4, 'Stoic reflections.', 'covers/meditations.jpg', 'pdfs/meditations.pdf', 1.80, 0, NOW()),

('Walden', 'Henry David Thoreau', 4, 'Life in the woods.', 'covers/walden.jpg', 'pdfs/walden.pdf', 1.60, 0, NOW()),
('Leaves of Grass', 'Walt Whitman', 5, 'Poetry collection.', 'covers/leaves_of_grass.jpg', 'pdfs/leaves_of_grass.pdf', 1.90, 1, NOW()),
('The Divine Comedy', 'Dante Alighieri', 5, 'Journey through Hell, Purgatory, and Heaven.', 'covers/divine_comedy.jpg', 'pdfs/divine_comedy.pdf', 3.75, 0, NOW()),
('Poetics', 'Aristotle', 4, 'Earliest surviving work of dramatic theory.', 'covers/poetics.jpg', 'pdfs/poetics.pdf', 1.50, 0, NOW()),
('Aesop’s Fables', 'Aesop', 7, 'Short moral stories.', 'covers/aesop_fables.jpg', 'pdfs/aesop_fables.pdf', 1.00, 1, NOW()),
('Metamorphosis', 'Franz Kafka', 5, 'Man turns into an insect.', 'covers/metamorphosis.jpg', 'pdfs/metamorphosis.pdf', 1.40, 0, NOW()),
('The Trial', 'Franz Kafka', 5, 'Absurd legal proceedings.', 'covers/the_trial.jpg', 'pdfs/the_trial.pdf', 1.70, 0, NOW()),
('The Sorrows of Young Werther', 'Goethe', 5, 'Romanticism and emotion.', 'covers/young_werther.jpg', 'pdfs/young_werther.pdf', 1.50, 0, NOW());