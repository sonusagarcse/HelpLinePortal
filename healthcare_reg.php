<!doctype html>
<html class="no-js" lang="zxx">
   <head>
    <?php include('csslink.php');?>
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
                           <h3 class="breadcrumb__title">Register with us</h3>
                           <div class="breadcrumb__list breadcrumb__list-translate">
                              <span><a href="home">Home</a></span>
                              <span class="dvdr"><i class="fa-sharp fa-regular fa-minus"></i></span>
                              <span>Register Now</span>
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
		<h5 style="color:red; text-align:center;font-size:18px;">  Thank You! <br> <strong style="color:#000;">Your registration has been completed successfully  <br/> Verify your Acccount, Member ID & OTP Received on your Email Id.</strong> <a href="email_otp_verify" style="color:blue;">Click Here</a></h5>
			<br/><br/>
		  <?php
      } elseif($_REQUEST['msg']=='password_not_matched'){?>
              <h5 style="color:red; text-align:center; font-size:18px;">  <strong style="color:#000;">Your confirm password was not matched, Please register again </strong> <a href="healthcare_reg" style="color:blue;">Click Here</a></h5> 
			  <br/><br/>   
			     <?php
      } elseif($_REQUEST['msg']=='already_registered'){?>
	       <h5 style="color:red; text-align:center; font-size:18px;">  <strong style="color:#000;">Your mobile number is already registered, Please register again </strong> <a href="healthcare_reg" style="color:blue;">Click Here</a></h5> 
		    <br/><br/> 
	<?php
      } elseif($_REQUEST['msg']=='notsend'){?>
	       <h5 style="color:red; text-align:center; font-size:18px;">  <strong style="color:#000;">Something went wrong, Please register again </strong> <a href="healthcare_reg" style="color:blue;">Click Here</a></h5> 
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
					  <?php
						   $sel=$con->prepare("select * from registration order by id DESC LIMIT 1");
						   $exe=$sel->execute(); 
						   $nres=$sel->fetch(); 
						  ?>    
					 <form action="code/user_reg" method="post" id="Register" novalidate="">
                            <div class="row">
                                <div class="col-xs-12"> 
                                    <div class="form-title"> 
                                       <!-- <h2>Register Account</h2>-->
                                    </div>
                                </div>
								<div class="row">
                                <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
                <label>Name</label> 
				<input type="hidden" class="form-control" name="username" value="<?php echo $uname=$nres->username+1;?>">
				<input type="hidden" class="form-control"  name="doj" value="<?php echo date('Y-m-d');?>">
				<input type="hidden" class="form-control"  name="asession" value="<?php echo date('Y');?>">
				<input type="hidden" class="form-control"  name="status" value="0">
				<input type="hidden" class="form-control"  name="reg_type" value="1">
				<input type="text" class="form-control" placeholder="Type Your Name" name="name" required="">  
                                </div>
								<div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
								 <label>Email Id</label> 
								<input type="email" class="form-control" placeholder="Type Your Email" name="email" required="">
								</div>
								
							     </div>
						    <div class="row"> 
                                 <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
							 <label>Gender </label>
			<select name="gender" class="form-control"> 
			<option value="">---Select Gender---</option>  
			<option value="Male">Male</option>  
			<option value="Female">Female</option>
			</select>
                                </div>    
                                 <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
                                  <label>Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" placeholder="Date of Birth" name="dob" required="">
                                </div>
								</div>
								
								<div class="row"> 
                                 <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
				<label>State</label> 
                <select name="state" class="form-control" onChange="FetchDistrict(this.value)" id="statel" required> 
				<?php
				$sel=$con->prepare("select * from state");
				$exe=$sel->execute();
				$i=1; 
				while($res_1=$sel->fetch())
				{?>
			 <option value="<?php echo $res_1->id;?>" <?php if($res_1->id==$_REQUEST['state']) echo "selected";?>><?php echo $res_1->name;?></option>
				<?php  $i++;}?>
				</select>
                                </div>    
                                 <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
                                  <label>District</label>
                                    <select name="dis" class="form-control" id="district" required>
									<option>Choose District</option>
									</select>
                                </div>
								</div> 
								<div class="row">
                                <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">  
                                    <label>Mobile Number </label>
                                    <input type="text" class="form-control" placeholder="Type Your Mobile No." name="mob" maxlength="10" required=""> 
                                </div> 
                                <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding"> 
								<label>WhatsApp Number </label> 
				<input type="text" class="form-control" id="phnumber" maxlength="10" placeholder="Type Your Whatsapp No." name="wat_no">
                                </div>   
								</div>
								<div class="row">
                                <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">
                                    <label>Password</label> 
                     <input type="password" class="form-control" placeholder="Type Your Password" name="pass" minlength="8" id="pass2" required="">
                                </div>
                                <div class="form-group col-md-6 col-sm-12 col-xs-12 col-left-padding">   
                                 <label>Confirm Password</label> 
                                    <input type="password" class="form-control" placeholder="Type Confirm Password" name="c_pass" minlength="8" data-parsley-equalto="#pass2" required="">
                                </div> 
                                </div>
								<div class="row">
                                <div class="form-group  col-sm-12 col-xs-12 col-left-padding">
                                    <label>Complete Address</label>
                                    <textarea name="address" class="form-control" rows="15" cols="5" required=""></textarea>
                                </div> 
								</div>
                                <div class="col-xs-12">
                                    <div class="checkbox-outer">
                                         <input type="checkbox" class="form-check-input" id="check" data-parsley-multiple="check" required>
    <label class="form-check-label" for="check">By signing up you <a href="term_condition_of_patient"> <strong>terms and conditions</strong> </a> accept to</label>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="comment-btn">
                                        <button class="tp-btn" id="btncheck">Register Now <i class="fa-regular fa-arrow-right-long"></i></button> 
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="comment-btn">
                Already have an account?<a href="healthcare_login"><strong>Log in</strong></a>
                                    </div>
                                </div>
                            </div>
                        </form>
					 </div>
					 <?php }?>
					 
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