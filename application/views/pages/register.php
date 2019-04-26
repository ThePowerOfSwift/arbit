<div id="google_translate_element"></div>
<style>
.container1 {
    display: block;
    position: relative;
    /*padding-left: 35px;*/
    margin: 15px 0px;
    cursor: pointer;
    font-size: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Hide the browser's default checkbox */
.container1 input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Create a custom checkbox */
.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container1:hover input ~ .checkmark {
    background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container1 input:checked ~ .checkmark {
    background-color: #daa521;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the checkmark when checked */
.container1 input:checked ~ .checkmark:after {
    display: block;
}

/* Style the checkmark/indicator */
.container1 .checkmark:after {
    left: 8px;
    top: 0px;
    width: 10px;
    height: 20px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
.label_39 ._bottom_3v ._pad100_GR ._init_Tk
{
    display:none !important;
}
</style>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<?php $this->load->view('pages/includes/header') ?>

<div class="container">
    <div class="card card-register mx-auto mt-5">
      <div class="card-header text-center"><img src="<?php echo base_url('assets/backend/img/logo.png?t='.mt_rand(0,9).'') ?>" /></div>
      <div class="card-header">Register an Account</div>
      <div class="text-center" style="margin-top: 20px;">
            <?php echo $this->session->flashdata('msg'); ?>
           <?php if(isset($_GET['error']) && $_GET['error']=='wallet_exist'){?><span class="alert alert-danger text-center" style="width:75%"> Wallet Already Exist</span><?php }?>
           <?php if(isset($_GET['error']) && $_GET['error']=='length_notvalid'){?><span class="alert alert-danger text-center" style="width:75%"> Wallet Length Is not valid.</span><?php }?>
           <?php if(isset($_GET['error']) && $_GET['error']=='enter_email'){?><span class="alert alert-danger text-center" style="width:75%"> Enter Your Email.</span><?php }?>
           <?php if(isset($_GET['error']) && $_GET['error']=='enter_username'){?><span class="alert alert-danger text-center" style="width:75%"> Enter Your Username.</span><?php }?>
       </div>
      <div class="card-body">
        <form id="registerForm" action="<?php echo base_url()?>register/add" method="post"> <!-- action="<?php echo base_url()?>register/add" -->
          <div class="form-group">
            <label>Email address</label>
            <input class="form-control" type="email" placeholder="Enter email" name="u_email" id="u_email" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control text-input" placeholder="Username" name="u_username" id="u_username" required>
          </div>

        <!--<div class="form-group">-->
            <!--<label>Code Affiliate (Code shown will be your code after signup)</label>-->
            <input type="hidden" class="form-control text-input" placeholder="Blank if not exist" value="<?php echo $codeAff; ?>" name="a_code" readonly>
            <input type="hidden" class="form-control text-input" placeholder="Blank if not exist" id="aff_val_register" name="code_parent" readonly>
        <!--</div>-->
          
          <div class="form-group">
            <label>MEW/MetaMask Wallet (Must be custom ERC20 compliant)</label>
            <input type="text" class="form-control text-input" placeholder="Wallet" id="mewAddress" name="u_wallet" required>
            <label id="mewError" class="error text-danger" style="display:none">Enter a Valid MEW/METAMASK Address</label>
          <div class="form-group">
            <div class="form-row">
              <div class="col-md-6">
                <label for="exampleInputPassword1">Password</label>
                <input class="form-control" type="password" placeholder="Password" name="u_pwd" id="u_pwd" required>
              </div>
              <div class="col-md-6">
                <label for="exampleConfirmPassword">Confirm password</label>
                <input class="form-control" type="password" placeholder="Confirm password" id="pwd2">
              </div>
            </div>
            <div class="col-12">
                <div class="pull-left">
                    <!--<label class="container1">I <b>Agree</b> to the <b><a href="http://www.arbitraging.co/index_files/docs/ArbitragingTOS.pdf" target="_blank">Terms of Services</a></b>.-->
                      <!--<input id="agreeCheckBox" type="checkbox" class="form-check-input" checked>-->
                      <!--<span class="checkmark"></span>-->
                      <span class="text-danger container1 btn-block">Clicking on Register means you agree on our <b><a href="http://www.arbitraging.co/index_files/docs/ArbitragingTOS.pdf" target="_blank">Terms of Services</a></b></span>
                    <!--</label>-->
                </div>
            </div>
            <p id="message"></p>
          </div>
          <button type="button" class="btn btn-primary btn-block g-recaptcha" data-sitekey="6LexR1YUAAAAACKMw-Reqz4624A3WXhs_jWhBsoe" data-callback="mySubmit">Register</button>
          
       
          
        </form>
        <div class="text-center">
          <a class="d-block small mt-3" href="<?php echo base_url() ?>login">Login Page</a>
          <a class="d-block small" href="<?php echo base_url()?>forgot">Forgot Password</a>
        </div>
      </div>
    </div>
  </div>
  
  <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    </div>
  </div>

</div>

  
<?php $this->load->view('pages/includes/footer') ?>

<script>
    
    var myCasheData_Affiliate = localStorage['casheData_Affiliate'];
    console.log(myCasheData_Affiliate);
    if(myCasheData_Affiliate != undefined)
    {
        $('#aff_val_register').val(myCasheData_Affiliate);
    }
    else
    {
        $('#aff_val_register').val("NULL");
    }
    
    // function openUrl() {
    //     if( $("#agreeCheckBox").is(':checked'))
    //     {
    //         window.open("https://www.arbitraging.co/index_files/docs/ArbitragingTOS.pdf","_blank");
    //     }
    // }
    
    $( "#mewAddress" ).keyup(function() {
         var value = document.getElementById('mewAddress').value;
         if (value.length < 42) {
          $("#mewError").css("display", "block");
         }
         else
         {
            $("#mewError").css("display", "none");
         }
    });
    
    function mySubmit(){
        document.getElementById("registerForm").submit();
    }
      function show(){
          $('#myModal').modal('show');
      }
      // show();
  </script>

