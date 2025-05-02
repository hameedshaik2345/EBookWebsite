<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - E-Books Library</title>
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
                        <a class="nav-link" href="about.php">About</a>
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
            <h1 class="display-4">Privacy Policy</h1>
            <p class="lead">Your Privacy Matters to Us</p>
        </div>
    </div>

    <!-- Privacy Policy Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <section class="mb-5">
                            <h2 class="h4 mb-4">Introduction</h2>
                            <p>Welcome to E-Books Library's Privacy Policy. This policy explains how we collect, use, protect, and share your personal information when you use our website and services.</p>
                            <p>Last updated: March 15, 2024</p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Information We Collect</h2>
                            <h3 class="h5 mb-3">Personal Information</h3>
                            <ul>
                                <li>Name and email address when you create an account</li>
                                <li>Profile information you provide</li>
                                <li>Payment information when making purchases</li>
                                <li>Reading history and preferences</li>
                            </ul>

                            <h3 class="h5 mb-3 mt-4">Automatically Collected Information</h3>
                            <ul>
                                <li>Device information and IP address</li>
                                <li>Browser type and settings</li>
                                <li>Usage data and reading patterns</li>
                                <li>Cookies and similar technologies</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">How We Use Your Information</h2>
                            <ul>
                                <li>To provide and maintain our services</li>
                                <li>To process your transactions</li>
                                <li>To personalize your reading experience</li>
                                <li>To communicate with you about our services</li>
                                <li>To improve our website and services</li>
                                <li>To detect and prevent fraud</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Information Sharing</h2>
                            <p>We do not sell your personal information to third parties. We may share your information with:</p>
                            <ul>
                                <li>Service providers who assist in our operations</li>
                                <li>Law enforcement when required by law</li>
                                <li>Other parties with your explicit consent</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Data Security</h2>
                            <p>We implement appropriate security measures to protect your personal information, including:</p>
                            <ul>
                                <li>Encryption of sensitive data</li>
                                <li>Regular security assessments</li>
                                <li>Access controls and authentication</li>
                                <li>Secure data storage practices</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Your Rights</h2>
                            <p>You have the right to:</p>
                            <ul>
                                <li>Access your personal information</li>
                                <li>Correct inaccurate information</li>
                                <li>Request deletion of your information</li>
                                <li>Opt-out of marketing communications</li>
                                <li>Export your data</li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Cookies Policy</h2>
                            <p>We use cookies and similar technologies to:</p>
                            <ul>
                                <li>Remember your preferences</li>
                                <li>Analyze website traffic</li>
                                <li>Personalize content</li>
                                <li>Improve user experience</li>
                            </ul>
                            <p>You can control cookie settings through your browser preferences.</p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Children's Privacy</h2>
                            <p>Our services are not intended for children under 13. We do not knowingly collect information from children under 13. If you believe we have collected information from a child under 13, please contact us.</p>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 mb-4">Changes to This Policy</h2>
                            <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>
                        </section>

                        <section>
                            <h2 class="h4 mb-4">Contact Us</h2>
                            <p>If you have questions about this privacy policy, please contact us:</p>
                            <ul class="list-unstyled">
                                <li><i class="bi bi-envelope me-2"></i>E_library@gmail.com</li>
                                <li><i class="bi bi-phone me-2"></i>+916302972086</li>
                                <li><i class="bi bi-geo-alt me-2"></i>123 Library Street, Booktown, BK 12345</li>
                            </ul>
                        </section>
                    </div>
                </div>
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