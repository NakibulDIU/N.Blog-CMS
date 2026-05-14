<?php
require_once("Includes/DB.php");
require_once("Includes/Function.php");
require_once("Includes/Sessions.php");

 $_SESSION["TrackingURL"] = $_SERVER["PHP_SELF"];
Confirm_Login();

 $AdminId = $_SESSION["UserId"];
global $ConnectingDB;

// Fetch existing Admin Data
 $sql = "SELECT * FROM admins WHERE id='$AdminId'";
 $stmt = $ConnectingDB->query($sql);
while ($DataRows = $stmt->fetch()) {
    $ExistingName     = $DataRows['aname'];
    $ExistingUsername = $DataRows['username'];
    $ExistingHeadline = $DataRows['aheadline'];
    $ExistingBio      = $DataRows['abio'];
    $ExistingImage    = $DataRows['animage'];
}

// Handle Form Submission
if (isset($_POST["Submit"])) {
    $Name     = $_POST["Name"];
    $Headline = $_POST["Headline"];
    $Bio      = $_POST["Bio"];
    $Image    = $_FILES["Image"]["name"];
    $Target    = "Images/".basename($_FILES["Image"]["name"]);
    
      
    // Validation
    if (strlen($Headline) > 30) {
        $_SESSION["ErrorMessage"] = "Headline should be less than 30 characters";
        Redirect_to("MyProfile.php");
    } elseif (strlen($Bio) > 500) {
        $_SESSION["ErrorMessage"] = "Bio should be less than 500 characters";
        Redirect_to("MyProfile.php");
    } else {
        // Query to Update Admin Data
        global $ConnectingDB;
        
        if (!empty($Image)) {
            // Update with new image
            $sql = "UPDATE admins SET aname=:name, aheadline=:headline, abio=:bio, animage=:image WHERE id=:id";
            $stmt = $ConnectingDB->prepare($sql);
            $stmt->bindValue(':name', $Name);
            $stmt->bindValue(':headline', $Headline);
            $stmt->bindValue(':bio', $Bio);
            $stmt->bindValue(':image', $Image);
            $stmt->bindValue(':id', $AdminId);
            
            $Execute = $stmt->execute();
            if ($Execute) {
                move_uploaded_file($_FILES["Image"]["tmp_name"], $Target);
            }
        } else {
            // Update without changing image
            $sql = "UPDATE admins SET aname=:name, aheadline=:headline, abio=:bio WHERE id=:id";
            $stmt = $ConnectingDB->prepare($sql);
            $stmt->bindValue(':name', $Name);
            $stmt->bindValue(':headline', $Headline);
            $stmt->bindValue(':bio', $Bio);
            $stmt->bindValue(':id', $AdminId);
            
            $Execute = $stmt->execute();
        }

        if ($Execute) {
            $_SESSION["SuccessMessage"] = "Details Updated Successfully";
            Redirect_to("MyProfile.php");
        } else {
            $_SESSION["ErrorMessage"] = "Something went wrong. Try Again!";
            Redirect_to("MyProfile.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (Consistent with Blog) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container">
            <a href="Dashboard.php" class="navbar-brand fw-bold">N.<span style="color:#198754">CMS</span></a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarcollapseCMS">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarcollapseCMS">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a href="Dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="Posts.php" class="nav-link">Posts</a>
                    </li>
                    <li class="nav-item">
                        <a href="Categories.php" class="nav-link">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a href="Admins.php" class="nav-link">Manage Admins</a>
                    </li>
                    <li class="nav-item">
                        <a href="Comments.php" class="nav-link">Comments</a>
                    </li>
                    <li class="nav-item">
                        <a href="Blog.php?page=1" class="nav-link" target="_blank"><i class="fas fa-external-link-alt me-1"></i> Live Blog</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="MyProfile.php" class="nav-link active text-success">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlentities($ExistingUsername); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="Logout.php" class="nav-link text-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- NAVBAR END -->

    <!-- MAIN CONTAINER -->
    <div class="container py-5">
        <?php echo ErrorMessage(); echo SuccessMessage(); ?>
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="border-bottom pb-2"><i class="fas fa-user-edit text-success me-2"></i> Edit My Profile</h2>
                <p class="text-muted">Update your personal details, bio, and profile picture.</p>
            </div>
        </div>

        <div class="row">
            <!-- Left Area: Profile Preview -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header header-bg py-3">
                        <h5 class="mb-0 text-white"><i class="fas fa-id-card me-2"></i> Current Info</h5>
                    </div>
                    <div class="card-body text-center">
                        <!-- Profile Image -->
                        <div class="mb-3">
                            <?php 
                                $imageSrc = !empty($ExistingImage) ? "Images/" . htmlentities($ExistingImage) : "https://via.placeholder.com/150";
                            ?>
                            <img src="<?php echo $imageSrc; ?>" class="rounded-circle shadow-sm" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                        </div>
                        
                        <h4 class="fw-bold"><?php echo htmlentities($ExistingName); ?></h4>
                        <p class="text-muted small mb-3"><i class="fas fa-at me-1"></i> <?php echo htmlentities($ExistingUsername); ?></p>
                        
                        <hr>
                        
                        <div class="text-start mt-3">
                            <h6 class="fw-bold text-uppercase text-muted" style="font-size: 0.75rem;">Headline</h6>
                            <p class="mb-3"><?php echo htmlentities($ExistingHeadline); ?></p>
                            
                            <h6 class="fw-bold text-uppercase text-muted" style="font-size: 0.75rem;">Bio</h6>
                            <p class="small text-secondary">
                                <?php echo htmlentities($ExistingBio); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Area: Edit Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Update Details</h5>
                    </div>
                    <div class="card-body bg-light">
                        <form action="MyProfile.php" method="post" enctype="multipart/form-data">
                            
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="Name" class="form-label fw-bold small">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                    <!-- FIX: Added value attribute -->
                                    <input type="text" class="form-control" name="Name" id="Name" value="<?php echo htmlentities($ExistingName); ?>" placeholder="Enter your full name" required>
                                </div>
                            </div>

                            <!-- Headline -->
                            <div class="mb-3">
                                <label for="Headline" class="form-label fw-bold small">Headline</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-briefcase text-muted"></i></span>
                                    <!-- FIX: Added value attribute -->
                                    <input type="text" class="form-control" name="Headline" id="Headline" value="<?php echo htmlentities($ExistingHeadline); ?>" placeholder="e.g. Senior Developer at TechCorp">
                                </div>
                                <div class="form-text">Max 30 characters. Keep it professional.</div>
                            </div>

                            <!-- Bio -->
                            <div class="mb-3">
                                <label for="Bio" class="form-label fw-bold small">About Me (Bio)</label>
                                <!-- FIX: Added content inside textarea -->
                                <textarea class="form-control" id="Bio" name="Bio" rows="6" placeholder="Tell us a little about yourself..."><?php echo htmlentities($ExistingBio); ?></textarea>
                                <div class="form-text">Max 500 characters.</div>
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label for="Image" class="form-label fw-bold small">Profile Picture</label>
                                <input class="form-control" type="file" name="Image" id="Image">
                                <div class="form-text">Leave empty to keep current image.</div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="Dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Cancel
                                </a>
                                <button type="submit" name="Submit" class="btn btn-success px-4">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END MAIN CONTAINER -->

    <!-- FOOTER -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-1">Theme By | <strong>NAKIBUL</strong> | <span id="year"></span> &copy; All Rights Reserved.</p>
                    <p class="small text-white-50">This is a CMS Dashboard.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Set Current Year
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</body>
</html>