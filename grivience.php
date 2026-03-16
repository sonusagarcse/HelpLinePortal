<!doctype html>
<html class="no-js" lang="zxx">
   <head>
    <?php include('csslink.php');?>
   </head>
   <body>
    
      <!-- pre loader area start -->
      <?php include('loader.php');?>
      <!-- pre loader area end -->

      <!-- back to top start -->
      <?php include('backtotop.php');?>
      <!-- back to top end -->

      
      <!-- header area start -->
       <?php include('header.php');?>
      <main>
        
		<section class="breadcrumb__area breadcrumb-style pt-130 pb-115 p-relative z-index-1" data-background="images/contact.jpg">
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
                           <h3 class="breadcrumb__title">Grivience Section</h3>
                           <div class="breadcrumb__list breadcrumb__list-translate">
                              <span><a href="home">Home</a></span>
                              <span class="dvdr"><i class="fa-sharp fa-regular fa-minus"></i></span>
                              <span>Grivience Section</span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
         <!-- about breadcrumb area end -->

         <!-- contact area start -->
            <div class="tp-contact-area pt-120 pb-120">
               <div class="container">
                  <div class="row">
                     <div class="col-lg-4">
                        <div class="tp-contact-phone d-sm-flex justify-content-xl-end">
                           <div class="tp-contact-icon">
                              <a href="tel:<?php echo $MOBILE;?>"><i class="flaticon-telephone-call"></i></a>
                           </div>
                           <div class="contact-inner">
                              <p>Phone:</p>
                              <a href="tel:<?php echo $MOBILE;?>"><?php echo $MOBILE;?></a>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="tp-contact-mail d-sm-flex justify-content-xl-center">
                           <div class="tp-contact-icon">
                              <a href="mail:<?php echo $EMAIL_ID;?>"><i class="flaticon-mail"></i></a>
                           </div>
                           <div class="contact-inner">
                              <p>E-mail:</p>
                              <a href="mail:<?php echo $EMAIL_ID;?>"><?php echo $EMAIL_ID;?></a>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4">
                        <div class="tp-contact-location d-sm-flex justify-content-xl-start">
                           <div class="tp-contact-icon">
                              <a href="#"><i class="flaticon-location"></i></a>
                           </div>
                           <div class="contact-inner">
                              <p>Address:</p>
                              <a target="_blank" href="#"><?php echo $ADDRESS;?></a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         <!-- contact area end -->
         

      </main>

      <!-- footer area start -->
        <?php include('footer.php');?>
   </body>
</html>
