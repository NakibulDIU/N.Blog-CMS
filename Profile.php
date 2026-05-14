<?php
require_once("Includes/DB.php");
require_once("Includes/Function.php");
require_once("Includes/Sessions.php");

// Fetching Admin Profile Data
if (isset($_GET["username"])) {
    $SearchQueryParameter = $_GET["username"];
} else {
    $_SESSION["ErrorMessage"] = "Bad Request !!";
    Redirect_to("Blog.php?page=1");
    exit;
}

global $ConnectingDB;
 $sql = "SELECT aname, aheadline, abio, animage FROM admins WHERE username=:userName";
 $stmt = $ConnectingDB->prepare($sql);
 $stmt->bindValue(':userName', $SearchQueryParameter);
 $stmt->execute();
 $Result = $stmt->rowcount();

if ($Result == 1) {
    while ($DataRows = $stmt->fetch()) {
        $ExistingName = $DataRows["aname"];
        $ExistingBio = $DataRows["abio"];
        $ExistingImage = $DataRows["animage"];
        $ExistingHeadline = $DataRows["aheadline"];
    }
} else {
    $_SESSION["ErrorMessage"] = "User not found !!";
    Redirect_to("Blog.php?page=1");
}

// Fetching Recent Posts for Sidebar (Using the new CSS classes)
 $sqlPosts = "SELECT * FROM posts ORDER BY id desc LIMIT 0,5";
 $stmtPosts = $ConnectingDB->query($sqlPosts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities($ExistingName); ?> - Profile</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Merriweather:ital,wght@0,300;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Your External CSS -->
    <link rel="stylesheet" href="CSS/style.css">

    <style>
        /* 
           OVERRIDES FOR PUBLIC PAGE 
           Since your style.css targets the Admin Dark Navbar, 
           we override it here to match the public Blog design (White Navbar).
        */
        .public-nav {
            background-color: #fff !important;
            border-bottom: 1px solid #e5e7eb !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .public-nav .navbar-brand {
            color: #000 !important; 
            font-weight: 800;
        }
        .public-nav .nav-link {
            color: #4b5563 !important; 
            font-weight: 500;
        }
        .public-nav .nav-link:hover, 
        .public-nav .nav-link.active {
            color: #198754 !important; 
        }
        /* Profile Image Styling */
        .profile-avatar {
            width: 100%;
            max-width: 200px;
            height: auto;
            aspect-ratio: 1/1;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        /* Bio Typography */
        .bio-text {
            font-family: 'Merriweather', serif;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #374151;
        }
    </style>
</head>
<body>

    <!-- PUBLIC NAVBAR (Overriding external CSS for White Theme) -->
    <nav class="navbar navbar-expand-lg public-nav sticky-top">
      <div class="container">
        <a href="Blog.php" class="navbar-brand"> N.<span style="color:#198754">Blog</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarBlog">
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarBlog">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><a href="Blog.php" class="nav-link">Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link">About Us</a></li>
            <li class="nav-item"><a href="Blog.php" class="nav-link">Blog</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
          </ul>
          
          <form action="Blog.php" class="d-flex ms-auto">
            <div class="input-group">
              <input type="text" class="form-control border-end-0" name="Search" placeholder="Search...">
              <button class="btn btn-outline-success" type="submit" name="SearchButton"><i class="fas fa-search"></i></button>
            </div>
          </form>
        </div>
      </div>
    </nav>

    <!-- HEADER (Using .header-bg from external CSS) -->
    <header class="header-bg py-4 mb-4">
      <div class="container">
        <div class="row">
          <div class="col-md-12 text-center text-white">
            <h1 class="fw-bold"><i class="fas fa-user-circle me-2 text-success"></i><?php echo htmlentities($ExistingName); ?></h1>
            <p class="lead mb-0"><?php echo htmlentities($ExistingHeadline); ?></p>
          </div>
        </div>
      </div>
    </header>

    <!-- MAIN CONTAINER -->
    <div class="container pb-5">
        <?php echo ErrorMessage(); ?>
        
        <div class="row">
            
            <!-- LEFT COLUMN: Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card text-center h-100">
                    <div class="card-body p-4">
                        <!-- Profile Image -->
                        <div class="mb-3">
                            <?php 
                                // Fix Path: Images/ (Capital I)
                                $imgSrc = !empty($ExistingImage) ? "Images/" . htmlentities($ExistingImage) : "https://via.placeholder.com/400x400";
                            ?>
                            <img src="<?php echo $imgSrc; ?>" class="profile-avatar" alt="Profile Image">
                        </div>

                        <h3 class="fw-bold mb-1"><?php echo htmlentities($ExistingName); ?></h3>
                        <p class="text-success fw-bold small text-uppercase mb-4"><?php echo htmlentities($ExistingHeadline); ?></p>
                        
                        <hr>

                        <!-- Social Links (Static for demo) -->
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-dark"><i class="fab fa-github"></i></a>
                        </div>

                        <a href="Blog.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                            <i class="fas fa-arrow-left me-1"></i> Back to Blog
                        </a>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Bio & Sidebar Widgets -->
            <div class="col-lg-8">
                
                <!-- Bio Card -->
                <div class="card mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-success"></i>Bio</h5>
                    </div>
                    <div class="card-body">
                        <div class="bio-text">
                            <?php echo nl2br(htmlentities($ExistingBio)); ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Posts Widget (Using styles from your external CSS) -->
                <div class="card sidebar-widget">
                    <div class="card-header sidebar-header py-3">
                        <i class="fas fa-bolt me-2"></i> Recent Posts
                    </div>
                    <div class="card-body p-0">
                        <?php
                        while ($DataRows = $stmtPosts->fetch()) {
                            $Id = $DataRows['id'];
                            $Title = $DataRows['title'];
                            $DateTime = $DataRows['datetime'];
                            $Image = $DataRows['image'];
                        ?>
                            <div class="d-flex p-3 border-bottom align-items-center">
                                <img src="Uploads/<?php echo htmlentities($Image); ?>" class="recent-post-img shadow-sm" alt="">
                                <div class="ms-3">
                                    <a class="text-decoration-none text-dark fw-bold small d-block mb-1 line-clamp-2" href="FullPost.php?id=<?php echo $Id; ?>">
                                        <?php echo htmlentities($Title); ?>
                                    </a>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                        <i class="far fa-calendar-alt me-1"></i> <?php echo htmlentities($DateTime); ?>
                                    </p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <p class="mb-1">Theme By | <strong>NAKIBUL</strong> | <span id="year"></span> &copy; All Rights Reserved.</p>
                    <a href="Login.php" class="text-white-50 text-decoration-none small">Admin Login</a>
                </div>
            </div>
        </div>
    </footer>
  
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>
</html>