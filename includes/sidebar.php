
<?php
// Get the root URL of the website
$rootUrl = (isset($_SERVER['HTTPS']) ? "http://localhost/admin/" : "http://") . $_SERVER['HTTP_HOST'];

// If your application is in a subfolder, append the folder name to the root URL
// For example, if your app is located in the 'myapp' folder, uncomment the line below and replace 'myapp' with the actual folder name
// $rootUrl .= '/myapp';
?>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" # " target="_blank">
        <img src="./assets/img/logo99n.webp" class="navbar-brand-img logoimg" alt="">
        <!-- <span class="ms-1 font-weight-bold">99notes</span> -->
      </a>
    </div>`
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse mysidebar w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link  active" href="<?php echo $rootUrl; ?>/admin/admin_dashboard.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
             <i class="fa fa-home text-white text-lg"></i>
            </div>
            <span class="nav-link-text ms-1">Admin Dashboard</span>
          </a>
        </li>
        <!-- Dropdown for Test Management -->
        <li class="nav-item" >
                <a class="nav-link" href="#testManagement" data-toggle="collapse" aria-expanded="false" aria-controls="testManagement">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-folder-open text-dark text-lg" aria-hidden="true"></i>
                    </div>
                    <span class="nav-link-text ms-1">Prelims</span>
                </a>
                <div class="collapse" id="testManagement">
                    <ul class="nav flex-column sub-menu">
                    <li class="nav-item" >
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/admin/edit_pre_test.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-hand-o-right text-dark text-lg" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">Pre Tests</span>
          </a>
        </li>
                        <!-- <li class="nav-item"> <a class="nav-link" href="<?php echo $rootUrl; ?>/teacher/create_test.php">Create Test</a></li>
                        <li class="nav-item"> <a class="nav-link" href="<?php echo $rootUrl; ?>/teacher/delete_test.php">Delete Test</a></li> -->
                        <li class="nav-item">
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/admin/edit_pre_score.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-hand-o-right text-dark text-lg" aria-hidden="true"></i>

            </div>
            <span class="nav-link-text ms-1">Pre Score</span>
          </a>
        </li>   
        <!-- <li class="nav-item">
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/teacher/test_score.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-line-chart text-dark text-lg" aria-hidden="true"></i>

            </div>
            <span class="nav-link-text ms-1">Add Score</span>
          </a>
        </li>      -->
                    </ul>
                </div>
            </li>


<!-- Dropdown for Mains -->
<li class="nav-item">
                <a class="nav-link" href="#mains" data-toggle="collapse" aria-expanded="false" aria-controls="mains">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-folder-open text-dark text-lg" aria-hidden="true"></i>
                    </div>
                    <span class="nav-link-text ms-1">Mains</span>
                </a>
                <div class="collapse" id="mains">
                    <ul class="nav flex-column sub-menu">
                    
        <li class="nav-item">
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/admin/edit_mains_test.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-hand-o-right text-dark text-lg" aria-hidden="true"></i>

            </div>
            <span class="nav-link-text ms-1">Edit Mains Test</span>
          </a>
        </li> 
        <li class="nav-item">
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/admin/edit_mains_score.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-hand-o-right text-dark text-lg" aria-hidden="true"></i>

            </div>
            <span class="nav-link-text ms-1">Edit Mains Score</span>
          </a>
        </li> 
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="#studentManagement" data-toggle="collapse" aria-expanded="false" aria-controls="studentManagement">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa fa-folder-open text-dark text-lg" aria-hidden="true"></i>
                    </div>
                    <span class="nav-link-text ms-1">Users Management</span>
                </a>
                <div class="collapse" id="studentManagement">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/admin/studentslist.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-hand-o-right text-dark text-lg" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">Students</span>
          </a>
        </li>
                  <li class="nav-item">
          <a class="nav-link  " href="<?php echo $rootUrl; ?>/admin/teachers.php">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-hand-o-right text-dark text-lg" aria-hidden="true"></i>
            </div>
            <span class="nav-link-text ms-1">Teachers</span>
          </a>
        </li>        
                    </ul>
                </div>
            </li>

 
       
      </ul>
    </div>
    <div class="sidenav-footer mx-3 ">
      <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">
        <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpg')"></div>
       
      </div>
     
    </div>
  </aside>