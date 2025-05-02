<?php
session_start();
require_once 'config.php';

// Check if book ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$book_id = $_GET['id'];

// Fetch book details
$stmt = $conn->prepare("SELECT b.*, g.name as genre_name 
                       FROM books b 
                       LEFT JOIN genres g ON b.genre_id = g.genre_id 
                       WHERE b.book_id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$book = $result->fetch_assoc();

// Handle book purchase
if (isset($_POST['purchase']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, book_id, status) VALUES (?, ?, 'completed')");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    
    $success_message = "Thank you for your purchase!";
}

// Handle book download
if (isset($_POST['download']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Record download
    $stmt = $conn->prepare("INSERT INTO user_downloads (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    
    // Redirect to actual PDF file
    header("Location: " . $book['pdf_file']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - E-Books Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
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

    <!-- Book Details -->
    <div class="container py-5">
        <div class="row">
            <!-- Book Cover -->
            <div class="col-md-4">
                <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" 
                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                     class="img-fluid book-cover mb-4">
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="d-grid gap-2">
                        
                        
                        <form method="POST" class="d-inline">
                            <button type="submit" name="download" class="btn btn-success w-100">
                                Read Online
                            </button>
                        </form>
                        
                        <form method="POST" class="d-inline">
                            <button type="submit" name="purchase" class="btn btn-warning w-100">
                                Buy ($<?php echo number_format($book['price'], 2); ?>)
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Please <a href="login.php">login</a> to read, download, or purchase this book.
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Book Information -->
            <div class="col-md-8">
                <div class="book-details">
                    <h1 class="mb-3"><?php echo htmlspecialchars($book['title']); ?></h1>
                    <p class="lead">By <?php echo htmlspecialchars($book['author']); ?></p>
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre_name']); ?></p>
                    <div class="my-4">
                        <h4>Description</h4>
                        <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Read Online Modal -->
    <div class="modal fade" id="readModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="<?php echo htmlspecialchars($book['pdf_file']); ?>" 
                            class="pdf-viewer"></iframe>
                </div>
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