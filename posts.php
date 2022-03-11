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

     // statement to return all posts
     $qposts = $connect->prepare("SELECT * FROM posts");
     $qposts->execute();
     $numPosts =$qposts->rowCount();
     $allPosts =$qposts->fetchAll();

     if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['edit'])){
            $postid = $_POST['post_id'];
            $title = $_POST['title'];
            $body = $_POST['body'];

            $stmt = $connect->prepare("SELECT * FROM posts WHERE post_id =?");
            $stmt->execute(array($postid));
            $rows =$stmt->rowCount();

            if($rows > 0){
                $stmtUpdate = $connect->prepare("UPDATE posts SET title=? , body=? WHERE post_id=?");
                $stmtUpdate->execute(array($title , $body , $postid));
                $sucss = "<div class='alert alert-success'>success</div>";
                header('Refresh: 1; url=posts.php');

            }
        }
    }

    // get user id  to delete
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $postid = intval($_GET['id']); 
        // check if user exists in db
        $stmt = $connect->prepare("SELECT * FROM posts WHERE post_id =?");
        $stmt->execute(array($postid));
        $rows2 =$stmt->rowCount();
        if($rows2 > 0){
            $stmt2 = $connect->prepare("DELETE FROM posts WHERE post_id =?");
            $stmt2->execute(array($postid));
            $exist = "<div class='alert alert-success'>delet</div>";
            header('Refresh: 2; url=posts.php');
        }
        else{
            $error = "<div class='alert alert-danger'>There is no such id</div>";
        }
    }

?>


<div class="">
  <h1 class=" text-center">Posts Management </h1>
  <div class="container">
      <?php if($page == "All") {  ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-5">
                    <h5 class="card-header">
                        Posts 
                        <span class="badge bg-primary">
                            <?PHP echo $numPosts ?>
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
                                        if(!empty($exist)){
                                        echo $exist;
                                        }
                                        if(!empty($error)){
                                        echo $error;
                                        }
                                        foreach($allPosts as $post){
                                            echo "    
                                            <tr>
                                            <th scope='row'>" . $post['post_id'] ."</th>
                                            <td>" . $post['title'] ."</td>
                                            <td>" . $post['body'] ."</td>
                                            <td>" . $post['user_id'] ."</td>
                                            <td>
                                                <a href='?id=".$post['post_id']."' style='text-decoration: none; color:red;' >
                                                    <i class='fa-solid fa-trash-can me-3'></i>
                                                </a>
                                                <a href='?page=editPost&postid=".$post['post_id']."' style='text-decoration: none; color:#0dcaf0;' >
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
      <?PHP } elseif($page == "editPost"){
                    $postid = $_GET['postid'];
                    $stmt = $connect->prepare("SELECT * FROM posts WHERE post_id =?");
                    $stmt->execute(array($postid));
                    $rows =$stmt->rowCount();
                
                    if($rows > 0){
                        $postData = $stmt->fetch();
                    } 
      ?>
        <div class="">
            <form action="<?PHP echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <p>Update Post Contant</p>
                <input type="hidden" name="post_id" value="<?PHP echo $postData['post_id']; ?>"/>
                <div class="form-outline mb-4">
                <label class="form-label" for="form2Example11">Title</label>
                <input type="text" name="title" value="<?PHP echo $postData['title']; ?>" class="form-control" placeholder="email address"/>
                </div>

                <div class="form-outline mb-4">
                <label class="form-label" for="form2Example11">Body</label>
                <input type="text" name="body" value="<?PHP echo $postData['body']; ?>" class="form-control" placeholder="email address"/>
                </div>

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
    header('Location:error.html');
    exit();
}
ob_end_flush()
?>