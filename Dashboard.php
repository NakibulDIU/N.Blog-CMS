<?php
  require_once("Includes/DB.php");
  require_once("Includes/Function.php");
  require_once("Includes/Sessions.php");
  $_SESSION["TrackingURL"]=$_SERVER["PHP_SELF"];
  Confirm_Login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="CSS/Style.css">
</head>
<body>

<div class="d-flex" id="wrapper">

    <div class="border-end" id="sidebar-wrapper">
        <div class="sidebar-heading">
            <i class="fas fa-layer-group"></i> CMS<span style="color:var(--primary)">.Pro</span>
        </div>
        <div class="list-group list-group-flush">
            <a href="Dashboard.php" class="list-group-item list-group-item-action active">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="AddNewPost.php" class="list-group-item list-group-item-action">
                <i class="fas fa-pen me-2"></i> Add New Post
            </a>
            <a href="Posts.php" class="list-group-item list-group-item-action ">
                <i class="fas fa-list me-2"></i> All Posts
            </a>
            <a href="Categories.php" class="list-group-item list-group-item-action">
                <i class="fas fa-tags me-2"></i> Categories
            </a>
            <a href="Admins.php" class="list-group-item list-group-item-action">
                <i class="fas fa-users-cog me-2"></i> Manage Admins
            </a>
            <a href="Comments.php" class="list-group-item list-group-item-action">
                <i class="fas fa-comments me-2"></i> Comments
            </a>
            <a href="Blog.php?page=1" class="list-group-item list-group-item-action" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i> Live Blog
            </a>
        </div>
        <div class="mt-auto p-3">
            <a href="Logout.php" class="btn btn-outline-danger w-100">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
            <button class="btn btn-light" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3 text-muted small"><i class="fas fa-user-circle me-1"></i> Admin</span>
                <a href="MyProfile.php" class="btn btn-sm btn-outline-primary">Profile</a>
            </div>
        </nav>

        <div class="container-fluid p-4">
            <?php 
                echo ErrorMessage(); 
                echo SuccessMessage(); 
            ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Dashboard Overview</h2>
                <div>
                    <a href="AddNewPost.php" class="btn btn-success shadow-sm">
                        <i class="fas fa-plus me-1"></i> New Post
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card bg-white p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold text-uppercase">Total Posts</p>
                            <h3 class="fw-bold mb-0"><?php TotalPosts(); ?></h3>
                        </div>
                        <div class="icon-box bg-light text-primary">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card bg-white p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold text-uppercase">Categories</p>
                            <h3 class="fw-bold mb-0"><?php TotalCategories(); ?></h3>
                        </div>
                        <div class="icon-box bg-light text-warning">
                            <i class="fas fa-folder"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card bg-white p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold text-uppercase">Admins</p>
                            <h3 class="fw-bold mb-0"><?php TotalAdmins(); ?></h3>
                        </div>
                        <div class="icon-box bg-light text-danger">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card bg-white p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-bold text-uppercase">Comments</p>
                            <h3 class="fw-bold mb-0"><?php TotalComments(); ?></h3>
                        </div>
                        <div class="icon-box bg-light text-success">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold">Recent Posts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Title</th>
                                    <th>Date</th>
                                    <th>Author</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                global $ConnectingDB;
                                $sql = "SELECT * FROM posts ORDER BY id desc LIMIT 0,5";
                                $stmt = $ConnectingDB->query($sql);
                                while ($DataRows = $stmt->fetch()) {
                                    $PostId   = $DataRows["id"];
                                    $DateTime = $DataRows["datetime"];
                                    $Author   = $DataRows["author"];
                                    $Title    = $DataRows["title"];
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?php echo $Title; ?></td>
                                    <td class="text-muted small"><?php echo $DateTime; ?></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo $Author; ?></span></td>
                                    <td>
                                        <?php 
                                        $Total = ApproveCommentsAccordingtoPost($PostId);
                                        echo $Total > 0 ? "<span class='badge bg-success'>$Total</span>" : "<span class='text-muted small'>0</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="EditPost.php?id=<?php echo $PostId; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="DeletePost.php?id=<?php echo $PostId; ?>" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle Sidebar Functionality
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