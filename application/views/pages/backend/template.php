<?php 

    $v1 = filemtime('/home/arbitrage/public_html/platform/assets/backend/js/sb-admin.min.js'); 
    $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri_segments = explode('/', $uri_path);
    
    $bot_fT = filemtime('/home/arbitrage/public_html/platform/assets/backend/css/bot.css');
    $sb_admin_fT= filemtime('/home/arbitrage/public_html/platform/assets/backend/css/sb-admin.css');
    
    $exchange_beta_fT= filemtime('/home/arbitrage/public_html/platform/assets/backend/css/exchange_beta.css');
    $accountNew_fT= filemtime('/home/arbitrage/public_html/platform/assets/backend/css/account_new.css');
    
    $wallet_fT = filemtime('/home/arbitrage/public_html/platform/assets/backend/css/wallet.css');
    $aBOT_fT = filemtime('/home/arbitrage/public_html/platform/assets/backend/css/aBOT.css');
    $support_fT = filemtime('/home/arbitrage/public_html/platform/assets/backend/css/support.css');
    $auction_fT = filemtime('/home/arbitrage/public_html/platform/assets/backend/css/auction.css');
    $trading_fT = filemtime('/home/arbitrage/public_html/platform/assets/backend/css/trading.css');
    /* */
    $u_id = $this->session->userdata('u_id');
    $announcement = $this->db->query("SELECT announcement FROM users Where u_id = $u_id")->row()->announcement;
    if($announcement == 0) { $announcement = ""; }
    
    if($red_flag = $this->db->query("SELECT read_flag FROM auction_req Where user_id = $u_id")->row()){
        if($red_flag->read_flag == 1){
            $auction_flag = 1;
        }else{
            $auction_flag = '';
        }
    }else{
        $auction_flag = '';
    }
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title id="arbPriceTab">ARBITRAGING</title>
  <!-- Bootstrap core CSS-->
  <link href="<?php echo base_url()?>assets/backend/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="<?php echo base_url()?>assets/backend/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <!-- Page level plugin CSS-->
  <link href="<?php echo base_url()?>assets/backend/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
  <link rel='stylesheet' href='https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css'/>
  <link rel='stylesheet' href='https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css'/>
  <!-- Custom styles for this template-->
  <link href="<?php echo base_url()?>assets/backend/css/sb-admin.css?v=1.<?php echo $sb_admin_fT; ?>" rel="stylesheet">
  <link href="<?php echo base_url()?>assets/backend/css/bot.css?v=1.<?php echo $bot_fT; ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
  
  <?php if( $uri_segments[3] == "exchange" || $uri_segments[3] == "exchange_beta_a"){  ?>
  <link href="<?php echo base_url()?>assets/backend/css/exchange_beta.css?v=1.<?php echo $exchange_beta_fT; ?>" rel="stylesheet">
  <?php }  ?>
  
  <?php if( $uri_segments[2] == "user" ){  ?>
  <link href="<?php echo base_url()?>assets/backend/css/account_new.css?v=1.<?php echo $accountNew_fT; ?>" rel="stylesheet">
  <?php }  ?>
  
  <?php if( $uri_segments[3] == "wallet" || $uri_segments[3] == "wallet_old" ){  ?>
  <link href="<?php echo base_url()?>assets/backend/css/wallet.css?v1=<?php echo $wallet_fT; ?>" rel="stylesheet">
  <?php }  ?>
  
  <?php if( $uri_segments[3] == "aBOT2_beta2" || $uri_segments[3] == "aBOT2" ){  ?>
  <link href="<?php echo base_url()?>assets/backend/css/aBOT.css?v1=<?php echo $aBOT_fT; ?>" rel="stylesheet">
  <?php }  ?>
  
  <?php if( $uri_segments[3] == "support_beta_a" || $uri_segments[3] == "support_new" ){  ?>
  <link href="<?php echo base_url()?>assets/backend/css/support.css?v1=<?php echo $support_fT; ?>" rel="stylesheet">
  <?php }  ?>
  
  <?php if( $uri_segments[3] == "auction"){  ?>
  <link href="<?php echo base_url()?>assets/backend/css/auction.css?v1=<?php echo $auction_fT; ?>" rel="stylesheet">
  <?php }  ?>
  
  <link href="<?php echo base_url()?>assets/backend/css/trading.css?v1=<?php echo $trading_fT; ?>" rel="stylesheet">
  
  <link rel="icon" href="<?php echo base_url('assets/backend/img/favicon.png') ?>">
    
  <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/amstock.js"></script>
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.0/socket.io.js"></script>-->
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.0/socket.io.dev.js"></script>-->
  <script src="https://www.amcharts.com/lib/3/plugins/dataloader/dataloader.min.js"></script>
  <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
  <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
  
<!--  <script src="//code.highcharts.com/highcharts.js"></script>-->
<!--<script src="//code.highcharts.com/modules/exporting.js"></script>-->

    <!-- AngularJS files-->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-animate.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular-sanitize.js"></script>
    <script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-2.5.0.js"></script>

   <!-- Bootstrap core JavaScript-->
    <script src="<?php echo base_url()?>assets/backend/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url()?>assets/backend/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="<?php echo base_url()?>assets/backend/vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <!--<script src="<?php //echo base_url()?>assets/backend/vendor/datatables/jquery.dataTables.js"></script>-->
    <!--<script src="<?php //echo base_url()?>assets/backend/vendor/datatables/dataTables.bootstrap4.js"></script>-->
    <!-- Custom scripts for all pages-->
    <script src="<?php echo base_url()?>assets/backend/js/sb-admin.min.js?v=<?php echo $v1; ?>"></script>
    <!-- Custom scripts for this page-->
    <!--<script src="<?php //echo base_url()?>assets/backend/js/sb-admin-datatables.min.js"></script>-->
<script>
    var t;
    
    function inactivityTime () {
        window.onload = resetTimer();
        
        document.onmousemove = resetTimer();
        document.onkeypress = resetTimer();
    };
    inactivityTime();
    
    function logout() {
        location.href = '<?php echo base_url()?>logout';
    }

    function resetTimer() {
        clearTimeout(t);
        t = setTimeout(logout, 9000000);
    }
    
</script>
<style>

.loader {
    border: 8px solid #f3f3f3;
    border-radius: 50%;
    border-top: 8px solid #daa552;
    width: 70px;
    height: 70px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
    margin-left: 50%;
}
.closedTabDiv{
    background: red;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    display: inline-block;
    float: left;
    margin-right: 8px;
    margin-top: 8px;
}
.openTabDiv{
    background: green;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    display: inline-block;
    float: left;
    margin-right: 8px;
    margin-top: 8px;
}
.workTabDiv{
    background: yellow;
    width: 10px;
    height: 10px;
    border-radius: 5px;
    display: inline-block;
    float: left;
    margin-right: 8px;
    margin-top: 8px;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

#supportBadge{display:none;}
.menuValue{
    display: inline-block;
    margin-right: 30px;
}
.menuValues .row .menuValue:last-of-type{margin-right: 0;}
.apiValue{
    color: #daa521;
    background-color: #343a40;
    /*border-color: #daa521;*/
    box-shadow: 0px 0px 10px 1px #daa521;
    position: relative;
    overflow: hidden;
    text-align: center;
    cursor: pointer;
    font-size: 15px;
    margin: 0;
    padding: 10px 10px;
    border-radius: 5px;
}
.nav-link.active{position: relative;background: #171f27;}
.nav-link.active i{color: #daa521;}
.nav-link.active span{color: #daa521;}
.nav-link.active:before {
    content: '';
    display: inline-block;
    position: absolute;
    left: 0px;
    width: 2px;
    top: 0;
    left: 0;
    bottom: 0;
    background: #daa521;
    height: 100%;
}

.nav-link:before {
    position: absolute;
    content: '';
    display: none;
    top: -100%;
    bottom: auto;
    height: 100%;
    width: 2px;
    left: 0;
    background: #daa521;
    transition: 0.3s ease all;
}
.nav-link:hover{position: relative;}
.nav-link:hover:before {
    display: inline-block;
    position: absolute;
    content: '';
    top: 0;
    bottom: 0;
    width: 2px;
    left: 0;
    background: #daa521;
    height: 100%;
}
.nav-link:hover i,.nav-link:hover span{transition: 0.3s ease all; color: #daa521;}
.hoverme {
    position: absolute;
    display: inline-block;
    visibility: visible;
    content: "";
    width: 40px;
    height: 130px;
    /* background: #daa521; */
    transition: 0.8s ease all;
    transform: rotate(20deg);
    left: -100%;
    top: -40px;
    background: -moz-linear-gradient(left, rgba(218,165,33,0) 3%, rgba(218,165,33,0.91) 43%, rgba(218,165,33,1) 47%, rgba(218,165,33,1) 54%, rgba(218,165,33,0.98) 55%, rgba(218,165,33,0) 100%); /* FF3.6-15 */
    background: -webkit-linear-gradient(left, rgba(218,165,33,0) 3%,rgba(218,165,33,0.91) 43%,rgba(218,165,33,1) 47%,rgba(218,165,33,1) 54%,rgba(218,165,33,0.98) 55%,rgba(218,165,33,0) 100%); /* Chrome10-25,Safari5.1-6 */
    background: linear-gradient(to right, rgba(218,165,33,0) 3%,rgba(218,165,33,0.91) 43%,rgba(218,165,33,1) 47%,rgba(218,165,33,1) 54%,rgba(218,165,33,0.98) 55%,rgba(218,165,33,0) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00daa521', endColorstr='#00daa521',GradientType=1 );
}
.apiValue:hover .hoverme {  left: 110%;}
.dropbtn {  border: none;}
.dropdown {
    position: relative;
    display: inline-block;}
.nav-item:hover .dropdown-content { display: block;}
.dropdown-content {
    display: none;
    min-width: 120px;
    z-index: 1;
    left: 25%;
}
.dropdown-content a {
    color: #868e96;
    padding: 2% 25%;
    text-decoration: none;
    display: block;
    transition: 0.3s ease all;
}
.fontWeight{font-weight:700;}
.dropdown-content a:hover {color:#daa520;}
.dropdown:hover .dropdown-content {display: block;}
.dropdown:hover .dropbtn {}
@media (max-width: 1199px){
    .apiValue{font-size: 12px;}
    .menuValue{margin-right: 15px;}
    #mainNav .navbar-brand {width: 220px;}
}
@media (max-width: 992px){
    .menuValues{text-align: center;}
    .apiValue{font-size: 13px;}
    #mainNav .navbar-brand {margin: 0;}
}
@media (max-width: 768px){
    .menuValue{display: block;width: 90%;margin: 0 auto 15px;}
    .apiValue{padding: 5px 5px;font-size: 12px;} 
    .menuValues .row .menuValue:last-of-type{margin-right: auto;}
}


/*form .fieldWrap_2K:nth-of-type(3) {*/
/*    display:none;*/
/*}*/
</style>
</head>
<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <div class="fixed-top">
        <!-- Navigation-->
          <nav class="navbar navbar-expand-lg navbar-dark bg-dark " id="mainNav">
            <a class="navbar-brand" href="#"><img src="<?php echo base_url('assets/backend/img/logo-on-dark.png') ?>" width="217.6" height="72" /></a> 
            
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span> 
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
              <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                  <a class="nav-link <?php if (strpos($uri_path, 'admin') == true && $uri_segments[3] == "" ) { echo 'active'; } else{ echo '';} ?>" href="<?php echo base_url() ?>admin">
                    <i class="fa fa-home"></i>
                    <span class="nav-link-text">Dashboard &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                  <a class="nav-link <?php echo $uri_segments[3] == "deposit" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/deposit">
                    <i class="fa fa-credit-card"></i>
                    <span class="nav-link-text">Deposit &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                  <a class="nav-link <?php echo $uri_segments[3] == "wallet" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/wallet"> 
                    <i class="fa fa-bank"></i>
                    <span class="nav-link-text">Wallet  &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
               <!-- a class="nav-link" -->  <a class="nav-link <?php echo $uri_segments[3] == "aBOT2" ?  'active' : '' ; ?>"  href="<?php echo base_url() ?>admin/aBOT2">
                    <i class="fa fa-line-chart"></i>
                    <span class="nav-link-text">aBOT &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                    <a class="nav-link  <?php echo $uri_segments[3] == "mBOT" ?  'active' : '' ; ?>">
                        <i class="fa fa-line-chart"></i>
                        <span class="nav-link-text dropdown">
                            <span class="dropbtn">mBOT </span>
                        </span>&nbsp;<div class="openTabDiv"></div>
                    </a>    
                    <div class="dropdown-content">
                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=USD"><i class="fas fa-dollar-sign"></i> USD</a>
                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=USDT"><i class="fas fa-dollar-sign"></i> USDT</a>
                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=BTC"><i class="fab fa-btc"></i> BTC</a>
                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=ETH"><i class="fab fa-ethereum"></i> ETH</a>
                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=USD-USDT"><i class="fas fa-dollar-sign"></i> USD-USDT</a>
                    </div>
                </li>    
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
               <a class="nav-link">  
                    <i class="fa fa-recycle"></i>
                    <span class="nav-link-text">Recycle &nbsp;<div class="closedTabDiv"></div></span>
                  </a>
                </li> 
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
               <a class="nav-link  <?php echo $uri_segments[3] == "vault" ?  'active' : '' ; ?>" href="<?php echo base_url(); ?>admin/vault">
                    <i class="fa fa-line-chart"></i>
                    <span class="nav-link-text">Vault  &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li> 
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
        		<a class="nav-link <?php echo $uri_segments[3] == "tradepro" ?  'active' : '' ; ?>" href="<?php echo base_url(); ?>admin/trading">
                    <i class="fa fa-line-chart"></i>
                    <span class="nav-link-text">Trade Pro  &nbsp;<div class="openTabDiv"></div></span>
                </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
        		<a class="nav-link <?php echo $uri_segments[3] == "exchange" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/exchange">
                    <i class="fa fa-money"></i>
                    <span class="nav-link-text">Exchange  &nbsp;<div class="openTabDiv"></div></span>
                </a>
                </li>
                
                <?php if( $this->session->userdata('original_session') == 0) {?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
        		<a class="nav-link <?php echo $uri_segments[3] == "affiliate" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/affiliate">
                    <i class="fa fa-users"></i>
                    <span class="nav-link-text">Affiliate &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                <a class="nav-link <?php echo $uri_segments[2] == "user" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>user">
                    <i class="fa fa-cogs"></i>
                    <span class="nav-link-text">Account &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <?php }?>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                 <a class="nav-link <?php echo $uri_segments[3] == "auction" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/auction">
                    <i class="fa fa-gavel"></i>
                    <span class="nav-link-text">Auction &nbsp;<i class="badge badge-warning"><?php echo $auction_flag; ?></i><div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                 <a class="nav-link <?php echo $uri_segments[3] == "announcement" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/announcement" onclick="annTabClick()">
                    <i class="fa fa-bullhorn"></i>
                    <span class="nav-link-text">Announcements &nbsp;<div class="openTabDiv"></div><i class="badge badge-warning annBadgeClass"><?php echo $announcement; ?></i></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                 <a class="nav-link <?php echo $uri_segments[3] == "support_new" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/support_new">
                    <i class="fa fa-life-ring"></i>
                    <span class="nav-link-text">Support  &nbsp;<div class="openTabDiv"></div></span> <i class="badge badge-warning" id="supportBadge"></i>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                 <a class="nav-link <?php echo $uri_segments[3] == "history" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/history">
                    <i class="fa fa-history"></i>
                    <span class="nav-link-text">History  &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right">
                 <a class="nav-link" href="http://arbfaq.factq.com/frequently-asked-questions-faq/" target="_blank">
                    <i class="fa fa-question"></i>
                    <span class="nav-link-text">FAQ's  &nbsp;<div class="openTabDiv"></div></span>
                  </a>
                </li>
                <!--  -->
                    <span id="collapseMenu1" class="">
                        <a class="nav-link" href="javascript:void(0);">
                            <i class="fa fa-angle-left" aria-hidden="true"></i> Collapse Menu
                        </a>
                    </span>
              </ul>
              <!--<ul class="navbar-nav sidenav-toggler">-->
              <!--  <li class="nav-item">-->
              <!--    <a class="nav-link text-center" id="sidenavToggler">-->
              <!--      <i class="fa fa-fw fa-angle-left"></i>-->
              <!--    </a>-->
              <!--  </li>-->
              <!--</ul>-->
              <div class="col-lg-11 col-md-12 menuValues">
                  <div class="row">
                      <span class="menuValue">
                          <div class="apiValue">
                              <span class="hoverme"></span>
                            <strong>1 ARB = <span id="arbPrice"></span></strong>
                          </div>
                      </span>
                      <span class="menuValue">
                          <div class="apiValue">
                              <span class="hoverme"></span>
                              <strong>aBOT = $<span id="abotPrice"></span></strong>
                          </div>
                      </span>
                      <span class="menuValue">
                          <div class="apiValue">
                              <span class="hoverme"></span>
                              <strong>1 ETH = $<span id="ethValueSpan"></span></span></strong>
                          </div>
                      </span>
                      <span class="menuValue">
                          <div class="apiValue">
                              <span class="hoverme"></span>
                              <strong>24h Vol = Ξ <span id="24hValueSpan"></span></span></strong>
                          </div>
                      </span>
                      <!--<div class="col-md-3">-->
                      <!--    <div class="alert alert-info apiValue">-->
                      <!--        <span class="hoverme"></span>-->
                      <!--      <strong> 1 ARB = <span id="arbPrice"></span></strong>-->
                      <!--    </div>-->
                      <!--</div>-->
                      <!--<div class="col-md-3">-->
                      <!--    <div class="alert alert-info apiValue">-->
                      <!--        <span class="hoverme"></span>-->
                      <!--        <strong> Abot = $ <span id="abotPrice"></span></strong>-->
                      <!--    </div>-->
                      <!--</div>-->
                      <!--<div class="col-md-2">-->
                      <!--    <div class="alert alert-info apiValue">-->
                      <!--        <span class="hoverme"></span>-->
                      <!--        <strong> 1 ETH = $<span id="ethValueSpan"></span></span></strong>-->
                      <!--    </div>-->
                      <!--</div>-->
                      <!--<div class="col-md-2">-->
                      <!--    <div class="alert alert-info apiValue">-->
                      <!--        <span class="hoverme"></span>-->
                      <!--        <strong> 24h Vol = <span id="24hValueSpan"></span></span></strong>-->
                      <!--    </div>-->
                      <!--</div>-->
                  </div>
                  
                    <script>
                        function msg_count() { 
                            $.ajax({
                              url: "<?php echo base_url(); ?>msg_count",
                              type: 'GET',
                              //async: false,
                              success: function(data) {
                                  //data = JSON.parse(data);
                                  if(data > 0)
                                    {
                                        $('#supportBadge').css('display', 'inline-block');
                                        $('#supportBadge').html(data);
                                    }
                                }
                            });
                        }
                        msg_count();
                        var arb_p = 0;
                        var arb_eth_p = 0;
                        var aeth = 0;
                        var coinexchange_price = 0;
                        function arbValueLive() { 
                            $.ajax({
                               url: "<?php echo base_url(); ?>arb_valueLive",
                               type: 'GET',
                               async: false,
                               success: function(data) {
                                  data = JSON.parse(data);
                                  arb_p = data.USD;
                                  arb_eth_p = data.ETH;
                                  vol24 = data.vol24;
                                  abot_usd = data.abot_usd;
                                  aeth = data.eth_usd_price;
                                  coinexchange_price = data.coinexchange_price;
                                  $('#ethValueSpan').text(aeth);
                                  $('#arbPrice').text(arb_eth_p + ' (' + arb_p + ' USD)' );
                                  $('#abotPrice').text(abot_usd + ' USD' );
                                  $('#24hValueSpan').text(vol24);
                                  $('#arbPriceTab').empty();
                                $('#arbPriceTab').text(arb_p + " | ARBITRAGING" );
                                }
                            });
                        }
                        arbValueLive();
                        window.setInterval( function() {arbValueLive();}, 60000);  
                        // function converEth(){
                        //     //actETH = ethValue;
                        //     $.get( "https://api.coinmarketcap.com/v2/ticker/1027/")
                        //         .done(function( data ) {
                        //             aeth = data.data.quotes.USD.price;
                        //             //aeth = aeth.toFixed(2);
                        //             //coinEth = aeth;
                                    
                        //         });
                        // }
                        // converEth();
                        // window.setInterval( function() {converEth();}, 60000); 
                    </script>
              </div>
              <ul id="actionMenu" class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a id="userList" class="nav-link btn userDetailIcon" data-toggle="dropdown">
                        <i class="fa fa-user"></i>
                    </a>
                    <ul class="dropdown-menu DmRight">
                      <li><strong>Username: </strong><?php echo $this->session->userdata('u_username') ?></li>
                      <ul id="otherLogins">
                        <?php 
                            if( $this->session->userdata('original_session') == 0) {
                                // ------------ get user you have accesstoo. 
                                $u_id = $this->session->userdata('u_id');
                                $verified_requests = $this->db->query("SELECT * FROM access_verified WHERE user_id = ?", array($u_id))->result();
                                if(sizeof($verified_requests) > 0){
                                    ?> Login as : <?php
                                    foreach($verified_requests as $req){
                                    $user = $this->db->query("SELECT * FROM users WHERE u_id = ?", array($req->accessed_user_id))->row();
                                    ?>
                                    <li onclick="getLoginAs(<?php echo $user->u_id; ?>)"><?php echo $user->u_email; ?></li>
                                    <?php
                                    }
                                }
                            }    
                            
                            if( $this->session->userdata('original_session') != 0) {?>
                               <button class="btn" onclick="getLoginAs(<?php echo $this->session->userdata('original_session');?>)">Switch Back</button> 
                        <?php }?>
                      <input type="hidden" value="<?php echo $this->session->userdata('u_id') ?>" name="u_id">
                      </ul>
                    </ul>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-fw fa-sign-out-alt"></i></a>
                </li>
              </ul>
            </div>
          </nav>
          <!--  -->
          <div id="menuFull" class="">
              <div id="menuFullInner" class="container-fluid" style="">
                  <span id="expandMenu">
                      <i class="fa fa-bars" aria-hidden="true"></i>
                  </span>
                  <div class="">
                      <ul class="" id="ulFlex">
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                          <a class="nav-link <?php if (strpos($uri_path, 'admin') == true && $uri_segments[3] == "" ) { echo 'active'; } else{ echo '';} ?>" href="<?php echo base_url() ?>admin">
                            <i class="fa fa-home"></i>
                            <span class="nav-link-text">Dashboard
                                <!--<div class="openTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                          <a class="nav-link <?php echo $uri_segments[3] == "deposit" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/deposit">
                            <i class="fa fa-credit-card"></i>
                            <span class="nav-link-text <?php echo $uri_segments[2] == "deposit" ?  'active' : '' ; ?>">Deposit
                                <!--<div class="openTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                          <a class="nav-link <?php echo $uri_segments[3] == "wallet" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/wallet"> 
                            <i class="fa fa-bank"></i>
                            <span class="nav-link-text">Wallet &nbsp; 
                                <!--<div class="openTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                            <a class="nav-link <?php echo $uri_segments[3] == "aBOT2" ?  'active' : '' ; ?>"  href="<?php echo base_url() ?>admin/aBOT2">
                            <i class="fa fa-line-chart"></i>
                            <span class="nav-link-text">aBOT &nbsp;
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                            <a class="nav-link">
                                <i class="fa fa-line-chart"></i>
                                <span class="nav-link-text dropdown">
                                    <span class="dropbtn">mBOT</span>
                                    <div id="dropdownContentID" class="dropdown-content">
                                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=USD"><i class="fas fa-dollar-sign"></i> USD</a>
                                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=USDT"><i class="fas fa-dollar-sign"></i> USDT</a>
                                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=BTC"><i class="fab fa-btc"></i> BTC</a>
                                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=ETH"><i class="fab fa-ethereum"></i> ETH</a>
                                        <a href="<?php echo base_url() ?>admin/mbot?&base_currency=USD-USDT"><i class="fas fa-dollar-sign"></i> USD-USDT</a>
                                    </div>
                                </span>
                            </a>
                        </li>     
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                          <a class="nav-link">  
                            <i class="fa fa-recycle"></i>
                            <span class="nav-link-text">Recycle &nbsp;
                                <div class="closedTabDiv"></div>
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                         <a class="nav-link <?php echo $uri_segments[3] == "vault" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/vault">
                            <i class="fa fa-history"></i>
                            <span class="nav-link-text">Vault &nbsp;
                                <!--<div class="openTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                		<a class="nav-link <?php echo $uri_segments[3] == "tradepro" ?  'active' : '' ; ?>" href="<?php echo base_url(); ?>admin/trading">
                            <i class="fa fa-line-chart"></i>
                            <span class="nav-link-text">Trade Pro &nbsp;
                                <!--i class="fa fa-lock" style="font-size:0.8em;"></i -->
                                <!--<div class="workTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                		<a class="nav-link <?php echo $uri_segments[3] == "exchange" ?  'active' : '' ; ?>" href="<?php echo base_url(); ?>admin/exchange">
                            <i class="fa fa-money"></i>
                            <span class="nav-link-text">Exchange &nbsp;
                                <!--i class="fa fa-lock" style="font-size:0.8em;"></i -->
                                <!--<div class="openTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                         <a class="nav-link <?php echo $uri_segments[3] == "announcement" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/announcement" onclick="annTabClick()">
                            <i class="fa fa-bullhorn"></i>
                            <span class="nav-link-text">Announcement &nbsp;<i class="badge badge-warning annBadgeClass"><?php echo $announcement; ?></i></span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                         <a class="nav-link <?php echo $uri_segments[3] == "auction" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/auction">
                            <i class="fa fa-gavel"></i>
                            <span class="nav-link-text">Auction &nbsp;<i class="badge badge-warning"><?php echo $auction_flag; ?></i>
                                <!--<div class="openTabDiv"></div>-->
                            </span>
                          </a>
                        </li>
                        <?php if( $this->session->userdata('original_session') == 0) {?>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                		  <a class="nav-link <?php echo $uri_segments[3] == "affiliate" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/affiliate">
                            <i class="fa fa-users"></i>
                            <span class="nav-link-text">Affiliate</span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                          <a class="nav-link <?php echo $uri_segments[2] == "user" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>user">
                            <i class="fa fa-cogs"></i>
                            <span class="nav-link-text">Account</span>
                          </a>
                        </li>
                        <?php }?>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                         <a class="nav-link <?php echo $uri_segments[3] == "support_new" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/support_new" onclick="readSupport()">
                            <i class="fa fa-history"></i>
                            <span class="nav-link-text">Support <i class="badge badge-warning" id="supportBadge"><?php echo $count; ?></i></span>
                          </a>
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right">
                         <a class="nav-link <?php echo $uri_segments[3] == "history" ?  'active' : '' ; ?>" href="<?php echo base_url() ?>admin/history">
                            <i class="fa fa-history"></i>
                            <span class="nav-link-text">History &nbsp;</span>
                          </a>
                        </li>
                      </ul>
                  </div>
              </div>
          </div><!-- top full menu -->
    </div> <!--div fixed top ends here -->

<div id="contentWrapperDiv" class="content-wrapper depositBtnExch">
    <div class="container-fluid">
        
        <?php echo $content; ?>
    </div>
</div>

    <footer id="stickyFooterDiv" class="sticky-footer">
      <div class="container">
        <div class="text-center">
          <small>Copyright © <a href="https://www.arbitraging.co" target="_blank">arbitraging.co</a> <?php echo date("Y");?></small>
        </div>
      </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="<?php echo base_url()?>logout">Logout </a>
          </div>
        </div>
      </div>
    </div>
  
  </div>
  
<!--  -->
<!--  -->


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-130805579-1"></script>
<script>

////////////////////////////////////            Announcement Notification Hit       ///////////////////////////////////////
function annTabClick(){
    $.get("<?php echo base_url();?>announcement_seen", function( data ) {});
    $(".annBadgeClass").css('display', 'none');
}

function getLoginAs(id) {
    
    var orignalUser = null;
    var accessUserTemp = null;
    
    if(<?php echo $this->session->userdata('original_session'); ?> == 0) {
        orignalUser = null;
        accessUserTemp = id;
    } else {
        orignalUser = id;
        accessUserTemp = null;
    }
    
    
    $.post( "<?php echo base_url(); ?>switch_session", {original_user_id:orignalUser,accessed_user_id:accessUserTemp})
    .done(function( data ) {
        data = JSON.parse(data);
        if(data.error == "1")
        {
            $('#ErrorModalGenericTemp').modal('show');
            $('#ErrorTextGenericTemp').html(data.msg);
        }
        else if(data.success == "1") {
            location.reload();
        }
    });
}

 window.dataLayer = window.dataLayer || [];
 function gtag(){dataLayer.push(arguments);}
 gtag('js', new Date());

 gtag('config', 'UA-130805579-1');
</script>    

<div class="modal fade" id="ErrorModalGenericTemp" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title text-danger">Error</h1>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h3 id="ErrorTextGenericTemp"></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--////////////////////////////////////////////////////     Loading Gif    //////////////////////////////////////////////-->   
    <div id='loadingmessage' style="position: fixed;top: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.81);display:none;text-align:center">
        <div style="margin-top: 22%;margin-left: 10%;">
            <div class="loader">
                
            </div>
            <!--<iframe src="https://giphy.com/embed/3oEjI6SIIHBdRxXI40" width="180" frameBorder="0" class="giphy-embed" allowFullScreen></iframe>-->
        </div>
    </div>  

<script type='text/javascript'>
    (function(){ var widget_id = 'TA1x4RgppO';var d=document;var w=window;function l(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id ; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);} if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);} else{w.addEventListener('load',l,false);}}})();
</script>
</body>

</html>
