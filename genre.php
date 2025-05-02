<?php
session_start();
require_once 'config.php';

// Get genre ID from URL
$genre_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if invalid genre ID
if ($genre_id <= 0) {
    header("Location: index.php");
    exit();
}

// Get current page number
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$books_per_page = 12;
$offset = ($page - 1) * $books_per_page;

// Fetch genre details
$stmt = $conn->prepare("SELECT name FROM genres WHERE genre_id = ?");
$stmt->bind_param("i", $genre_id);
$stmt->execute();
$genre_result = $stmt->get_result();
$genre = $genre_result->fetch_assoc();

if (!$genre) {
    header("Location: index.php");
    exit();
}

// Get total books in this genre
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM books WHERE genre_id = ?");
$stmt->bind_param("i", $genre_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $books_per_page);

// Fetch books for the current page
$stmt = $conn->prepare("
    SELECT b.*, g.name AS genre_name 
    FROM books b 
    JOIN genres g ON b.genre_id = g.genre_id 
    WHERE b.genre_id = ? 
    ORDER BY b.title 
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $genre_id, $books_per_page, $offset);
$stmt->execute();
$books_result = $stmt->get_result();

// Fetch all genres for sidebar
$genres_result = $conn->query("SELECT * FROM genres ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($genre['name']); ?> Books - E-Books Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">E-Books Library</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="browse.php">Browse</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <div class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
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
<div class="container py-5">
    <div class="row">
        <!-- Sidebar Genres -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Genres</h5>
                    <div class="list-group">
                        <?php while ($g = $genres_result->fetch_assoc()): ?>
                            <a href="genre.php?id=<?php echo $g['genre_id']; ?>"
                               class="list-group-item list-group-item-action <?php echo ($g['genre_id'] == $genre_id) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($g['name']); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Grid -->
        <div class="col-md-9">
            <h2 class="mb-4"><?php echo htmlspecialchars($genre['name']); ?> Books</h2>

            <?php if ($books_result->num_rows === 0): ?>
                <div class="alert alert-info">No books found in this genre.</div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php while ($book = $books_result->fetch_assoc()): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" class="card-img-top"
                                     alt="<?php echo htmlspecialchars($book['title']); ?>" style="height: 300px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title text-truncate"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text mb-1">By <?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($book['genre_name']); ?></small></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 mb-0">$<?php echo number_format($book['price'], 2); ?></span>
                                        <a href="book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary">View Details</a>


                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?id=<?php echo $genre_id; ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?id=<?php echo $genre_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item"><a class="page-link" href="?id=<?php echo $genre_id; ?>&page=<?php echo $page + 1; ?>">Next</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif;?>
