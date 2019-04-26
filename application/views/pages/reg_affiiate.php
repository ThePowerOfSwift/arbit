<div id="google_translate_element"></div>
<style>
.container1 {
    display: block;
    position: relative;
    padding-left: 35px;
    margin: 15px 0px;
    cursor: pointer;
    font-size: 18px;
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
</style>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="StartCheme">
  <title>ARB - Platform</title>
  <!-- Bootstrap core CSS-->
  <link href="<?php echo base_url()?>assets/backend/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="<?php echo base_url()?>assets/backend/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
  <link href="<?php echo base_url()?>assets/backend/css/sb-admin.css" rel="stylesheet">
  <link href="<?php echo base_url()?>assets/backend/css/main.css" rel="stylesheet">
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="<?php echo base_url()?>assets/backend/js/main.js"></script>
  <script src="<?php echo base_url()?>assets/backend/vendor/jquery/jquery.validate.js"></script>
  <script src="<?php echo base_url()?>assets/backend/vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url()?>assets/backend/vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo base_url()?>assets/backend/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Core plugin JavaScript-->
  <script src="<?php echo base_url()?>assets/backend/vendor/jquery-easing/jquery.easing.min.js"></script>
<body class="bg-dark">
<div class="container">
    <div class="card card-register mx-auto mt-5">
      <div class="card-header text-center"><img src="<?php echo base_url('assets/backend/img/logo.png?t='.mt_rand(0,9).'') ?>" /></div>
      <div class="card-header">Register an Account</div>
      <div class="card-body">
          <?php echo $this->session->flashdata('msg'); ?>
        <form id="registerForm" action="<?php echo base_url()?>register/add_child_affiliate" method="post">
          <div class="form-group">
            <label>Email address</label>
            <input class="form-control" type="email" placeholder="Enter email" name="u_email" id="u_email" required>
          </div>
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control text-input" placeholder="Username" name="u_username" id="u_username" required>
          </div>
          <div class="form-group">
            <label>MEW/MetaMask Wallet (Must be custom ERC20 compliant)</label>
            <input type="text" class="form-control text-input" placeholder="Wallet" name="u_wallet">
          </div>          
          <div class="form-group">
            <label style="display: none;">Code affiliate</label>
            <input type="text" class="form-control text-input" placeholder="Blank if not exist" value="<?php echo $codeAff ?>" name="code_parent" readonly style="display: none;">
          </div>          
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
            <div class="pull-left">
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
<!-- Bootstrap core JavaScript-->
<script>
    
    
    
    localStorage['casheData_Affiliate'] = '<?php echo $codeAff ?>';
    //console.log();
    
    // function openUrl() {
    //     if( $("#agreeCheckBox").is(':checked'))
    //     {
    //         window.open("http://www.arbitraging.co/index_files/docs/ArbitragingTOS.pdf","_blank");
    //     }
    // }

    function mySubmit(){
        document.getElementById("registerForm").submit();
    }
</script>
</body>

	


