<div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php $this->load->view('pages/includes/header') ?>
<div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header text-center"><img src="<?php echo base_url('assets/backend/img/logo.png?t='.mt_rand(0,9).'') ?>" /></div>
      <div class="card-header">Login</div>
      <div class="card-body">
		<?php echo $this->session->flashdata('msg'); ?>
        <form action="<?php echo base_url();?>authpin" method="post"> <!-- action="<?php echo base_url();?>auth" -->
          <div class="form-group">
            <label>Enter Pin</label>
            <input class="form-control" type="text" name="pin" placeholder="Enter pin" required>
          </div>
          
          <input class="form-control" type="hidden" name="emi" value="<?php var_dump($maill); ?>">
          
          <button type="submit" class="btn btn-primary btn-block">Verify</button>
        </form>
        <div class="text-center">
          <a class="d-block small mt-3" href="<?php echo base_url()?>register">Register an Account</a>
          <a class="d-block small" href="<?php echo base_url()?>forgot">Forgot Password</a>
          <br>
          <p>If you enter your 2fa pin and screen turns white: <br>
          Clear Cache & HardReset (Control + F5) or view via incognito browser</P>
        </div>
      </div>
    </div>
  </div>
  
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
 
      function show(){
          $('#myModal').modal('show');
      }
      // show();
  </script>
