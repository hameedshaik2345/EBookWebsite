<?php
session_start();
require_once 'config.php';

// Initialize sorting and pagination
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;

// Build base query
$sql = "SELECT b.*, g.name as genre_name 
        FROM books b 
        LEFT JOIN genres g ON b.genre_id = g.genre_id";

// Add sorting
switch ($sort_by) {
    case 'title_desc':
        $sql .= " ORDER BY b.title DESC";
        break;
    case 'author':
        $sql .= " ORDER BY b.author";
        break;
    case 'author_desc':
        $sql .= " ORDER BY b.author DESC";
        break;
    case 'price_low':
        $sql .= " ORDER BY b.price";
        break;
    case 'price_high':
        $sql .= " ORDER BY b.price DESC";
        break;
    default:
        $sql .= " ORDER BY b.title";
}

// Get total results for pagination
$count_sql = str_replace("SELECT b.*, g.name as genre_name", "SELECT COUNT(*) as count", $sql);
$total_results = $conn->query($count_sql)->fetch_assoc()['count'];
$total_pages = ceil($total_results / $per_page);

// Add pagination
$sql .= " LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$offset = ($page - 1) * $per_page;
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$results = $stmt->get_result();

// Fetch genres for sidebar
$genres = $conn->query("SELECT * FROM genres ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - E-Books Library</title>
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
                        <a class="nav-link active" href="browse.php">Browse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
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

    <!-- Browse Section -->
    <div class="container py-5">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Genres</h5>
                        <div class="list-group">
                            <a href="browse.php" class="list-group-item list-group-item-action <?php echo !isset($_GET['genre']) ? 'active' : ''; ?>">
                                All Genres
                            </a>
                            <?php 
                            // Reset the genres result pointer
                            $genres->data_seek(0);
                            while($genre = $genres->fetch_assoc()): 
                            ?>
                                <a href="genre.php?id=<?php echo $genre['genre_id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <?php echo htmlspecialchars($genre['name']); ?>
                                </a>
                            <?php endwhile; ?>
                        </div>

                        <h5 class="card-title mt-4">Sort By</h5>
                        <form method="GET" action="browse.php">
                            <div class="mb-3">
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="title" <?php echo $sort_by == 'title' ? 'selected' : ''; ?>>
                                        Title (A-Z)
                                    </option>
                                    <option value="title_desc" <?php echo $sort_by == 'title_desc' ? 'selected' : ''; ?>>
                                        Title (Z-A)
                                    </option>
                                    <option value="author" <?php echo $sort_by == 'author' ? 'selected' : ''; ?>>
                                        Author (A-Z)
                                    </option>
                                    <option value="author_desc" <?php echo $sort_by == 'author_desc' ? 'selected' : ''; ?>>
                                        Author (Z-A)
                                    </option>
                                    <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>
                                        Price (Low to High)
                                    </option>
                                    <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>
                                        Price (High to Low)
                                    </option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Browse Books</h2>
                    <p class="text-muted mb-0">
                        Showing <?php echo $total_results; ?> book<?php echo $total_results != 1 ? 's' : ''; ?>
                    </p>
                </div>

                <div class="row">
                    <?php while($book = $results->fetch_assoc()): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     style="height: 250px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title text-truncate"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text">By <?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="card-text">
                                        <small class="text-muted"><?php echo htmlspecialchars($book['genre_name']); ?></small>
                                    </p>
                                    <p class="card-text">
                                        <strong>$<?php echo number_format($book['price'], 2); ?></strong>
                                    </p>
                                    <a href="book.php?id=<?php echo $book['book_id']; ?>" 
                                       class="btn btn-primary w-100">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Browse pages" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?sort=<?php echo $sort_by; ?>&page=<?php echo ($page - 1); ?>">
                                        Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?sort=' . $sort_by . '&page=1">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?sort=<?php echo $sort_by; ?>&page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor;

                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?sort=' . $sort_by . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?sort=<?php echo $sort_by; ?>&page=<?php echo ($page + 1); ?>">
                                        Next
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
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
                        <li><i class="bi bi-envelope"></i> E_library@gmail.com</li>
                        <li><i class="bi bi-phone"></i> +916302972086</li>
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