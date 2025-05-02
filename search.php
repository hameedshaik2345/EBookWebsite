<?php
session_start();
require_once 'config.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 12;

// Build search query
$sql = "SELECT b.*, g.name as genre_name 
        FROM books b 
        LEFT JOIN genres g ON b.genre_id = g.genre_id 
        WHERE 1=1";
$params = [];
$types = "";

if ($search_query) {
    $sql .= " AND (b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
    $search_term = "%{$search_query}%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $types .= "sss";
}

if ($genre_filter) {
    $sql .= " AND b.genre_id = ?";
    $params[] = $genre_filter;
    $types .= "i";
}

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
$stmt = $conn->prepare($count_sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_results = $stmt->get_result()->fetch_assoc()['count'];
$total_pages = ceil($total_results / $per_page);

// Add pagination
$sql .= " LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = ($page - 1) * $per_page;
$types .= "ii";

// Execute final query
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();

// Fetch genres for filter
$genres = $conn->query("SELECT * FROM genres ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - E-Books Library</title>
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

    <!-- Search Results -->
    <div class="container py-5">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Filters</h5>
                        <form method="GET" action="search.php">
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Genres</option>
                                    <?php while($genre = $genres->fetch_assoc()): ?>
                                        <option value="<?php echo $genre['genre_id']; ?>" 
                                                <?php echo $genre_filter == $genre['genre_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($genre['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Sort By</label>
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

            <!-- Results -->
            <div class="col-md-9">
                <h2 class="mb-4">
                    Search Results
                    <?php if ($search_query): ?>
                        for "<?php echo htmlspecialchars($search_query); ?>"
                    <?php endif; ?>
                </h2>

                <p class="text-muted">
                    Found <?php echo $total_results; ?> result<?php echo $total_results != 1 ? 's' : ''; ?>
                </p>

                <div class="row">
                    <?php while($book = $results->fetch_assoc()): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text">By <?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="card-text">
                                        <small class="text-muted"><?php echo htmlspecialchars($book['genre_name']); ?></small>
                                    </p>
                                    <p class="card-text">
                                        <strong>$<?php echo number_format($book['price'], 2); ?></strong>
                                    </p>
                                    <a href="book.php?id=<?php echo $book['book_id']; ?>" 
                                       class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Search results pages">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?q=<?php echo urlencode($search_query); ?>&genre=<?php echo $genre_filter; ?>&sort=<?php echo $sort_by; ?>&page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
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
                <div class="col text-center">
                    <p class="mb-0">&copy; 2024 E-Books Library. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>