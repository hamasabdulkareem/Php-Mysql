<?PHP
ob_start();
include 'init.php';

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['register'])){
            $username = filter_var($_POST['username'] , FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'] , FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'] , FILTER_SANITIZE_STRING);
            $passwordConfirm = filter_var($_POST['password_confirm'] , FILTER_SANITIZE_STRING);

            if (empty($username)) {  
              $usernameErr = "<div class='text-danger'>please enter Username </div>";
            }
            elseif(strlen($username) < 4 ){
              $usernameErr = "<div class='text-danger'>Username Must Greater than 4 Chars</div>";
            }
            elseif(!preg_match("/^[a-zA-Z-' ]*$/",$username)){
              $usernameErr = "<Only letters and white space allowed</div>";
            }
            if(empty($email)) {
              $emailErr = "<div class='text-danger'>please enter Email </div>";
            }
            elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
              $emailErr = "<div class='text-danger'>please Enter the correct formula</div>";
            }
            if(empty($password)) {
              $passErr = "<div class='text-danger'>please enter password </div>";
            }
            if(empty($passwordConfirm)) {
              $passConError = "<div class='text-danger'>please enter passwordConfirm </div>";
            }
            elseif($password != $passwordConfirm) {
              $passConError = "<div class='text-danger'>Password and Confirm password should match!</div>";   
           }

           else{
            $hashedPassword = password_hash($password , PASSWORD_DEFAULT);
            $stmt = $connect->prepare("INSERT INTO users(username,email,password,status) VALUES(? , ? , ? , 1)");
            $stmt->execute(array($username , $email , $hashedPassword ));
            header("Location:login.php");
            exit();
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
                      <p>create User Account</p>
                      <input type="hidden" name="user_id" value="<?PHP echo $userData['user_id']; ?>"/>
                      <div class="form-outline mb-4">
                        <label class="form-label" for="form2Example11">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" />
                        <?PHP if(!empty($usernameErr)){echo $usernameErr; } ?>
                      </div>

                      <div class="form-outline mb-4">
                        <label class="form-label" for="form2Example11">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email address" />
                        <?PHP if(!empty($emailErr)){echo $emailErr; } ?>
                      </div>

                      <div class="form-outline mb-4">
                        <label class="form-label" for="form2Example22">Password</label>
                        <input type="password" name="password"  class="form-control" />
                        <?PHP if(!empty($passErr)){echo $passErr; } ?>
                      </div>

                      <div class="form-outline mb-4">
                        <label class="form-label" for="form2Example22">Password Confirm</label>
                        <input type="password" name="password_confirm"  class="form-control" />
                        <?PHP if(!empty($passConError)){echo $passConError; } ?>
                      </div>

                      <div class="text-center pt-1 mb-5 pb-1">
                        <button name="register" class="btn btn-primary btn-block gradient-custom-2 mb-3" type="submit">Register</button>
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