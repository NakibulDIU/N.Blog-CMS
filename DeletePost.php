<?php
  require_once("Includes/DB.php");
  require_once("Includes/Function.php");
  require_once("Includes/Sessions.php");
  
  Confirm_Login();

  // Get the ID from the URL
  $SearchQueryParameter = $_GET["id"];
  global $ConnectingDB;

  // --- FETCHING EXISTING CONTENT ---
  // We use a prepared statement here for security as well
  $sql  = "SELECT * FROM posts WHERE id=:id";
  $stmt = $ConnectingDB->prepare($sql);
  $stmt->bindValue(':id', $SearchQueryParameter);
  $stmt->execute();
  $DataRows = $stmt->fetch();

  // If ID doesn't exist, redirect back to Posts
  if (!$DataRows) {
      $_SESSION["ErrorMessage"] = "Post not found!";
      Redirect_to("Posts.php");
  }

  $TitleToBeDeleted    = $DataRows['title'];
  $CategoryToBeDeleted = $DataRows['category'];
  $ImageToBeDeleted    = $DataRows['image'];
  $PostToBeDeleted     = $DataRows['post'];

  // --- HANDLING THE DELETE ACTION ---
  if(isset($_POST["Submit"])){
      // Delete from Database first
      $sql = "DELETE FROM posts WHERE id=:id";
      $stmt = $ConnectingDB->prepare($sql);
      $Execute = $stmt->execute([':id' => $SearchQueryParameter]);
      
      if($Execute){
          // If DB delete was successful, delete physical image from folder
          $Target_Path_To_DELETE_Image = "Uploads/" . $ImageToBeDeleted;
          
          if (file_exists($Target_Path_To_DELETE_Image)) {
              unlink($Target_Path_To_DELETE_Image);
          }
          
          $_SESSION["SuccessMessage"] = "Post Deleted Successfully";
          Redirect_to("Posts.php");
      } else {
          $_SESSION["ErrorMessage"] = "Something went wrong. The post was not deleted.";
          Redirect_to("DeletePost.php?id=" . $SearchQueryParameter);
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Post</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- YOUR EXTERNAL CSS -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<div class="d-flex" id="wrapper">

    <!-- Sidebar -->
    <div class="border-end" id="sidebar-wrapper">
        <div class="sidebar-heading"><i class="fas fa-layer-group"></i> CMS<span style="color:var(--primary)">.Pro</span></div>
        <div class="list-group list-group-flush">
            <a href="Dashboard.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            <a href="AddNewPost.php" class="list-group-item list-group-item-action"><i class="fas fa-pen me-2"></i> Add New Post</a>
            <a href="Posts.php" class="list-group-item list-group-item-action active"><i class="fas fa-list me-2"></i> All Posts</a>
            <a href="Categories.php" class="list-group-item list-group-item-action"><i class="fas fa-tags me-2"></i> Categories</a>
            <a href="Admins.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i> Manage Admins</a>
            <a href="Comments.php" class="list-group-item list-group-item-action"><i class="fas fa-comments me-2"></i> Comments</a>
            <a href="Blog.php?page=1" class="list-group-item list-group-item-action" target="_blank"><i class="fas fa-external-link-alt me-2"></i> Live Blog</a>
        </div>
        <div class="mt-auto p-3">
            <a href="Logout.php" class="btn btn-outline-danger w-100"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
            <button class="btn btn-light" id="menu-toggle"><i class="fas fa-bars"></i></button>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 text-muted small"><i class="fas fa-user-circle me-1"></i> Admin</span>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <?php 
              echo ErrorMessage();
              echo SuccessMessage();
            ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Warning Alert -->
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <strong>Warning!</strong> You are about to delete a post. This action cannot be undone.
                        </div>
                    </div>

                    <form action="DeletePost.php?id=<?php echo $SearchQueryParameter; ?>" method="post">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold text-danger"><i class="fas fa-trash-alt me-2"></i> Delete Post</h4>
                            </div>
                            <div class="card-body bg-light">
                                
                                <!-- Title -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Post Title</label>
                                    <input disabled class="form-control bg-white" type="text" value="<?php echo htmlentities($TitleToBeDeleted); ?>">
                                </div>

                                <!-- Category -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                                    <div class="p-2 bg-white border rounded">
                                        <?php echo htmlentities($CategoryToBeDeleted); ?>
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Existing Image</label>
                                    <div class="text-center bg-white p-3 border rounded">
                                        <?php if(!empty($ImageToBeDeleted)): ?>
                                            <img src="Uploads/<?php echo htmlentities($ImageToBeDeleted); ?>" class="img-fluid rounded shadow-sm" style="max-height: 300px;" alt="Post Image">
                                        <?php else: ?>
                                            <p class="text-muted py-4">No image uploaded.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Content Preview -->
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Post Content</label>
                                    <textarea disabled class="form-control bg-white" rows="8" style="resize: none;"><?php echo htmlentities($PostToBeDeleted); ?></textarea>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between gap-3">
                                    <a href="Posts.php" class="btn btn-outline-secondary flex-fill">
                                        <i class="fas fa-arrow-left me-1"></i> Cancel
                                    </a>
                                    <button type="submit" name="Submit" class="btn btn-danger flex-fill fw-bold">
                                        <i class="fas fa-trash me-1"></i> Yes, Delete Forever
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("wrapper").classList.toggle("toggled");
    });
</script>
</body>
</html>