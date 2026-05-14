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
    <title>My Awesome Blog</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- YOUR EXTERNAL CSS -->
    <link rel="stylesheet" href="CSS/style.css">

    <style>
        /* Specific styles for the Public Blog layout */
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        .nav-link {
            font-weight: 500;
            color: #4b5563 !important;
        }
        .nav-link:hover {
            color: var(--primary) !important;
        }
        .card-img-top {
            height: 240px;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .post-card:hover .card-img-top {
            transform: scale(1.02);
        }
        .badge-category {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--primary);
            font-weight: 600;
            padding: 0.4em 0.8em;
            border-radius: 6px;
        }
        .sidebar-widget {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .sidebar-header {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }
        .recent-post-img {
            width: 70px; height: 70px; object-fit: cover; border-radius: 8px;
        }
    </style>
</head>
<body>

    <!-- PUBLIC NAVBAR -->
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
                <li class="nav-item"><a href="ContactUs.php" class="nav-link">Contact</a></li>
            </ul>
          
          <form action="Blog.php" class="d-flex ms-auto">
            <div class="input-group">
              <input type="text" class="form-control border-end-0" name="Search" placeholder="Search articles...">
              <button class="btn btn-outline-success" type="submit" name="SearchButton"><i class="fas fa-search"></i></button>
            </div>
          </form>
        </div>
      </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <div class="container py-5">
      <div class="row">
        
        <!-- MAIN CONTENT AREA (Left Side) -->
        <div class="col-lg-8">
          
          <!-- Welcome Header -->
          <div class="mb-5">
            <h1 class="fw-bold text-dark display-5">Insights & News</h1>
            <p class="lead text-muted">Latest stories, tutorials, and updates from our team.</p>
          </div>

          <?php echo ErrorMessage(); echo SuccessMessage(); ?>

          <?php
          global $ConnectingDB;
          // SQL Logic (Search / Category / Pagination)
          if(isset($_GET["SearchButton"])){
            $Search = $_GET["Search"];
            $sql = "SELECT * FROM posts
            WHERE datetime LIKE :search
            OR title LIKE :search
            OR category LIKE :search
            OR post LIKE :search";
            $stmt = $ConnectingDB->prepare($sql);
            $stmt->bindValue(':search','%'.$Search.'%');
            $stmt->execute();
          } elseif (isset($_GET["page"])) {
            $Page = $_GET["page"];
            if($Page==0||$Page<1){ $ShowPostFrom=0; }
            else{ $ShowPostFrom=($Page*5)-5; }
            $sql ="SELECT * FROM posts ORDER BY id desc LIMIT $ShowPostFrom,5";
            $stmt=$ConnectingDB->query($sql);
          } elseif (isset($_GET["category"])) {
            $Category = $_GET["category"];
            $sql = "SELECT * FROM posts WHERE category='$Category' ORDER BY id desc";
            $stmt=$ConnectingDB->query($sql);
          } else {
            $sql  = "SELECT * FROM posts ORDER BY id desc LIMIT 0,3";
            $stmt =$ConnectingDB->query($sql);
          }

          while ($DataRows = $stmt->fetch()) {
            $PostId          = $DataRows["id"];
            $DateTime        = $DataRows["datetime"];
            $PostTitle       = $DataRows["title"];
            $Category        = $DataRows["category"];
            $Admin           = $DataRows["author"];
            $Image           = $DataRows["image"];
            $PostDescription = $DataRows["post"];
          ?>

          <!-- POST CARD -->
          <div class="card mb-5 border-0 shadow-sm post-card">
            <?php if(!empty($Image)): ?>
            <img src="Uploads/<?php echo htmlentities($Image); ?>" class="card-img-top" alt="Blog Image">
            <?php endif; ?>
            
            <div class="card-body p-4">
                <div class="mb-3 d-flex align-items-center text-muted small">
                    <span class="badge-category me-3">
                        <i class="fas fa-folder-open me-1"></i> <?php echo htmlentities($Category); ?>
                    </span>
                    <span><i class="far fa-clock me-1"></i> <?php echo htmlentities($DateTime); ?></span>
                </div>

                <h2 class="card-title fw-bold mb-3">
                    <a href="FullPost.php?id=<?php echo $PostId; ?>" class="text-dark text-decoration-none">
                        <?php echo htmlentities($PostTitle); ?>
                    </a>
                </h2>
                
                <!-- CONTENT DISPLAY -->
                <p class="card-text text-secondary">
                    <?php 
                    // NOTE: We removed htmlentities() so the HTML from TinyMCE renders correctly.
                    // We use strip_tags only for the excerpt length calculation if needed, 
                    // but here we use a safer substr approach.
                    if (strlen($PostDescription) > 150) { 
                        echo substr($PostDescription, 0, 150) . "..."; 
                    } else { 
                        echo $PostDescription; 
                    } 
                    ?>
                </p>
                
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-user me-1"></i> By <a href="Profile.php?username=<?php echo htmlentities($Admin); ?>" class="text-decoration-none fw-bold"><?php echo htmlentities($Admin); ?></a>
                    </div>
                    <a href="FullPost.php?id=<?php echo $PostId; ?>" class="btn btn-success rounded-pill px-4">
                        Read More <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                
                <div class="mt-2">
                    <span class="badge bg-secondary">Comments: <?php echo ApproveCommentsAccordingtoPost($PostId); ?></span>
                </div>
            </div>
          </div>
          <?php } ?>

          <!-- PAGINATION -->
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
              <?php 
              // Backward Button
              if( isset($Page) && $Page > 1 ) { ?>
                <li class="page-item">
                    <a class="page-link" href="Blog.php?page=<?php echo $Page-1; ?>" tabindex="-1">Previous</a>
                </li>
              <?php } ?>

              <?php
              // Page Numbers
              global $ConnectingDB;
              $sql = "SELECT COUNT(*) FROM posts";
              $stmt = $ConnectingDB->query($sql);
              $RowPagination = $stmt->fetch();
              $TotalPosts = array_shift($RowPagination);
              $PostPagination = ceil($TotalPosts/5);
              
              for ($i=1; $i <= $PostPagination; $i++) {
                  if(isset($Page) && $i == $Page) { ?>
                    <li class="page-item active"><a class="page-link" href="#"><?php echo $i; ?></a></li>
                  <?php } else { ?>
                    <li class="page-item"><a class="page-link" href="Blog.php?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                  <?php }
              } 
              
              // Forward Button
              if ( isset($Page) && $Page+1 <= $PostPagination ) { ?>
                <li class="page-item">
                    <a class="page-link" href="Blog.php?page=<?php echo $Page+1; ?>">Next</a>
                </li>
              <?php } ?>
            </ul>
          </nav>
        </div> 
        <!-- End Main Area -->

        <!-- SIDEBAR (Right Side) -->
        <div class="col-lg-4">
            
            <!-- Widget 1: About/Join -->
            <div class="card sidebar-widget mb-4">
                <div class="card-body text-center p-4">
                    <img src="images/N.jpg" class="img-fluid rounded-circle mb-3" style="max-width: 100px;" alt="Logo">
                    <h5 class="fw-bold">Welcome to N.Blog</h5>
                    <p class="small text-muted">Exploring web development, design, and technology. Join our community today.</p>
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <a href="#" class="btn btn-sm btn-outline-primary"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-info"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-danger"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Widget 2: Categories -->
            <div class="card sidebar-widget mb-4">
                <div class="card-header sidebar-header py-3">
                    <i class="fas fa-layer-group me-2"></i> Categories
                </div>
                <div class="list-group list-group-flush">
                    <?php
                    global $ConnectingDB;
                    $sql = "SELECT * FROM category ORDER BY id desc";
                    $stmt = $ConnectingDB->query($sql);
                    while ($DataRows = $stmt->fetch()) {
                        $CategoryName = $DataRows["title"];
                    ?>
                        <a href="Blog.php?category=<?php echo urlencode($CategoryName); ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span><?php echo htmlentities($CategoryName); ?></span>
                            <i class="fas fa-chevron-right small text-muted"></i>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <!-- Widget 3: Recent Posts -->
            <div class="card sidebar-widget mb-4">
                <div class="card-header sidebar-header py-3">
                    <i class="fas fa-bolt me-2"></i> Recent Posts
                </div>
                <div class="card-body p-0">
                    <?php
                    $sql = "SELECT * FROM posts ORDER BY id desc LIMIT 0,5";
                    $stmt = $ConnectingDB->query($sql);
                    while ($DataRows = $stmt->fetch()) {
                        $Id = $DataRows['id'];
                        $Title = $DataRows['title'];
                        $DateTime = $DataRows['datetime'];
                        $Image = $DataRows['image'];
                    ?>
                        <div class="d-flex p-3 border-bottom hover-bg-light">
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
    <div class="container text-center">
        <p class="mb-1">Theme By | <strong>NAKIBUL</strong> | <span id="year"></span> &copy; All Rights Reserved.</p>
        <div class="mb-3">
            <a href="AboutUs.php" class="text-white-50 text-decoration-none me-3">About Us</a>
            <a href="ContactUs.php" class="text-white-50 text-decoration-none me-3">Contact Us</a>
            
        </div>
    </div>
</footer>
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>
</html>