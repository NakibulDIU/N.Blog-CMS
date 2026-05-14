<?php
 require_once("Includes/DB.php");
 require_once("Includes/Function.php");
 require_once("Includes/Sessions.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | N.Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        .navbar-brand { font-weight: 800; font-size: 1.5rem; color: var(--primary) !important; }
        .nav-link { font-weight: 500; color: #4b5563 !important; }
        .about-header { background: linear-gradient(135deg, #198754 0%, #115e3b 100%); color: white; padding: 80px 0; border-radius: 0 0 50px 50px; }
        .feature-icon { font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
      <div class="container">
        <a href="Blog.php" class="navbar-brand"> N.<span style="color:#000">Blog</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarBlog">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarBlog">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><a href="Blog.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="AboutUs.php" class="nav-link active">About Us</a></li>
            <li class="nav-item"><a href="Blog.php" class="nav-link">Blog</a></li>
            <li class="nav-item"><a href="ContactUs.php" class="nav-link">Contact</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <header class="about-header text-center">
        <div class="container">
            <h1 class="display-3 fw-bold">Into the New Era</h1>
            <p class="lead">Exploring Technology, Inventions, and the Digital Revolution.</p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-4">Who We Are</h2>
                    <p class="text-secondary">Welcome to <strong>N.Blog</strong>, a dedicated platform where curiosity meets innovation. Our mission is to bridge the gap between complex technological advancements and the enthusiasts eager to learn about them.</p>
                    <p class="text-secondary">We focus on the "New Era"—a time defined by rapid digital transformation, groundbreaking inventions, and the ever-evolving landscape of software and hardware engineering.</p>
                </div>
                <div class="col-md-6">
                    <img src="images/N.jpg" class="img-fluid rounded-4 shadow" alt="Innovation">
                </div>
            </div>

            <div class="row g-4 mt-5 text-center">
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                        <i class="fas fa-microchip feature-icon"></i>
                        <h4>Latest Tech</h4>
                        <p class="text-muted">Deep dives into the processors, gadgets, and systems shaping our future.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                        <i class="fas fa-lightbulb feature-icon"></i>
                        <h4>Inventions</h4>
                        <p class="text-muted">Showcasing brilliant ideas that solve real-world problems through digital thinking.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                        <i class="fas fa-globe-americas feature-icon"></i>
                        <h4>Digital Era</h4>
                        <p class="text-muted">Analyzing how the shift to digital impacts our society, economy, and lifestyle.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-1">Theme By | <strong>NAKIBUL</strong> | 2026 &copy; All Rights Reserved.</p>
            <div class="mb-3">
                <a href="AboutUs.php" class="text-white-50 text-decoration-none me-3">About Us</a>
                <a href="ContactUs.php" class="text-white-50 text-decoration-none me-3">Contact Us</a>
                
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>