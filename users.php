<?PHP
ob_start();
session_start();
if(isset($_SESSION['admin_id'])){
    include 'init.php';
    include 'Admin/include/templet/navbar.php';

    if(isset($_GET['page'])){
        $page = $_GET['page'];
      }else{
        $page='All';
    }

    // statement to return all users
    $qusers = $connect->prepare("SELECT * FROM users");
    $qusers->execute();
    $numUsers =$qusers->rowCount();
    $allUsers =$qusers->fetchAll();

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['edit'])){
            $userid = $_POST['user_id'];
            $username = $_POST['username'];
            $email = $_POST['email'];

            $stmt = $connect->prepare("SELECT * FROM users WHERE user_id =?");
            $stmt->execute(array($userid));
            $rows =$stmt->rowCount();

            if($rows > 0){
                $stmtUpdate = $connect->prepare("UPDATE users SET username=? , email=? WHERE user_id=?");
                $stmtUpdate->execute(array($username , $email , $userid));
                $sucss = "<div class='alert alert-success'>success</div>";
                header('Refresh: 1; url=users.php');

            }
        }
    }

     // get user id  to delete
     if(isset($_GET['id']) && !empty($_GET['id'])){
        $userid = intval($_GET['id']);
       // echo $userid;
        // check if user exists in db
        $stmt = $connect->prepare("SELECT * FROM users WHERE user_id =?");
        $stmt->execute(array($userid));
        $rows2 =$stmt->rowCount();
      //  echo $rows;
        if($rows2 > 0){
            $stmt2 = $connect->prepare("DELETE FROM users WHERE user_id =?");
            $stmt2->execute(array($userid));
            $exist = "<div class='alert alert-success'>delet</div>";
            header('Refresh: 1; url=users.php');
        }
        else{
            $error = "<div class='alert alert-danger'>There is no such id</div>";
        }
    }
       

?>

<div class="">
  <h1 class=" text-center">Users Management </h1>
  <div class="container">
      <?php if($page == "All") {  ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-5">
                    <h5 class="card-header">
                        Users
                        <span class="badge bg-primary">
                            <?PHP echo $numUsers; ?>
                        </span>
                    </h5>
                    <?PHP 
                       if(!empty($sucss)){
                           echo $sucss;}
                    ?>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <?PHP 
                                        if($numUsers > 0){
                                        echo "  
                                        <tr>
                                            <th scope='col'>#</th>
                                            <th scope='col'>Username</th>
                                            <th scope='col'>Email</th>
                                            <th scope='col'>Action</th>
                                        </tr>
                                        ";
                                    ?>
                                </thead>
                                <tbody>
                                    <?PHP 
                                        if(!empty($exist)){
                                        echo $exist;
                                        }
                                        if(!empty($error)){
                                        echo $error;
                                        }
                                        foreach($allUsers as $user){
                                            echo "    
                                            <tr> 
                                                <th scope='row'>" . $user['user_id'] ."</th>
                                                <td>" . $user['username'] ."</td>
                                                <td>" . $user['email'] ."</td>
                                                <td>
                                                <a href='?id=".$user['user_id']."' style='text-decoration: none; color:red;' >
                                                    <i class='fa-solid fa-trash-can me-3'></i>
                                                </a>
                                                <a href='?page=editUser&userid=".$user['user_id']."' style='text-decoration: none; color:#0dcaf0;' >
                                                    <i class='fas fa-edit'></i>
                                                </a>
                                                </td>
                                            </tr>";
                                        }
                                        }else{
                                        echo "<div class='alert alert-danger'>There is no users</div>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      <?PHP }elseif($page == "editUser"){
        $userid = $_GET['userid'];
        $stmt = $connect->prepare("SELECT * FROM users WHERE user_id =?");
        $stmt->execute(array($userid));
        $rows =$stmt->rowCount();
    
        if($rows > 0){
            $userData = $stmt->fetch();
        }    
      ?>
        <div class="">
          <form action="<?PHP echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
            <p>Update User Account</p>
            <input type="hidden" name="user_id" value="<?PHP echo $userData['user_id']; ?>"/>
            <div class="form-outline mb-4">
              <label class="form-label" for="form2Example11">Username</label>
              <input type="text" name="username" value="<?PHP echo $userData['username']; ?>" class="form-control" placeholder="username"/>
            </div>

            <div class="form-outline mb-4">
              <label class="form-label" for="form2Example11">Email</label>
              <input type="email" name="email" value="<?PHP echo $userData['email']; ?>" class="form-control" placeholder="email address"/>
            </div>

            <!-- <div class="form-outline mb-4">
              <label class="form-label" for="form2Example22">Password</label>
              <input type="password" name="password" value="<?PHP //echo $userData['password']; ?>" class="form-control"/>
            </div> -->

            <div class="text-center pt-1 mb-5 pb-1">
              <button name="edit" class="btn btn-primary btn-block gradient-custom-2 mb-3" type="submit">edit</button>
            </div>
          </form>
        </div>
      <?PHP } ?>
  </div>
</div>


<?PHP
include 'Admin/include/templet/footer.php';
}else{
    //echo "<div class='alert alert-danger'> you can inter in to the page befor login</div>";
    //header('Refresh: 5; url=login.php ');
    header('Location:error.html');
    exit();
}
ob_end_flush()
?>