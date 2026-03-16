<header class="tp-header-3-area p-relative tp-header-height">
   <div class="tp-header-3-top d-none d-xxl-block">
      <div class="container">
         <div class="row align-items-center">
            <div class="col-lg-5">
               <div class="tp-header-3-top-info-text">
                  <p>A Brighter Future is Humanly Possible.<span><a href="contact.html"> Contact Us <i
                              class="fa-regular fa-arrow-right"></i></a></span></p>
               </div>
            </div>
            <div class="col-lg-7">
               <div class="tp-header-3-top-info text-end">
                  <ul>
                     <li>
                        <a href="tel:<?php echo $MOBILE; ?>"><span><i
                                 class="flaticon-telephone-call"></i></span><?php echo $MOBILE; ?></a>
                     </li>
                     <li>
                        <a href="mailto:<?php echo $EMAIL_ID; ?>"><span><i
                                 class="flaticon-mail"></i></span><?php echo $EMAIL_ID; ?></a>
                     </li>
                     <li>
                        <a href="#" target="_blank">
                           <span>
                              <i class="flaticon-location"></i>
                           </span><?php echo $ADDRESS; ?></a>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div id="header-sticky" class="tp-header-3-bottom header__sticky p-relative">
      <div class="tp-header-3-color">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-6 col-lg-6 col-xl-2">
                  <div class="tp-header-logo tp-header-logo-border">
                     <a href="<?php echo $SITE_URL; ?>">
                        <img src="images/logo.png" alt="Rabroz Logo" style="height:50px;">
                     </a>
                  </div>
               </div>
               <div class="col-6 col-xl-10">
                  <div
                     class="tp-main-menu-3-area d-flex align-items-center justify-content-end justify-content-xl-center justify-content-xxl-end">
                     <div class="tp-main-menu home-3">
                        <nav id="tp-mobile-menu">
                           <ul>
                              <li>
                                 <a href="<?php echo $SITE_URL; ?>">Home</a>

                              </li>
                              <li class="has-dropdown"><a href="#">Who We Are</a>
                                 <ul class="submenu">
                                    <li> <a href="<?php echo $SITE_URL; ?>/about">About Us</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/values-and-vision">Value & Vision</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/ethics">Ethics</a></li>
                                 </ul>
                              </li>

                              <li class="has-dropdown"><a href="#">About the Career </a>
                                 <ul class="submenu">
                                    <li> <a href="<?php echo $SITE_URL; ?>/after_10th">After 10th</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/after_12th">After 12th</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/new_vacancy">New Vacancy</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/new_admissions">New Admissions</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/self_employment">Self Employment</a></li>
                                 </ul>
                              </li>

                              <li><a href="<?php echo $SITE_URL; ?>/news">News</a></li>



                              <li class="has-dropdown"> <a href="#">Login As</a>
                                 <ul class="submenu">
                                    <li><a href="<?php echo $SITE_URL; ?>/branch_login">Branch</a></li>
                                    <li><a href="<?php echo $SITE_URL; ?>/caller_login">Caller</a></li>
                                    <li><a href="<?php echo $SITE_URL; ?>/deo_login">DEO</a></li>
                                    <li><a href="<?php echo $SITE_URL; ?>/supervisor_login">Supervisor</a></li>
                                    <li><a href="<?php echo $SITE_URL; ?>/manager_login">Manager</a></li>
                                 </ul>
                              </li>

                              <li class="has-dropdown"> <a href="#">Contact Us</a>
                                 <ul class="submenu">
                                    <li><a href="<?php echo $SITE_URL; ?>/contact_us">Contact</a></li>
                                    <li><a href="<?php echo $SITE_URL; ?>/grivience">Grivience Section</a></li>
                                    <li> <a href="<?php echo $SITE_URL; ?>/career">Career</a></li>
                                 </ul>
                              </li>

                           </ul>
                        </nav>
                     </div>

                     <div class="mobile-menu d-block d-xxl-none text-end">
                        <button class="tp-side-action tp-toogle hamburger-btn">
                           <span></span>
                           <span></span>
                           <span></span>
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
</header>
<!-- header area end -->

<!-- mobile menu style start -->
<div class="tp-offcanvas-area fix">
   <div class="tp-side-info">
      <div class="tp-side-logo">
         <a href="<?php echo $SITE_URL; ?>">
            <img src="images/logo.png" alt="logo">
         </a>
      </div>
      <div class="tp-side-close">
         <button> <i class="fa-thin fa-xmark"></i></button>
      </div>
      <div class="tp-mobile-menu-pos"></div>
      <div class="tp-side-content p-relative">
         <div class="tp-sidebar__contact">
            <h4 class="tp-sidebar-title">Contact Info</h4>
            <ul>
               <li class="d-flex align-items-center">
                  <div class="tp-sidebar__contact-text">
                     <a target="_blank" href="#"><i class="fal fa-map-marker-alt"></i> <?php echo $ADDRESS; ?></a>
                  </div>
               </li>
               <li class="d-flex align-items-center">
                  <div class="tp-sidebar__contact-text">
                     <a href="tel:<?php echo $MOBILE; ?>"><i class="far fa-phone"></i> <?php echo $MOBILE; ?></a>
                  </div>
               </li>
               <li class="d-flex align-items-center">
                  <div class="tp-sidebar__contact-text">
                     <a href="mailto:<?php echo $EMAIL_ID; ?>"><i class="fal fa-envelope"></i>
                        <?php echo $EMAIL_ID; ?></a>
                  </div>
               </li>
            </ul>
         </div>
         <div class="tp-sidebar-icons tp-btn-effect-blue">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-skype"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
         </div>
      </div>
   </div>
   <div class="offcanvas-overlay"></div>
</div>
<!-- mobile menu style end -->