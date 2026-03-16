<section id="teamBubble" class="tp-team-3-area tp-team-2-bg pt-120 pb-90 p-relative">
               <div class="tp-team-2-shape">
                  <img class="shape-1 d-none d-lg-block" src="images/shape-1qe.png" alt="">
                  <img class="shape-2" src="images/shape-2qe.png" alt="">
                  <img class="shape-3" src="images/shape-3qe.png" alt="">
                  <img class="mousemove__image shape-4 d-none d-lg-block" src="images/bubble-1qe.png" alt="">
                  <img class="mousemove__image shape-5 d-none d-lg-block" src="images/bubble-2qe.png" alt="">
                  <img class="mousemove__image shape-6 d-none d-lg-block" src="imges/bubble-3qe.png" alt="">
               </div>
               <div class="container">
                  <div class="row align-items-center">
                     <div class="col-lg-7">
                        <div class="tp-team-3-wrapper p-relative pb-60 wow fadeInLeft" data-wow-duration="1s"
                        data-wow-delay=".3s">
                           <div class="tp-inner-pre">
                              <span><img src="images/favicon.png" alt="yuva logo" style="height:65px;"></span>
                           </div>
                           <h3 class="tp-section__title">Awarded Member & <br> Learner of KYP Centers</h3>
                        </div>
                     </div>
                     <div class="col-lg-5">
                        <div class="tp-team-2-btn team-3 text-lg-end wow fadeInRight" data-wow-duration="1s"
                        data-wow-delay=".3s">
                           <a class="tp-btn" href="#">Nominate for Award <i class="fa-regular fa-arrow-down-long"></i></a>
                        </div>
                     </div>
                  </div>
                  <div class="row wow fadeInUp" data-wow-duration="1s"
                  data-wow-delay=".3s">
				  <?php 
              require 'connection.php';
					$bcid=18;
					$bsel = mysqli_query($con, "SELECT * FROM photos WHERE cid = $bcid ORDER BY id ASC");
					while($bres = mysqli_fetch_assoc($bsel))
					{
					?>
                     <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
					  <h5 style="text-align:center; font-size:13px; background-color:#FFC700; padding:5px;"><?php echo $bres['name'];?></h5>
                        <div class="tp-team-2-thumb mb-30">
                           <a href="#"><img src="images/photos/<?php echo $bres['img'];?>" alt=""></a>
                           <div class="tp-team-2-inner text-center">
                              <h4 class="tp-team-title"><a href="#"><?php echo $bres['title'];?></a></h4>
                              </div>
                        </div>
                     </div>
					 <?php } ?>
					 
                     
                  </div>
               </div>
            </section>