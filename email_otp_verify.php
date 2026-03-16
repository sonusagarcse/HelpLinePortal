<!doctype html>
<html class="no-js" lang="zxx">
   <head>
<?php include('csslink.php');?>
<?php 
if(isset($_REQUEST['submit'])){
$reg=$_REQUEST['regno'];
$mob=$_REQUEST['mob'];
$sel=$con->prepare("select * from registration where regno=? and mob=?");
$exe=$sel->execute([$reg,$mob]);
$resn=$sel->fetch();
}
?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
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
                           <h3 class="breadcrumb__title">OTP Verification</h3>
                           <div class="breadcrumb__list breadcrumb__list-translate">
                              <span><a href="home">Home</a></span>
                              <span class="dvdr"><i class="fa-sharp fa-regular fa-minus"></i></span>
                              <span>OTP Verification</span>
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
	<?php
     if(isset($_REQUEST['msg'])){
      if($_REQUEST['msg']=='send'){
          ?>
		<h5 style="color:red; text-align:center;font-size:18px;">  Thank You! <br> <strong style="color:#000;">Your OTP has been verified successfully  <br/> Login your Acccount </strong> <a href="healthcare_login" style="color:blue;">Login Here</a></h5>
			<br/><br/>		
		  <?php
      } elseif($_REQUEST['msg']=='otp_not_matched'){?>
              <h5 style="color:red; text-align:center; font-size:18px;">  <strong style="color:#000;">Your OTP not matched, Please verify again </strong> <a href="email_otp_verify" style="color:blue;">Click Here</a></h5> 
			  <br/><br/> 
	<?php
      } elseif($_REQUEST['msg']=='already_verified'){?>
	       <h5 style="color:red; text-align:center; font-size:18px;">  <strong style="color:#000;">Your OTP is already verified</strong> <a href="healthcare_login" style="color:blue;">Login Here </a></h5> 
		    <br/><br/> 		  
			   
	<?php
      } elseif($_REQUEST['msg']=='notsend'){?>
	       <h5 style="color:red; text-align:center; font-size:18px;">  <strong style="color:#000;">Something went wrong, Please verify again </strong> <a href="email_otp_verify" style="color:blue;">Click Here</a></h5> 
		    <br/><br/> 
			
	<?php }}else{?>
<style>
.login-form form {
    text-align: left;
    padding: 30px;
    border: 1px solid #f1f1f1;
    box-shadow: 1px 1px 20px #cccccc57;
}
.form-title {
    text-align: center;
    margin-bottom: 30px;
}
.form-title h2{
    margin-bottom: 20px;
    padding-bottom: 15px;
}

 h2{
    font-weight: 800;
    color: #212121;
    font-family: 'Raleway', sans-serif;
    margin-top: 0;
    line-height: 1.5;
    margin-bottom: 15px;
	text-align:center;
	font-size: 32px;
}
label {
    display: inline-block;
    color: #666;
    margin-bottom: 8px;
    font-weight: 400;
    font-size: 15px;
}
input[type=text], input[type=email], input[type=number], input[type=search], input[type=password], input[type=tel], textarea, select {
    font-size: 14px;
    font-weight: 300;
    background-color: #fff;
    border: 1px solid #060606;
    border-radius: 0;
    padding: 10px 15px;
    width: 100%;
    color: #444444;
    margin-bottom: 15px;
    font-family: 'Poppins', sans-serif;
    height: 42px;
    box-shadow: none;
    margin-bottom: 0;
}
.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #2b2929;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
    box-shadow: inset 0 1px 1px rgb(0 0 0 / 8%);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
.form-group {
    margin-bottom: 15px;
}
</style>

					<div class="login-form">
                        <form  method="post">
			           <div class="row"> 
					   <?php
     				if(isset($_REQUEST['submit'])){
     				if($resn->regno!=$_REQUEST['regno'] && $resn->mob!=$_REQUEST['mob']){?>
		  			<strong style="color:#FF0000;">"Oops!", "Your Details  Are Wrong!", "Status are Inactive"</strong><br/>
	  				 <?php } }?>  
					 <div class="form-group col-md-6">                     
   							<label>Member ID</label>
  <input type="text" value="<?php echo $_REQUEST['regno'];?>" class="form-control" id="regno" placeholder="Enter Your Member ID" name="regno" required>
                                </div>         
					   <div class="form-group col-md-6">                     
   							<label>Mobile Number</label>
  <input type="text" value="<?php echo $_REQUEST['mob'];?>" class="form-control" id="mob" placeholder="Enter Your Mobile Number" name="mob" required>
                                </div>
								
                                <div class="col-md-12">
                                    <div class="comment-btn">
                                        <button class="tp-btn" type="submit" name="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
							</form>
               			 </div>
					  
					 
					  <style>
		   .textdb{
		   color:#000000; 
		   font-size:16px;
		   }
		   </style>
		   <div class="textdb">
		
				
<?php 
if($resn->regno==$_REQUEST['regno']){
if(isset($_REQUEST['submit'])){
?>
<br/>		
<div style="background:rgb(245, 245, 245); padding:10px;">

<form action="code/emailotpverification" method="post" >
								
                       <div class="row"> 
					    
					 <div class="form-group col-md-6">                     
   							<label>Member ID</label>
  			<input type="text" value="<?php echo $resn->regno;?>" class="form-control" id="regno"  name="regno" readonly="" required>
                            </div>
								
					 <div class="form-group col-md-6">                     
   							<label>Name</label>
  			<input type="text" value="<?php echo $resn->name;?>" class="form-control" id="name"  name="name" readonly="" required>
                            </div>  
							       
					   <div class="form-group col-md-6">                     
   							<label>Mobile Number</label>
  <input type="text" value="<?php echo $resn->mob;?>" class="form-control" id="mob" name="mob" readonly="" required>
                                </div>
								
							<div class="form-group col-md-6">                     
   							<label>Email ID</label>
  <input type="text" value="<?php echo $resn->email;?>" class="form-control" id="email"  name="email" readonly="" required>
                            </div>
							
							<div class="form-group col-md-12">                     
   							<label>Enter Your OTP</label>
  <input type="text"  class="form-control" id="otp"  name="otp" placeholder="Type your OTP" required>
                            </div>
								
                                <div class="col-md-12">
                                    <div class="comment-btn">
                                        <button class="tp-btn" type="submit" name="submit">Verify OTP</button>
                                    </div>
                                </div>
                            </div>
							</form>


</div>
<?php }} ?>
</div>
<?php } ?>			
					 
					 </div>
                     </div>
                  </div>
            </section>
         <!-- contact input area end -->
		 
      </main>
<!-- footer area start -->
<?php include('footer.php');?>
		
<script type="text/javascript">
  function FetchDistrict(id){
  $('#district').html('');
    $.ajax({
      type:'post',
      url: 'ajaxdata.php',
      data : { state_id : id},
      success : function(data){
         $('#district').html(data);
      }

    })
  }
</script>

<script>
	   $("input[type='file']").on("change", function () {
     if(this.files[0].size > 2000000) {
       alert("Please upload file less than 2MB. Thanks!!");
       $(this).val('');
     }
    });
</script>
</body>
</html>