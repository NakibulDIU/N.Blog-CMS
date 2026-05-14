<?php
 require_once("Includes/DB.php");
 require_once("Includes/Function.php");
 require_once("Includes/Sessions.php");

 if(isset($_POST["Submit"])){
    $Name    = $_POST["Name"];
    $Email   = $_POST["Email"];
    $Subject = $_POST["Subject"];
    $Message = $_POST["Message"];

    if(empty($Name)||empty($Email)||empty($Message)){
        $_SESSION["ErrorMessage"]= "All fields must be filled out";
        Redirect_to("ContactUs.php");
    }else{
        // Dummy Mail Logic
        $to = "admin@example.com";
        $header = "From: " . $Email;
        $body = "Message from: " . $Name . "\n" . "Subject: " . $Subject . "\n" . "Content: " . $Message;
        
        // mail($to, $Subject, $body, $header); // Uncomment this on a live server
        
        $_SESSION["SuccessMessage"]="Your message has been sent successfully!";
        Redirect_to("ContactUs.php");
    }
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | N.Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        .navbar-brand { font-weight: 800; font-size: 1.5rem; color: var(--primary) !important; }
        .nav-link { font-weight: 500; color: #4b5563 !important; }
        .contact-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .info-box { background-color: var(--primary); color: white; border-radius: 15px; padding: 40px; }
    </style>
</head>
<body class="bg-light">
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
            <li class="nav-item"><a href="AboutUs.php" class="nav-link">About Us</a></li>
            <li class="nav-item"><a href="Blog.php" class="nav-link">Blog</a></li>
            <li class="nav-item"><a href="ContactUs.php" class="nav-link active">Contact</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold">Get In Touch</h1>
            <p class="text-muted">Have a question about the latest tech? We'd love to hear from you.</p>
        </div>

        <?php echo ErrorMessage(); echo SuccessMessage(); ?>

        <div class="row g-0 contact-card bg-white">
            <div class="col-lg-5 info-box d-flex flex-column justify-content-center">
                <h3>Contact Information</h3>
                <p class="mb-5">Fill out the form and our team will get back to you within 24 hours.</p>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-phone-alt me-3"></i> <span>+1 234 567 890</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-envelope me-3"></i> <span>contact@nblog.com</span>
                </div>
                <div class="d-flex align-items-center mb-5">
                    <i class="fas fa-map-marker-alt me-3"></i> <span>123 Digital Era St, Tech City</span>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white fs-4"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="col-lg-7 p-4 p-md-5">
                <form action="ContactUs.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="Name" class="form-control" placeholder="John Doe">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="Email" class="form-control" placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="Subject" class="form-control" placeholder="Inquiry about Tech">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Message</label>
                        <textarea name="Message" rows="5" class="form-control" placeholder="Your message here..."></textarea>
                    </div>
                    <button type="submit" name="Submit" class="btn btn-success px-5 py-2 rounded-pill">Send Message</button>
                </form>
            </div>
        </div>
    </div>

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