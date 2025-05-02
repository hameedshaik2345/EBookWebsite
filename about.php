<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - E-Books Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
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
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
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

    <!-- Hero Section -->
    <div class="hero-section py-5 text-center text-white">
        <div class="container">
            <h1 class="display-4">About E-Books Library</h1>
            <p class="lead">Your Gateway to Digital Knowledge</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Mission Section -->
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-4">Our Mission</h2>
                <p class="lead">
                    E-Books Library is dedicated to making literature and knowledge accessible to everyone. 
                    We believe in the power of digital books to educate, inspire, and entertain readers worldwide.
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2>What We Offer</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-collection display-4 text-primary mb-3"></i>
                        <h4 class="card-title">Extensive Collection</h4>
                        <p class="card-text">
                            Access thousands of digital books across various genres, from classic literature 
                            to contemporary works, all carefully curated for our readers.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-laptop display-4 text-primary mb-3"></i>
                        <h4 class="card-title">Easy Access</h4>
                        <p class="card-text">
                            Read your favorite books anytime, anywhere. Our platform is designed 
                            to provide a seamless reading experience across all devices.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                        <h4 class="card-title">Secure Platform</h4>
                        <p class="card-text">
                            Your security is our priority. We ensure safe transactions and protect 
                            your personal information with industry-standard security measures.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="row mb-5 py-4 bg-light rounded">
            <div class="col-12 text-center mb-4">
                <h2>Our Impact</h2>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-book"></i>
                </div>
                <h3 class="h2 mb-2"><?php 
                    $books_count = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
                    echo number_format($books_count); 
                ?></h3>
                <p class="text-muted">Books Available</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="h2 mb-2"><?php 
                    $users_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
                    echo number_format($users_count); 
                ?></h3>
                <p class="text-muted">Active Users</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-download"></i>
                </div>
                <h3 class="h2 mb-2"><?php 
                    $downloads_count = $conn->query("SELECT COUNT(*) as count FROM user_downloads")->fetch_assoc()['count'];
                    echo number_format($downloads_count); 
                ?></h3>
                <p class="text-muted">Total Downloads</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-tags"></i>
                </div>
                <h3 class="h2 mb-2"><?php 
                    $genres_count = $conn->query("SELECT COUNT(*) as count FROM genres")->fetch_assoc()['count'];
                    echo number_format($genres_count); 
                ?></h3>
                <p class="text-muted">Book Categories</p>
            </div>
        </div>

        <!-- Team Section -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2>Our Team</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <img src="assets/images/team/librarian.jpg" alt="Head Librarian" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h4>HAMEED SHAIK</h4>
                        <p class="text-muted">Head Librarian</p>
                        <p>Passionate about digital literature and making books accessible to everyone.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <img src="assets/images/team/curator.jpg" alt="Content Curator" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h4>VISHAL SAI</h4>
                        <p class="text-muted">Content Curator</p>
                        <p>Expert in digital content curation and preservation of literary works.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <img src="assets/images/team/tech.jpg" alt="Technical Lead" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h4>JASHWANTH LAVUDYA</h4>
                        <p class="text-muted">Technical Lead</p>
                        <p>Ensures smooth operation of our digital platform and user experience.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-4">Join Our Community</h2>
                <p class="lead mb-4">
                    Start your reading journey today and become part of our growing community of book lovers.
                </p>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-primary btn-lg">Sign Up Now</a>
                <?php else: ?>
                    <a href="browse.php" class="btn btn-primary btn-lg">Browse Books</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>About E-Books Library</h5>
                    <p>Your digital destination for books. Read, download, and explore thousands of titles.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-white">About Us</a></li>
                        <li><a href="contact.php" class="text-white">Contact</a></li>
                        <li><a href="privacy.php" class="text-white">Privacy Policy</a></li>
                        <li><a href="terms.php" class="text-white">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope"></i> info@ebookslibrary.com</li>
                        <li><i class="bi bi-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="bi bi-geo-alt"></i> 123 Library Street, Booktown, BK 12345</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col text-center">
                    <p class="mb-0">&copy; 2024 E-Books Library. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>