<?PHP
ob_start();
session_start();
if(isset($_SESSION['admin_id'])){
    include 'init.php';
    include 'Admin/include/templet/navbar.php';


    // statement to return all users
    $qusers = $connect->prepare("SELECT * FROM users");
    $qusers->execute();
    $numUsers =$qusers->rowCount();
    $allUsers =$qusers->fetchAll();

    // statement to return all posts
    $qposts = $connect->prepare("SELECT * FROM posts");
    $qposts->execute();
    $numPosts =$qposts->rowCount();
    $allPosts =$qposts->fetchAll();


    // get user id  to delete
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $userid = intval($_GET['id']);
   
        // check if user exists in db
        $stmt = $connect->prepare("SELECT * FROM users WHERE user_id =?");
        $stmt->execute(array($userid));
        $rows =$stmt->rowCount();
    
        if($rows > 0){
             echo $userid;
            $stmt2 = $connect->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt2->execute(array($userid));
            $exist = "<div class='alert alert-success'>delet</div>";
            header('Refresh: 5; url=index.php');
        }
        else{
            $error = "<div class='alert alert-danger'>There is no such id</div>";
        }
    }

?>

<div class="mb-5">
    <h1 class="text-center">Admin Dashboard</h1>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card mt-5">
                    <h5 class="card-header">
                        Users
                        <span class="badge bg-primary">
                            <?PHP echo $numUsers ?>
                        </span>
                    </h5>
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
                                                  <a href='show_user.php?id=".$user['user_id']."' style='text-decoration: none; color:#0dcaf0;' >
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
            <div class="col-md-6">
                <div class="card mt-5">
                    <h5 class="card-header">
                        Posts 
                        <span class="badge bg-primary">
                           <?PHP echo $numPosts ?>
                        </span>
                    </h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <?PHP 
                                        if($numPosts > 0){
                                        echo "  
                                          <tr>
                                            <th scope='col'>#</th>
                                            <th scope='col'>Title</th>
                                            <th scope='col'>body</th>
                                            <th scope='col'>Author</th>
                                            <th scope='col'>Action</th>
                                          </tr>
                                        ";
                                     ?>
                                </thead>
                                <tbody>
                                <?PHP 
                                        foreach($allPosts as $post){
                                            echo "    
                                            <tr>
                                            <th scope='row'>" . $post['post_id'] ."</th>
                                            <td>" . $post['title'] ."</td>
                                            <td>" . $post['body'] ."</td>
                                            <td>" . $post['user_id'] ."</td>
                                            <td>
                                                <a href='#' style='text-decoration: none; color:red;' >
                                                    <i class='fa-solid fa-trash-can me-3'></i>
                                                </a>
                                                <a href='#' style='text-decoration: none; color:#0dcaf0;' >
                                                    <i class='fas fa-edit'></i>
                                                </a>
                                            </td>
                                            </tr>";
                                        }
                                    }else{
                                        echo "<div class='alert alert-danger'>There is no Posts</div>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?PHP
include 'Admin/include/templet/footer.php';
}else{
   // echo "<div class='alert alert-danger'> you can inter in to the page befor login</div>";
    header('Location:error.html');
    exit();
}
ob_end_flush()
?>