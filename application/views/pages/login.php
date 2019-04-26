<div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php $this->load->view('pages/includes/header') ?>
<style>
    .label_39 ._bottom_3v ._pad100_GR ._init_Tk
    {
        display:none !important;
    }
</style>
<div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header text-center"><img src="<?php echo base_url('assets/backend/img/logo.png?t='.mt_rand(0,9).'') ?>" /></div>
      <div class="card-header">Login</div>
      <div class="card-body">
		<?php echo $this->session->flashdata('msg'); ?>
        <form action="<?php echo base_url();?>auth" method="post"> <!-- action="<?php echo base_url();?>auth" -->
          <div class="form-group">
            <label>Email address</label>
            <input class="form-control" type="text" name="email" placeholder="Enter email or username" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input class="form-control" type="password" name="pwd" placeholder="Password" required>
          </div>
          <div class="form-group">
            <div class="form-check">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox"> Remember Password</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div class="text-center">
          <a class="d-block small mt-3" href="<?php echo base_url()?>register">Register an Account</a>
          <a class="d-block small" href="<?php echo base_url()?>forgot">Forgot Password</a>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="resendModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
            <form action="<?php echo base_url();?>resend_email_verification" method="post"> 
          <div class="form-group">
            <label>Email address</label>
            <input class="form-control" type="text" name="u_email" placeholder="Enter Your Email Address" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Send</button>
        </form>
            </div>
        </div>
    </div>
</div>

    <!--<div id="onLoadModal" class="modal fade" role="dialog">-->
    <!--  <div class="modal-dialog">-->
    
    <!--     Modal content-->
    <!--    <div class="modal-content">-->
    <!--      <div class="modal-header">-->
    <!--        <h4 class="modal-title"><b>Announcement: </b></h4>-->
    <!--        <button type="button" class="close" data-dismiss="modal">&times;</button>-->
    <!--      </div>-->
    <!--      <div class="modal-body">-->
    <!--        <p>Dear ARBnation,<br><br>We are pleased to inform everyone the necessary updates have been completed and the platform will be live at 11:15pm UTC. <br>Thanks,<br><br> ARB Community Manager - OZI</p>-->
    <!--      </div>-->
    <!--      <div class="modal-footer">-->
    <!--        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
    <!--      </div>-->
    <!--    </div>-->
    
    <!--  </div>-->
    <!--</div>-->
  
  
  <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

 
<?php $this->load->view('pages/includes/footer') ?>
<script>

// $('#onLoadModal').modal('show');

$('#resend').click(function() {
   $('#resendModal').modal('show');
});
      function show(){
          $('#myModal').modal('show');
      }
      // show();
  </script>
