<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <?php include('csslink.php'); ?>
   <?php $id = 13;
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
                     <center>
                        <div style="font-size:25px; text-align:center"><?php echo $res->des; ?> </div>
                     </center>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- about area end -->
      <br />
      <section class="product__details-tab-area pb-110">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="product__details-tab-nav">
                     <nav>
                        <div class="product__details-tab-nav-inner nav tp-tab-menu d-flex flex-sm-nowrap flex-wrap"
                           id="nav-tab-info" role="tablist">

                           <button class="nav-link active" id="nav-desc-tab" data-bs-toggle="tab"
                              data-bs-target="#nav-desc" type="button" role="tab" aria-controls="nav-desc"
                              aria-selected="false" tabindex="-1">People</button>

                           <button class="nav-link " id="nav-additional-tab" data-bs-toggle="tab"
                              data-bs-target="#nav-additional" type="button" role="tab" aria-controls="nav-additional"
                              aria-selected="true">Knowledge</button>

                           <button class="nav-link" id="nav-review-tab" data-bs-toggle="tab"
                              data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review"
                              aria-selected="false" tabindex="-1">Innovation</button>
                           <span id="marker" class="tp-tab-line d-none d-sm-inline-block"
                              style="left: 110px; display: block; width: 190px;"></span>
                        </div>
                     </nav>
                  </div>
                  <div class="product__details-tab-content">
                     <div class="tab-content" id="nav-tabContent-info">
                        <div class="tab-pane fade active show" id="nav-desc" role="tabpanel"
                           aria-labelledby="nav-desc-tab">
                           <div class="product__details-description pt-95">

                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="product__details-description-content">
                                       <h3 class="product-desc-title">People </h3>
                                       <p>We care about people and the role of service in their lives. We respect people
                                          as individuals, trust them, support them, enable them to achieve their goals
                                          in work and life.<br />
                                          We help people develop their careers through planning, action, coaching and
                                          training.<br />
                                          We recognize everyone's contribution to our success – our employees, our
                                          customers and our candidates. We encourage and reward achievement.</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="nav-additional" role="tabpanel"
                           aria-labelledby="nav-knowledge-tab">
                           <div class="product__details-review pt-60">
                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="product__details-description-content">
                                       <h3 class="product-desc-title">Knowledge </h3>
                                       <p>We share our knowledge, our expertise and our resources, so that everyone
                                          understands what is important now and what’s happening next in the world of
                                          service - and knows how best to respond.<br />
                                          We actively listen and act upon this information to improve our relationships,
                                          solutions and services.</p>
                                    </div>

                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="nav-review" role="tabpanel" aria-labelledby="nav-review-tab">
                           <div class="product__details-review pt-60">
                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="product__details-description-content">
                                       <h3 class="product-desc-title">Innovation </h3>
                                       <p>Based on our understanding of the world of service, we actively pursue the
                                          development and adoption of the best practices worldwide.<br />
                                          We lead in the world of services.<br />
                                          We dare to innovate, to pioneer and to evolve.<br />
                                          We never accept the status quo. We constantly challenge the norm to find new
                                          and better ways of doing things.<br />
                                          We thrive on our entrepreneurial spirit and speed of response; taking risks,
                                          knowing that we will not always succeed, but never exposing our clients to
                                          risk. </p>
                                    </div>

                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>

      <?php include('section9.php'); ?>

   </main>

   <!-- footer area start -->
   <?php include('footer.php'); ?>

</body>

</html>