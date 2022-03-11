<?PHP
ob_start();
session_start();
if(isset($_SESSION['user_id'])){
    include 'init.php';
    include 'Frontend/include/templet/navbar.php';

?>

<?PHP
include 'Frontend/include/templet/footer.php';
}else{
    echo "<div class='alert alert-danger'> you can inter in to the page befor login</div>";
    header('Refresh: 5; url=login.php ');
    exit();
}
ob_end_flush()
?>