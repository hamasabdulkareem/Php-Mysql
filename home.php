<?PHP
ob_start();
session_start();
if(isset($_SESSION['user_id'])){
    include 'init.php';
    include 'Frontend/include/templet/navbar.php';
    
    // statement to return all categories
     $qcategory = $connect->prepare("SELECT * FROM categories WHERE status = 'visible' ");
     $qcategory->execute();
     $allCategory =$qcategory->fetchAll();


?>

<div class="container">
    <div class="categories mt-3">
        <div class="row">
            <?PHP 
             if(!empty($allCategory)){
                foreach($allCategory as $category){
                    echo "
                        <div class='col-md-4 mt-3'>
                            <div class='single-category'>
                                <div class='card'>
                                    <img src='Frontend/images/6170_children.jpg' style='object-fit: cover;height: 30vh;width: 100%;' class='card-img-top' alt=''>
                                    <div class='card-body'>
                                        <h5 class='card-title'>".$category['title']."</h5>
                                        <p class='card-text'>".$category['description']."</p>
                                        <a href='category_posts.php?category_id=".$category['category_id']."' class='btn btn-primary'>Show Category</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";
                }
             }
            ?>
        </div>
    </div>
</div>


<?PHP
include 'Frontend/include/templet/footer.php';
}else{
    echo "<div class='alert alert-danger'> you can inter in to the page befor login</div>";
    header('Refresh: 5; url=login.php ');
    exit();
}
ob_end_flush()
?>