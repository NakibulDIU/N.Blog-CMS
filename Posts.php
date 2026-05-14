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
    <title>Manage Posts</title>
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
            <a href="Dashboard.php" class="list-group-item list-group-item-action">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
            <a href="AddNewPost.php" class="list-group-item list-group-item-action">
                <i class="fas fa-pen me-2"></i> Add New Post
            </a>
            <a href="Posts.php" class="list-group-item list-group-item-action active">
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
            </div>
        </nav>

        <div class="container-fluid p-4">
            <?php 
                echo ErrorMessage(); 
                echo SuccessMessage(); 
            ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark">All Posts</h2>
                    <p class="text-muted mb-0">Manage your blog content</p>
                </div>
                <div>
                    <a href="AddNewPost.php" class="btn btn-success shadow-sm">
                        <i class="fas fa-plus me-2"></i>Add New
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4">Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Author</th>
                                    <th>Comments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                global $ConnectingDB;
                                $sql ="SELECT * FROM posts ORDER BY id desc";
                                $stmt = $ConnectingDB->query($sql);
                                while($DataRows = $stmt->fetch()){
                                    $Id        = $DataRows["id"];
                                    $DateTime  = $DataRows["datetime"];
                                    $PostTitle = $DataRows["title"];
                                    $Category  = $DataRows["category"];
                                    $Admin     = $DataRows["author"];
                                    $Image     = $DataRows["image"];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if(!empty($Image)): ?>
                                            <img src="Uploads/<?php echo htmlentities($Image); ?>" class="img-thumbnail-sm" alt="Post">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width:50px;height:50px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlentities($PostTitle); ?></div>
                                        <div class="small text-muted" style="font-size:0.75rem;">ID: <?php echo $Id; ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?php echo htmlentities($Category); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo htmlentities($DateTime); ?></td>
                                    <td><?php echo htmlentities($Admin); ?></td>
                                    <td>
                                        <?php 
                                            $Total = ApproveCommentsAccordingtoPost($Id);
                                            echo $Total > 0 ? "<span class='badge bg-success rounded-pill'>$Total</span>" : "<span class='text-muted'>0</span>";
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="FullPost.php?id=<?php echo $Id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="EditPost.php?id=<?php echo $Id;?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="DeletePost.php?id=<?php echo $Id;?>" class="btn btn-sm btn-outline-danger" title="Delete">
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