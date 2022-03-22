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
    $qposts = $connect->prepare("SELECT posts.* , users.* FROM posts INNER JOIN users ON posts.user_id = users.user_id ");
    $qposts->execute();
    $numPosts =$qposts->rowCount();
    $allPosts =$qposts->fetchAll();

     
    // statement to return all users
    $qusers = $connect->prepare("SELECT * FROM users");
    $qusers->execute();
    $numUsers =$qusers->rowCount();
    $allUsers =$qusers->fetchAll();
    
    // statement to return all categories
    $qcategory = $connect->prepare("SELECT * FROM categories WHERE status = 'visible' ");
    $qcategory->execute();
    $allCategory =$qcategory->fetchAll();

     if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['edit'])){
            $postid = $_POST['post_id'];
            $title = filter_var($_POST['title'] , FILTER_SANITIZE_STRING);
            $body = filter_var($_POST['body'] ,  FILTER_SANITIZE_STRING);
            $status = $_POST['status'];

            $stmt = $connect->prepare("SELECT * FROM posts WHERE post_id =?");
            $stmt->execute(array($postid));
            $rows =$stmt->rowCount();

            if($rows > 0){
                $stmtUpdate = $connect->prepare("UPDATE posts SET title=? , body=? , status=? WHERE post_id=?");
                $stmtUpdate->execute(array($title , $body ,  $status , $postid));
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

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if(isset($_POST['save'])){
            $title = filter_var($_POST['title'] , FILTER_SANITIZE_STRING);
            $body = filter_var($_POST['body'] ,  FILTER_SANITIZE_STRING);
            $status = $_POST['status'];
            $user_id = $_POST['user_id'];
            $category_id = $_POST['category_id'];

            $imageName = $_FILES['image']['name'];
            $imageSize = $_FILES['image']['size'];
            $imageTmp_name = $_FILES['image']['tmp_name'];
            $imagetype = $_FILES['image']['type'];

            $imageExtension1 = explode('.' , $imageName);
            $imageExtension2 = strtolower(end($imageExtension1));
            $allowedExtensions = array('jpeg','jpg','png','gif','svg');

            $finalimage = rand(0,10000) . "_" . $imageName;
            move_uploaded_file($imageTmp_name,"Admin/uploads/posts/". $finalimage);
            $stmt = $connect->prepare("INSERT INTO posts(title,body,image,status,user_id,category_id) VALUES(? , ? , ? , ? , ? , ?)");
            $stmt->execute(array($title , $body , $finalimage , $status , $user_id , $category_id));
            header('Refresh: 1; url=posts.php');
            exit();
           
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>
                        Posts <span class="badge bg-primary"><?PHP echo $numPosts ?> </span>
                        </h5>
                        <a href="?page=createPost" class="btn btn-primary">New Post</a>
                    </div>
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
                                            <th scope='col'>image</th>
                                            <th scope='col'>Title</th>
                                            <th scope='col'>body</th>
                                            <th scope='col'>Author</th>
                                            <th scope='col'>status</th>
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
                                            <td> 
                                                <a target='_blank' href='Admin/uploads/posts/" . $post['image'] ."'>
                                                    <img style='height:60px;width:60px;border-radius:50%' src='Admin/uploads/posts/" . $post['image'] ."'>
                                                </a>
                                            </td>
                                            <td>" . $post['title'] ."</td>
                                            <td>" . $post['body'] ."</td>
                                            <td>" . $post['username'] ."</td>
                                            <td>";
                                                if($post['status'] === 'published'){
                                                    echo"<span class='badge bg-success'>published</span>";
                                                }else{
                                                    echo"<span class='badge bg-danger'>hidden</span>";
                                                }
                                            echo "
                                            </td>
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

                <div class="form-outline mb-4">
                    <label class="form-label" for="form2Example22">Status</label>
                    <select class="form-select" name="status" >
                        <option value="<?PHP echo $postData['status']; ?>"><?PHP echo $postData['status']; ?></option>
                        <option value="published">published</option>
                        <option value="hidden">hidden</option>
                    </select>
                </div>



                <div class="text-center pt-1 mb-5 pb-1">
                <button name="edit" class="btn btn-primary btn-block gradient-custom-2 mb-3" type="submit">edit</button>
                </div>
            </form>
        </div>
       <?PHP } elseif($page == "createPost"){ ?>
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="d-flex justify-content-center align-items-center" >
                    <div class="" style="width: 50%; background-color: #eee;">
                    <div class="">
                        <div class="card-body p-md-2 mx-md-4">
                            <div class="text-center">
                            <h4 class="mt-1 mb-5 pb-1">Add New Post</h4>
                        </div>
                            <form action="<?PHP echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example11">Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="Title" require/>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example11">Body</label>
                                    <textarea class="form-control" name="body" placeholder="Body" style="height: 100px"></textarea>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example22">image</label>
                                    <input type="file" name="image"  class="form-control" />
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example22">Auther</label>
                                    <select class="form-select" name="user_id" >
                                        <?PHP
                                          foreach($allUsers as $user){
                                              echo "<option value='".$user['user_id']."'>".$user['username']."</option>";
                                          }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example22">Category</label>
                                    <select class="form-select" name="category_id" >
                                    <?PHP
                                          foreach($allCategory as $category){
                                              echo "<option value='".$category['category_id']."'>".$category['title']."</option>";
                                          }
                                    ?>
                                    </select>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="form2Example22">Status</label>
                                    <select class="form-select" name="status" >
                                        <option selected>Choose...</option>
                                        <option value="published">published</option>
                                        <option value="hidden">hidden</option>
                                    </select>
                                </div>

                                <div class="text-center pt-1 mb-5 pb-1">
                                <button name="save" class="btn btn-primary btn-block gradient-custom-2 mb-3" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    </div>
                
                </div>
            </div>
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