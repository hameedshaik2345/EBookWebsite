<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Verify admin status
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user['is_admin']) {
    header("Location: ../index.php");
    exit();
}

$success = $error = '';

// Handle book deletion
if (isset($_POST['delete_book']) && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Get book info for file deletion
    $stmt = $conn->prepare("SELECT cover_image, pdf_file FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    
    // Delete files
    if ($book) {
        if (file_exists("../" . $book['cover_image'])) unlink("../" . $book['cover_image']);
        if (file_exists("../" . $book['pdf_file'])) unlink("../" . $book['pdf_file']);
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    
    if ($stmt->execute()) {
        $success = "Book deleted successfully";
    } else {
        $error = "Error deleting book";
    }
}

// Handle book addition/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre_id = $_POST['genre_id'];
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // File upload handling
    $cover_path = $pdf_path = '';
    $upload_error = false;
    
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $cover_path = 'assets/images/covers/' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], "../" . $cover_path)) {
                $error = "Error uploading cover image";
                $upload_error = true;
            }
        } else {
            $error = "Invalid cover image format";
            $upload_error = true;
        }
    }
    
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        if (strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION)) == 'pdf') {
            $pdf_path = 'assets/pdfs/' . uniqid() . '.pdf';
            if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], "../" . $pdf_path)) {
                $error = "Error uploading PDF file";
                $upload_error = true;
            }
        } else {
            $error = "Invalid PDF file format";
            $upload_error = true;
        }
    }
    
    if (!$upload_error) {
        if (isset($_POST['book_id'])) {
            // Update existing book
            $book_id = $_POST['book_id'];
            $sql = "UPDATE books SET 
                    title = ?, 
                    author = ?, 
                    genre_id = ?, 
                    description = ?, 
                    price = ?, 
                    is_featured = ?";
            
            $params = [$title, $author, $genre_id, $description, $price, $is_featured];
            $types = "ssissi";
            
            if ($cover_path) {
                $sql .= ", cover_image = ?";
                $params[] = $cover_path;
                $types .= "s";
            }
            if ($pdf_path) {
                $sql .= ", pdf_file = ?";
                $params[] = $pdf_path;
                $types .= "s";
            }
            
            $sql .= " WHERE book_id = ?";
            $params[] = $book_id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
        } else {
            // Add new book
            $stmt = $conn->prepare("INSERT INTO books (title, author, genre_id, description, cover_image, pdf_file, price, is_featured) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisssdi", $title, $author, $genre_id, $description, $cover_path, $pdf_path, $price, $is_featured);
        }
        
        if ($stmt->execute()) {
            $success = isset($_POST['book_id']) ? "Book updated successfully" : "Book added successfully";
        } else {
            $error = "Error saving book";
        }
    }
}

// Fetch all books with genre names
$books = $conn->query("SELECT b.*, g.name as genre_name 
                      FROM books b 
                      LEFT JOIN genres g ON b.genre_id = g.genre_id 
                      ORDER BY b.created_at DESC");

// Fetch genres for dropdown
$genres = $conn->query("SELECT * FROM genres ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - E-Books Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="index.php">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="books.php">
                                <i class="bi bi-book"></i>
                                Manage Books
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">
                                <i class="bi bi-people"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="genres.php">
                                <i class="bi bi-tags"></i>
                                Manage Genres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../index.php">
                                <i class="bi bi-house"></i>
                                Back to Site
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Books</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                        Add New Book
                    </button>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Books Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Genre</th>
                                <th>Price</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($book = $books->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                             alt="Cover" 
                                             style="width: 50px; height: 70px; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['genre_name']); ?></td>
                                    <td>$<?php echo number_format($book['price'], 2); ?></td>
                                    <td><?php echo $book['is_featured'] ? 'Yes' : 'No'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-book" 
                                                data-book-id="<?php echo $book['book_id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editBookModal">
                                            Edit
                                        </button>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this book?');">
                                            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                            <button type="submit" name="delete_book" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="genre_id" class="form-label">Genre</label>
                            <select class="form-control" id="genre_id" name="genre_id" required>
                                <?php 
                                $genres->data_seek(0);
                                while($genre = $genres->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $genre['genre_id']; ?>">
                                        <?php echo htmlspecialchars($genre['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">PDF File</label>
                            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">Featured Book</label>
                        </div>
                        
                        <button type="submit" name="submit" class="btn btn-primary">Add Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id="editBookForm">
                        <input type="hidden" name="book_id" id="edit_book_id">
                        
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="edit_author" name="author" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_genre_id" class="form-label">Genre</label>
                            <select class="form-control" id="edit_genre_id" name="genre_id" required>
                                <?php 
                                $genres->data_seek(0);
                                while($genre = $genres->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $genre['genre_id']; ?>">
                                        <?php echo htmlspecialchars($genre['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_cover_image" class="form-label">Cover Image (leave empty to keep current)</label>
                            <input type="file" class="form-control" id="edit_cover_image" name="cover_image" accept="image/*">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_pdf_file" class="form-label">PDF File (leave empty to keep current)</label>
                            <input type="file" class="form-control" id="edit_pdf_file" name="pdf_file" accept=".pdf">
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_is_featured" name="is_featured">
                            <label class="form-check-label" for="edit_is_featured">Featured Book</label>
                        </div>
                        
                        <button type="submit" name="submit" class="btn btn-primary">Update Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit book button clicks
        document.querySelectorAll('.edit-book').forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.dataset.bookId;
                // Fetch book details via AJAX and populate the edit form
                fetch(`get_book.php?id=${bookId}`)
                    .then(response => response.json())
                    .then(book => {
                        document.getElementById('edit_book_id').value = book.book_id;
                        document.getElementById('edit_title').value = book.title;
                        document.getElementById('edit_author').value = book.author;
                        document.getElementById('edit_genre_id').value = book.genre_id;
                        document.getElementById('edit_description').value = book.description;
                        document.getElementById('edit_price').value = book.price;
                        document.getElementById('edit_is_featured').checked = book.is_featured == 1;
                    });
            });
        });
    </script>
</body>
</html>