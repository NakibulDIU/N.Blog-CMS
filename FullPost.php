<?php
require_once("Includes/DB.php");
require_once("Includes/Function.php");
require_once("Includes/Sessions.php");

// Get Post ID from URL
 $SearchQueryParameter = $_GET["id"];

// --- HANDLE COMMENT SUBMISSION ---
if(isset($_POST["Submit"])){
    $Name    = $_POST["CommenterName"];
    $Email   = $_POST["CommenterEmail"];
    $Comment = $_POST["CommenterThoughts"];
       
    date_default_timezone_set("Asia/Dhaka");
    $DateTime = date("Y-m-d H:i:s");

    // Validation
    if(empty($Name)||empty($Email)||empty($Comment)){
      $_SESSION["ErrorMessage"]= "All fields must be filled out";
    } elseif (strlen($Comment)>500){
      $_SESSION["ErrorMessage"] = "Comment length should be less than 500 characters";
    } else {
      // Insert Comment
      global $ConnectingDB;
      $sql = "INSERT INTO comments(datetime, name, email, comment, approvedby, status, post_id) 
              VALUES(:dateTime, :name, :email, :comment, 'Pending', 'OFF', :postIdFromURL)";

      $stmt = $ConnectingDB->prepare($sql);
      $stmt->bindValue(':dateTime', $DateTime);
      $stmt->bindValue(':name', $Name);
      $stmt->bindValue(':email', $Email);
      $stmt->bindValue(':comment', $Comment);
      $stmt->bindValue(':postIdFromURL', $SearchQueryParameter);

      $Execute = $stmt->execute();

      if($Execute){
        $_SESSION["SuccessMessage"] = "Comment Submitted for Approval";
      } else {
        $_SESSION["ErrorMessage"] = "Something went wrong. Try Again!";
      }
    }
    // Redirect to self to clear form and show message
    Redirect_to("FullPost.php?id={$SearchQueryParameter}");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Full Post</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Merriweather:ital,wght@0,300;0,700;1,400&display=swap" rel="stylesheet">
    <!-- YOUR EXTERNAL CSS -->
    <link rel="stylesheet" href="CSS/style.css">

    <style>
        /* Public Blog Styles */
        .navbar-brand { font-weight: 800; font-size: 1.5rem; color: var(--primary) !important; }
        .nav-link { font-weight: 500; color: #4b5563 !important; }
        .nav-link:hover { color: var(--primary) !important; }
        
        .post-content {
            font-family: 'Merriweather', serif; 
            font-size: 1.1rem;
            line-height: 1.8;
            color: #374151;
        }
        .post-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
        .post-content blockquote { border-left: 5px solid var(--primary); padding-left: 20px; font-style: italic; color: #666; }
        
        .comment-box {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .sidebar-widget { background: #fff; border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden; }
        .sidebar-header { background-color: var(--primary); color: white; font-weight: 600; }

        /* FIX: Specific styles for Recent Post Sidebar */
        .recent-post-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0; /* Prevents image from shrinking */
        }
        
        /* Truncates text to 2 lines */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
            <li class="nav-item"><a href="#" class="nav-link">About Us</a></li>
            <li class="nav-item"><a href="Blog.php" class="nav-link">Blog</a></li>
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

    <!-- MAIN CONTAINER -->
    <div class="container py-5">
        <?php echo ErrorMessage(); echo SuccessMessage(); ?>
        
        <div class="row">
            
            <!-- MAIN CONTENT (Article) -->
            <div class="col-lg-8">
                <!-- Fetch Post Data -->
                <?php
                global $ConnectingDB;
                $PostIdFromURL = $_GET["id"];
                if (!isset($PostIdFromURL)) {
                    $_SESSION["ErrorMessage"] = "Bad Request !";
                    Redirect_to("Blog.php?page=1");
                }
                
                $sql  = "SELECT * FROM posts WHERE id= '$PostIdFromURL'";
                $stmt = $ConnectingDB->query($sql);
                $Result = $stmt->rowcount();
                
                if ($Result != 1) {
                    $_SESSION["ErrorMessage"] = "Post not found !";
                    Redirect_to("Blog.php?page=1");
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
                
                <!-- Back Button -->
                <a href="Blog.php" class="btn btn-outline-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to Blog</a>

                <!-- Post Header -->
                <h1 class="fw-bold display-5 mb-3"><?php echo htmlentities($PostTitle); ?></h1>
                
                <div class="d-flex align-items-center text-muted mb-4 border-bottom pb-3">
                    <span class="badge bg-success me-3"><?php echo htmlentities($Category); ?></span>
                    <span class="me-3"><i class="far fa-user me-1"></i> <?php echo htmlentities($Admin); ?></span>
                    <span><i class="far fa-clock me-1"></i> <?php echo htmlentities($DateTime); ?></span>
                </div>

                <!-- Post Image -->
                <?php if(!empty($Image)): ?>
                <img src="Uploads/<?php echo htmlentities($Image); ?>" class="img-fluid rounded shadow-sm mb-4 w-100" alt="Post Image">
                <?php endif; ?>

                <!-- Post Content -->
                <div class="post-content mb-5">
                    <?php echo $PostDescription; ?>
                </div>

                <?php } ?> <!-- End Post While Loop -->

                <!-- Comments Section -->
                <div class="mt-5">
                    <h3 class="fw-bold mb-4"><i class="fas fa-comments me-2"></i> Comments</h3>
                    
                    <span class="FieldInfo text-muted mb-3 d-block">Approved comments:</span>
                    <br><br>
                    
                    <?php 
                    global $ConnectingDB;
                    $sql = "SELECT * FROM comments WHERE post_id='$SearchQueryParameter' AND status='ON' ORDER BY id DESC";
                    $stmt = $ConnectingDB->query($sql);
                    
                    if($stmt->rowCount() > 0){
                        while($DataRows = $stmt->fetch()){
                            $CommentDate   = $DataRows['datetime'];
                            $CommenterName = $DataRows['name'];
                            $CommentContent = $DataRows['comment'];
                    ?>
                        <!-- Single Comment -->
                        <div class="comment-box">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; color: var(--primary);">
                                        <i class="fas fa-user fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-1"><?php echo htmlentities($CommenterName); ?></h6>
                                    <small class="text-muted mb-2 d-block"><?php echo htmlentities($CommentDate); ?></small>
                                    <p class="mb-0 text-secondary"><?php echo htmlentities($CommentContent); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php 
                        } 
                    } else {
                        echo "<p class='text-muted'>No comments yet. Be the first to comment!</p>";
                    }
                    ?>

                    <!-- Comment Form -->
                    <div class="card mt-5 border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Share your thoughts</h5>
                        </div>
                        <div class="card-body">
                            <form action="FullPost.php?id=<?php echo $SearchQueryParameter?>" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                                            <input type="text" name="CommenterName" class="form-control" placeholder="Your Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" name="CommenterEmail" class="form-control" placeholder="Your Email" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Comment</label>
                                        <textarea name="CommenterThoughts" class="form-control" rows="5" placeholder="Write something nice..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="Submit" class="btn btn-success">Submit Comment</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div> 
            <!-- End Main Content -->

            <!-- SIDEBAR -->
            <div class="col-lg-4">
                <!-- Widget: About -->
                <div class="card sidebar-widget mb-4">
                    <div class="card-body text-center p-4">
                        <h5 class="fw-bold mb-3">About This Blog</h5>
                        <p class="small text-muted">A place for tech enthusiasts, developers, and designers to share knowledge and insights.</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-sm btn-outline-primary"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-info"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-danger"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Widget: Recent Posts -->
                <div class="card sidebar-widget mb-4">
                    <div class="card-header sidebar-header py-3">
                        <i class="fas fa-bolt me-2"></i> Recent Posts
                    </div>
                    <div class="card-body p-0">
                        <?php
                        // Selects the 5 most recent posts
                        $sql = "SELECT * FROM posts ORDER BY id desc LIMIT 0,5";
                        $stmt = $ConnectingDB->query($sql);
                        while ($DataRows = $stmt->fetch()) {
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
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>
</html>