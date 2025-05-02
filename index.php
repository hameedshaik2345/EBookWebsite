<?php
session_start();
include __DIR__ . '/config.php';

if (!$conn) {
    die("Database connection not established.");
}


// Fetch featured books
$featured_query = "SELECT b.*, g.name as genre_name FROM books b 
                  LEFT JOIN genres g ON b.genre_id = g.genre_id 
                  WHERE b.is_featured = 1 
                  LIMIT 6";
$featured_result = $conn->query($featured_query);

// Fetch all genres
$genres_query = "SELECT * FROM genres ORDER BY name";
$genres_result = $conn->query($genres_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Books Library - Your Digital Reading Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">E-Books Library</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="browse.php">Browse</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="genresDropdown" role="button" data-bs-toggle="dropdown">
                            Genres
                        </a>
                        <ul class="dropdown-menu">
                            <?php while($genre = $genres_result->fetch_assoc()): ?>
                                <li><a class="dropdown-item" href="genre.php?id=<?php echo $genre['genre_id']; ?>">
                                    <?php echo htmlspecialchars($genre['name']); ?>
                                </a></li>
                            <?php endwhile; ?>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex me-3" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search books...">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
                <div class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="profile.php">Profile</a>
                        <a class="nav-link" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="nav-link" href="login.php">Login</a>
                        <a class="nav-link" href="register.php">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container index-page">
        <!-- Hero Section -->
        <div class="hero-section py-5 text-center text-white">
            <div class="container">
                <h1 class="display-4">Welcome to E-Books Library</h1>
                <p class="lead">Discover thousands of free and premium e-books</p>
            </div>
        </div>

        <!-- Featured Books Section -->
        <section class="featured-books py-5">
            <div class="container">
                <h2 class="text-center mb-4">Featured Books</h2>
                <div class="row">
                    <?php while($book = $featured_result->fetch_assoc()): ?>
                        <div class="col-md-4 col-lg-2 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text">By <?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($book['genre_name']); ?></small></p>
                                    <a href="book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>

        <!-- Genres Grid -->
        <section class="genres-grid py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">Browse by Genre</h2>
                <div class="row">
                    <?php 
                    $genres_result->data_seek(0);
                    while($genre = $genres_result->fetch_assoc()): 
                    ?>
                        <div class="col-md-3 mb-4">
                            <a href="genre.php?id=<?php echo $genre['genre_id']; ?>" class="text-decoration-none">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($genre['name']); ?></h5>
                                        <p class="card-text small"><?php echo htmlspecialchars($genre['description']); ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>About E-Books Library</h5>
                    <p>Your one-stop destination for digital books. Read, download, and explore thousands of titles across various genres.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-white">About Us</a></li>
                        <li><a href="contact.php" class="text-white">Contact</a></li>
                        <li><a href="privacy.php" class="text-white">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <p class="mb-0">&copy; 2024 E-Books Library. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>