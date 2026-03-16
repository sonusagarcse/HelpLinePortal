<section class="tp-counter-3-area p-relative">
            <div class="container">
               <div class="tp-counter-2-wrapper p-relative">
                  <div class="tp-counter-2-shape d-none d-lg-block">
                     <div class="shape-1">
                        <img src="images/bubble-1.png" alt="">
                     </div>
                     <div class="shape-2">
                        <img src="images/bubble-2.png" alt="">
                     </div>
                     <div class="shape-3">
                        <img src="images/bubble-3.png" alt="">
                     </div>
                  </div>
                  <div class="row wow fadeInUp" data-wow-duration="1s"
                  data-wow-delay=".3s">
                     <div class="col-12 col-sm-6 col-md-6 col-lg-3"> 
                     <center>
                        <div class="tp-counter-2-inner p-relative mb-40">
                           <div class="tp-counter-thumb">
                              <i class="flaticon-house"></i>
                           </div>
                           <div class="tp-counter-content">
				<?php
                $sel=mysqli_query($con, "select count(*) as number from branch");
                $res=mysqli_fetch_assoc($sel);
                ?>
                <h4 data-purecounter-duration="1" data-purecounter-end="<?php echo $res['number']?>" class="purecounter tp-counter-title">0</h4>
                              <p>Partners Center</p>
                           </div>
                        </div>
                        </center>
                     </div>
                     <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                         <center>
                        <div class="tp-counter-2-inner-1 p-relative mb-40">
                           <div class="tp-counter-thumb">
                              <i class="flaticon-cleaning-lady"></i>
                           </div>
                           <div class="tp-counter-content">
					<h4 data-purecounter-duration="1" data-purecounter-end="270" class="purecounter tp-counter-title">0</h4>
                              <p>Team Member</p>
                           </div>
                           <div class="tp-counter-2-shape-2">
                              <img src="images/shape-3.png" alt="">
                           </div>
                        </div>
                        </center>
                     </div>
                     <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                         <center>
                        <div class="tp-counter-2-inner-2 p-relative mb-40">
                           <div class="tp-counter-thumb">
                              <i class="flaticon-medal"></i>
                           </div>
                           <div class="tp-counter-content">
				<?php
               //  $sel=$con->prepare("select count(*) as number from registration");
               //  $exe=$sel->execute();
               //  $res=$sel->fetch();
               require 'connection.php';
                $sel = mysqli_query($con, "select count(*) as number from registration");
                $res = mysqli_fetch_assoc($sel);

                ?>
                     <h4 data-purecounter-duration="1" data-purecounter-end="<?php echo $res['number'];?>" class="purecounter tp-counter-title">0</h4>
                              <p>Registered Youth</p>
                           </div>
                           <div class="tp-counter-2-shape-3 d-none d-lg-block">
                              <img src="images/shape-3.png" alt="">
                           </div>
                        </div>
                        </center>
                     </div>
                     <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                         <center>
                        <div class="tp-counter-2-inner-3 p-relative mb-40">
                           <div class="tp-counter-thumb">
                              <i class="flaticon-thumbs-up"></i>
                           </div>
                           <div class="tp-counter-content">
				<?php
                $sel=mysqli_query($con, "select count(*) as number from registration where status=1");
                $res=mysqli_fetch_assoc($sel);
                ?>
                    <h4 data-purecounter-duration="1.5" data-purecounter-end="<?php echo $res['number'];?>" class="purecounter tp-counter-title">0</h4>
                              <p>Admission Complete</p>
                           </div>
                           <div class="tp-counter-2-shape-4">
                              <img src="images/shape-3.png" alt="">
                           </div>
                        </div>
                        </center>
                     </div>
                  </div>
               </div>
            </div>
            </section>