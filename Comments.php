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
    <title>Manage Comments</title>
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
            <a href="Posts.php" class="list-group-item list-group-item-action"><i class="fas fa-list me-2"></i> All Posts</a>
            <a href="Categories.php" class="list-group-item list-group-item-action"><i class="fas fa-tags me-2"></i> Categories</a>
            <a href="Admins.php" class="list-group-item list-group-item-action"><i class="fas fa-users-cog me-2"></i> Manage Admins</a>
            <a href="Comments.php" class="list-group-item list-group-item-action active"><i class="fas fa-comments me-2"></i> Comments</a>
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

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Manage Comments</h2>
            </div>

            <!-- PENDING COMMENTS SECTION -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-header bg-warning bg-opacity-10 text-dark py-3 border-0">
                    <h5 class="mb-0 fw-bold text-warning-dark"><i class="fas fa-exclamation-circle me-2"></i> Un-Approved Comments (Pending)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="50">#</th>
                                    <th>Date & Time</th>
                                    <th width="150">Name</th>
                                    <th>Comment</th>
                                    <th width="120" class="text-center">Actions</th>
                                    <th width="140">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                global $ConnectingDB;
                                $sql = "SELECT * FROM comments WHERE status='OFF' ORDER BY id DESC";
                                $Execute = $ConnectingDB->query($sql);
                                $SrNo = 0;
                                while ($DataRows = $Execute->fetch()) {
                                    $CommentId = $DataRows["id"];
                                    $DateTimeOfComment = $DataRows["datetime"];
                                    $CommenterName = $DataRows["name"];
                                    $CommentContent = $DataRows["comment"];
                                    $CommentPostId = $DataRows["post_id"];
                                    $SrNo++;
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?php echo htmlentities($SrNo); ?></td>
                                    <td class="small text-muted"><?php echo htmlentities($DateTimeOfComment); ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlentities($CommenterName); ?></td>
                                    <td>
                                        <div class="text-truncate-custom" style="max-width: 300px; color: #6b7280;">
                                            <?php echo htmlentities($CommentContent); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="ApproveComments.php?id=<?php echo $CommentId; ?>" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="DeleteComments.php?id=<?php echo $CommentId; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this comment?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary w-100" href="FullPost.php?id=<?php echo $CommentPostId; ?>" target="_blank">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php if($Execute->rowCount() == 0): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No pending comments found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- APPROVED COMMENTS SECTION -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success bg-opacity-10 text-dark py-3 border-0">
                    <h5 class="mb-0 fw-bold text-success-dark"><i class="fas fa-check-circle me-2"></i> Approved Comments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-custom">
                            <thead>
                                <tr>
                                    <th class="ps-4" width="50">#</th>
                                    <th>Date & Time</th>
                                    <th width="150">Name</th>
                                    <th>Comment</th>
                                    <th width="150">Approved By</th>
                                    <th width="120" class="text-center">Actions</th>
                                    <th width="140">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM comments WHERE status='ON' ORDER BY id DESC";
                                $Execute = $ConnectingDB->query($sql);
                                $SrNo = 0;
                                while ($DataRows = $Execute->fetch()) {
                                    $CommentId = $DataRows["id"];
                                    $DateTimeOfComment = $DataRows["datetime"];
                                    $CommenterName = $DataRows["name"];
                                    $ApprovedBy = $DataRows["approvedby"];
                                    $CommentContent = $DataRows["comment"];
                                    $CommentPostId = $DataRows["post_id"];
                                    $SrNo++;
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?php echo htmlentities($SrNo); ?></td>
                                    <td class="small text-muted"><?php echo htmlentities($DateTimeOfComment); ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlentities($CommenterName); ?></td>
                                    <td>
                                        <div class="text-truncate-custom" style="max-width: 300px; color: #6b7280;">
                                            <?php echo htmlentities($CommentContent); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?php echo htmlentities($ApprovedBy); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="DisApproveComments.php?id=<?php echo $CommentId; ?>" class="btn btn-sm btn-warning" title="Revert/Disapprove">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="DeleteComments.php?id=<?php echo $CommentId; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this comment?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary w-100" href="FullPost.php?id=<?php echo $CommentPostId; ?>" target="_blank">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
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
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("wrapper").classList.toggle("toggled");
    });
</script>
</body>
</html>