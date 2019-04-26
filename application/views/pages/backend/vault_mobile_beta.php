<!--<!DOCTYPE html>-->
<!--<html lang="en" style="width: 360px; margin: 0 auto 0 auto;">-->

<!--<head>-->

<!--    <meta charset="utf-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>-->
<!--    <meta property="og:title" content="ARBvault">-->
<!--    <meta property="og:image" content="http://poshdevs.surge.sh/images/vectorlogo.png">-->
<!--    <meta property="og:description" content="Passive Income through Smart-Contract Arbitrage.">-->
<!--    <meta property="og:url" content="https://arbitraging.co">-->

    <!-- THIS IS THE WINDOWS etc TAB DETAIL - tiny thumbnail and tab name -->
<!--    <link rel="shortcut icon" href="images/arbgoldenlogo.png">-->
<!--    <title>VAULT</title>-->

    

    <!-- Font Awesome -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <link href="https://fonts.googleapis.com/css-family=IBM+Plex+Sans+Condensed.css" rel="stylesheet">


    <!-- Semantic UI -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/backend/vault/semantic/dist/semantic.min.css">
    <script
            src="https://code.jquery.com/jquery-3.1.1.min.js"
            integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
            crossorigin="anonymous"></script>
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
    img{display: block; margin: 30 auto 0 auto;}
    .sticky-footer
    {
        bottom: auto !important;
    }
    #buy-ArbTokens {
        background-color: transparent;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        height: 50px;
        margin:0;
        border: 2px solid;
        border-radius: 5px;
        margin-top: 20px;
        width: 100%;
    }
    #withdraw-ArbTokens
    {
        background-color: transparent;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        height: 50px;
        border: 2px solid;
        border-radius: 5px;
        margin-top: 20px;
        width: 100%;
    }
    #totalArbUSD
    {
        font-size: 14px;margin-top: 15px;
    }
    .balancesClass
    {
        margin-top: 20px;margin-bottom: 3px;text-align: center;font-family: Arial;font-size:12px;color:#fff;
    }
    .totalActiveArbClass {
        margin-top: 35px;
        color: #fff;
        font-size: 20px;
        text-align: center;
    }
    #userActiveArbHoldings{text-align: center;margin-top: 10px;}
    .totalActiveArbClass label {margin-bottom:0;}
</style>
 </head>

<!--<body class="lang_us" style="width: 360px;">-->

<header id="styledHeaderMobileCentre" style="display: inline-block; margin: 40 auto;"> <!-- THIS IS A REF TO STYLEDHEADERDOCREAL -->
<!--
<header id="styledHeaderA"; style="width:1600px; margin-bottom: 0px; background-color: white;background-position: 100px; 
background-size: cover; margin-left: 20px;--> <!-- THIS IS A REF TO STYLEDHEADERDOCREAL -->


<!-- THIS IS THE END OF MY DIV CLASS "SPEAK AND SPELL ICON LINKS" -->


<!-- THIS IS THE DIV BLOCK THAT HOLDS THE CONTRACT -->
<div id="token-sale" style="background-color: transparent;  width: 360px;border:none;">




    <div class="movePlease" style="background-color: transparent; width: 360px;">

<!-- THIS IS THE CONTAINER FOR THE CONTRACT INFO -  SECTION -->


<!-- THIS OPENS THE ui ELEVEN wide column interface  DIV -- THE BIG CONTRACT PAGE -- usually YELLOW  -->
        <div class="ui eleven wide column" id="value-panel"style="background-color: transparent;border: none; border-radius: 33px;width: 360px; margin-top: 00px; height: 1200px; margin: 0 auto;">




<!-- THIS OPENS THE ui eight wide column interface logged-out DIV -->
<div class="ui eight wide column interface logged-out" id="meta-mask-ui">
<div style="background-color: transparent; height: 20px; width:360px;"></div>

<!-- ******************************************************************************************************* -->
<!-- ******************************************************************************************************* -->
<!-- THIS OPENS THE UI STACKABLE RELAXED GRID DIV --> <!-- THIS OPENS THE UI STACKABLE RELAXED GRID DIV -->
    <div class="lexxCustomUi" style="border-color: white; border: none;"> <!-- THIS OPENS THE UI STACKABLE RELAXED GRID DIV -->



          <div class="ui four wide column" style="display: block; margin: 0 auto;">
                <div 
                class="price-box" 
                style="
                width: 360px;
                height: 320px;
                background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobilea.png);
                margin-top: 0px; 
                margin-left: 00px;
                border: none;
                border-radius: 2px;
                text-align: center;
                line-height: 12px;">

                    <div class="contract-tokens-usd value-usd" style="margin-top: 20px;"></div>
                </div>
            </div>

                <div class="ui ten wide column traffic-message" style="text-align: center; margin-left: 00px; width: 360px;">
                <i class="fas fa-exclamation-circle"></i>
                                Depending on the Ethereum network traffic, figures may be delayed.
                </div>
            <div class="ui four wide column" style="">
                <div class="price-box" 
                style="
                width: 360px;
                height: 320px;
                background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobileb.png);
                margin-top: 0px; 
                margin-left: 0px;
                border-radius: 2px;
                line-height: 12px;
                background-size: 100% 100%">

                <div class="titleA" style="font-size: 33px; margin-top: 170px; margin-left: 0px; color: grey;font-family: Arial;"></div>
                <div class="contract-balance" style="font-size: 34px; margin-top: 80px; line-height: 42px;font-family: Arial;"></div>
            <!--    <div class="contract-tokens-usd value-usd" style="margin-top: 20px;font-family: Arial;">BALANCE USD</div> -->
                <div class="contract-balance-usd value-usd" style="margin-top: 20px;">BALANCE USD</div>
                </div>
            </div>
            <div class="ui ten wide column traffic-message" style="text-align: center; margin-left: 00px; width: 360px;">
                <a target="_blank" href="https://etherscan.io/address/0x5eee354e36ac51e9d3f7283005cab0c55f423b23">View On Etherscan</a>
            </div>

<div class="ui four wide column when-logged-in" style="">
    <div class="row price-box" style="width: 360px;height: 240px;background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/e.png);margin-top: 20px;margin-left: 00px;border: none;border-radius: 2px;line-height: 12px; background-size: 100%;background-repeat-y: no-repeat;">
        <div id="arbValMetaMask" class="col-md-6 value" style="font-size: 34px; margin-top: 115px; line-height: 30px; font-size: 40px;color: #100c00;font-family: Arial;"></div>
        <div class="col-md-6 user-hold-balance" style="font-size: 34px; margin-top: 115px; line-height: 30px; font-size: 40px;color: #100c00;font-family: Arial;font-weight: 700;"></div>
        <div class="col-md-6"> 
		            <span style="font-size: 20px;">ARB</span>
		        </div>
		        <div class="col-md-6">
		            <span style="font-size: 20px;">ETH</span>
		        </div>
    </div>
</div>

         <!-- THIS IS THE TOP ROW OF THREE BOXES -->
<div class="buyAndSellSideBySide" style="display: flex; margin-left: 00px; margin-top: 20px; background-color: transparent; width: 360px;">           
            <div class="ui four wide column">
                <div class="price-box" 
                style="
                width: 360px; 
                margin-top: 00px; 
                margin-left: 00px;
                border-color: transparent; 
                border-radius: 2px; 
              
              
                background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobileh.png);
                height: 320px; 
                line-height: 12px;">

                    <div class="titleA" style="font-size: 33px; margin-top: 160px; color: gold; font-family: Arial;"> </div>
<!--
                    <div class="contract-arbtokens" style="font-size: 34px; margin-top: 60px; line-height: 42px;color: gold;">
                    <span style="line-height: 20px;color: gold;" ></span></div> 

                    <div class="contract-arbethbalance" style="font-size: 34px; margin-top: 1px; line-height: 42px;color: gold;">
                    <span style="line-height: 20px;color: gold;" ></span></div>
                    <div class="contract-tokens-usd value-usd" style="margin-top: 00px;font-family: Arial;">BALANCE USD</div>
-->

                    

                    <div class="titleA" style="font-size: 34px; margin-top: 80px; line-height: 42px;color: gold;">
                        <span id="totalArbInVault" style="line-height: 20px;color: gold;"></span>
                    </div> 
                    <div class="titleA" style="font-size: 15px; margin-top: 1px; line-height: 42px;color: gold;">
                        <span id="totalArbUSD" style="line-height: 20px;color: gold;"></span>
                    </div>
                    <!--
                    <div class="poh-arbbalance" style="margin-top: 00px;font-family: Arial;">bal</div>

                    <div class="contract-arbname-text" style="margin-top: 0px;font-family: Arial;color: "></div>-->
                </div>
            </div>
</div>
            <!-- THIS IS THE METAMASK DETECTIONSECTION -->
<div class="ui four wide column when-logged-out" style="">
                <div 
                class="price-box" 
                style="
                width: 360px;
                height: 320px;
                background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobilecout.png);
                margin-top: 10px; 
                margin-left: 00px;
                border: none;
                border-radius: 2px;
                 
                line-height: 12px;">

                    
                    <div style="font-size: 34px; margin-top: 20px; line-height: 30px; font-size: 40px;color: black;font-family: Arial;">PLEASE LOG IN </div>
                    <div>
                        <div style="margin-top: 30px; background-color: transparent;">
                                        <p style="color: black;font-size: 30px;font-family: Arial;margin-bottom: 0px;">To retrieve your ETH balances log into Metamask.</p>
                                        <!--
                                        <p style="color: blue;margin-top: 20px;"><strong style="color: red">WARNING</strong> this feature is in BETA, use at your own risk.</p>
                                    -->
                                    </div>

                                    <div class="myWork when-logged-out" style="margin-left: 0px; display: flex; height: 10px;">



                                        <div class="ui column when-logged-out" id="unlock-wallet-container" style="height: 100px; background-color: pink">
                                            <button id="unlock-wallet" class="ui button large secondary" style="font-family: Arial;">Unlock Wallet                                            </button>
                                            <p>
                                                Unlocks the wallet currently encrypted and stored in this browser.</p>
                                        </div>



                                    </div>
                </div>

                </div>
</div>

<div class="ui four wide column when-logged-out">
            <div class="price-box" 
                style="
                width: 360px; 
                margin-top: 10px; 
                margin-left: 00px;
                border-color: transparent; 
                border-radius: 2px;
                background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobilefout.png);
                height: 320px; 
                line-height: 12px;">
                
                <div class="ui four wide column when-logged-out" style="width: 260px; margin-left: 10px; text-align: center; background-color: transparent;">

                <!--<p style="margin-left: 40px;font-size: 20px; color: white;">Generates and displays a wallet seed. </p>-->
                <!--<p style="margin-top: 0px;margin-left: 30px;font-size: 20px; width: 250px;font-family: Arial; color: white;">-->
                <!--                            The supplied password is used to encrypt the wallet and store it securely.</p>-->
                <!--<button id="generate-wallet" class="ui button large primary when-logged-out "style="width: 250px; margin-left: 30px; color: black;font-family: Arial;">Generate Wallet</button>-->
                </div>
            </div>
</div>

<div class="ui four wide column when-logged-out" style="">

                <div 
                class="price-box" 
                style="
                width: 360px;
                height: 320px;
                background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobilegout.png);
                margin-top: 20px; 
                margin-left: 00px;
                border: none;
                border-radius: 2px;
                line-height: 12px;">
                                        <div class="ui column when-logged-out" style="margin-left: 10px; width: 260px;font-family: Arial; height: 200px; background-color: transparent;">
                                      
                                        <!--    <p style="width: 260px; margin-left: 30px;font-size: 20px;font-family: Arial;">-->
                                        <!--        Restores a wallet from the supplied seed. -->
                                        <!--    </p>-->
                                        <!--    <p style="width: 260px; margin-left: 30px;font-size: 20px; margin-top: 45px;font-family: Arial;">-->
                                        <!--    The supplied password is used to encrypt the wallet and store it securely.                                           </p>-->
                                        <!--      <button id="recover-wallet" class="ui button large primary when-logged-out" style="background-color: black;width: 250px; color: gold;margin-left: 30px;">Restore Wallet                                -->
                                        <!--</button>-->
                                        </div>
                </div>
</div>



<div class="ui four wide column when-logged-in" style="">
<div class="ui four wide column when-logged-in" 
    style="display: flex; 
    margin-left: 0px; 
    margin-top: 25px;
    width: 360px;
    height: 320px;
    background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobiled.png);
    line-height: 10px;">

<div class="ui six wide column center aligned  when-logged-in" id="buy-panel" style="margin-top: 110px; margin-left: 5px;
                    background: transparent;width: 360px;">

<input type="number" id="purchase-amount" min="0" step="0.01" class="  input-amountA" placeholder="Amount of ETH (e.g. 0.7)" style="width: 250px; background-color: grey;margin-top: 30px; margin-bottom: 0px; height: 50px;text-align: center; color: black; font-size: 21px;">

                    <div class=" " id="address-balance" style="margin-top: 10px;font-family: Arial;">
                    Balance: <span class="address-balance" style="margin-top: 10px;margin-bottom: 0px;color: black;text-align: center;font-family: Arial;"></span>
                    <br/>

                    </div>
                    <button id="buy-tokens" class="ui button large primary when-logged-in" 
                    style="
                    display: flex;
                    width: 250px; 
                    height: 70px;
                    margin-top: 10px;
                    color: none;
                    border: none;
                    box-shadow: none;
                    background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/depositblack.png);
                    border-color: transparent;
                    background-color:transparent;
                    margin-top: 10px;
                    text-align: center;
                    justify-content: center;
                    font-family: Arial;
                    font-size: 30px;
                    "></button>
    </div>
</div>
</div>



<!--  THIS IS THE WITHDRAW FUNCTION - WITHDRAWS THE USERS EARNINGS - AN AMOUNT MUST BE SPECIFIED -->
<div class="ui four wide column when-logged-in" style="">
<div class="buyAndSellSideBySide" 
    style="display: flex; 
    margin-left: 0px; 
    margin-top: 20px;
    width: 360px;
    height: 320px;
    background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobilee.png);
    line-height: 10px;">

    <div class="ui six wide column center aligned when-logged-in" id="buy-panel" style="margin-top: 110px; margin-left: 0px;
                    background: transparent;width: 360px;">

                    <input type="number" id="withdraw-eth-amount" min="0" step="0.01" class="input-amountA" placeholder="Amount of ETH (e.g. 0.7)" style="width: 250px; background-color: grey;margin-top: 30px; margin-bottom: 0px; height: 50px;text-align: center; color: black; font-size: 21px;">

                    <div class=" " id="address-balance" style="margin-top: 10px;font-family: Arial;">
                    Holdings: <span class="user-hold-balance" style="margin-top: 10px;margin-bottom: 0px;color: black;text-align: center;font-family: Arial;"></span>
                    <br/>

                    </div>
                 <!--   <div class="user-hold-old-usd" style="margin-top: 10px;">BALANCE USD</div> -->
                    <button id="withdraw-some-btn"  class="ui button large primary when-logged-out"
                    style="
                    display: flex;
                    width: 250px; 
                    height: 70px;
                    margin-top: 10px;
                    color: none;
                    background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/withdrawblack.png);
                    border:none;
                    border-color: transparent;
                    background-color:transparent;
                    box-shadow: none;
                    margin-top: 10px;
                    text-align: center;
                    justify-content: center;
                    font-family: Arial;
                    font-size: 30px;
                    "></button>
    </div>
</div>
</div>




<!--
<div class="ui four wide column when-logged-in">
            <div class="price-box" 
                style="
                width: 360px; 
                margin-top: 20px; 
                margin-left: 00px;
                border-color: transparent; 
             
                border-radius: 2px;
                background-image: url(images/mobilef.png);
                height: 320px; 
                line-height: 12px;">
            </div>
</div>
-->

            <!-- THIS IS THE METAMASK DETECTIONSECTION -->

            <!-- THE WHEN LOGGED IN MODIFFIER AFFECTS THE USER IF METAMASK IS DETECTED/NOT -->
<!--            
            <div class="ui four wide column when-logged-in" style="">

                <div 
                class="price-box" 
                style="
                width: 360px;
                height: 320px;
                background-image: url(images/mobileg.png);
                margin-top: 20px; 
                margin-left: 00px;
                border: none;
                border-radius: 2px;
                 
                line-height: 12px;">

                </div>
            </div>
-->
<!--///////////////////////////////////////////////////////////         ARB DEPOSIT AND WITHDRAW        //////////////////////////////////////////////////////////-->

<div class="ui four wide column">
    <div class="buyAndSellSideBySide" style="display: flex;margin-left: 00px;margin-top: 20px;width: 360px;height: 320px; background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobileq.png);line-height: 10px;">
        <div class="ui six wide column center aligned" id="buy-panel" style="margin-top: 110px; margin-left: 0px;background: transparent;width: 360px;">
            <input type="number" id="amountArbDepo" min="0" step="0.01" placeholder="Amount of ARB" style="width: 250px; background-color: grey;margin-top: 30px; margin-bottom: 0px; height: 50px;text-align: center; color: black; font-size: 21px;">
            <div style="color: white;margin-top: 10px;font-family: Arial;">
                Balance: 
                <span id="userTotalArbBalance" class="balancesClass"></span>
                <br/>
            </div>
            <button id="buy-ArbTokens" class="ui button large primary when-logged-out vault_buttons" onclick=depositArbCoins(); style="display: block"></button>
        </div>
    </div>
</div>

<div class="ui four wide column">
    <div class="buyAndSellSideBySide" style="display: flex;width: 360px;height: 320px;background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/mobilep.png);margin-top: 20px;margin-left:00px;margin-right: 0px;border-radius: 2px;line-height: 12px;">
        <div class="ui six wide column center aligned" id="buy-panel" style="margin-top: 110px; margin-left: 0px;background: transparent;width: 360px;">
            <div class="buttonparentparent arbDepositDiv">
                <div class="col-md-12 totalActiveArbClass">
                    <label>Total Active ARB</label>
                    <div id="userActiveArbHoldings"></div>
                </div>
                <div class="row balancesClass">
                    <div class="col-md-12">
                        Internal Holdings: 
                        <span id="userArbHoldings"></span>
                    </div>
                    <!--<div class="col-md-6">-->
                    <!--    External Holdings: -->
                    <!--    <span id="userExternalArbHoldings"></span>-->
                    <!--</div>-->
                </div>
                <button id="withdraw-ArbTokens" class="ui button large primary when-logged-out vault_buttons" data-target="#withdrawARBModal" data-toggle="modal"></button>
            </div>
        </div> 
    </div> 
</div>

<!-- THIS COLUMN HOLDS THE TWO SLICES IN THE MIDDLE  -->
<div style="display: flex; margin-left: 00px; background-color: transparent;width: 360px; margin-top: 10px;">
    <div class="ui four wide column">
        <div class="price-box" style="width: 175px;height: 660px;background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/i.png);margin-top: 0px;margin-left: 0px;border-radius: 2px;line-height: 12px;">
        </div>
    </div>

    <div class="ui four wide column">
        <div class="price-box" 
            style="
            width: 175px;
            height: 660px;
            background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/j.png);
            background-size: cover;
            margin-top: 0px; 
            margin-left: 10px;
            border-radius: 2px;
            line-height: 12px;">
        </div>
    </div>
</div>

<div class="ui four wide column" style="">
    <div 
        class="price-box" 
        style="
        width: 360px;
        height: 320px;
        background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/f.png);
        margin-top: 10px; 
        margin-left: 00px;
        border: none;
        border-radius: 2px;
        background-size:100% 100%;                 
        line-height: 12px;">

        <div class="contract-tokens-usd value-usd" style="margin-top: 20px;"></div>
    </div>
</div>

<div class="ui four wide column" style="">
    <div 
        class="price-box" 
        style="
        width: 360px;
        height: 320px;
        background-image: url(<?php echo base_url(); ?>assets/backend/vault/images/d.png);
        margin-top: 10px; 
        margin-left: 00px;
        border: none;
        border-radius: 2px;
        background-size:100% 100%;                 
        line-height: 12px;">

        <div class="contract-tokens-usd value-usd" style="margin-top: 20px;"></div>
    </div>
</div>






<!-- THIS CLOSES THE UI STACKABLE CENTERED GRID DIV - FOR BUYING AND SELLING TOKENS --WHEN LOGGED IN -->











</div>




































</div> 
<!-- THIS IS THE "NETWORK TRAFFIC WARNING" TEXT-->
                <div class="ui ten wide column traffic-message" style="text-align: center; margin-left: 00px; width: 360px;">
                <i class="fas fa-exclamation-circle"></i>
                                Depending on the Ethereum network traffic, figures may be delayed.
                </div>






            
<!-- THIS IS THE SECOND ROW OF THREE BOXES -->





        





                
                </div>
            </div>
</div> <!-- THIS CLOSES THE LOGGED OUT INTERFACE SECTION FROM LINE 127 -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->
<!-- THIS IS THE END OF THE SECOND ROW OF BOXES -->


</div>

<!-- ***************************************************************** -->
<!-- ***************************************************************** -->







<!-- THIS IS THE THIRD ROW OF  BOXES -->
<!-- THIS IS THE THIRD ROW OF  BOXES -->
<!-- THIS IS THE THIRD ROW OF  BOXES -->

<div style="background-color: transparent;display: flex; width: 360px; height:650px; margin-top: 0px;">


<!-- THIS COLUMN HOLDS THE ARB DEPOSIT AND WITHDRAWALS -->
<!--
<div class="mybuysection when-logged-in" style="width: 440px;height: 500px; background: transparent;">

<div class="buyAndSellSideBySide" 
	style="display: flex; 
	margin-left: 20px; 
	margin-top: 25px;
	width: 460px;
	height: 320px;

	background-image: url(images/h.png);
	line-height: 10px;">

<div class="ui six wide column center aligned when-logged-in" id="buy-panel" style="margin-top: 80px; margin-left: 20px;
                    background: transparent;width: 380px;">

                   

<input type="number" id="purchase-amount" min="0" step="0.01" class="  input-amountA" placeholder="Amount of ARB (e.g. 500)" style="width: 250px; background-color: black;margin-top: 30px; margin-bottom: 0px; height: 50px;text-align: center; color: gold;font-size: 21px;">

                    <div class=" " id="address-balanceA" style="margin-top: 10px;">
                    Balance: <span class="address-balance" style="margin-top: 10px;margin-bottom: 0px;color: white;text-align: center;"></span><br/>
                    </div>
                           
                    <button id="buy-tokens"  
                    style="
                    display: flex;
                    width: 250px; 
                    height: 60px;
                    margin-top: 10px;
                    color: gold;
                    background-color: black;
                    margin-top: 10px;
                    text-align: center;
                    justify-content: center;
                    font-size: 30px;
                    ">Make Deposit</button>
         
    </div>
</div>
	          <div class="price-box" 
                style="
                display: flex;
                width: 460px;
                height: 320px;
                background-image: url(images/g.png);
                margin-top: 20px; 
                margin-left:20px;
                margin-right: 0px;
                border-radius: 2px;
                line-height: 12px;">

                <button id="withdraw-btn"
                
                style="
                margin-top: 150px;
                margin-left: 75px;
                margin-right: 75px;
                height: 70px;
                width: 250px;
                text-align: center;
                padding-left: 10px;
                background-image: url(images/withdrawgold.png);
                border-color: transparent;
                justify-content: center;
                font-size: 30px;
                color: gold;
                background-color: transparent;
                font-family: Arial;
                "></button>

            </div>
</div> 
-->





<!--  THIS IS THE WITHDRAW ALL FUNCTION - SELLS ALL THE USERS ETH IN CONTRACT AND PUTS IT IN THEIR WALLET  -->
<!--  THIS IS THE WITHDRAW ALL FUNCTION - MUTED FOR NOW   -->
<!--	          <div class="price-box" 
                style="
                display: flex;
                width: 460px;
                height: 320px;
                background-image: url(images/l.png);
                margin-top: 20px; 
                margin-left:125px;
                margin-right: 0px;
                border-radius: 2px;
                line-height: 12px;">

                <button id="withdraw-all-btn"
                
                style="
                margin-top: 150px;
                margin-left: 75px;
                margin-right: 75px;
                height: 70px;
                width: 250px;
                text-align: center;
                padding-left: 10px;
                justify-content: center;
                font-size: 30px;
                color: black;
                background-image: url(images/withdrawblack.png);
                border-color: transparent;
                background-color: lightgrey;
                font-family: Arial;
                "></button>

            </div>
-->

            <!--
                  <div class="when-wallet-web">
                            <a href="#" id="wallet-open">Wallet Management</a>
                        </div>
                    -->
</div> 

</div> <!-- THIS ENDS THE PURPLE HAZE    -->
<!-- THIS ENDS THE PURPLE HAZE    -->
<!-- THIS ENDS THE PURPLE HAZE    -->
<!-- THIS ENDS THE PURPLE HAZE    -->
<!-- THIS ENDS THE PURPLE HAZE    -->
<!-- THIS ENDS THE PURPLE HAZE    -->
<!-- THE USERS MASTERNODE LINK SECTION STARTS HERE -->
<!--
    <div class="ui sixteen wide column when-logged-in" id="eth-address-container">
                <br>
                <div id="quoteDisplay"><b><i>Your Masternode link:</i></b></div>
            <script>

                function whenAvailable(name, callback) {
                    var interval = 10;
                    window.setTimeout(function() {
                        if (window[name]) {
                            callback(window[name]);
                        } else {
                            window.setTimeout(arguments.callee, interval);
                        }
                    }, interval);
                }

                whenAvailable("web3js", function(){
                    if (web3js.eth.accounts[0] !== null) {
                        var element = "<br/><a href='https://poshdevstest.surge.sh/?masternode="+ currentAddress + "'>https://poshdevstest.surge.sh/?masternode=" + currentAddress + "</a>";
                        $("#quoteDisplay").append(element);
                    }

                });

            </script>


            <div>Note that your masternode does not become active until you have 100 Tokens.*</div>
    </div>
-->


</div>
</div>




<!-- ***************************************************************** -->
<!--
                            <div class="ui sixteen wide column when-logged-out" style="margin-left: 30px; margin-top: 0px; width: 630px;">
                                <div class="login-box green" style="margin-left: 0px; margin-top: 0px; border-radius: 20px;background-color: lightgrey">
                                    <div class="value" style="margin-top: 0px">Please Log In</div>
                                    <div class="value-usd">
                                        <p style="color: blue">To retrieve your balances log into Metamask UI or use our in-browser wallet below.</p>
                                        <p style="color: blue"><strong style="color: red">WARNING</strong> this feature is in BETA, use at your own risk.</p>
                                    </div>

                                    <div class="myWork" style="margin-left: 0px; display: flex;">


                                        <div class="ui column" style="width: 260px; margin-left: 0px;">
                                            <button id="generate-wallet" class="ui button large primary"style="width: 260px; margin-left: 0px;">Generate Wallet                                            </button>
                                            <p>
                                                Generates and displays a wallet seed. 
                                            </p>
                                            <p style="margin-top: 0px;">
                                            The supplied password is used to encrypt the wallet and store it securely.                                            </p>
                                        </div>


                                        <div class="ui column" id="unlock-wallet-container">
                                            <button id="unlock-wallet" class="ui button large secondary">Unlock Wallet                                            </button>
                                            <p>
                                                Unlocks the wallet currently encrypted and stored in this browser.                                            </p>
                                        </div>


                                        <div class="ui column" style="margin-left: 60px; width: 260px;">
                                        <button id="recover-wallet" class="ui button large" style="width: 260px;">Restore Wallet                                
                                        </button>
                                            <p style="width: 260px; margin-left: 0px;">
                                                Restores a wallet from the supplied seed. 
                                            </p>
                                            <p>
                                            The supplied password is used to encrypt the wallet and store it securely.                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        -->
<!-- ***************************************************************** -->
<!-- ***************************************************************** -->



</div>

</div>



                
            


 <!--               
            <div class="ui four wide column when-logged-in">
                    <div class="price-box blue token-balance">
                    <div class="title">Referral Rewards</div>
                    <div class="poh-refdiv value"></div>
                </div>
            </div>
-->             

<!-- ************************************************************** --> 
<!-- ************************************************************** -->           



<!-- ************************************************************** -->
<!-- THIS IS THE c-message" style="text-align: center;">
                <i class="fas fa-exclamation-circle"></i>
                                
                </div>
******* --> 


<!-- ************************************************************** 

<div class="buyAndSellSideBySide" style="display: flex; margin-left: 30px; margin-top: 10px; background-color: transparent;  border: solid; border-color: black; border-radius: 10px; width: 630px; background-image: url(images/TOY-SAFE-RED-ANGLE-B-512x512altB.png); background-size: 180px; background-position: 220px; background-repeat: no-repeat;">



           
            <div class="ui four wide column when-logged-in">
                <div class="price-box" style="width: 265px; margin-top: 0px; border-color: grey; border: none; border-radius: 10px; margin-left: 00px; background-color: transparent; height: 140px; line-height: 12px;">
                    <div class="title" style="font-size: 33px; margin-top: 0px; line-height: 25px;">TOKENS</div>
                    <div class="poh-balance value" style="font-size: 34px; margin-top: 10px; line-height: 30px;"></div>
                    <div class="poh-value-usd value-usd" style="margin-top: 10px; color: #0099cc"></div>
                </div>
            </div>

           
            <div class="ui four wide column when-logged-in">
                <div class="icon-box" style="width: 100px; margin-top: 0px; border-color: grey; border: none; border-radius: 10px; background-color: transparent; height: 130px; line-height: 12px; margin-bottom: 0px">
                </div>
            </div>
           

          
            <div class="ui four wide column when-logged-in">
                <div class="price-box" style="width: 265px; margin-top: 0px; border-color: grey; border: none; border-radius: 10px; margin-left: 00px; background-color: transparent; height: 130px; line-height: 12px;">
                    <div class="title" style="font-size: 33px; margin-top: 0px; line-height: 25px;">REWARDS</div>
                    <div class="poh-div value" style="font-size: 34px; margin-top: 10px; line-height: 30px;"></div>
                    <div class="poh-div-usd value-usd" style="margin-top: 10px;color: #0099cc"></div>
                </div>
            
            </div>
</div>            
***************************************************************** -->
<!-- ************************************************************** --> 

<!-- THIS OPENS THE UI STACKABLE CENTERED GRID DIV - FOR BUYING AND REINVESTING TOKENS 
<div class="buyAndSellSideBySide" style="display: flex; margin-left: 30px; margin-top: 10px; background: transparent; background-repeat: no-repeat;  border: solid; border-color: black; border-radius: 10px; width: 630px;">
                
                
                <div class="ui four wide column when-logged-in">
                        
                        <button id="buy-tokens" style="width: 265px; height: 45px; margin-left: 0px; margin-top: 0px; margin-bottom: 0px; background-color: lightgrey" 
                        
                        class='ui primary huge button when-logged-in'>Buy Tokens</button>


                        <input type="number" id="purchase-amount" min="0" step="0.01" class="when-logged-in input-amount" placeholder="Amount in ETH (e.g. 0.5)" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px; font-size: 15px; background-color: lightgrey;">


                        <div class="when-logged-in" id="address-balance" style="margin-left: 20px; margin-top: 0px; margin-bottom: 0px;">
                                Balance: <span class="address-balance"></span><br/><span class="number-of-tokens" style="margin-left: 0px; margin-top: 0px; margin-bottom: 0px;"></span>
                        </div>
                        
                        <div class="when-wallet-web">
                            <a href="#" id="wallet-open">Wallet Management</a>
                        </div>

                </div>
                 
               BUYING TOKENS ENDS HERE-->


           <!-- THIS IS THE ICON BOX IN THE MIDDLE   --  start -->

            <!-- THIS IS THE ICON BOX IN THE MIDDLE   --  end -->
            


            <!--  THIS IS THE REINVEST / REPLAY YOUR TOKENS SECTION --- START -->

            <!--  THIS IS THE REINVEST / REPLAY YOUR TOKENS SECTION --- END -->

</div>
<!-- THIS CLOSES THE UI STACKABLE CENTERED GRID DIV - FOR BUYING AND REINVESTING TOKENS -->


<!-- ***************************************************************** -->
<!--
                            <div class="ui sixteen wide column when-logged-out" style="margin-left: 30px; margin-top: 0px; width: 630px;">
                                <div class="login-box green" style="margin-left: 0px; margin-top: 0px; border-radius: 20px;background-color: lightgrey">
                                    <div class="value" style="margin-top: 0px">Please Log In</div>
                                    <div class="value-usd">
                                        <p style="color: blue">To retrieve your balances log into Metamask UI or use our in-browser wallet below.</p>
                                        <p style="color: blue"><strong style="color: red">WARNING</strong> this feature is in BETA, use at your own risk.</p>
                                    </div>

                                    <div class="myWork" style="margin-left: 0px; display: flex;">


                                        <div class="ui column" style="width: 260px; margin-left: 0px;">
                                            <button id="generate-wallet" class="ui button large primary"style="width: 260px; margin-left: 0px;">Generate Wallet                                            </button>
                                            <p>
                                                Generates and displays a wallet seed. 
                                            </p>
                                            <p style="margin-top: 0px;">
                                            The supplied password is used to encrypt the wallet and store it securely.                                            </p>
                                        </div>


                                        <div class="ui column" id="unlock-wallet-container">
                                            <button id="unlock-wallet" class="ui button large secondary">Unlock Wallet                                            </button>
                                            <p>
                                                Unlocks the wallet currently encrypted and stored in this browser.                                            </p>
                                        </div>


                                        <div class="ui column" style="margin-left: 60px; width: 260px;">
                                        <button id="recover-wallet" class="ui button large" style="width: 260px;">Restore Wallet                                
                                        </button>
                                            <p style="width: 260px; margin-left: 0px;">
                                                Restores a wallet from the supplied seed. 
                                            </p>
                                            <p>
                                            The supplied password is used to encrypt the wallet and store it securely.                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
-->
<!-- ***************************************************************** -->
<!-- ***************************************************************** -->

<!-- ***************************************************************** -->

<!-- DUPLICATE FOR TEST FOR DUPLICATES -->
<!--  ***************************************************************** -->




<!-- THIS IS THE SELL TRANSFER AND REINVEST SECTION   --  SHOULD ONLY APPEAR WHEN LOGGED IN   
<div class="ui four wide column when-logged-in" style="display: flex; margin-left: 10px; width: 630px;">

      THIS IS THE TRANSFER TOKENS SECTION --- START  
            <div class="transfer-tokens" style="margin-left: 0px; width: 220px; margin-right: 0px;">
                            <button 
                            id="transfer-tokens-btn"
                            style="margin-bottom: 0px; width: 100px; background-color:lightgrey;" 
                            class='ui method button big secondary when-logged-in'>Transfer Tokens</button>
                              
                              <input type="number" id="transfer-amount" min="0" step="0.01" class="when-logged-in input-amount" placeholder="Amount of Tokens" style="width: 220px; background-color: lightgrey">
                              <input type="text" id="transfer-address" 
                              class="when-logged-in input-amount" 
                              style="width: 220px; background-color:lightgrey; margin-left: 0px" 
                              placeholder="Receiver's Address">                                    
            </div>
    THIS IS THE TRANSFER TOKENS SECTION --- END  -->

    <!--  THIS IS THE SELL YOUR TOKENS SECTION --- START 
        <div class="column" style="width: 220px; margin-left: 00px;">
            <button id="sell-tokens-btn"
                style="margin-left: 5px; margin-right: 00px; width: 100px; margin-top: 0px; margin-bottom: 0px;" 
                class='ui method button big secondary when-logged-in'
                onmousedown="speakandspellincorrect.play()"
                value="Sell Your Tokens">
                Sell Some Tokens</button>
            
            <input id="sell-tokens-amount"
                type="number"
                 class="ui method big when-logged-in input-amount"
                style="width: 220px margin-top: 0px; height:50px; margin-right: 0px; margin-left: 5px;background-color: lightgrey;"
                min="0" step="0.01" placeholder="Amount of ARB Tokens">
        </div>
      THIS IS THE SELL YOUR TOKENS SECTION --- END -->

<!-- THIS IS THE WITHDRAW SOME SECTION 
            <div class="column" style="width: 180px; margin-left: 00px;">
                        <button id="withdraw-btn"
                        style="width: 180px; margin-left: 10px; margin-bottom: 0px;" 
                        class="ui method button big secondary when-logged-in">Take Rewards</button>
                        <p class="when-logged-in" style="margin-left: 30px; margin-top: 0px; width: 180px;">Withdraw your rewards balance back into your Ethereum wallet.</p>
            
            </div>

</div>
-->           
<!--  THIS IS THE REINVEST / REPLAY YOUR TOKENS SECTION --- END -->
<!-- ******************************************************************************************************************** -->

<!-- ***************************************************************** -->
               
<!--
        <div class="ui four wide column when-logged-in"  style="background: transparent;">
                    <div class="price-box blue token-balance" style="background: transparent">
                        <div class="title">Non-Referral Rewards</div>
                        <div class="poh-nonrefdiv value"></div>
                    </div>
        </div>
 -->               

<!-- ******************************************************************************************************************** -->
<!-- ******************************************************************************************************************** -->                   


                            </div>
                            <div class="ui sixteen wide column">
                                <div id="transaction-history-container" style="display: none">
                                    <h2>Transaction History</h2>
                                    <div id="transaction-history">
                                    </div>
                                </div>
                            </div>
    </div> <!-- THIS CLOSES THE UI STACKABLE RELAXED GRID DIV -->
        <!-- THIS CLOSES THE UI STACKABLE RELAXED GRID DIV --> 


<!-- ******************************************************************************************************* -->
                    </div>

        </div> <!-- THIS CLOSES THE ui ELEVEN wide column interface  DIV -->
<!-- THIS CLOSES THE ui ELEVEN wide column interface  DIV -->
                <div class="ui four column stackable grid center aligned methods when-logged-in" style="width: 400px;">
</div>
                <div id="metamask-not-found" class="ui dimmer">
                    <div class="inner">
                        <h2 class="float-left">MetaMask Not Found</h2></br>
                        <p>To interact with the network, you must have <a href="https://metamask.io/">Metamask</a> installed and setup.</p>
                    </div>
                </div>
                <!--
                <div id="metamask-detecting" class="ui dimmer"style="width: 360px; height: 100px;">
                    <div class="inner">
                        <h2 class="float-left">Detecting MetaMask</h2></br>
                        <p>Please wait while we ATTEMPT to load MetaMask</p>
                    </div>
                </div>
            -->
                <div id="seed-dimmer" class="ui dimmer">
                    <div class="inner">
                        <h2 class="float-left">Wallet Seed</h2></br>
                        <p><strong>WARNING</strong>
                            This is your wallet's seed. If you lose this, you lose access to your ETH and any ARB along with it. This is only ever stored locally in your web browser. If you clear your browser data, generate a new wallet over an existing, or your computer dies, and you don't have this saved anywhere, nobody can recover this for you. Seriously, save it somewhere safe.</p>
                        <textarea id="wallet-seed">

                        </textarea>
                        <button class="ui button huge primary" id="close-seed">I Have Stored My Seed Somewhere Safe                        </button>
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
                                    <button id="send-action"
                                            class="ui primary huge button">Send ETH</button>
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

<div class="modal fade" id="withdrawARBModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">This will Withdraw your ARB.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div style="margin:10px 0px 35px 0px">
                        <h3>Withdraw ARB</h3>
                    </div>
                    <div class="form-group row">
                        <label for="pages-option" class="col-4 col-form-labelDash">Move To </label>
                        <div class="col-8">
                            <select class="form-control" id="Withdraw-optionArb" onchange="changeValue();">
                                <!--<option value="" selected>Select One</option>-->
                                <option value="system" selected>System Wallet</option>
                                <!--<option value="external">External Wallet</option>-->
                            </select>
                        </div>
                    </div>    
                    <div class="form-group row">
                        <label class="col-4 col-form-labelDash">Total ARB</label>
                        <div class="col-8">
                            <input id="amountWithdrawArb" onkeyup="WithdrawArbTransfer(this.value);" class="form-control" type="number" min="1"/> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=withdrawArbCoins()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


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
  
                                                          ///////////////////   Check If Deposit / Withdraw is in process   ////////////////
    function checkProgressStatus() 
    {
        $.get( "<?php echo base_url(); ?>in_progress", function( data ) {
            data = JSON.parse(data);
            if(data.in_progress == 0) {
                $('#buy-ArbTokens').text("DEPOSIT");
                $('#withdraw-ArbTokens').text("WITHDRAW");
                $('#buy-ArbTokens').prop('disabled', false);
                $('#withdraw-ArbTokens').prop('disabled', false);
            } 
            else if(data.in_progress == 1) {
                $('#buy-ArbTokens').text("DEPOSIT IN PROCESS");
                $('#withdraw-ArbTokens').text("WITHDRAW IN PROCESS");
                $('#buy-ArbTokens').prop('disabled', true);
                $('#withdraw-ArbTokens').prop('disabled', true);
            }
        });
    }
    checkProgressStatus();
    window.setInterval( function() {checkProgressStatus();}, 20000);
                                                              ///////////////////   Get ARB Amounts and Values   ////////////////
    var activeUserArbsVault = 0;
    var totalUserArbs = 0;
    var totalArbsInVault = 0;
    var userExternalArbHoldings = 0;
    var userInternalArbHoldings = 0;
  
    function getAllValuesArb() {
        $.get( "<?php echo base_url(); ?>vault_data", function( data ) {
            data = JSON.parse(data);
            
            activeUserArbsVault = parseFloat(data.user_activeArb);
            totalUserArbs = parseFloat(data.sw_activeArb);
            totalArbsInVault = parseFloat(data.total_activeArb);
            userExternalArbHoldings = parseFloat(data.external);
            userInternalArbHoldings = parseFloat(data.internal);
            
            $('#totalArbInVault').text(totalArbsInVault);
            $('#userArbHoldings').html(userInternalArbHoldings);
            $('#userExternalArbHoldings').html(userExternalArbHoldings);
            $('#userTotalArbBalance').html(totalUserArbs);
            $('#userActiveArbHoldings').html(activeUserArbsVault);
            
            $.get( "<?php echo base_url(); ?>arb_valueLive", function( data ) {
                data = JSON.parse(data);
                var arbDollarPrice = data.USD;
                
                $('#totalArbUSD').text("(" + parseFloat(totalArbsInVault * arbDollarPrice).toFixed(3) + " USD)");
            });
            
        });
    }
    getAllValuesArb();
    window.setInterval( function() {getAllValuesArb();}, 20000);
    
                                                            ///////////////////   ARB Deposit Function   ////////////////
    function depositArbCoins() {
        var amountArbs = document.getElementById("amountArbDepo").value;
        var userTotalArbCheck = parseInt($('#userTotalArbBalance').html());
       
        if(amountArbs <= 0)
        {
            alert("Add some amount please.");
        }
        else if(amountArbs > userTotalArbCheck)
        {
            alert("You don't have sufficient ARBs.");
        }
        else
        {
            $.post( "<?php echo base_url(); ?>wallet_to_vault", { amount: amountArbs})
            .done(function( data ) {
                data = JSON.parse(data);
                if(data.success == '1')
                {
                    alert(data.msg);
                    location.reload();
                }
                else if(data.error == '1')
                {
                    alert(data.msg);
                }
            });
        }
    }
                                                                ///////////////////   ARB Withdraw Function   ////////////////
    function withdrawArbCoins() {
        var amountArbsWith = document.getElementById("amountWithdrawArb").value;
        var optionWithdrawCoins = document.getElementById('Withdraw-optionArb').value;
        amountArbsWith = parseFloat(amountArbsWith);

        if(amountArbsWith <= 0)
        {
            alert("Add some amount please.");
        }
        else if (optionWithdrawCoins == "system")
        {
            if(amountArbsWith > userInternalArbHoldings) {
                alert("You don't have sufficient balance");
            }
            else {
                $.post( "<?php echo base_url(); ?>vault_to_wallet", { amount: amountArbsWith})
                .done(function( data ) {
                    data = JSON.parse(data);
                    if(data.success == '1')
                    {
                        alert(data.msg);
                        location.reload();
                    }
                    else if(data.error == '1')
                    {
                        alert(data.msg);
                    }
                });
            }    
        }
        else {
            alert("You can only select System Wallet");
        }
        // else if(optionWithdrawCoins == "external") 
        // {
        //     if(amountArbsWith > userExternalArbHoldings) {
        //         alert("You don't have sufficient balance");
        //     }
        //     else {
        //         $.post( "<?php //echo base_url(); ?>vault_to_external_wallet", { amount: amountArbsWith})
        //         .done(function( data ) {
        //             data = JSON.parse(data);
        //             if(data.success == '1')
        //             {
        //                 alert(data.msg);
        //                 location.reload();
        //             }
        //             else if(data.error == '1')
        //             {
        //                 alert(data.msg);
        //             }
        //         });
        //     }    
        // }
    }
    
    function changeValue(){
        var optionWithdraw = document.getElementById('Withdraw-optionArb').value;
        $('#amountWithdrawArb').val(userInternalArbHoldings);
    }   
    
    function WithdrawArbTransfer(value) {
        var optionWithdrawCheck = document.getElementById('Withdraw-optionArb').value;
        var valueWithdrawInput = document.getElementById('amountWithdrawArb').value;
        valueWithdrawInput = parseFloat(valueWithdrawInput);
        
        if(valueWithdrawInput > userInternalArbHoldings){
            $('#amountWithdrawArb').val(userInternalArbHoldings);
        } 
        
        // if(optionWithdrawCheck == "system") {
        //     if(valueWithdrawInput > userInternalArbHoldings){
        //         $('#amountWithdrawArb').val(userInternalArbHoldings);
        //     }   
        // } 
        // else if(optionWithdrawCheck == "external") {
        //     if(valueWithdrawInput > userInternalArbHoldings){
        //         $('#amountWithdrawArb').val(userExternalArbHoldings);
        //     }   
        // }
    }
  
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/web3.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/lightwallet.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/alertify.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/vault/js/contractbrb-final.js"></script>
<!--<script type="text/javascript" src="js/arbethsol.js"></script>-->

</header>
<!-- THIS IS THE END OF THE HEADER - SECTION -->


<!-- THIS IS THE FOOTER SECTION -->

<!-- THIS IS THE TOKEN SALE - PART TWO -  SECTION -->

<!-- THIS IS THE TOKEN SALE - PART TWO -  SECTION -->


</body>
<!-- THIS IS THE END OF THE BODY - SECTION -->


<script>'undefined'=== typeof _trfq || (window._trfq = []);'undefined'=== typeof _trfd && (window._trfd=[]),_trfd.push({'tccl.baseHost':'secureserver.net'}),_trfd.push({'ap':'cpbh'},{'server':'a2plvcpnl45424'}) // Monitoring performance to make your website faster. If you want to opt-out, please contact web hosting support.</script><script src='https://img1.wsimg.com/tcc/tcc_l.combined.1.0.6.min.js'></script></html>