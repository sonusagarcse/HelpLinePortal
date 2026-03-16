<section id="testimonialBubble" class="tp-testimonial-3-area p-relative pt-120" data-background="images/img-1tt.jpg">
               <div class="tp-testimonial-3-overlay"></div>
               <div class="tp-testimonial-3-shape d-none d-md-block">
                  <img class="shape-1" src="images/bubble-1tt.png" alt="">
                  <img class="mousemove__image shape-2" src="images/bubble-2tt.png" alt="">
               </div>
               <div class="container">
                  <div class="row">
                     <div class="col-lg-6">
                        <div class="tp-testimonial-2-section-title-wrapper text-center">
                           <div class="tp-inner-pre">
                              <span><img src="images/favicon.png" alt="yuva logo" style="height:65px;"></span>
                           </div>
                           <h3 class="tp-section__title mb-50">OUR LOVELY CUSTOMER</h3>
                        </div>
                        <div class="testimonial-active-3 splide wow fadeInLeft" data-wow-duration="1s"
                        data-wow-delay=".3s">
                           <div class="splide__arrows splide__arrows--ltr tp-btn-effect">
                              <button class="splide__arrow splide__arrow--prev">
                              <i class="fa-light fa-arrow-left"></i>
                            </button>
                              <button class="splide__arrow splide__arrow--next">
                               <i class="fa-light fa-arrow-right"></i>
                            </button>
                           </div>
                           <div class="splide__track">
                              <div class="splide__list">
<?php 
$cid=10;
$sel=mysqli_query($con, "select * from webpage where cid='$cid' order by id DESC");
while($res=mysqli_fetch_object($sel))
{
?>
                                 <div class="splide__slide">
                                    <div class="tp-testimonial-2-wrapper">
                                       <div class="tp-testimonial-2-shape">
                                          <img src="images/shape.png" alt="">
                                       </div>
                                       <p><?php echo $res->des;?></p>
                                       <h3 class="tp-testimonial-title"><?php echo $res->name;?></h3>
                                       <span><?php echo $res->title;?></span>
                                    </div>
                                 </div>
								 <?php }?>
                                 
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-6">
                        <div class="tp-testimonial-3-from p-relative wow fadeInRight" data-wow-duration="1s"
                        data-wow-delay=".3s" data-background="images/form-img.jpg" style="background-image: url(&quot;images/form-img.jpg&quot;);">
                           <div class="tp-brands-from-overlay"></div>
                           <span class="tp-section__title-pre">GET A FREE QUOTE</span>
                           <h3 class="tp-brands-title">GET A FREE QUOTE</h3>
						   <?php
     if(isset($_REQUEST['msg'])){
      if($_REQUEST['msg']=='send'){
          ?>
		<h5 style="color:red; text-align:center;">  Thank You! <br> <strong style="color:#000;">Your Message Submitted Successfully <br/> We will contact you soon.</strong> <a href="contact_us" style="color:blue;">Click Here</a></h5>
			
		  <?php
      }} else{?>
						   <form  action="code/contact" method="POST">
                           <div class="tp-brands-from-input">
                              <input type="text" placeholder="Full Name:" name="name" required="">
                           </div>
						   <div class="tp-brands-from-input">
                              <input type="number" placeholder="Mobile:" name="mob" required="" maxlength="10">
                           </div>
                           <div class="tp-brands-from-input">
                              <input type="email" placeholder="Email Address:" name="email" required="">
                           </div>
                           <div class="tp-brands-from-input">
                              <select class="wide" style="display: none;" name="sub" required="">
                                 <option>Choose Service:</option>
                                 <option value="Calling for Center">Calling for Center</option>
                                 <option value="Mobilization & Marketing">Mobilization & Marketing</option>
                                 <option value="Media & News Management">Media & News Management</option>
								 <option value="Staff Management & Training">Staff Management & Training</option>
								 <option value="Job">Job</option>
                              </select>
							  <div class="nice-select wide" tabindex="0"><span class="current">Choose Service:</span>
							  <ul class="list">
							  <li data-value="Choose Service:" class="option selected">Choose Service:</li>
							  <li data-value="Calling for Center" class="option">Calling for Center</li>
							  <li data-value="Mobilization & Marketing" class="option">Mobilization & Marketing</li>
							  <li data-value="Media & News Management" class="option">Media & News Management</li>
							  <li data-value="Staff Management & Training" class="option">Staff Management & Training</li>
							   <li data-value="Job" class="option">Job</li>
							  </ul>
							  </div>
                           </div>
                          <div class="tp-brands-from-input">
                           <textarea name="msg" placeholder="Write Message..." required=""></textarea>
                          </div>
                          <button class="tp-btn" type="submit" name="submit">Submit Now <i class="fa-regular fa-arrow-right-long"></i></button>
						  </form>
						  <?php }?>	
                        </div>
                     </div>
                  </div>
               </div>
            </section>