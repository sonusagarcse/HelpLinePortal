<!doctype html>
<html class="no-js" lang="zxx">
   <head>
    <?php include('csslink.php');?>
   </head>
   <body>
      <!--[if lte IE 9]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
      <![endif]-->


      <!-- pre loader area start -->
      <?php if(file_exists('loader.php')) include('loader.php'); ?>
      <!-- pre loader area end -->

      <!-- back to top start -->
      <?php include('backtotop.php');?>
      <!-- back to top end -->

      
      <!-- header area start -->
       <?php include('header.php');?>
      <main>
         <!-- hero area start -->
             <?php include('section.php');?>
         <!-- hero area end -->
         
         <!-- about area start -->
            <?php include('section1.php');?>
         <!-- about area end -->

         <!-- our service area start -->
            <?php include('section2.php');?>
         <!-- our service area end -->

         <!-- fun fact area start -->
            <?php include('section3.php');?>
         <!-- fun fact area end -->

         <!-- portfolio area start -->
             <?php include('section4.php');?>
         <!-- portfolio area end -->

         <!-- team area start -->
            <?php include('section5.php');?>
         <!-- team area end -->

         <!-- price area start -->
             <?php include('section6.php');?>
         <!-- price area end -->

         <!-- testimonial area start -->
             <?php include('section7.php');?>
         <!-- testimonial area end -->

         <!-- blog area start -->
            <?//php include('section8.php');?>
         <!-- blog area end -->
         
         <?php include('section9.php');?>
      </main>

      <!-- footer area start -->
      
        <?php include('footer.php');?>
         <script src="js/nice-select.js"></script>
         <script src="js/main.js"></script>
   </body>
</html>
