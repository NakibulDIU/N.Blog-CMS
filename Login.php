<?php require_once("Includes/DB.php"); ?>
<?php require_once("Includes/Function.php"); ?>
<?php require_once("Includes/Sessions.php"); ?>
<?php
if(isset($_SESSION["UserId"])){ Redirect_to("Dashboard.php"); }

if (isset($_POST["Submit"])) {
  $UserName = $_POST["Username"];
  $Password = $_POST["Password"];
  if (empty($UserName)||empty($Password)) {
    $_SESSION["ErrorMessage"]= "All fields must be filled out";
    Redirect_to("Login.php");
  }else {
    $Found_Account=Login_Attempt($UserName,$Password);
    if ($Found_Account) {
      $_SESSION["UserId"]=$Found_Account["id"];
      $_SESSION["UserName"]=$Found_Account["username"];
      $_SESSION["AdminName"]=$Found_Account["aname"];
      $_SESSION["SuccessMessage"]= "Welcome ".$_SESSION["AdminName"]."!";
      Redirect_to("Dashboard.php");
    }else {
      $_SESSION["ErrorMessage"]="Incorrect Username/Password";
      Redirect_to("Login.php");
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #198754 0%, #0f5132 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background-color: #fff;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .brand-logo {
            font-size: 2rem;
            color: #198754;
            font-weight: bold;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #198754;
        }
        .btn-login {
            background-color: #198754;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }
        .btn-login:hover {
            background-color: #146c43;
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="login-header">
        <div class="brand-logo mb-2">N.</div>
        <h5 class="text-muted">Admin Panel</h5>
    </div>
    <div class="login-body">
        <?php echo ErrorMessage(); echo SuccessMessage(); ?>
        
        <form action="Login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">USERNAME</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" name="Username" class="form-control border-start-0 ps-0" placeholder="Enter your username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small text-muted fw-bold">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="Password" class="form-control border-start-0 ps-0" placeholder="Enter your password" required>
                </div>
            </div>

            <button type="submit" name="Submit" class="btn btn-primary btn-login text-white">
                LOGIN <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="Blog.php" class="text-decoration-none small text-muted">Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>