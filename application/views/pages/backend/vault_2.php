<!-- MOBILE PHONE REDIRECT -->
    <script type="text/javascript">
    <!--
        if (screen.width <=699)
        {
            document.location = '<?php echo base_url(); ?>' + 'admin/vault_mobile';
        }
    //-->
    </script>
    <!-- Semantic UI -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/semantic/dist/semantic.min.css">
    
    <!-- Font Awesome -->
    <!--<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>-->
    <link href="https://fonts.googleapis.com/css-family=IBM+Plex+Sans+Condensed.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
    <script src="<?php echo base_url(); ?>assets/backend/vault/semantic/dist/semantic.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/backend/vault/js/arbvaultcom.js"></script>    
<script type="text/javascript">
	function getCookie(name) {
    		var dc = document.cookie;
    		var prefix = name + "=";
    		var begin = dc.indexOf("; " + prefix);
		if (begin == -1) {
        		begin = dc.indexOf(prefix);
        		if (begin != 0) return null;
    		}
    		else
    		{
        		begin += 2;
        		var end = document.cookie.indexOf(";", begin);
        		if (end == -1) {
	        		end = dc.length;
	        	}
    		}
		return decodeURI(dc.substring(begin + prefix.length, end));
	}
	var url_string = window.location.href;
	var url = new URL(url_string);
	var theCookie = "masternode=" + url.searchParams.get("masternode");
	if (url.searchParams.get("masternode") !== null) {
		var toSet = "masternode=" + url.searchParams.get("masternode");
		document.cookie=theCookie;
	} else {
		var refCookie = getCookie("masternode");
		if (refCookie === null) {
			console.log("Ref cookie was null. Setting to default.");
			document.cookie = "masternode=0x0000000000000000000000000000000000000000";
		} else {
			// do nothing if the cookie is already set and there is no new mnode link
		}
	}
    </script>
    <!-- Custom Styles -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/css/arbvaultmain.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/css/tkn.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/css/alertify.core.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/css/alertify.default.css" id="toggleCSS" />
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/css/styledHeaderDocReal.css">
    
<style type="text/css">
    #mainNav.navbar-dark .navbar-collapse .navbar-sidenav > .nav-item > .nav-link {
        font-size: 15px;
    }
    .firstRow{display: flex; background-color: transparent;}
    .thirdRow{display: flex; margin-left: 00px; margin-top:10px;}
    .headerBody
    {
        background-color: transparent;
        height: 1290px; 
        width: 100%; 
        margin-top: 40px !important;
    }
    .headerBody::-webkit-scrollbar {
       width: 0px;
    }   
    
    img{display: block; margin: 30 auto 0 auto;}
    .sticky-footer{
        bottom: auto !important;
    }
    .ui.four.wide.column.width_percentage {
        width: 33.33%;
        margin-left: 20px;
    }
    .price-box {
        text-align: center;
        min-height: 100px;
    }
    .price-box, #transaction-history-container, .login-box, .announcement-box {
        padding: 1em;
        background: #fff;
        border-radius: 0px;
        /* box-shadow: 0px 10px 10px 10px #0000001a; */
        -webkit-box-shadow: 0px 0px 2.5em -1.25em rgba(0,0,0,0.75);
        -moz-box-shadow: 0px 0px 2.5em -1.25em rgba(0,0,0,0.75);
        box-shadow: 0px 0px 2.5em -1.25em rgba(0,0,0,0.75);
    }
    .price-box{
        /*width: 415px;*/
        margin-top: 00px;
        margin-left: 00px;
        border-color: transparent;
        border-radius: 2px;
        height: 288px;
        background-size: 100% 100%;
        background-repeat-y: no-repeat;
    }
    /**/
    .third_row_box{
        background-color: transparent;
        display: flex;
        /* width: 100%; */
        height: 650px;
        margin-top: 30px;
    }
    .mybuysection.when-logged-in{
         width: 33.33%;
        /*width: 480px;*/
        /*height: 500px;*/
        /*background: transparent;*/
        padding-left: 20px;
    }
    .ethValueVault
    {
        margin-top: 120px; 
        font-size: 40px;
        line-height: 30px;
        color: black;
        font-family: Arial;
    }
    .buyAndSellSideBySide2nd{
        display: flex;
        margin-left: 00px;
        margin-top: 10px;
    }
    .two_slices{
        display: flex; 
        margin-left: 00px; 
        background-color: transparent;
        width: 430px;
        width: 33.33%;
        /*margin: 0 20px; */
        margin-left: 20px;
    }
    .two_slices .price-box{
        width: 95%;
        height: 595px;
        margin-top: 0px; 
        margin-left: 10px;
        border-radius: 2px;
        line-height: 12px;
    }
    
    .vault_buttons{
        display: flex;
        width: 250px; 
        height: 70px;
        margin-top: 10px;
        color: transparent;
        border-color: transparent;
        background-color:transparent;
        border: none;
        background-color: transparent;
        margin-top: 10px;
        text-align: center;
        justify-content: center;
        font-family: Arial;
        font-size: 30px;
    }
    #buy-tokens{
        background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/depositblack.png);
        background-color: transparent;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        height: 50px;
        margin:0;
    }
    #withdraw-some-btn{
        background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/withdrawblack.png);
        background-color: transparent;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        height: 50px;
    }
    
    .two_slices .ui.four.wide.column{ width: 100%;    margin: 0;}
    .two_slices .ui.four.wide.column:nth-of-type(2){margin-left: 20px;}
    
    .titleA{
        font-size: 34px;
        margin-top: 38%;
        line-height: 42px;
        color: gold;
    }
    .titleA span{display: block;}
    .titleABlack{color: #000;}
    .titleSubValue{margin-top:20px;color:#000 !important;}
    .price-box_marginTop{margin-top: 20px;}
    .rowTow_text{
        width: 100%;
        margin: 0;
        text-align: center;
        background-color: transparent;
        padding-top: 10%;
        font-size: 20px;
        color: white;
    }
    .rowTow_text p{
        max-width: 90%;
        margin: 0 auto;
        
    }
    .rowTow_text p:first-of-type{
        max-width: 60%;
        
    }
    .rowTow_text button{
        width: 360px;
        margin-left: 0px;
        background-color: black !important;
        color: gold;
        font-family: Arial;
        border: none;
        border: transparent;
        border-color: transparent;
        box-shadow: none;
        box-shadow: transparent;
        margin-top: 20px !important;
    }
    .loginBoxDiv
    {
        margin-top: 20px;
        line-height: 30px;
        font-size: 40px;
        color: black;
        font-family: Arial;   
    }
    .loginBoxDiv p
    {
        color: black;
        font-size: 25px;
        font-family: Arial;
        margin-bottom: 0px;
        padding: 20px;
    }
    .buttonparentparent{
        margin-top: 75px; 
        margin-left: 40px;
        background: transparent;
        width: 380px;
    }
    .buttonparentparent input{
            width: 250px; 
            background-color: grey;
            margin-top: 30px; 
            margin-bottom: 0px; 
            height: 50px;
            text-align: center; 
            color: black; 
            font-size: 16px;
    }
    .buttonparentparent input::placeholder {
       color:#fff;
    }
    
    .buttonparentparent input:-ms-input-placeholder { /* Internet Explorer 10-11 */
       color:#fff;
    }
    
    .buttonparentparent input::-ms-input-placeholder { /* Microsoft Edge */
       color:#fff;
    }
    #buy-panel{background-color: transparent;}
    #unlock-wallet-container
    {
        height: 180px;
        background-color: #c79d2d;
        display: block;
        z-index: 4;
        padding: 5px;
        border-radius: 5px;
    }
    
    
    
    @media screen and (max-width: 3100px) {
        .titleA {
            margin-top: 16%;
        }   
        .buttonparentparent {
            margin-left: 22%;
        }
        .two_slices .price-box {
            width: 95%;
        }    
    }
    @media screen and (max-width: 2400px) {
        .titleA {
            margin-top: 24%;
        }   
        .buttonparentparent {
            margin-left: 22%;
        }
        .two_slices .price-box {
            width: 95%;
        }    
    }
    @media screen and (max-width: 2000px) {
        .titleA {
            margin-top: 28%;
        } 
    }
    @media screen and (max-width: 1800px) {
        .titleA {
            margin-top: 35%;
        } 
    }
    @media screen and (max-width: 1600px)
    {
        .titleA {
            margin-top: 39%;
        }   
        .buttonparentparent {
            margin-left: 0%;
        }
    }
    @media screen and (max-width: 1400px) {
        .price-box{
            height:240px;
        }
        .loginBoxDiv
        {
            font-size: 30px;
            margin-top: 0px;
        }
        .loginBoxDiv p
        {
            font-size: 20px;
            padding: 10px;
        }
        .rowTow_text {
            padding-top: 3%;
            font-size: 18px;
        }
        .rowTow_text p {
            max-width: 70%;
        }
        .titleA {
        font-size: 28px;
        margin-top: 40%;
        line-height: 32px;
        }
        .rowTow_text button{width: 280px;}
        .rowTow_text p:first-of-type {
            max-width: 70%;
        }
        .two_slices .price-box {
            width: auto;
            max-height: 500px;
        }
        .two_slices .ui.four.wide.column:nth-of-type(2) {
            margin-left: 0;
        }
        .buttonparentparent {
            margin-top: 40px;
            margin-left: -10px;
            background: transparent;
            width: 330px;
        }
        
        .buttonparentparent input{
            width: 250px;
            background-color: grey;
            margin-top: 30px;
            margin-bottom: 0px;
            height: 40px;
            text-align: center;
            color: white;
            font-size: 16px;
        }
        .buttonparentparent input:placeholder{
            color:#fff;
        }
        #buy-tokens {
            background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/depositblack.png);
            background-color: transparent;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            height: 40px;
            margin: 0;
        }
        #withdraw-some-btn{
            background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/withdrawblack.png);
            background-color: transparent;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            height: 40px;
        }
        .headerBody
        {
            height:auto;
        }
        #token-sale {
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .ethValueVault {
            margin-top:95px;
        }
    }    
    @media screen and (max-width: 1280px) {
        .price-box {
            height: 215px;background-size: 100% 100%;
        }
        .rowTow_text {
            padding-top: 0%;
        }
        .rowTow_text p {
            max-width: 75%;
        }
        .headerBody {
            margin-top: 80px !important;
        }
        .buttonparentparent {
            margin-top: 30px;
            margin-left: -30px;
        }
        .two_slices .price-box {
            width: auto;
            max-height: 450px;
        }
        .ethValueVault {
            margin-top:80px;
        }
    }
    @media screen and (max-width: 1199px) {
        .titleA {
            font-size: 22px;
            margin-top: 40%;
            line-height: 30px;
        }
        .titleSubValue{margin-top:5px;}
        .rowTow_text p:first-of-type {
            max-width: 100%;
        }
        .rowTow_text p {
            max-width: 100%;
        }
        .loginBoxDiv {
            font-size: 24px;
            margin-top: 0px;
        }
        .loginBoxDiv p {
            font-size: 16px;
            padding: 5px;
        }
        .rowTow_text button{width: 100% !important; margin-top: 10px !important;}
        .price-box {
            height: auto;background-size: 100% 100%;    min-height: 210px;
        }
        .buttonparentparent input {
            width: 200px;
        }   
        .buttonparentparent {
            margin-left: -16%;
        }
        #withdraw-some-btn {
            width: 220px;
        }
    }
    @media screen and (max-width: 1024px){
        .titleA {
            margin-top: 55%;
        }
        .buttonparentparent {
            margin-left: -50%;
            margin-top: 20px;
        }
        .buttonparentparent input {
            height: 30px;
        }
        #buy-tokens
        {
            width:220px;
            margin-left: 30px;
        }
    }
    @media screen and (max-width: 992px) {
        .firstRow{display: flow-root; }
        .thirdRow{display: flow-root; }
        .ui.four.wide.column.width_percentage {
            width: 48%;
            margin-left: 10px;
            float: left;
        }
        .price-box {
            min-height: 220px;
        }
        .mybuysection.when-logged-in{width: 100%;}
        .two_slices{display: none;}
        .titleA {
            margin-top: 38%;
        }
        .buttonparentparent {
            margin-left: 1%;
            margin-top: 15px;
        }
        .buttonparentparent input {
            height: 40px;
        }
        .div_hide992
        {
            display:none;
        }
    }    
    /*@media screen and (max-width: 767px) {*/
        
    /*    .ui.four.wide.column.width_percentage {*/
    /*        width: 100%;*/
    /*        margin: 20px 10px;*/
    /*        float: left;*/
    /*    }*/
    /*    .titleA {*/
    /*        font-size: 28px;*/
    /*        margin-top: 30%;*/
    /*        line-height: 30px;*/
    /*        margin-bottom: 5%;*/
    /*    }*/
    /*}*/
    
    
    /*@media screen and (max-width: 680px) {*/
    /*    .third_row_box{display: block;}*/
    /*    .buttonparentparent{width: 100%;    margin-left: auto;margin-top: 80px;}*/
    /*    #buy-tokens{*/
    /*     margin: 0 auto;*/
    /*    width: 250px;}*/
    /*    }*/
</style>
</head>

<!--<body class="lang_us" style="background-color: transparent; width: 1480px; margin-top: 20px; margin-left: auto; margin-bottom: 20px; margin-right: auto;">-->

<!-- THIS IS THE DIV BLOCK THAT HOLDS THE ENTIRE CONTRACT -->
<header id="styledHeaderA" class="headerBody"> 

<!-- THIS IS THE DIV BLOCK INSIDE THE DIV THAT HOLDS THE ENTIRE CONTRACT -->
<div id="token-sale" style="background-color: transparent;height: 1220px;border:none;">
    <div class="movePlease" style="background-color:transparent;">
<!-- THIS IS THE CONTAINER FOR THE CONTRACT INFO -  SECTION -->
<!-- THIS OPENS THE ui ELEVEN wide column interface  DIV - THE BIG CONTRACT PAGE - usually YELLOW  -->
        <div class="ui eleven wide column" id="value-panel" style="background-color: transparent;border: none; border-radius: 33px; 
        height: 1200px;">
<!-- THIS OPENS THE ui eight wide column interface logged-out DIV -->
<div class="ui eight wide column interface logged-out" id="meta-mask-ui">
<div style="background-color: transparent;">
    
</div>
<!-- ******************************************************************************************************* -->
<!-- ******************************************************************************************************* -->
<!-- THIS OPENS THE UI STACKABLE RELAXED GRID DIV --> <!-- THIS OPENS THE UI STACKABLE RELAXED GRID DIV -->
    <div class="lexxCustomUi" style="border-color: white; border: none; background-color: transparent;"> 
    	<!-- THIS OPENS THE UI STACKABLE RELAXED GRID DIV -->
         <!-- THIS IS THE TOP ROW OF THREE BOXES -->
 		<div class="buyAndSellSideBySide firstRow" style="">           
			<div class="ui four wide column width_percentage">
                <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/a.png);">
                    <div class="titleA">
                        <span>ARB CONTRACT</span>
                        <span>COMING SOON</span>
                    </div> 
                </div>
            </div>
            <div class="ui four wide column width_percentage div_hide992">
                <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/b.png);">
                    <div class="contract-tokens-usd value-usd" style="margin-top: 20px;"></div>
                </div>
            </div>
            <div class="ui four wide column width_percentage" style="">
                <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/c.png);">
                <div class="contract-balance titleA titleABlack"></div>
                <div class="contract-balance-usd value-usd titleSubValue">BALANCE USD</div>
                </div>
            </div>
		</div> 
<!-- THIS IS THE "NETWORK TRAFFIC WARNING" TEXT-->
<div class="ui ten wide column traffic-message" style="text-align: center; margin: 0 auto; position: relative;">
	<i class="fa fa-exclamation-circle"></i>Depending on the Ethereum network traffic, figures may be delayed.
	<a target="_blank" href="https://etherscan.io/address/0x5eee354e36ac51e9d3f7283005cab0c55f423b23" style="position: absolute;display: inline-block;right: 11%;top: 0;">View On Etherscan</a>
</div>
<!-- THIS IS THE SECOND ROW OF THREE BOXES -->
 <div class="buyAndSellSideBySide thirdRow" style="">         
	<div class="ui four wide column width_percentage when-logged-out">
        <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/dout.png);">
	        <div class="ui four wide column when-logged-out rowTow_text">
		        <p>Generates and displays a wallet seed.</p>
		        <p>The supplied password is used to encrypt the wallet and store it securely. </p>
		         <!--<button id="generate-wallet" class="ui button large primary when-logged-out">Generate Wallet-->
		         <!--</button>-->
		        </div>
	    	</div>
	    </div>
	    <div class="ui four wide column width_percentage when-logged-in">
            <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/d.png);">

            </div>
	    </div>
	  	<div class="ui four wide column width_percentage when-logged-in" style="">
		    <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/e.png);">
		        <div class="user-hold-balance ethValueVault"></div>
		    </div>
		</div>      
    <!-- THIS IS THE METAMASK DETECTIONSECTION -->
	        <div class="ui four wide column width_percentage when-logged-out">
	            <div class="price-box loginBoxDiv" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/arbgoldenlogob.png);">                   
    	            <div>PLEASE LOG IN </div>
                    <div>
                        <div>
                            <p>To retrieve your balances log into Metamask UI or use our in-browser wallet options.</p>
                        </div>
                        <div class="myWork when-logged-out" style="margin-left: 0px; display: flex; height: 10px;">
                            <div class="ui column when-logged-out" id="unlock-wallet-container">
                            	<button id="unlock-wallet" class="ui button large secondary" style="font-family: Arial;">Unlock Wallet                                            </button>
                            	<p>Unlocks the wallet currently encrypted and stored in this browser.</p>
                            </div>
                        </div>
                    </div>
	            </div>
	        </div>

	            <!-- THE WHEN LOGGED IN MODIFFIER AFFECTS THE USER IF METAMASK IS DETECTED/NOT -->
	        <div class="ui four wide column width_percentage when-logged-in div_hide992">
		        <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/f.png);">

		        </div>
	        </div>
	        <div class="ui four wide column width_percentage when-logged-out">
	            <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/fout.png);">
	                <div class="ui column when-logged-out rowTow_text">
	                	<p>
	                		Restores a wallet from the supplied seed.
	                	</p>
	                	<p>
	                    	The supplied password is used to encrypt the wallet and store it securely.
	                	</p>
						<!--<button id="recover-wallet" class="ui button large primary when-logged-out">Restore Wallet</button> -->
	                </div>
	            </div>    
	        </div>                
        </div>        
    </div>
</div> <!-- THIS CLOSES THE LOGGED OUT INTERFACE SECTION FROM LINE 127 -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->


<!-- THIS IS THE THIRD ROW OF BOXES -->
<!-- THIS IS THE THIRD ROW OF BOXES -->
<!-- THIS IS THE THIRD ROW OF BOXES -->
<div class="third_row_box">
<!-- THIS COLUMN HOLDS THE ARB DEPOSIT AND WITHDRAWALS -->
    <div class="mybuysection when-logged-in">
        <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/h.png);">    <!-- buyAndSellSideBySide  -->
        
        </div>
        <div class=" price-box price-box_marginTop" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/g.png);">  <!-- buyAndSellSideBySide -->
        
        </div>
    </div>
    <!-- THIS COLUMN HOLDS THE TWO SLICES IN THE MIDDLE  -->
    <div class="two_slices">
        <div class="ui four wide column when-logged-in">
            <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/i.png);">
            </div>
        </div>
        <div class="ui four wide column when-logged-in " style="">
            <div class="price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/j.png);">
            </div>
        </div>
    </div>
    <!-- THIS IS THE END OF THE TWO SLICES IN THE MIDDLE -->

    <!-- THIS COLUMN HOLDS THE ETH DEPOSIT AND WITHDRAWALS -->
    <div class="mybuysection when-logged-in" style="height: 600px; background-color: transparent;">
        <div class="buyAndSellSideBySide price-box" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/k.png);">
            <div class="ui six wide column center aligned when-logged-in buttonparentparent" id="buy-panel">
                <input type="number" id="purchase-amount" min="0" step="0.01" class="  input-amountA" placeholder="Amount of ETH (e.g. 0.7)" style="">
                <div class=" " id="address-balance" style="margin-top: 10px;font-family: Arial;">
                    Balance: 
                    <span class="address-balance" style="margin-top: 10px;margin-bottom: 0px;color: black;text-align: center;font-family: Arial;"></span>
                    <br/>
                </div>
                <button id="buy-tokens" class="ui button large primary when-logged-out vault_buttons"></button>
            </div>
        </div>
        <!--  THIS IS THE WITHDRAW FUNCTION - WITHDRAWS THE USERS EARNINGS - AN AMOUNT MUST BE SPECIFIED -->
        <div class="buyAndSellSideBySide price-box price-box_marginTop" style="background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/l.png);">
            <div class="ui six wide column center aligned when-logged-in buttonparentparent" id="buy-panel">
                <input type="number" id="withdraw-eth-amount" min="0" step="0.001" class="input-amountA" placeholder="Amount of ETH (e.g. 0.7)" style="">
                <div class=" " id="address-balance" style="margin-top: 10px;font-family: Arial;">
                    Holdings: 
                    <span class="user-hold-balance" style="margin-top: 10px;margin-bottom: 0px;color: black;text-align: center;font-family: Arial;"></span>
                    <br/>
                </div>
                <button id="withdraw-some-btn"  class="ui button large primary when-logged-out vault_buttons"></button>
            </div>
        </div>
    </div>
    <div class="ui sixteen wide column">
        <div id="transaction-history-container" style="display: none">
            <h2>Transaction History</h2>
            <div id="transaction-history">
            </div>
        </div>
    </div>
</div> 
<!-- THIS CLOSES THE UI STACKABLE RELAXED GRID DIV -->
        <!-- THIS CLOSES THE UI STACKABLE RELAXED GRID DIV --> 
<!-- ******************************************************************************************************* -->
</div>
</div> <!-- THIS CLOSES THE ui ELEVEN wide column interface  DIV -->
<!-- THIS CLOSES THE ui ELEVEN wide column interface  DIV -->

    <div id="metamask-not-found" class="ui dimmer">
        <div class="inner">
            <h2 class="float-left">MetaMask Not Found</h2><br>
            <p>To interact with the network, you must have <a href="https://metamask.io/">Metamask</a> installed and setup.</p>
        </div>
    </div>
    <div id="metamask-detecting" class="ui dimmer" style="width:1440px;height: 1450px; ">
        <div class="inner">
            <h2 class="float-left">Detecting MetaMask</h2><br>
            <p>Please wait while we ATTEMPT to load MetaMask</p>
        </div>
    </div>
    <div id="seed-dimmer" class="ui dimmer">
        <div class="inner">
            <h2 class="float-left">Wallet Seed</h2><br>
            <p><strong>WARNING</strong>
                This is your wallet's seed. If you lose this, you lose access to your ETH and any ARB along with it. This is only ever stored locally in your web browser. If you clear your browser data, generate a new wallet over an existing, or your computer dies, and you don't have this saved anywhere, nobody can recover this for you. Seriously, save it somewhere safe.</p>
            <div id="wallet-seed">
            </div>
            <button class="ui button huge primary" id="close-seed">I Have Stored My Seed Somewhere Safe </button>
        </div>
    </div>
<!-- THIS IS THE WALLET MANAGEMENT AND DIMMER  SECTION -->                
                <div id="wallet-dimmer" class="ui dimmer">
                    <div class="inner">
                        <h2>Wallet Management</h2>
                        <h4>Balance: <span class="address-balance"></span></h4>
                        <hr/>
                        <div class="ui equal width stackable grid">
                            <div class="ui column">
                                <h3>Send</h3>
                                <p>Send ETH to another address.</p>
                                <div class="center aligned actions">
                                    <input type="text" id="send-address" class="input-amount" placeholder="Destination address"/>
                                    <input type="number" id="send-amount" min="0" step="0.1" class="input-amount"
                                           placeholder="Amount in ETH (e.g. 0.5)"/>
                                    <button id="send-action" class="ui primary huge button">Send ETH</button>
                                </div>
                            </div>
                            <div class="ui column">
                                <h3>Receive</h3>
                                <p>
                                    To deposit ETH into this wallet, send ETH to your public address:                                </p>
                                <p id="eth-public-address">
                                  <a href="#" class="etherscan-link" target="_blank"></a> <a href="#" id="copy-eth-address"><i class="fas fa-copy"></i></a>
                                </p>
                                <h3>Actions</h3>
                                <p>
                                    <a id="export-seed" href="#" class="ui button small">Export Seed</a>
                                    <a id="export-private-key" href="#" class="ui button small">Export Private Key</a>
                                    <a id="delete-wallet" href="#" class="ui button small">Delete Wallet</a>
                                </p>
                                <textarea id="exported-seed"></textarea>
                                <input type="text" id="exported-private-key">
                            </div>
                        </div>
                        <div class="ui center aligned" style="margin-top: 5em">
                            <a href="#" id="wallet-close" class="ui button huge secondary">Close</a>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
<!-- THIS IS THE START OF THE TRANSACTION CONFIRMATION SECTION -->
<div id="tx-confirmation" class="ui modal">
    <div class="header">
        Transaction Submitted    
    </div>
    <div class="content">
        <p>
             <span id="tx-hash">Transaction successfully submitted to network. Transaction hash:</span>
        </p>
    </div>
</div>
<!-- THIS IS THE END OF THE TRANSACTION CONFIRMATION SECTION -->
<!-- THIS IS THE PASSWORD PROMPT SECTION --  --   START -->
<div id="password-prompt" class="ui modal">
    <div class="header"> Enter your wallet password </div>
    <div class="ui content form">
        <div>
            <input type="password" id="password"/>
        </div>
        <div style="padding: 1em;">
            <button id="confirm-tx" class="ui button primary" style="float: right;">Confirm</button>
            <button id="cancel-tx" class="ui button">Cancel</button>
        </div>
    </div>
</div>
<!-- THIS IS THE PASSWORD PROMPT SECTION -- -   END -->
<script type="text/javascript">
  var default_currency = 'USD'
  $('#metamask-detecting').dimmer({closable: false})
  $('#metamask-not-found').dimmer({closable: false})
  $('#donate-dimmer').dimmer({closable: false})
  $('#seed-dimmer').dimmer({closable: false})
  $('#wallet-dimmer').dimmer({closable: false})
  $('#metamask-detecting').dimmer('show')
</script>
<script type="text/javascript">
  var lang = 
  {
    fund: "{1} ARB bought for {0} ETH",
    reinvest: "{0} ARB bought using dividends",
    withdraw: "Dividends withdrawn",
    sold: "{0} ARB sold",
    walletGenConfirmation : "We've detected an existing wallet, are you sure you want to generate a new one?",
    enterPassword : "Enter password for encryption",
    incorrectPassword : "Incorrect password supplied",
    enterSeed : "Enter your wallet seed",
    seedInvalid : "Supplied seed is invalid",
    deleteWalletConfirmation : "Are you sure you want to delete this wallet? Make sure you have a backup of your seed or private key.",
    copiedToClip : "Copied to clipboard",
    invalidInput : "Invalid Input",
    invalidInputResponse : "Please input a valid non-negative, non-zero value."
  }
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/web3.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/lightwallet.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/alertify.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/contractbrb-final.js"></script>
<!--
<script type="text/javascript" src="js/contractbrb-arbtokensmart.js"></script>
-->
</header>
<!-- THIS IS THE END OF THE HEADER - SECTION -->
<!--</body>-->
<!-- THIS IS THE END OF THE BODY - SECTION -->
<script>'undefined'=== typeof _trfq || (window._trfq = []);'undefined'=== typeof _trfd && (window._trfd=[]),_trfd.push({'tccl.baseHost':'secureserver.net'}),_trfd.push({'ap':'cpbh'},{'server':'a2plvcpnl45424'}) // Monitoring performance to make your website faster. If you want to opt-out, please contact web hosting support.</script><script src='https://img1.wsimg.com/tcc/tcc_l.combined.1.0.6.min.js'></script>
</html>