<?php
  require_once("Includes/DB.php");
  require_once("Includes/Function.php");
  require_once("Includes/Sessions.php");
  $_SESSION["TrackingURL"]=$_SERVER["PHP_SELF"];
  Confirm_Login();

  if(isset($_POST["Submit"])){
    $PostTitle    = $_POST["PostTitle"];
    $Category     = $_POST["Category"];
    $Image        = $_FILES["Image"]["name"];
    $Target       = "Uploads/".basename($_FILES["Image"]["name"]);
    $PostText     = $_POST["PostDescription"];
    $Admin        = $_SESSION["UserName"]; 
    
    date_default_timezone_set("Asia/Dhaka");
    $DateTime     = date("F d, Y h:i:s A");

    if(empty($PostTitle)){
      $_SESSION["ErrorMessage"]= "Title Can't be empty";
    } elseif (strlen($PostTitle) < 5){
      $_SESSION["ErrorMessage"] = "Post title should be at least 5 characters";
    } elseif (strlen($PostText) > 9999){
      $_SESSION["ErrorMessage"] = "Post Description is too long (Max 9999 chars)";
    } else {
      if(move_uploaded_file($_FILES["Image"]["tmp_name"],$Target)){
          $sql = "INSERT INTO posts(title,category,author,image,post,datetime) VALUES(:title,:category,:authorName,:image,:post,:dateTime)";
          $stmt = $ConnectingDB->prepare($sql);
          $stmt->bindValue(':title',$PostTitle);
          $stmt->bindValue(':category',$Category);
          $stmt->bindValue(':authorName',$Admin);
          $stmt->bindValue(':image',$Image);
          $stmt->bindValue(':post',$PostText);
          $stmt->bindValue(':dateTime',$DateTime);
          
          $Execute=$stmt->execute();
          if($Execute){
            $_SESSION["SuccessMessage"] = "Post Published Successfully";
            Redirect_to("Posts.php");
          } else {
            $_SESSION["ErrorMessage"] = "Database error. Please try again.";
            Redirect_to("AddNewPost.php");
          }
      } else {
          $_SESSION["ErrorMessage"] = "Something went wrong with image upload.";
          Redirect_to("AddNewPost.php");
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    

    <link rel="stylesheet" href="CSS/Style.css">
</head>
<body>

<div class="d-flex" id="wrapper">

    <div class="border-end" id="sidebar-wrapper">
        <div class="sidebar-heading"><i class="fas fa-layer-group"></i> CMS<span style="color:var(--primary)">.Pro</span></div>
        <div class="list-group list-group-flush">
            <a href="Dashboard.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            <a href="AddNewPost.php" class="list-group-item list-group-item-action active"><i class="fas fa-pen me-2"></i> Add New Post</a>
            <a href="Posts.php" class="list-group-item list-group-item-action"><i class="fas fa-list me-2"></i> All Posts</a>
            <a href="Categories.php" class="list-group-item list-group-item-action"><i class="fas fa-tags me-2"></i> Categories</a>
            <a href="Admins.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i> Manage Admins</a>
            <a href="Comments.php" class="list-group-item list-group-item-action"><i class="fas fa-comments me-2"></i> Comments</a>
        </div>
        <div class="mt-auto p-3">
            <a href="Logout.php" class="btn btn-outline-danger w-100"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
            <button class="btn btn-light" id="menu-toggle"><i class="fas fa-bars"></i></button>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 text-muted small"><i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['UserName']; ?></span>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <?php echo ErrorMessage(); echo SuccessMessage(); ?>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h4 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-success"></i>Create New Post</h4>
                        </div>
                        <div class="card-body">
                            <form action="AddNewPost.php" method="post" enctype="multipart/form-data">
                                
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold small text-uppercase">Post Title</label>
                                        <input class="form-control form-control-lg" type="text" name="PostTitle" id="title" placeholder="Enter an engaging title">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-uppercase">Category</label>
                                        <select class="form-select form-select-lg" name="Category">
                                            <?php
                                            global $ConnectingDB;
                                            $sql = "SELECT * FROM category";
                                            $stmt = $ConnectingDB->query($sql);
                                            while($DataRows = $stmt->fetch()){
                                                $CategoryName = $DataRows["title"];
                                            ?>
                                                <option value="<?php echo $CategoryName; ?>"><?php echo $CategoryName; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase">Post Image</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-image text-muted"></i></span>
                                        <input class="form-control" type="File" name="Image" id="imageSelect">
                                    </div>
                                    <div class="form-text">Recommended size: 1200x600px. Max size: 5MB.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase">Content</label>
                                    <textarea class="form-control" name="PostDescription" id="PostDescription" rows="10" placeholder="Write your masterpiece here..."></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="Posts.php" class="btn btn-light text-muted px-4">Cancel</a>
                                    <button type="submit" name="Submit" class="btn btn-success px-5">Publish Now</button>
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
    document.addEventListener("DOMContentLoaded", function() {
        const menuToggle = document.getElementById("menu-toggle");
        const wrapper = document.getElementById("wrapper");
        
        if (menuToggle) {
            menuToggle.addEventListener("click", function(e) {
                e.preventDefault();
                wrapper.classList.toggle("toggled");
            });
        }
    });
</script>

</body>
</html>