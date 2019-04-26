<?php
$day = Date('d');

function getTruncatedValue ( $value, $precision )
    {
        //Casts provided value
        $value = ( string )$value;

        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );

        //Returns the full pattern match
        return $matches[0];            
    }

$nolimit_withdraw = array(78572, 80394, 13870);
foreach($code as $c){
    $a_code = $c['a_code'];
    $u_email = $c['u_email'];
	$u_username =$c['u_username'];
	if($c['u_wallet'] == ''){
	    $u_wallet = 'empty';
	}else{
	    $u_wallet = $c['u_wallet'];
	}
	$allow_pin = $c['allow_pin'];
}

foreach($tokens as $t){
    $activeArb = $t->activeArb;
    $activeEth = $t->activeEth;
}

$arb = "";

if($activeArb < 0.0001){ $activeArb = 0; }
if($activeEth < 0.00001){ $activeEth = 0; }
$activeArb = getTruncatedValue($activeArb, 4);
$activeEth = getTruncatedValue($activeEth, 5);




@$mbot_status = $mbot_status;

if($a_code == "" || $a_code == NULL){
 $a_code = "jvlKdrUN";
}

$ann = $ann;
if(!isset($allow_pin)){$allow_pin = 0;}
?>
<div class="container-fluid">

    <!--////////////////////////////////////////////////////////// Withdraw Buttons / Values /////////////////////////////////////////////////////////////////////-->    
    <div class="row rowDataBot2 margin_Top_50">
        <div class="col-md-6 textAlignCenter height220">
            <!--<span class="lggFontBOT2">
            <?php if(number_format($activeArb, 6) > 100){echo abs($activeArb);} else {echo number_format($activeArb, 6);} ?></span>-->
            
            <div class="first_wDiv">
                <span class="lggFontBOT2"><?php  echo $activeArb; ?></span>     <span class="lgFontBOT2"><?php echo $arb;?></span>
            </div>
            <div class="secondNull_wDiv"></div>
            <div class="second_wDiv paddingRelative">
                <span class="lgFontBOT2">Available ARB</span>    
            </div>
            <div class="third_wDiv">
                <button class="btn btn-warning btn-blockDash" onclick="withDrawArb()">Withdraw ARB</button>    
            </div>
        </div>
        
        <div class="col-md-6 textAlignCenter height220">
            <div class="first_wDiv">
                <span class="lggFontBOT2"><?php echo $activeEth; ?></span> <span class="lgFontBOT2">($<span id="actETH"></span>)</span>
            </div>
            <div class="secondNull_wDiv"></div>
            <div class="second_wDiv paddingRelative">
                <span class="lgFontBOT2">Available ETH </span>
            </div>
            <div class="row third_wDiv">
                <div class="col-lg-6 col-md-12">
                    <button class="btn btn-warning btn-blockDash" onclick="withDrawEth()">Withdraw ETH</button>
                </div>
                <div class="col-lg-6 col-md-12">
                    <button class="btn btn-warning btn-blockDash" onclick="sendEthToAbot()">Send ETH to aBOT</button>
                </div>
            </div>
        </div>
        <!--<div class="col-md-4 textAlignCenter height220">-->
        <!--    <div class="first_wDiv">-->
        <!--        <span class="lggFontBOT2"><?php //echo $external_wallet_arb; ?></span>    <span class="lgFontBOT2"><?php //echo $arb;?></span>    -->
        <!--    </div>-->
        <!--    <div class="secondNull_wDiv">-->
        <!--        <span id="pendingMoney" class="lgFontBOT2" style="font-size: 15px;">(<span><?php  //echo $pending_arb; ?></span> Pending ARB)</span>    -->
        <!--    </div>-->
        <!--    <div class="second_wDiv paddingRelative">-->
        <!--        <span class="lgFontBOT2">External Wallet (ARB) </span>    -->
        <!--    </div>-->
        <!--    <div class="third_wDiv">-->
        <!--        <button class="btn btn-warning btn-blockDash" onclick="withDrawExternalArbCheck()">Withdraw External ARB</button>    -->
        <!--    </div>-->
        <!--</div>-->
    </div>
    
    <div class="row rowDataBot2">
        <!--////////////////////////////////////////////////////////// Transfer ARB /////////////////////////////////////////////////////////////////////-->
        <div class="textAlignCenter col-lg-4 col-md-6">
            <div class="transferDiv">
                <div class="marg35">
                    <h3>Transfer ARB</h3>
                </div>
                <div class="form-group row">
                    <label for="pages-option" class="col-4 col-form-labelDash">Move To </label>
                    <div class="col-8">
                        <select class="form-control" id="pages-optionArb" onchange="changeValue();">
                            <option value="" selected>Select One</option>
                            <option value="aBOT">aBOT</option>
                            <option value="ex">Exchange</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <!--<div class="col-4" style="padding:0">-->
                        <label class="col-4 col-form-labelDash">Total ARB</label>
                    <!--</div>-->
                    <div class="col-8">
                        <input id="amountArb" onkeyup="wdrawamtArbTransfer(this.value);" value='' class="form-control" type="number" min="1" max='<?php echo $activeArb; ?>' /> 
                        <span id="dollarSign" class="error text-danger" style="font-size:12px"></span>
                    </div>
                </div>
                <div class="textAlignRight">
                    <button type="button" class="btn sendTokenBtn" onclick="sendArb()">Transfer</button>
                </div>
            </div>
        </div>
        <!--////////////////////////////////////////////////////////// Transfer ETH /////////////////////////////////////////////////////////////////////-->
        <div class="textAlignCenter col-lg-4 col-md-6">
            <div class="transferDiv">
                <div class="marg35">
                    <h3>Transfer ETH</h3>
                </div>
                <div class="form-group row">
                    <label for="pages-option" class="col-4 col-form-labelDash">Move To </label>
                    <div class="col-8">
                        <select class="form-control" id="pages-optionEth" onchange="changeValueETH();">
                            <option value="" selected>Select One</option>
                            <option value="ex">Exchange</option>
                            <option value="abot">aBOT</option>
                            <!--(live)<option value="gas">Gas</option>-->
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <!--<div class="col-4" style="padding:0">-->
                        <label class="col-4 col-form-labelDash">Total ETH</label>
                    <!--</div>-->
                    <div class="col-8">
                        <input id="amountEth" onkeyup="wdrawamtEthTransfer(this.value);" class="form-control" type="number" value='' min="0" max='<?php echo $activeEth; ?>' />
                        <span id="abotSendEThText" class="text-danger" style="display:none;"></span>
                    </div>
                </div>
                <div class="textAlignRight">
                    <button type="button" class="btn sendTokenBtn" onclick="sendEth()">Transfer</button>
                </div>
            </div>
        </div>    
        <!--////////////////////////////////////////////////////////// Transfer External Wallet ARB /////////////////////////////////////////////////////////////////////-->
        <!--<div class="textAlignCenter col-lg-3 col-md-6">-->
        <!--    <div class="transferDiv">-->
        <!--        <div class="marg35">-->
        <!--            <h4>Transfer External ARB</h4>-->
        <!--        </div>-->
        <!--        <div class="form-group row">-->
        <!--            <label for="pages-option" class="col-4 col-form-labelDash">Move To </label>-->
        <!--            <div class="col-8">-->
        <!--                <select class="form-control" id="ExWallet-optionArb" onchange="changeValueExWall();">-->
        <!--                    <option value="" selected>Select One</option>-->
        <!--                    <option value="aBOT">aBOT</option>-->
        <!--                    <option value="vault">Vault</option>-->
        <!--                </select>-->
        <!--            </div>-->
        <!--        </div>    -->
        <!--        <div class="form-group row">-->
        <!--            <label class="col-4 col-form-labelDash">Total ARB</label>-->
        <!--            <div class="col-8">-->
        <!--                <input id="amountExWallet" onkeyup="ExWalletArbTransfer(this.value);" class="form-control" type="number" min="1" max='<?php //echo $external_wallet_arb; ?>' /> -->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="row">-->
        <!--            <div class="col-sm-4"></div>-->
        <!--            <div class="col-sm-8" style="text-align:left;">-->
        <!--                <span id="dollarSignExWall" class="error text-danger"></span>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="textAlignRight">-->
        <!--            <button type="button" class="btn sendTokenBtn" onclick=transferExWalletARB()>Transfer</button>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <!--////////////////////////////////////////////////////////// mBot /////////////////////////////////////////////////////////////////////-->
        <div class="textAlignCenter col-lg-4 col-md-6">
            <div class="transferDiv">
                <h3>Register In mBot</h3>
                <div class="textAlignCenter">
                    <span class="lggFontBOT2">500</span>
                    <span>$</span>
                </div>
                <div class="text-left">
                    <span class="fa fa-check"> Arbitrage Professionally. </span></br>
                    <span class="fa fa-check"> Make your own profits. </span></br>
                    <span class="fa fa-check"> Over 1500 spread opportunities. </span></br>
                </div>
                <div class="textAlignRight">
                    <button type="button" class="btn sendTokenBtn mbotBtn" style="margin-top:13px" data-toggle="modal" data-target="#mBotModal">Activate</button>
                </div>
            </div>    
        </div>
    </div>
    
    <?php if($user_current_add_on == 1) {?>
    <div class="row poolTableDiv height220">
        <div class="col-md-4">
            <div class="paddingRelative">
                <span class="lggFontBOT2">Plus+ Wallet</span>    
            </div>
        </div>
        <div class="col-md-4">
            <div class="">
                <span class="lggFontBOT2"><?php  echo $pp_wallet_activeArb; ?></span>    
            </div>
            <div class="">
                <span class="lgFontBOT2">Active ARB</span>     
            </div>
        </div>
        <div class="col-md-4">
            <div>
                <div class="">
                    <span class="lggFontBOT2"><?php  echo $pp_wallet_freeArb; ?></span>
                </div>
                <div class="">
                    <span class="lgFontBOT2">Rollover ARB</span>     
                </div>
            </div>    
            <button class="btn btn-warning" data-toggle="modal" data-target="#poolToSystemArbModal">Transfer To Wallet</button>
        </div>
    </div>
    <?php }?>
</div>

 <script>
        var actETH = <?php echo $activeEth; ?>;
        
        // $.get( "https://api.coinmarketcap.com/v2/ticker/1027/")
        //     .done(function( data ) {
        //      aeth = data.data.quotes.USD.price;
        //      aeth = aeth * actETH;
        //      aeth = aeth.toFixed(2);
            $('#actETH').text((aeth * actETH).toFixed(2));
        //   });
    </script>

<!--////////////////////////////////////////////////////////// Modals /////////////////////////////////////////////////////////////////////-->

<!--<div class="modal fade" id="externalWalletModal">-->
<!--    <div class="modal-dialog modal-md" role="document">-->
<!--        <div class="modal-content" style="font-size:12px">-->
<!--            <div class="modal-header modalHeaderExchange">-->
<!--                <h5 class="modal-title">This will Withdraw your External ARB Balance.</h5>-->
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                <span aria-hidden="true">&times;</span>-->
<!--                </button>-->
<!--            </div>-->
<!--            <div class="modal-body pb-5">-->
<!--                <div class="col-md-12">-->
<!--                    <h5>Your balance will reset to 0 when full withdrawal is completed.</h5>-->
<!--                    <h5>All withdrawls made to blank wallet will be lost.</h5>-->
<!--                    <h5>Please Confirm your ARB withdrawal address.</h5>       -->
<!--                </div>-->
<!--                <div class="col-md-12 pt-4">-->
<!--                    <h5> Your MEW Wallet: </h5>-->
<!--                    <input class="form-control" type="text" value="<?php //echo $u_wallet ?>" readonly>-->
                    
<!--                    <br>-->
<!--                    <h5> Withdraw Amount (ARB): </h5>-->
<!--                    <input id="withdrawExternalArb" onkeyup="withdrawExternalArb(this.value);" class="form-control" type="number" value='<?php //echo $external_wallet_arb; ?>' min="1" max='<?php //echo $external_wallet_arb; ?>'>-->
<!--                    <br>-->
<!--                    <h5> Enter 2FA code: </h5>-->
<!--                    <input id="withdrawExternalArbVerifyCode" class="form-control" type="number" placeholder="Enter your google 2FA code here.">-->
<!--                    <br>-->
<!--                    <span class="text-danger" style="font-size:13px"><?php //echo $arb_withdraw_fee;?> ARB as fee will deduct in every ARB withdrawal. </span>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--                <button class="btn btn-success" onclick=sendExternalWithdraw()>Confirm</button>-->
<!--                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<div class="modal fade" id="ArbwithdrawModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">This will Withdraw your Earned ARB Balance.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-5">
                <div class="col-md-12">
                    <h5>Your balance will reset to 0 when full withdrawal is completed.</h5>
                    <h5>All withdrawls made to blank wallet will be lost.</h5>
                    <h5>Please Confirm your ARB withdrawal address.</h5>       
                </div>
                <div class="col-md-12 pt-4">
                    <h5> Your MEW Wallet: </h5>
                    <input class="form-control" type="text" value="<?php echo $u_wallet ?>" readonly>
                    
                    <br>
                    <h5> Withdraw Amount (ARB): </h5>
                    <input id="wdrawamtArb" onkeyup="wdrawamtArb(this.value);" class="form-control" type="number"  value='<?php echo $activeArb; ?>' min="1" max='<?php echo $activeArb; ?>'>
                    <br>
                    <h5> Enter 2FA code: </h5>
                    <input id="wdrawamtArbVerifyCode" class="form-control" type="number" placeholder="Enter your google 2FA code here.">
                    <br>
                    <span class="text-danger" style="font-size:13px"><?php echo $arb_withdraw_fee;?> ARB as fee will deduct in every ARB withdrawal. </span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=sendWithdrawProcess('ARB')>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="EthwithdrawModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">This will Withdraw your Earned ETH Balance.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-5">
                <div class="col-md-12">
                    <h5>Your balance will reset to 0 when full withdrawal is completed.</h5>
                    <h5>All withdrawls made to blank wallet will be lost.</h5>
                    <h5>Please Confirm your ETH withdrawal address.</h5>
                </div>
                <div class="col-md-12 pt-4">
                    <h5> Your MEW Wallet: </h5>
                    <input class="form-control" type="text" value="<?php echo $u_wallet ?>" readonly>
                    
                    <br>
                    <h5> Withdraw Amount (ETH): </h5>
                    <input id="wdrawamtEth" onkeyup="wdrawamtEth(this.value);" class="form-control" type="number" value='<?php echo $activeEth; ?>' max='<?php echo $activeEth; ?>'>
                    <br>
                    <h5> Enter 2FA code: </h5>
                    <input id="wdrawamtEthVerifyCode" class="form-control" type="number" placeholder="Enter your google 2FA code here.">
                    <br>
                    <span class="text-danger" style="font-size:13px"><?php echo $eth_withdraw_fee;?> ETH as fee will deduct in every ETH withdrawal. </span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=sendWithdrawProcess('ETH')>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> 

<div class="modal fade" id="poolToSystemArbModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">This will Transfer your Free ARB from Plus+ Wallet to Exchange Earned Wallet.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-5">
                <div class="col-md-12 pt-3">
                    <h5> Amount: </h5>
                    <input id="freeArbPool" onkeyup="freeArbPool(this.value);" class="form-control" type="number"  value='<?php echo $pp_wallet_freeArb; ?>' min="1" max='<?php echo $pp_wallet_freeArb; ?>'>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=poolToSystem()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mBotModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4>500 USD worth of ARB will deduct from your account after the activation of mBOT. Once activate, you cannot withdraw the gas.</h4>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" onclick=activatemBot()>Confirm</button>
        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="mBotActiveErrorModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-danger">Error</h5>
            </button>
          </div>
          <div class="modal-body">
            <h3>Your mBOT didn't activated, please check your balance.</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
    </div>
</div>

<div class="modal" id="mBotActivatedModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-success">mBOT Activation</h5>
            </button>
          </div>
          <div class="modal-body">
            <h3>Your mBOT is successfully activated.</h3>
            <h5>If you have any further questions, please join our group by <a href="http://t.me/mbotknowledge" target="_blank">click here.</a></h5>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
    </div>
</div>

<div class="modal fade" id="error_Modal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body">
            
            <div class="col-md-12">
                <h5 id="withdrawError"></h5>
            </div>
            
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="Arbwithdraw2faModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p><h3>Enter your Google 2FA code.</h3></p>
                     <div class="col-md-12">
                        <input id="codeSave2Fa" class="form-control" type="number" placeholder="Enter your Google 2FA to withdraw.">
                        <br><br>
                        <button class="btn btn-success" onclick=saveCode2Fa()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="announceModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><b>Announcement: </b></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p id="annTextDiv"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<script>
    
    $(".content-wrapper").css("background-color", "#ececec");
    var noAmountTransfer = "Please Enter More Value.";
    var falseTransfer = "Your Transfer has failed.";
    var pending = "Your Withdrawal is in Process.";
    var alreadyRequested = "You are only allowed one pending withdraw at a time, please try again later";
    var create2fa = "Please Activate your 2FA First";
    var lessFive = "ARB should be more then 5 for requesting a withdrawal";
    var lesspointFive = "ETH should be more then 0.05 for requesting a withdrawal";
    var currType;
    var pendingMoney;

    pendingMoney = '<?php  echo $pending_arb; ?>';
    if(pendingMoney == '')
    {
        $('#pendingMoney').css("visibility", "hidden");
    }

    var arb_in_usd = (arb_p - ((arb_p * 2) / 100)).toFixed(4);
    $.ajax({
        async: false,
        type: "GET",
        url: "<?php echo base_url();?>abot_arb",
        success: function (data) {
        arb_in_usd = data;
     // $('#dollarSign').text('Total in USD: ' + ($('#amountArb').val() * arb_in_usd).toFixed(4));
        }
    });
    //(live)  
    //   $.get( "https://api.coinmarketcap.com/v1/ticker/ethereum/")
    //       .done(function( data ) {
    //          eth_usd = data[0].price_usd;
    //     });
    var dollar_arb = 0;
    
    function setArbPrice(){
        dollar_arb = parseFloat(arb_in_usd * <?php echo $activeArb; ?>);
    }
    setArbPrice();
    setInterval(function(){ setArbPrice(); }, 5000);
            
    var userAbotPrice = 0;
    $.get( "<?php echo base_url(); ?>send_abot_price", function( data ) {
        data = JSON.parse(data);
        if(data.success == '1')
        {
            if(data.avg_price != 0) {
                userAbotPrice = data.avg_price;
            } else {
                userAbotPrice = data.abot_usd;
            }
        }
    });
    
    $('#amountArb').keyup(function(){
        if($('#pages-optionArb').val() == "gas") {
            $('#dollarSign').text('Total in USD: ' + ($('#amountArb').val() * abot_usd).toFixed(4));
        } else {
            $('#dollarSign').text('Total in aUSD: ' + ($('#amountArb').val() * userAbotPrice).toFixed(4));
        }
    }); 
    //(live)
    // $('#amountEth').keyup(function(){
        
    //     if($('#pages-optionEth').val() == "gas") {
    //         $('#abotSendEThText').text('Total in USD: ' + ($('#amountEth').val() * eth_usd).toFixed(4));
    //     }
    // });
    
    $('#amountExWallet').keyup(function(){
        $('#dollarSignExWall').text('Total in USD: ' + ($('#amountExWallet').val() * coinexchange_price).toFixed(3));
    });
    
    function changeValue(){
        var option = document.getElementById('pages-optionArb').value;
        if(option=="aBOT")
        {
            $('#amountArb').val(<?php echo $activeArb; ?>);
            $('#dollarSign').text('Total in aUSD: ' + ($('#amountArb').val() * userAbotPrice).toFixed(4));
            $('#dollarSign').css("display", "block");
        }
        else if (option=="ex")
        {
            document.getElementById('amountArb').value="<?php echo $activeArb;?>";
             $('#dollarSign').css("display", "none");
        }
        else if(option=="gas")
        {
            $('#amountArb').val(<?php echo $activeArb; ?>);
            $('#dollarSign').text('Total in aUSD: ' + ($('#amountArb').val() * abot_usd).toFixed(4));
            $('#dollarSign').css("display", "block");
            $('#arbSign').css("display", "none");
        }
        else
        {
            document.getElementById('amountArb').value="";
            $('#dollarSign').css("display", "none");
        }
    }    
    
    function changeValueETH(){
        $('#amountEth').val(<?php echo $activeEth; ?>);
        var option = document.getElementById('pages-optionEth').value;
        if(option=="abot")
        {
            $('#abotSendEThText').css("display", "block");
            $('#abotSendEThText').text("with 5% bonus.");
        }
        // (live)
        // else if(option=="gas")
        // {
        //     console.log(aeth);
        //     $('#abotSendEThText').text('Total in USD: ' + ($('#amountEth').val() * eth_usd).toFixed(4));
        //     $('#abotSendEThText').css("display", "block");
        // }
        else {
            $('#abotSendEThText').css("display", "none");
        }
    }
    
    // function changeValueExWall(){
    //     var optionExWall = document.getElementById('ExWallet-optionArb').value;
    //     if(optionExWall=="aBOT")
    //     {
    //         $('#amountExWallet').val(<?php //echo $external_wallet_arb; ?>);
    //         $('#dollarSignExWall').text('Total in USD: ' + ($('#amountExWallet').val() * coinexchange_price).toFixed(4));
    //         $('#dollarSignExWall').css("display", "block");
    //     }
    //     else if (optionExWall=="vault")
    //     {
    //         document.getElementById('amountExWallet').value="<?php //echo $external_wallet_arb;?>";
    //          $('#dollarSignExWall').css("display", "none");
    //     }
    //     else
    //     {
    //         document.getElementById('amountExWallet').value="";
    //         $('#dollarSignExWall').css("display", "none");
    //     }
    // }
  
    function withDrawArb(){
        if(<?php echo $allow_pin ;?> == 1)
        {
            if(<?php echo $withdraw_status; ?> == "1")
            {
                    $('#ArbwithdrawModal').modal('show');
            }
            else
            {
                $('#error_Modal').modal('show');
                $("#withdrawError").text(alreadyRequested);
            }
        }
        else
        {
            $('#error_Modal').modal('show');
            //$('#error_Modal').modal({backdrop: 'static', keyqboard: false});
            $("#withdrawError").text(create2fa);
        }
    }
    
    function withDrawEth(){
        if(<?php echo $allow_pin ;?> == 1)
        {
            if(<?php echo $withdraw_status; ?> == "1")
            {
                    $('#EthwithdrawModal').modal('show');
            }
            else
            {
                $('#error_Modal').modal('show');
                $("#withdrawError").text(alreadyRequested);
            }
        }
        else
        {
            $('#error_Modal').modal('show');
            $("#withdrawError").text(create2fa);
        }
    }
    
    // function withDrawExternalArbCheck(){
    //     if(<?php //echo $allow_pin ;?> == 1)
    //     {
    //         if(<?php //echo $withdraw_status; ?> == "1")
    //         {
    //                 $('#externalWalletModal').modal('show');
    //         }
    //         else
    //         {
    //             $('#error_Modal').modal('show');
    //             $("#withdrawError").text(alreadyRequested);
    //         }
    //     }
    //     else
    //     {
    //         $('#error_Modal').modal('show');
    //         $("#withdrawError").text(create2fa);
    //     }
    // }
    
    function sendWithdrawProcess(curr) {
        
        var code2FA = 0;
        var selectedAmount = 0;
        
        if(curr == 'ARB') {
            $('#ArbwithdrawModal').modal('hide');
            code2FA = document.getElementById('wdrawamtArbVerifyCode').value;
            selectedAmount = document.getElementById("wdrawamtArb").value;
            
            if(selectedAmount < 5){
                $('#error_Modal').modal('show');
                $("#withdrawError").text(lessFive);
            }
            else if(code2FA == '' || code2FA.length != 6) {
                $('#error_Modal').modal('show');
                $("#withdrawError").text("Please Insert your 2FA code correctly.");
            }
            else
            {
                $.post( "<?php echo base_url(); ?>sendWithdraw", { currency: 'ARB', amount: selectedAmount, google_code:code2FA, wallet: "<?php echo $u_wallet ?>"  })
                  .done(function( data ) {
                    data = JSON.parse(data);
                    if(data.error == "1")
                    {
                        $('#error_Modal').modal('show');
                        $("#withdrawError").text(data.msg);
                    }
                    else if(data.success == "1")
                    {
                        $('#error_Modal').modal('show');
                        $("#withdrawError").text(data.msg);
                        setInterval(function(){ location.reload(); }, 3000);
                    }
                });
            }
        }
        else if(curr == 'ETH') {
            $('#EthwithdrawModal').modal('hide');
            code2FA = document.getElementById('wdrawamtEthVerifyCode').value;
            selectedAmount = document.getElementById("wdrawamtEth").value;
            
            if(selectedAmount < 0.05){
                $('#error_Modal').modal('show');
                $("#withdrawError").text(lesspointFive);
            }
            <?php
            if(!in_array($user_id, $nolimit_withdraw)){ ?>
                else if(selectedAmount > 100){
                    $('#error_Modal').modal('show');
                    $("#withdrawError").text("You cannot withdraw more then 100 ETH at once.");
                }
            <?php } ?>    
            else if(code2FA == '' || code2FA.length != 6) {
                $('#error_Modal').modal('show');
                $("#withdrawError").text("Please Insert your 2FA Code Correctly.");
            }
            else
            {
                $.post( "<?php echo base_url(); ?>sendWithdraw", { currency: 'ETH', amount: selectedAmount, google_code:code2FA, wallet: "<?php echo $u_wallet ?>"  })
                  .done(function( data ) {
                    data = JSON.parse(data);
                    if(data.error == "1")
                    {
                        $('#error_Modal').modal('show');
                        $("#withdrawError").text(data.msg);
                    }
                    else if(data.success == "1")
                    {
                        $('#error_Modal').modal('show');
                        $("#withdrawError").text(data.msg);
                        setInterval(function(){ location.reload(); }, 3000);
                    }    
                });
            }
        }
    }
    
    function sendWithdrawArb(value){
        currType = value;
       
        $('#ArbwithdrawModal').modal('hide');
        $('#Arbwithdraw2faModal').modal('show');
    }
    
    function sendWithdrawEth(value){
        if("<?php echo $u_wallet; ?>" == "empty")
        {
            $('#EthwithdrawModal').modal('hide');
            $('#error_Modal').modal('show');
            $("#withdrawError").text("Please Add Your MEW Address First.");      
        }
        else
        {
            currType = value;
            $('#EthwithdrawModal').modal('hide');
            $('#Arbwithdraw2faModal').modal('show');
        }
    }
    
    function wdrawamtArb(value){
        if(value > <?php echo $activeArb; ?>){
            $('#wdrawamtArb').val(<?php echo $activeArb; ?>);
        }
    }
    function wdrawamtArbTransfer(value){
        if(value > <?php echo $activeArb; ?>){
            $('#amountArb').val(<?php echo $activeArb; ?>);
        }
    }
        
    function wdrawamtEth(value){
        if(value > <?php echo $activeEth; ?>){
            $('#wdrawamtEth').val(<?php echo $activeEth; ?>);
        }
    }
    function wdrawamtEthTransfer(value){
        if(value > <?php echo $activeEth; ?>){
            $('#amountEth').val(<?php echo $activeEth; ?>);
        }
    }
    // function withdrawExternalArb(value){
    //     if(value > <?php //echo $external_wallet_arb; ?>){
    //         $('#withdrawExternalArb').val(<?php //echo $external_wallet_arb; ?>);
    //     }
    // }
    // function ExWalletArbTransfer(value) {
    //     if(value > <?php //echo $external_wallet_arb; ?>){
    //         $('#amountExWallet').val(<?php //echo $external_wallet_arb; ?>);
    //     }
    // }
    function freeArbPool(value){
        if(value > <?php echo $pp_wallet_freeArb; ?>){
            $('#freeArbPool').val(<?php echo $pp_wallet_freeArb; ?>);
        }
    }
    
    function sendArb (){
        var amount = document.getElementById("amountArb").value;
        var arb = document.getElementById("amountArb").value;
        var page = document.getElementById("pages-optionArb").value;

        if(page == "aBOT"){
        
            if(amount > <?php echo $activeArb; ?>)
            {
                $('#error_Modal').modal('show');
                $("#withdrawError").text(falseTransfer);
            }
            else if(amount <= 0)
            {
                $('#error_Modal').modal('show');
                $("#withdrawError").text(noAmountTransfer);
            }
            else
            {
                if(confirm("Do you want to transfer your amount?"))
                {
                    amount = (amount * arb_in_usd);
                    $.post( "<?php echo base_url(); ?>system_to_abot", { arb_selected: arb , abot_amount: amount})
                    .done(function( data ) {
                        if(data == 'true')
                        {
                            $('#error_Modal').modal('show');
                            $("#withdrawError").text("Your Amount is Successfully Transfered.");
                            window.setInterval( function() {location.reload();}, 3000);
                        }
                        else if(data == "You are not able to add more then 10000$ in aBOT pending") {
                            $('#error_Modal').modal('show');
                            $("#withdrawError").text(data);
                        }
                        else {
                            $('#error_Modal').modal('show');
                            $("#withdrawError").text("Your Amount didn't Transfered. Try Again Later.");
                        }
                    });
                }    
            }
        }
        else if (page == "ex")
        {
            if(amount <= 0)
            {
                $('#error_Modal').modal('show');
                $("#withdrawError").text(noAmountTransfer);
            }
            else
            {
                if(confirm("Do you want to transfer your amount?"))
                {
                    $.post( "<?php echo base_url(); ?>system_to_exchange", { ex_amount: amount, currency:'ARB'})
                    .done(function( data ) 
                    {
                        data = JSON.parse(data);
                        if(data.success == '1')
                        {
                            $('#error_Modal').modal('show');
                            $("#withdrawError").text(data.msg);
                            window.setInterval( function() {location.reload();}, 3000);
                        }
                        else if(data.error == '1') {
                          $('#error_Modal').modal('show');
                          $("#withdrawError").text(data.msg);
                        }
                    });
                }    
            }
        }
        else {
            $('#error_Modal').modal('show');
            $("#withdrawError").text("Select Some Option");
        }
    }
    
    function sendEth (){
        var amount = document.getElementById("amountEth").value;
        var page = document.getElementById("pages-optionEth").value;
        
        if(amount <= 0)
        {
            $('#error_Modal').modal('show');
            $("#withdrawError").text(noAmountTransfer);
        }
        else if (page == "ex")
        {
            if(confirm("Do you want to transfer your amount?"))
            {
                $.post( "<?php echo base_url(); ?>system_to_exchange", { ex_amount: amount, currency:'ETH'})
                 .done(function( data ) {
                    data = JSON.parse(data);
                    if(data.success == '1')
                    {
                        $('#error_Modal').modal('show');
                        $("#withdrawError").text(data.msg);
                        window.setInterval( function() {location.reload();}, 3000);
                    }
                    else if(data.error == '1')
                    {
                      $('#error_Modal').modal('show');
                      $("#withdrawError").text(data.msg);
                    }
                });
            } 
        }
        else if (page == "abot")
        {
            $('#error_Modal').modal('show');
            $("#withdrawError").text("Temporarily Locked");
        //   if(confirm("Do you want to transfer your amount?"))
        //     {
        //         $.post( "<?php //echo base_url(); ?>buy_for_abot", { total_eth: amount})
        //           .done(function( data ) {
        //             data = JSON.parse(data);
        //             if(data.success == '1')
        //             {
        //                 $('#error_Modal').modal('show');
        //                 $("#withdrawError").text(data.msg);
        //                 setInterval(function(){ location.reload(); }, 3000);
        //             }
        //             else if(data.error == '1')
        //             {
        //                 $('#error_Modal').modal('show');
        //                 $("#withdrawError").text(data.msg);
        //             }
        //         });
        //     } 
        }
        //(live)
        // else if (page == "gas")
        // {
        //   if(confirm("Do you want to transfer your amount?"))
        //     {
        //         $.post( "<?php echo base_url(); ?>send_eth_to_gas", { eth: amount})
        //           .done(function( data ) {
        //               console.log(data);
        //               data = JSON.parse(data);
        //             if(data.success == '1')
        //             {
        //                 $('#error_Modal').modal('show');
        //                 $("#withdrawError").text(data.msg);
        //                 setInterval(function(){ location.reload(); }, 3000);
        //             }
        //             else if(data.error == '1')
        //             {
        //                 $('#error_Modal').modal('show');
        //                 $("#withdrawError").text(data.msg);
        //             }
        //         });
        //     } 
        // }
        else {
            $('#error_Modal').modal('show');
            $("#withdrawError").text("Select Some Option");
        }
    }
    
    function sendEthToAbot() {
        $('#error_Modal').modal('show');
        $("#withdrawError").text("Temporarily Locked");
        // if(confirm("Are you sure to transfer your complete ETH amount in aBOT?"))
        // {
        //     if(<?php echo $activeEth; ?> <= '0') {
        //         $('#error_Modal').modal('show');
        //         $("#withdrawError").text(noAmountTransfer);
        //     } 
        //     else {
        //         $.post( "<?php //echo base_url(); ?>buy_for_abot", { total_eth: <?php //echo $activeEth; ?>})
        //           .done(function( data ) {
        //             data = JSON.parse(data);
        //             if(data.success == '1')
        //             {
        //                 $('#error_Modal').modal('show');
        //                 $("#withdrawError").text(data.msg);
        //                 setInterval(function(){ location.reload(); }, 3000);
        //             }
        //             else if(data.error == '1')
        //             {
        //                 $('#error_Modal').modal('show');
        //                 $("#withdrawError").text(data.msg);
        //             }
        //         });
        //     }    
        // } 
    }
    
    if(<?php echo $mbot_status; ?> == 1 )
    {
        $(".mbotBtn").html('Activated');
        $(".mbotBtn").prop('disabled', true);
        $(".mbotBtn").css({'color' : 'black', 'opacity': 1});
    }
    
    function activatemBot()
    {
        $.get( "<?php echo base_url(); ?>mbot_register_form_system", function( data ) {
            if(data == "true")
            {
                $('#mBotModal').modal('hide');
                $('#mBotActivatedModal').modal('show');
                setInterval(function(){ location.reload(); }, 10000);
            }
            else
            {
                $('#mBotActiveErrorModal').modal('show');
            }
        });
    }
    
    if("<?php echo $ann; ?>" == 'false')
    {
        $('#announceModal').modal('hide');
    }
    else
    {
        $('#annTextDiv').html('<?php echo $ann; ?>');
        $('#announceModal').modal('show');
    }
    
    // function transferExWalletARB (){
    //     var amountExArb = $("#amountExWallet").val();
    //     var pageExWall = $("#ExWallet-optionArb").val();
        
    //     if (pageExWall == "aBOT")
    //     {
    //         if(amountExArb <= 0)
    //         {
    //             $('#error_Modal').modal('show');
    //              $("#withdrawError").text(noAmountTransfer);
    //         }
    //         else
    //         {
    //             if(confirm("Do you want to transfer your amount?"))
    //             {
    //                 $.post( "<?php //echo base_url(); ?>external_to_abot", { amount: amountExArb})
    //                   .done(function( data ) {
    //                     data = JSON.parse(data);
    //                     if(data.success == '1')
    //                     {
    //                         $('#error_Modal').modal('show');
    //                         $("#withdrawError").text(data.msg);
    //                         setInterval(function(){ location.reload(); }, 3000);
    //                     }
    //                     else if(data.error == '1')
    //                     {
    //                         $('#error_Modal').modal('show');
    //                         $("#withdrawError").text(data.msg);
    //                     }
    //                 });
    //             }    
    //         }
                
    //     }
    //     else if (pageExWall == "vault")
    //     {
    //         if(amountExArb <= 0)
    //         {
    //             $('#error_Modal').modal('show');
    //              $("#withdrawError").text(noAmountTransfer);
    //         }
    //         else
    //         {
    //             if(confirm("Do you want to transfer your amount?"))
    //             {
    //                 $.post( "<?php //echo base_url(); ?>external_wallet_to_vault", { amount: amountExArb})
    //                   .done(function( data ) {
    //                     data = JSON.parse(data);
    //                     if(data.success == '1')
    //                     {
    //                         $('#error_Modal').modal('show');
    //                         $("#withdrawError").text(data.msg);
    //                         setInterval(function(){ location.reload(); }, 3000);
    //                     }
    //                     else if(data.error == '1')
    //                     {
    //                         $('#error_Modal').modal('show');
    //                         $("#withdrawError").text(data.msg);
    //                     }
    //                 });    
    //             }    
    //         }
                
    //     }
        
    // }
    
    // function sendExternalWithdraw() {
        
    //     var code2FAExWall = 0;
    //     var selectedAmountExWall = 0;
        
    //     $('#externalWalletModal').modal('hide');
    //     code2FAExWall = document.getElementById('withdrawExternalArbVerifyCode').value;
    //     selectedAmountExWall = document.getElementById("withdrawExternalArb").value;
        
    //     if(selectedAmountExWall < 5){
    //         $('#error_Modal').modal('show');
    //         $("#withdrawError").text(lessFive);
    //     }
    //     else if(code2FAExWall == '' || code2FAExWall.length != 6) {
    //         $('#error_Modal').modal('show');
    //         $("#withdrawError").text("Please Insert your 2FA code correctly.");
    //     }
    //     else
    //     {
    //         $.post( "<?php //echo base_url(); ?>sendWithdraw_external", { amount: selectedAmountExWall, google_code:code2FAExWall, wallet: "<?php //echo $u_wallet ?>"  })
    //           .done(function( data ) {
    //             data = JSON.parse(data);
    //             if(data.error == "1")
    //             {
    //                 $('#error_Modal').modal('show');
    //                 $("#withdrawError").text(data.msg);
    //             }
    //             else if(data.success == "1")
    //             {
    //                 $('#error_Modal').modal('show');
    //                 $("#withdrawError").text(data.msg);
    //                 setInterval(function(){ location.reload(); }, 3000);
    //             }
    //         });
    //     }
        
    // }
    
    function poolToSystem() {
        $('#poolToSystemArbModal').modal('hide');
        var freeArbPoolAmount = $('#freeArbPool').val();
        
        if(freeArbPoolAmount <= 0){
            $('#error_Modal').modal('show');
            $("#withdrawError").text(noAmountTransfer);
        }
        else if(freeArbPoolAmount > <?php echo $pp_wallet_freeArb ?>) {
            $('#error_Modal').modal('show');
            $("#withdrawError").text("You don't have sufficient balance.");
        }
        else
        {
            $.post( "<?php echo base_url(); ?>freearb_to_wallet", { amount: freeArbPoolAmount})
              .done(function( data ) {
                data = JSON.parse(data);
                if(data.error == "1")
                {
                    $('#error_Modal').modal('show');
                    $("#withdrawError").text(data.msg);
                }
                else if(data.success == "1")
                {
                    $('#error_Modal').modal('show');
                    $("#withdrawError").text(data.msg);
                    setInterval(function(){ location.reload(); }, 3000);
                }
            });
        }
        
    }

</script>

<?php $this->load->view('pages/backend/modal_lending') ?>
<?php $this->load->view('pages/backend/modal_profit') ?>
