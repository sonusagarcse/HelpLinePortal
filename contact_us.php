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
                           <h3 class="breadcrumb__title">Contact us</h3>
                           <div class="breadcrumb__list breadcrumb__list-translate">
                              <span><a href="home">Home</a></span>
                              <span class="dvdr"><i class="fa-sharp fa-regular fa-minus"></i></span>
                              <span>Contact</span>
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
                  <div class="col-lg-10">
                     <div class="tp-portfolio-2-section-title-wrapper text-center">
                        
                        <h3 class="tp-section__title">GET IN TOUCH</h3>
                     </div>
                        <div class="tp-contact-from p-relative" data-background="images/form-input.png">
                           <div class="tp-brands-from-overlay"></div>
	<?php
     if(isset($_REQUEST['msg'])){
      if($_REQUEST['msg']=='send'){
          ?>
		<h5 style="color:red; text-align:center;">  Thank You! <br> <strong style="color:#000;">Your Message Submitted Successfully <br/> We will contact you soon.</strong> <a href="contact_us" style="color:blue;">Click Here</a></h5>
			
		  <?php
      }} else{?>
                              <form  action="code/contact" method="POST">
                                 <div class="row tp-gx-20">
                                    <div class="col-12 col-sm-6">
									
                                       <div class="tp-brands-from-input contact-mb">
                                          <input name="name" type="text" placeholder="Full Name:" required="">
                                       </div>
                                       <div class="tp-brands-from-input contact-mb">
                                          <input name="mob" type="number" placeholder="Mobile:" maxlength="10" required="">
                                       </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                       <div class="tp-brands-from-input contact-mb">
                                          <input name="email" type="email" placeholder="Email Address:" required="">
                                       </div>
                                       <div class="tp-brands-from-input contact-mb">
										<select class="wide" name="sub" style="background: var(--tp-common-white);width: 100%;height: 58px;outline: 0;resize: none;padding: 0 25px;border: 1px solid #fff;" required="">
                                             <option>Choose Service:</option>
                                   <option value="Calling for Center">Calling for Center</option>
                                 <option value="Mobilization & Marketing">Mobilization & Marketing</option>
                                 <option value="Media & News Management">Media & News Management</option>
								 <option value="Staff Management & Training">Staff Management & Training</option>
								 <option value="Job">Job</option>
                                          </select>
                                       </div>
                                       </div>
                                       <div class="col-12">
                                       <div class="tp-brands-from-input contact-textarea">
                                       <textarea name="msg" placeholder="Write Message..." required=""></textarea>
                                       </div>
                                    </div>
                                    
                                    </div>
                                    <div class="tp-contact-submit text-center mt-20">
                               <button class="tp-btn" type="submit" name="submit">Send Message <i class="fa-regular fa-arrow-right-long"></i></button>
                                 </div>
                              </form>
							  <?php }?>	
                           </div>
                        </div>
                     </div>
                  </div>
            </section>
         <!-- contact input area end -->

         <!-- contact map area start
               <div class="tp-contact-map-area">
                  <div class="container-fluid g-0">
                     <div class="row gx-0">
                        <div class="col-lg-12">
                           <div class="tp-contact-map">
                              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d56557.40337080792!2d-122.06589254433018!3d37.87941669573798!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80856152cc733413%3A0x4250b3bb90c93019!2sPleasant%20Hill%2C%20CA%2C%20USA!5e0!3m2!1sen!2sbd!4v1675489390593!5m2!1sen!2sbd" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
         <!-- contact map area end -->

		
      </main>

      <!-- footer area start -->
        <?php include('footer.php');?>
   </body>
</html>
