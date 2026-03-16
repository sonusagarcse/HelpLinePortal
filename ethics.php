<!doctype html>
<html class="no-js" lang="zxx">

<head>
   <?php include('csslink.php'); ?>
   <?php $id = 12;
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
                              aria-selected="false" tabindex="-1">Code of Conduct</button>

                           <button class="nav-link " id="nav-additional-tab" data-bs-toggle="tab"
                              data-bs-target="#nav-additional" type="button" role="tab" aria-controls="nav-additional"
                              aria-selected="true">Anti-Corruption Policy</button>

                           <button class="nav-link" id="nav-review-tab" data-bs-toggle="tab"
                              data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review"
                              aria-selected="false" tabindex="-1">Yuva Helpline Business Ethics Hotline</button>
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
                                       <h3 class="product-desc-title">Code of Conduct</h3>
                                       <p> The purpose of our Code of Business Conduct and Ethics (“Code”) is to provide
                                          guidance to all of our colleagues and partners on the conduct of our business
                                          according to the highest ethical standards. By adhering to the Code, we uphold
                                          our Values and Attributes. Our Company’s brand and reputation is best known
                                          for its trustworthiness — an attribute that we intend to uphold in all that we
                                          do. Yuva Helpline has grown and prospered with a culture of honesty, integrity
                                          and accountability and we believe that this culture remains a strong
                                          competitive advantage for us. As a guide, the Code contributes to our future
                                          success by helping to maintain this culture. This Code also helps in the
                                          effective promotion and protection of our Brand and our various stakeholders.
                                          It helps to focus everyone on areas of ethical risk, provides guidance in
                                          recognizing and dealing with ethical issues and provides mechanisms to report
                                          unethical conduct without fear of retribution.<br />
                                          Our Code of Business Conduct and Ethics promotes honest and ethical conduct
                                          throughout the organization, as well as provides a mechanism to report
                                          unethical conduct via the Yuva Helpline Ethics Hotline to help preserve the
                                          culture of honesty and accountability throughout the company.</p>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane fade" id="nav-additional" role="tabpanel" aria-labelledby="nav-anti-tab">
                           <div class="product__details-review pt-60">
                              <div class="row">
                                 <div class="col-lg-12">
                                    <div class="product__details-description-content">
                                       <h3 class="product-desc-title">Anti-Corruption Policy </h3>
                                       <p>Yuva Helpline and its subsidiaries and affiliates worldwide are committed to
                                          conducting our business with honesty, integrity, trustworthiness, and
                                          accountability. This policy, updated in 2023, is straight forward—the Company
                                          prohibits offering or receiving bribes or corrupt payments in any form. This
                                          policy applies equally to all employees of Yuva Helpline, regardless of
                                          location or role, and also applies to agents or representatives, vendors,
                                          clients, business partners or other service providers.</p>
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
                                       <h3 class="product-desc-title">Yuva Helpline Business Ethics Hotline </h3>
                                       <p>To report suspected, planned or actual violations of our Company's policies or
                                          the law, Yuva Helpline provides access to the Yuva Helpline Business Ethics
                                          Hotline, using a telephone number or online reporting system that are
                                          monitored 24 hours per day.

                                          Individuals may use the hotline, at the numbers listed below, or the website
                                          <strong>(www.rabros.in)</strong> to submit a complaint regarding accounting,
                                          internal accounting controls, or auditing matters, among other issues; or to
                                          communicate with the non-management directors of the company.
                                          <br />
                                          <strong>Grivience section :</strong> <?php echo $MOBILE; ?>,
                                          <?php echo $EMAIL_ID; ?>
                                       </p>
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