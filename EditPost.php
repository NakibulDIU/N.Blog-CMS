<?php
  require_once("Includes/DB.php");
  require_once("Includes/Function.php");
  require_once("Includes/Sessions.php");

  Confirm_Login();

  $SearchQueryParameter = $_GET["id"];

  // --- HANDLE UPDATE SUBMISSION ---
  if(isset($_POST["Submit"])){
    $PostTitle    = $_POST["PostTitle"];
    $Category     = $_POST["Category"];
    $Image        = $_FILES["Image"]["name"];
    $Target       = "Uploads/".basename($_FILES["Image"]["name"]);
    $PostText     = $_POST["PostDescription"];
    $Admin        = $_SESSION["UserName"];
    
    date_default_timezone_set("Asia/Dhaka");
    $CurrentTime  = date("F d, Y");
    $DateTime     = date("F d, Y h:i:s A");

    // Validation
    if(empty($PostTitle)){
      $_SESSION["ErrorMessage"]= "Title Can't be empty";
      Redirect_to("EditPost.php?id=$SearchQueryParameter");
    } elseif (strlen($PostTitle) < 5){
      $_SESSION["ErrorMessage"] = "Post title should be at least 5 characters";
      Redirect_to("EditPost.php?id=$SearchQueryParameter");
    } elseif (strlen($PostText) > 9999){
      $_SESSION["ErrorMessage"] = "Post Description is too long";
      Redirect_to("EditPost.php?id=$SearchQueryParameter");
    } else {
      // Check if a new image is uploaded
      if(!empty($Image)){
          // UPDATE WITH NEW IMAGE
          if(move_uploaded_file($_FILES["Image"]["tmp_name"], $Target)){
              $sql = "UPDATE posts SET title=:title, category=:category, image=:image, post=:post WHERE id=:id";
              $stmt = $ConnectingDB->prepare($sql);
              $stmt->bindValue(':title', $PostTitle);
              $stmt->bindValue(':category', $Category);
              $stmt->bindValue(':image', $Image);
              $stmt->bindValue(':post', $PostText);
              $stmt->bindValue(':id', $SearchQueryParameter);
              
              $Execute = $stmt->execute();
              
              if($Execute){
                  $_SESSION["SuccessMessage"] = "Post with Image Updated Successfully";
                  Redirect_to("Posts.php");
              } else {
                  $_SESSION["ErrorMessage"] = "Something went wrong (Image Update Failed)";
                  Redirect_to("EditPost.php?id=$SearchQueryParameter");
              }
          } else {
              $_SESSION["ErrorMessage"] = "File upload failed. Check permissions.";
              Redirect_to("EditPost.php?id=$SearchQueryParameter");
          }
      } else {
          // UPDATE KEEPING OLD IMAGE
          // We fetch the old image name first just to be safe (though logic implies keeping existing)
          // For this logic, we just don't update the 'image' column in SQL
          $sql = "UPDATE posts SET title=:title, category=:category, post=:post WHERE id=:id";
          $stmt = $ConnectingDB->prepare($sql);
          $stmt->bindValue(':title', $PostTitle);
          $stmt->bindValue(':category', $Category);
          $stmt->bindValue(':post', $PostText);
          $stmt->bindValue(':id', $SearchQueryParameter);
          
          $Execute = $stmt->execute();
          
          if($Execute){
            $_SESSION["SuccessMessage"] = "Post Updated Successfully (Image Unchanged)";
            Redirect_to("Posts.php");
          } else {
            $_SESSION["ErrorMessage"] = "Something went wrong. Try Again!";
            Redirect_to("EditPost.php?id=$SearchQueryParameter");
          }
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Matches your CSS) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- TyniMCE -->
     <!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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

                // Fetch Existing Data to pre-fill form
                global $ConnectingDB;
                $sql = "SELECT * FROM posts WHERE id='$SearchQueryParameter'";
                $stmt = $ConnectingDB->query($sql);
                
                // Check if post exists
                if($stmt->rowCount() == 0){
                    $_SESSION["ErrorMessage"] = "Post not found!";
                    Redirect_to("Posts.php");
                }

                while($DataRows=$stmt->fetch()){
                    $TitleToBeUpdated    = $DataRows['title'];
                    $CategoryToBeUpdated = $DataRows['category'];
                    $ImageToBeUpdated    = $DataRows['image'];
                    $PostToBeUpdated     = $DataRows['post'];
                }
            ?>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h4 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-success"></i>Edit Post</h4>
                        </div>
                        <div class="card-body">
                            <form action="EditPost.php?id=<?php echo $SearchQueryParameter;?>" method="post" enctype="multipart/form-data">
                                
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold small text-uppercase">Post Title</label>
                                        <input class="form-control form-control-lg" type="text" name="PostTitle" value="<?php echo htmlentities($TitleToBeUpdated); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-uppercase">Category</label>
                                        <select class="form-select form-select-lg" name="Category">
                                            <?php
                                            global $ConnectingDB;
                                            $sqlCat = "SELECT * FROM category";
                                            $stmtCat = $ConnectingDB->query($sqlCat);
                                            while($DataRowsCat = $stmtCat->fetch()){
                                                $CategoryId    = $DataRowsCat["id"];
                                                $CategoryName  = $DataRowsCat["title"];
                                            ?>
                                                <option value="<?php echo $CategoryName; ?>" 
                                                    <?php if($CategoryName == $CategoryToBeUpdated) { echo 'selected'; } ?>>
                                                    <?php echo $CategoryName; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase">Current Image</label>
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <!-- Fixed path: Uploads (plural) -->
                                        <img src="Uploads/<?php echo htmlentities($ImageToBeUpdated); ?>" class="img-thumbnail-sm shadow-sm" alt="Current Post Image">
                                        <span class="text-muted small">Filename: <?php echo htmlentities($ImageToBeUpdated); ?></span>
                                    </div>
                                    
                                    <label class="form-label fw-bold small text-uppercase mt-2">Upload New Image (Optional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-image text-muted"></i></span>
                                        <input class="form-control" type="File" name="Image">
                                    </div>
                                    <div class="form-text">Leave blank to keep the current image.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase">Content</label>
                                    <textarea class="form-control" name="PostDescription" rows="10"><?php echo htmlentities($PostToBeUpdated); ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="Posts.php" class="btn btn-light text-muted px-4">Cancel</a>
                                    <button type="submit" name="Submit" class="btn btn-success px-5">Update Post</button>
                                </div>

                            </form>
                        </div>
                    </div>
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