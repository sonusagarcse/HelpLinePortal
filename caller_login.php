<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <?php include('csslink.php'); ?>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>

<body>

   <!-- pre loader area start -->
   <?php include('loader.php'); ?>
   <!-- pre loader area end -->

   <!-- back to top start -->
   <?php include('backtotop.php'); ?>
   <!-- back to top end -->


   <!-- header area start -->
   <?php include('header.php'); ?>
   <main>

      <section class="breadcrumb__area breadcrumb-style pt-130 pb-115 p-relative z-index-1"
         data-background="images/contact.jpg">
         <div class="breadcrumb__bg-overlay m-img"></div>
         <div class="breadcrumb-shape  d-none d-lg-block">
            <div class="shape-1">
               <img src="images/breadcrumb-shape.png" alt="">
            </div>
            <div class="shape-2">
               <img src="images/breadcrumb-shape-2.png" alt="">
            </div>
         </div>
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-xl-10">
                  <div class="breadcrumb__content text-center">
                     <h3 class="breadcrumb__title">Caller Login</h3>
                     <div class="breadcrumb__list breadcrumb__list-translate">
                        <span><a href="<?php echo $SITE_URL; ?>">Home</a></span>
                        <span class="dvdr"><i class="fa-sharp fa-regular fa-minus"></i></span>
                        <span>Login Now</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- about breadcrumb area end -->


      <!-- contact input area start -->
      <section class="tp-contact-input pt-100">
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-lg-6">

                  <style>
                     .login-form form {
                        text-align: left;
                        padding: 30px;
                        border: 1px solid #f1f1f1;
                        box-shadow: 1px 1px 20px #cccccc57;
                     }

                     .form-title {
                        text-align: center;
                        margin-bottom: 30px;
                     }

                     .form-title h2 {
                        margin-bottom: 20px;
                        padding-bottom: 15px;
                     }

                     h2 {
                        font-weight: 800;
                        color: #212121;
                        font-family: 'Raleway', sans-serif;
                        margin-top: 0;
                        line-height: 1.5;
                        margin-bottom: 15px;
                        text-align: center;
                        font-size: 32px;
                     }

                     label {
                        display: inline-block;
                        color: #666;
                        margin-bottom: 8px;
                        font-weight: 400;
                        font-size: 15px;
                     }

                     input[type=text],
                     input[type=email],
                     input[type=number],
                     input[type=search],
                     input[type=password],
                     input[type=tel],
                     textarea,
                     select {
                        font-size: 14px;
                        font-weight: 300;
                        background-color: #fff;
                        border: 1px solid #060606;
                        border-radius: 0;
                        padding: 10px 15px;
                        width: 100%;
                        color: #444444;
                        margin-bottom: 15px;
                        font-family: 'Poppins', sans-serif;
                        height: 42px;
                        box-shadow: none;
                        margin-bottom: 0;
                     }

                     .form-control {
                        display: block;
                        width: 100%;
                        height: 34px;
                        padding: 6px 12px;
                        font-size: 14px;
                        line-height: 1.42857143;
                        color: #555;
                        background-color: #fff;
                        background-image: none;
                        border: 1px solid #2b2929;
                        border-radius: 4px;
                        -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
                        box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
                        -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
                        -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
                        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
                     }

                     .form-group {
                        margin-bottom: 15px;
                     }
                  </style>

                  <div class="login-form">
                     <form action="code/caller_login" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                        <div class="row">
                           <div class="form-group col-md-12">
                              <label>Reg. Number</label>
                              <input type="text" class="form-control" placeholder="Enter Your Reg. Number" name="regno" required>
                           </div>
                           <div class="form-group col-md-12">
                              <label>Password</label>
                              <input type="password" class="form-control" placeholder="Enter Your Password" name="password" required>
                           </div>
                        </div>
                           <div class="col-md-12">
                              <div class="checkbox-outer mb-3">
                                 <input type="checkbox" name="remember" value="1"> Remember Me?
                              </div>
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="comment-btn">
                              <button class="tp-btn">Login</button>
                           </div>
                        </div>
                        <!--  <div class="col-xs-12">
                                    <div class="login-accounts">
                                     <a href="user_forget_password" class="forgotpw">Forget Password?</a>
                                </div>
                            </div>-->
                     </form>
                  </div>



                  <br /><br />

               </div>
            </div>
         </div>
      </section>
      <!-- contact input area end -->

   </main>
   <!-- footer area start -->
   <?php include('footer.php'); ?>

</body>

</html>