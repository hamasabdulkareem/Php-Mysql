<?PHP
ob_start();
session_start();
if (isset( $_SESSION['user_id'])){
  header("Location:index.php");
  exit();
}
include 'init.php';

// check if user exit in DB
if($_SERVER['REQUEST_METHOD'] == "POST"){
  if(isset($_POST['submit'])){
    $email = filter_var($_POST['email'] , FILTER_SANITIZE_EMAIL);
    $password = filter_var($_POST['password'] , FILTER_SANITIZE_STRING);
    $stmt = $connect->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute(array($email));
    $result = $stmt->rowCount();
    if($result > 0 ){
      $userdata = $stmt->fetch();
      $hashedPassword = $userdata['password'];
        if(password_verify($password , $hashedPassword)){
          if($userdata['type'] === 'admin'){
            $_SESSION['admin_id'] = $userdata['user_id']; 
            $_SESSION['admin_username'] = $userdata['username']; 
            $_SESSION['admin_email'] = $userdata['email']; 
      
            header("Location:index.php");
            exit();
          } 
        elseif($userdata['type'] === 'user'){
          $_SESSION['user_id'] = $userdata['user_id']; 
            $_SESSION['user_username'] = $userdata['username']; 
            $_SESSION['user_email'] = $userdata['email']; 
      
            header("Location:home.php");
            exit();
        }
      
      }else{
        $error = "<div class='text-danger'>worng password</div>";
      }

    }else{
      $error = "<div class='text-danger'>There is no such user</div>";
    }
  }
}
?>

<section class="h-100 gradient-form">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="d-flex justify-content-center align-items-center" >
          <div class="" style="width: 50%; background-color: #eee;">
            <div class="">
              <div class="card-body p-md-5 mx-md-4">
                 <div class="text-center">
                 <h4 class="mt-1 mb-5 pb-1">Admin Login</h4>
                </div>
                <form action="<?PHP echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <?PHP
                  if(!empty($error)){
                    echo $error;
                  }
                ?>
                  <p>Please login to your account</p>
                  <div class="form-outline mb-4">
                    <input type="email" name="email" id="form2Example11" class="form-control" placeholder="email address" require/>
                    <label class="form-label" for="form2Example11">Username</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" name="password" id="form2Example22" class="form-control" require/>
                    <label class="form-label" for="form2Example22">Password</label>
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                    <button name="submit" class="btn btn-primary btn-block gradient-custom-2 mb-3" type="submit">Log in</button>
                    <a class="text-muted" href="#!">Forgot password?</a>
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Don't have an account?</p>
                    <button type="button" class="btn btn-outline-danger"> <a href="register.php">Create new</a></button>
                  </div>

                </form>

              </div>
            </div>
            
          </div>
        
      </div>
    </div>
  </div>
</section>
<?PHP
ob_end_flush()

?>