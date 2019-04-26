<div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php $this->load->view('pages/includes/header') ?>
<div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header text-center"><img src="<?php echo base_url('assets/backend/img/logo.png?t='.mt_rand(0,9).'') ?>" /></div>
      <div class="card-header">Change Password</div>
      <div class="card-body">
		<?php echo $this->session->flashdata('msg'); ?>
        <form action="<?php echo base_url()?>register/forgetpassword" method="post"> <!-- action="<?php echo base_url()?>register/forgetpassword" -->
          <div class="form-group">
            <label>Email address</label>
            <input class="form-control" type="text" name="email" placeholder="Enter email" required>
          </div>
          
          <button type="submit" class="btn btn-primary btn-block">Send</button>
        </form>
		<div class="text-center">
          <a class="d-block small mt-3" href="<?php echo base_url() ?>login">Login Page</a>       
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
        <h4 class="modal-title pull-left">Round Sale 3</h4>
      </div>
      <div class="modal-body">
        <p>During the token sale, we are pausing the platform login/register for safety. You can purchase using the contract address on the front page directly</p>
      </div>
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
