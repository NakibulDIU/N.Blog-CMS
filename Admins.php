<?php
  require_once("Includes/DB.php");
  require_once("Includes/Function.php");
  require_once("Includes/Sessions.php");

  $_SESSION["TrackingURL"]=$_SERVER["PHP_SELF"];
  Confirm_Login();

  // --- HANDLE ADD ADMIN SUBMISSION ---
  if(isset($_POST["Submit"])){
    $UserName        = $_POST["Username"];
    $Name            = $_POST["Name"];
    $Password        = $_POST["Password"];
    $ConfirmPassword = $_POST["ConfirmPassword"];
    
    // Use the currently logged-in admin's name, not hardcoded "nakib"
    $AddedBy = $_SESSION["UserName"];
    
    date_default_timezone_set("Asia/Dhaka");
    $DateTime = date("Y-m-d H:i:s");

    if(empty($UserName)||empty($Password)||empty($ConfirmPassword)){
      $_SESSION["ErrorMessage"]= "All fields must be filled out";
      Redirect_to("Admins.php");
    } elseif (strlen($Password) < 4){
      $_SESSION["ErrorMessage"] = "Password should be greater than 3 characters";
      Redirect_to("Admins.php");
    } elseif ($Password !== $ConfirmPassword){
      $_SESSION["ErrorMessage"] = "Password and Confirm Password should Match";
      Redirect_to("Admins.php");
    } elseif (CheckUserNameExistsOrNot($UserName)) {
      $_SESSION["ErrorMessage"] = "Username Exists. Try Another One!";
      Redirect_to("Admins.php");
    } else {
      // SECURE: Hash the password before saving
      $HashedPassword = password_hash($Password, PASSWORD_DEFAULT);

      global $ConnectingDB;
      $sql = "INSERT INTO admins(datetime, username, password, aname, addedby) 
              VALUES(:dateTime, :userName, :password, :aName, :adminName)";      
      $stmt = $ConnectingDB->prepare($sql);
      $stmt->bindValue(':dateTime', $DateTime);
      $stmt->bindValue(':userName', $UserName);
      $stmt->bindValue(':password', $HashedPassword); // Save the HASH, not the plain text
      $stmt->bindValue(':aName', $Name);
      $stmt->bindValue(':adminName', $AddedBy);

      $Execute = $stmt->execute();
      
      if($Execute){
        $_SESSION["SuccessMessage"] = "New Admin <strong>".$Name."</strong> Added Successfully";
        Redirect_to("Admins.php");
      } else {
        $_SESSION["ErrorMessage"] = "Something went wrong. Try Again!";
        Redirect_to("Admins.php");
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
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
            <a href="Admins.php" class="list-group-item list-group-item-action active"><i class="fas fa-users-cog me-2"></i> Manage Admins</a>
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark">Manage Admins</h2>
            </div>

            <div class="row">
                <!-- Add New Admin Form (Left Side) -->
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-user-plus text-primary me-2"></i>Add New Admin</h5>
                        </div>
                        <div class="card-body">
                            <form action="Admins.php" method="post">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase">Username</label>
                                    <input class="form-control" type="text" name="Username" placeholder="e.g. johndoe">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase">Full Name <span class="text-muted fw-normal">(Optional)</span></label>
                                    <input class="form-control" type="text" name="Name" placeholder="e.g. John Doe">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase">Password</label>
                                    <input class="form-control" type="password" name="Password" placeholder="Min 4 chars">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase">Confirm Password</label>
                                    <input class="form-control" type="password" name="ConfirmPassword" placeholder="Re-type password">
                                </div>

                                <button type="submit" name="Submit" class="btn btn-primary w-100">Create Admin</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Existing Admins Table (Right Side) -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="mb-0 fw-bold">Existing Admins</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-custom">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>Date & Time</th>
                                            <th>Username</th>
                                            <th>Admin Name</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        global $ConnectingDB;
                                        $sql = "SELECT * FROM admins ORDER BY id DESC";
                                        $Execute = $ConnectingDB->query($sql);
                                        $SrNo = 0;
                                        while ($DataRows = $Execute->fetch()) {
                                            $AdminId = $DataRows["id"];
                                            $DateTime = $DataRows["datetime"];
                                            $AdminUserName = $DataRows["username"];
                                            $AdminName = $DataRows["aname"];
                                            $AddedBy = $DataRows["addedby"];
                                            
                                            $SrNo++;
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-muted"><?php echo htmlentities($SrNo); ?></td>
                                            <td class="text-muted small"><?php echo htmlentities($DateTime); ?></td>
                                            <td class="fw-bold text-dark"><?php echo htmlentities($AdminUserName); ?></td>
                                            <td><?php echo htmlentities($AdminName); ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo htmlentities($AddedBy); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="DeleteAdmin.php?id=<?php echo $AdminId; ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure? This cannot be undone.');">
                                                   <i class="fas fa-trash-alt"></i> Delete
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