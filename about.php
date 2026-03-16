<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <?php include('csslink.php'); ?>
   <?php $id = 10;
   $sel = mysqli_query($con, "select * from webpage where id='$id' order by id DESC");
   $res = mysqli_fetch_object($sel);
   ?>
</head>

<body style="background:#f3f3f4;">
   <!-- pre loader area start -->
   <?php include('loader.php'); ?>
   <!-- pre loader area end -->

   <!-- back to top start -->
   <?php include('backtotop.php'); ?>
   <!-- back to top end -->
   <!-- header area start -->
   <?php include('header.php'); ?>

   <main>
      <!-- about breadcrumb area start -->
      <section class="breadcrumb__area breadcrumb-style pt-130 pb-115 p-relative z-index-1"
         data-background="images/about_banner.jpg">
         <div class="breadcrumb__bg-overlay m-img"></div>
         <div class="breadcrumb-shape d-none d-lg-block">
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
                     <h3 class="breadcrumb__title"><?php echo $res->name; ?></h3>
                     <div class="breadcrumb__list breadcrumb__list-translate">
                        <span><a href="<?php echo $SITE_URL; ?>">Home</a></span>
                        <span class="dvdr"><i class="fa-sharp fa-regular fa-minus"></i></span>
                        <span><?php echo $res->name; ?></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- about breadcrumb area end -->


      <!-- about area start -->
      <section class="tp-about-area pt-135">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s">
                     <div style="font-size:25px;"><?php echo $res->des; ?> </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- about area end -->


      <?php include('section9.php'); ?>

   </main>

   <!-- footer area start -->
   <?php include('footer.php'); ?>

</body>

</html>