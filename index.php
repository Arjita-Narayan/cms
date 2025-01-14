<?php 
include "includes/db.php"; 
include "includes/header.php"; 
session_start(); // Start the session to track user login

// Include navigation
include "includes/navigation.php"; 
?>

<!-- Page Content -->
<div class="container">
    <div class="row">
        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php
            // Check if the user is logged in
            if (!isset($_SESSION['username'])): ?>
                <!-- Login Component -->
                <h2>Welcome, Guest!</h2>
                <p>Please <a href="login.php">login</a> to access more features.</p>
            <?php else: ?>
                <!-- Blog Posts for Logged-In Users -->
                <?php
                $per_page = 2; // Pagination system
                
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                } else {
                    $page = "";
                }

                if ($page == "" || $page == 1) {
                    $page_1 = 0;
                } else {
                    $page_1 = ($page * $per_page) - $per_page;
                }

                if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                    // Admin: Fetch all posts
                    $post_query_count = "SELECT * FROM posts";
                } else {
                    // Non-admin: Fetch only published posts
                    $post_query_count = "SELECT * FROM posts WHERE post_status = 'published'";
                }
                

                $find_count = mysqli_query($connection, $post_query_count);
                $count = mysqli_num_rows($find_count);

                if ($count < 1) {
                    echo "<h1 class='text-center'>NO POSTS AVAILABLE</h1>";
                } else {


                    $count = ceil($count / $per_page); // Round off the float value into an integer

                    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                        $query = "SELECT * FROM posts LIMIT $page_1, $per_page";
                    } else {
                        $query = "SELECT * FROM posts WHERE post_status = 'published' LIMIT $page_1, $per_page";
                    }
                    
                    $select_all_posts_query = mysqli_query($connection, $query);
                    if (!$select_all_posts_query) {
                        die("Query Failed: " . mysqli_error($connection));
                    }
                    

                    while ($row = mysqli_fetch_assoc($select_all_posts_query)) {
                        $post_id = $row['post_id'];
                        $post_title = $row['post_title'];
                        $post_author = $row['post_user'];
                        $post_date = $row['post_date'];
                        $post_image = $row['post_image'];
                        $post_content = substr($row['post_content'], 0, 400);
                        ?>

                        <h2>
                            <a href="post.php?p_id=<?php echo $post_id; ?>">
                                <?php echo $post_title ?>
                            </a>
                        </h2>
                        <p class="lead">by 
                            <a href="author_posts.php?author=<?php echo $post_author ?>&p_id=<?php echo $post_id; ?>">
                                <?php echo $post_author ?>
                            </a>
                        </p>
                        <p><span class="glyphicon glyphicon-time"></span>
                            <?php echo $post_date ?>
                        </p>
                        <hr>
                        <a href="post.php?p_id=<?php echo $post_id; ?>">
                            <img class="img-responsive" src="images/<?php echo $post_image; ?>" alt="">
                        </a>
                        <hr>
                        <p><?php echo $post_content ?></p>
                        <a class="btn btn-primary" href="post.php?p_id=<?php echo $post_id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
                        <hr>
                    <?php }
                }
                ?>
            <?php endif; ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include "includes/sidebar.php"; ?>
    </div>
    <!-- /.row -->

    <hr>

    <ul class="pager">
        <?php
        for ($i = 1; $i <= $count; $i++) {
            if ($i == $page) {
                echo "<li><a class='active_link' href='index.php?page={$i}'>{$i}</a></li>";
            } else {
                echo "<li><a href='index.php?page={$i}'>{$i}</a></li>";
            }
        }
        ?>
    </ul>
</div>

<?php include "includes/footer.php"; ?>
