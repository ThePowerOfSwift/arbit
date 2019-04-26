<?php
$com_sum = 0;
$day = Date('d');

foreach($code as $c){
    $a_code = $c['a_code'];
    $u_email = $c['u_email'];
	$u_username =$c['u_username'];
	$u_wallet = $c['u_wallet'];
	$u_id =$c['u_id'];
	$allow_pin = $c['allow_pin'];
}
$arbdbvalue = round($arb_db_value, 4);

foreach($abotData as $a){
    $profit = $a->profit;
    $earned = $a->earned;
    $active = $a->active;
    $pending = $a->pending;
    $pending_date = $a->pending_date;
    
    $lock_status = $a->abot_lock_status;
    $lock_days = $a->lock_days;
    $lock_time = $a->lock_time;
    $gas = $a->gas;
}

foreach($commission as $com){
    $commi[] = $com->value;
    $commiD[] = $com->date;
    $com_sum =  $com_sum + $com->value;
}

if($a_code == "" || $a_code == NULL){
 $a_code = "jvlKdrUN";
}

$before_24hour = date("Y-m-d H:i:s", strtotime('-24 hour'));

$block_days = '+'.$lock_days.' days';
$block_time = date('Y-m-d H:i:s', strtotime($block_days, strtotime($lock_time)));
?>

<div class="container">
    <div class='row rowDataBot2 margin_Top_50'>
        <div class='col-sm-4 textAlignCenter'>
            <span class="lggFontBOT2">$<span id="abot_arb_usd"></span></span></br>
            <span class="lgFontBOT2">ARB Price in aBOT</span></br>
        </div>
        
        <div class='col-sm-4 textAlignCenter'>
            <span class="lggFontBOT2">$<?php echo round($gas, 4); ?></span></br>
            <span class="lgFontBOT2">Total Gas Remaining</span></br>
        </div>
        
        <div class='col-sm-4 textAlignCenter'>
            <span class="lggFontBOT2">aBOT ID: <span id="userId"></span></span></br>
            <span class="font_17px">Time: <span id="userTimeCal"></span></span></br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 textAlignCenter">
            <span class="lggFontBOT2">$<?php echo round($pending, 2); ?></span></br>
            <span class="lgFontBOT2">Pending (Over $250 activate at Group Time)</span></br>
        </div>
        <div class="col-md-6 textAlignCenter">
            <span class="lggFontBOT2">$<?php echo round($active, 2); ?></span></br>
            <span class="lgFontBOT2">aBOT Active</span></br>
        </div> 
       
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2"><?php echo round($profit, 2); ?></span></br>
            <span class="lgFontBOT2">Today's Profit (ARB)</span>
        </div>
        
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2"><?php echo round($earned, 2); ?></span></br>
            <span class="lgFontBOT2">Total Earned (ARB)</span></br>
        </div>
        
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2">$<?php echo (round($pending, 2) + round($active, 2)); ?></span></br>
            <span class="lgFontBOT2">Total aUSD</span></br>
        </div>
    </div>
    
    <div class="row">
        <?php 
        $current = date('Y-m-d H:i:s');
        if($lock_status == 1 && $block_time > $current){
        ?>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" disabled>Transfer Active To Wallet (STOP aBOT)</button>
        </div>
        
        <?php }else{ ?>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" <?php if($pending_date > $before_24hour){?> disabled <?php }else{?> onclick="check2faStatus()" <?php }?>>Transfer Active To Wallet (STOP aBOT)</button>
        </div> 
        <?php }?>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#transferEarnedWalletModal">Transfer Earned To Wallet</button>
        </div> 
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#profitModal">PROFIT CALCULATOR</button>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#reinvestConfirmModal">Reinvest Earned (Only balances over $250 pending in aBOT activate)</button>
        </div>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block autoReinvestBtn" id="shade" onclick="ReinvestActivateFun()"></button>
        </div>
    </div>
    
    <div class="row rowDataBot2">
        <div class="col-md-6 textAlignCenter" style="display:none;">
            <span class="lggFontBOT2">0.51%</span></br>
            <span>Pending Profit</span>
        </div>
        <div class="col-md-4 textAlignCenter">
           
            <span class="lggFontBOT2"><!-- ?php echo  $commission; ? -->
                 <script>
                var cccom = <?php print_r(json_encode($commission)); ?>;
                document.write(cccom[0].value);
            </script>
             %</span></br>
            <span>Today Profit</span></br>
        </div>
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2"></span></br>
            <span> $ = aUSD </span></br>
        </div>
        
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2"><?php echo $com_sum;?>%</span></br>
            <span>Profit Last 30 Days</span></br>
        </div>
    </div>
    
    <!--<div class="row imgLogoMarg">-->
    <!--    <div class="col-md-12">-->
    <!--        <div class="col-md-12" style="border:1px solid grey;">-->
    <!--            <div class="row backgroundColorAB">-->
    <!--                <div class="col-md-12"><span class="fa fa-history"></span> aBOT Trade History (All trade values are calculated to display the ETH value of trade.)</div>-->
    <!--            </div>-->
    <!--            <div class="row">-->
    <!--                <div class="col-md-12 tableDivExchange" style="background-color:white">-->
    <!--                    <table class="table table-hover table-striped textAlignCenter">-->
    <!--                        <thead>-->
    <!--                            <tr>-->
    <!--                                <th>Price</th>-->
    <!--                                <th>Volume </th>-->
    <!--                                <th>Timestamps</th>-->
    <!--                                <th>Type</th>-->
    <!--                                <th>Total</th>-->
    <!--                                <th>Sum</th>-->
    <!--                            </tr>-->
    <!--                        </thead>-->
    <!--                        <tbody id="marketHistory">-->
                                
    <!--                        </tbody>-->
    <!--                    </table>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
    <div class="row descpBOT2">
        <div class="col-md-12 bot2Graph colBorder">
            <div id="chartdiv" style="height:300px !important;"></div>			
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 textAlignCenter">
            <p><h4>aBOT Payout History Graph</h4></p>
        </div>
    </div>
    
</div>


<script>

function calcTime(offset, dd) {
    // create Date object for current location
    var d = new Date(dd); 
    d.setHours((d.getHours()+1) + offset); 
    /*// convert to msec
    // subtract local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*offset));

    // return time as a string
    */
    return d.toLocaleString();
}

//alert(calcTime(5.5));

    var userID = 0;                                                         //Get Last Digit of UserID
    userID = "<?php echo $u_id; ?>";
    var userIDLastDig = userID.toString().split('').pop();
    $('#userId').html(userIDLastDig);
    
    var timeCalculated = 0;                                                 // Get Time of aBOT 
    offset = new Date().getTimezoneOffset() / 60;

//offset = 5;
    //userIDLastDig = 9;
    if(userIDLastDig == 0) {
        timeCalculated = '<?php echo date("M-d-Y 04:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
        
    }
    else if(userIDLastDig == 1) {
        timeCalculated = "6 AM";
        timeCalculated = '<?php echo date("M-d-Y 06:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 2) {
        timeCalculated = "8 AM";
        timeCalculated = '<?php echo date("M-d-Y 08:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 3) {
        timeCalculated = "10 AM";
        timeCalculated = '<?php echo date("M-d-Y 10:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 4) {
        timeCalculated = "12 PM";
        timeCalculated = '<?php echo date("M-d-Y 12:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 5) {
        timeCalculated = "2 PM";
        timeCalculated = '<?php echo date("M-d-Y 14:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 6) {
        timeCalculated = "4 PM";
        timeCalculated = '<?php echo date("M-d-Y 16:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 7) {
        timeCalculated = "6 PM";
        timeCalculated = '<?php echo date("M-d-Y 18:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 8) {
        timeCalculated = "8 PM";
        timeCalculated = '<?php echo date("M-d-Y 20:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else if(userIDLastDig == 9) {
        timeCalculated = "10 PM";
        timeCalculated = '<?php echo date("M-d-Y 22:00:00"); ?>'; 
        timeCalculated = calcTime(offset * (-1), timeCalculated);
    }
    else
    {
        timeCalculated = "No date found";    
    }
    
    $('#userTimeCal').html(timeCalculated);

    var arbTotalInvestModal = 0;
    var arbTotalInvestPercent = 0;
    
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

// console.log(arb_p - ((arb_p * 1.5) / 100));
// var arb_in_usd = <?php echo ARB_VALUE_IN_USD ?>;
    // setInterval(function(){ $.get("<?php echo base_url();?>abot_arb").done(function( data ) {
    //     arb_in_usd = data;
    //     $('#abot_arb_usd').text(arb_in_usd);
    // }); }, 5000);
    
    // setInterval(function(){
    // $.ajax({
    //     async: false,
    //     type: "GET",
    //     url: "<?php echo base_url();?>abot_arb",
    //     success: function (data) {
    //     arb_in_usd = data;
        
    //     }
    // });
    // }, 5000);

    $('#abot_arb_usd').text(parseFloat(arb_in_usd).toFixed(4));

    function check2faStatus() {
        if(<?php echo $allow_pin;?> == 0)
        {
            $('#2faErrorModal').modal('show');
        }
        else
        {
            $('#stopAbot2faModal').modal('show');
        }
    }
    
    function saveCode2Fa(){
        $('#stopAbot2faModal').modal('hide');
        var codeVerify = document.getElementById('codeSave2Fa').value;
        $.post( "<?php echo base_url(); ?>verify2fa", {code:codeVerify})
          .done(function( data ) {
            if(data == "true")
            {
               $('#transferActiveWalletModal').modal('show');
            }
            else
            {
              alert("Please Enter the Correct 2FA Pin.");
            }
        });
    }

    function TransferActive(){
        $('#transferActiveWalletModal').modal('hide');
        var dollar = document.getElementById("wdrawamtactive").value;
        var arb = document.getElementById("wdrawamtactive").value;
        arb = (arb / arb_in_usd);
        if(dollar <= 0)
        {
            $('#failed_Modal').modal('show');
        }
        else
        {
            $.post( "<?php echo base_url(); ?>abot_active_to_stop_abot_wallet", {dollar_selected: dollar , abot_active_transfer_amount:arb})
              .done(function( data ) {
                if(data == "Lock by admin")
                {
                    $('#LockAdminModal').modal('show');
                }
                else
                {
                    $('#DoneModal').modal('show');
                    location.reload();
                }
              });
        }
    }   
    
    function TransferEarned(){
        $('#transferEarnedWalletModal').modal('hide');
        var earnedWallet = $('input[name="earnedRadio"]:checked').val();
        var value = document.getElementById("wdrawamtearned").value;
        
        if(value <= 0)
        {
            $('#negativeModal').modal('show');
        }
        else
        {
            if(earnedWallet == "SystemWallet")
            {
                $.post( "<?php echo base_url(); ?>abot_earned_to_Wallet", {abot_earned_transfer_amount:value})
                  .done(function( data ) {
                    if(data == "Lock by admin")
                    {
                        $('#LockAdminModal').modal('show');
                    }
                    else
                    {
                        $('#DoneModal').modal('show');
                        location.reload();
                    }    
                });
            }
            else if(earnedWallet == "ExchangeWallet")
            {
                $.post( "<?php echo base_url(); ?>transfer_to_exearned", {abot_earned_transfer_amount:value})
                  .done(function( data ) {
                    if(data == "Lock by admin")
                    {
                        $('#LockAdminModal').modal('show');
                    }
                    else
                    {
                        $('#DoneModal').modal('show');
                        location.reload();
                    }    
                });
            }
            else if(earnedWallet == "Vault")
            {
                if(value >= 5) {
                    $.post( "<?php echo base_url(); ?>transfer_to_vault", {abot_earned_transfer_amount:value})
                      .done(function( data ) {
                        if(data == "Lock by admin")
                        {
                            $('#LockAdminModal').modal('show');
                        }
                        else if(data == "true")
                        {
                            $('#DoneModal').modal('show');
                            location.reload();
                        }
                        else {
                            alert("Something went wrong. Please Try Again.");
                        }
                    });  
                }else {
                    alert("You can't transfer less than 5 ARB in vault.");
                }
            }
        }    
    }
    
    function reinvestFun(){
        if(<?php echo $earned;?> <= 0 )
        {
            $('#reinvestConfirmModal').modal('hide');
            $('#reinvestModalZero').modal('show');
        }
        else
        {
            $.get("<?php echo base_url(); ?>abot_reinvest", function( data ) {
                if(data == "Lock by admin")
                {
                    $('#LockAdminModal').modal('show');
                }
                else
                {
                    $('#reinvestModal').modal('show');
                    location.reload();
                }    
            });
        }
    }
    
    function wdrawamtearned(value){
        if(value > <?php echo $earned; ?>){
            $('#wdrawamtearned').val(<?php echo $earned; ?>);
        }
    }
        
    function wdrawamtactive(value){
        if(value > <?php echo $active; ?>){
            $('#wdrawamtactive').val(<?php echo $active; ?>);
        }
    }
    
    ///////////////////////////////////////////// Auto Reinvest Check /////////////////////////////////////
    
    if(<?php echo $auto_reinvest;?> == 1)
    {
        $('.autoReinvestBtn').html("Deactivate Auto Reinvest");
    }
    else
    {
        $('.autoReinvestBtn').html("Activate Auto Reinvest");
    }
    
    function ReinvestActivateFun()
    {
        if(<?php echo $auto_reinvest;?> == 0)
        {
            var newdate = new Date();
            newdate.setDate(newdate.getDate()+7);
            newdate.setHours(0,0,0,0);
            
            $('#reinvestAbotConfirmation').modal('show');
            $('#newdate').html(newdate);
        }    
        else if(<?php echo $auto_reinvest;?> == 1)
        {
            if(confirm("Are you sure you want to deactivate auto reinvest?"))
            {
                $.get("<?php echo base_url(); ?>auto_reinvest", function( data ) {
                    data = JSON.parse(data);
                    if(data.success == '1') {
                        $('#apiResponseModal').modal('show');
                        $('#apiResponseDiv').html(data.msg);
                        setInterval(function(){ location.reload(); }, 3000);
                    } 
                    else if(data.error == '1') {
                        $('#apiResponseModal').modal('show');
                        $('#apiResponseDiv').html(data.msg);
                    } 
                });
            }    
        }
    }
    
    $(".content-wrapper").css("background-color", "lightgrey");
        
    // function getData(){
    //     $.post("<?php echo base_url(); ?>market_history", function( data ) {
    //         var parsed = $.parseJSON(data);
          
    //         tdh = "";
    //         total = 0;
    //         var totalPre = 0;
    //         var totalPrice;
             
    //         $.each(parsed, function(i) {
    //             price = parsed[i].price;
    //             volume = parsed[i].amount;
            
    //             sum =  price * volume;
    //             total += sum;
    //             sum = sum.toFixed(2);
               
    //             totalPrice = sum;
    //             totalPre += parseFloat(totalPrice); 
               
    //             time = parsed[i].created_at;
    //             mode = parsed[i].order_type;

    //             tdh += "<tr><td>" + price + "</td><td>" + volume + "</td><td>" + time + "</td><td>" + mode + "</td><td>" + sum + "</td><td>" + totalPre + "</td><td>";
    //         });
    //          $('#marketHistory').html(tdh);
    //     })
    // }
    // getData();
    // window.setInterval( function() {getData();}, 15000);

    // document.getElementById("metamaskWalletAddress").innerHTML = web3.eth.coinbase;
</script>

<div class="modal fade" id="apiResponseModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body">
            <div class="col-md-12">
                <h3 id="apiResponseDiv"></h3>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="2faErrorModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12">
                    <h3>Please activate Your 2FA first.</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stopAbot2faModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p><h3>Enter you Google 2FA code.</h3></p>
                     <div class="col-md-12">
                        <input id="codeSave2Fa" class="form-control" type="number" placeholder="Enter your Google 2FA to stop aBot.">
                        <br><br>
                        <button class="btn btn-success" onclick=saveCode2Fa()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stopModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <h1>This feature coming soon.</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="DoneModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body">
            <div class="col-md-12 border">
                <h1>Your Transfer is Done.</h1>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="failed_Modal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body">
            <div class="col-md-12 border">
                <h1>Your Transfer is Failed.</h1>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body" id="dmodal"><i class="fa fa-spinner fa-spin" style="font-size:40px"></i></div>
    </div>
    </div>
</div>

<div class="modal fade" id="transferActiveWalletModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12">
                    <h4>This will Transfer your Active Investment into your Stop aBOT Wallet.</br></h4>
                    <small>(These ARB will go back to aBOT for the same price(aUSD) you're getting out.)</small>
                    <h6 class="text-danger">(4% aBOT STOP fee)</h6>
                </div>
                <div class="col-md-12">
                    <br>
                    <input id="wdrawamtactive" onkeyup="wdrawamtactive(this.value);" class="form-control" type="number"  value='<?php echo $active; ?>' min="1" max='<?php echo $active; ?>'>
                    <br>
                        <span id="amountInArb" class="error text-danger"></span>
                    <br><br>
                    <button class="btn btn-success" onclick=TransferActive()>Confirm</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <script>
                        arbTotalInvestModal = ($('#wdrawamtactive').val() / <?php echo $arbdbvalue; ?>);
                        arbTotalInvestPercent = arbTotalInvestModal * 0.005;
                        arbTotalInvestPercent = arbTotalInvestModal - arbTotalInvestPercent;
                        
                        $('#amountInArb').text('Total in ARB: ' + arbTotalInvestPercent.toFixed(4));
                        $('#wdrawamtactive').keyup(function()
                        {
                            arbTotalInvestModal = ($('#wdrawamtactive').val() / <?php echo $arbdbvalue; ?>);
                            arbTotalInvestPercent = arbTotalInvestModal * 0.005;
                            arbTotalInvestPercent = arbTotalInvestModal - arbTotalInvestPercent;
                            $('#amountInArb').text('Total in ARB: ' + arbTotalInvestPercent.toFixed(4));
                        }); 
                    </script>
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="transferEarnedWalletModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                
                <div class="col-md-12">
                    <h4>This will Transfer your Earned Investment.</h4>
                </div>
                <hr>
                
                <div class="col-md-12">
                    <div>
                        <input id="wdrawamtearned" onkeyup="wdrawamtearned(this.value);" class="form-control" type="number"  value='<?php echo $earned; ?>' min="1" max='<?php echo $earned; ?>'>
                    </div><br>
                    <form>
                        <div class="radioAutoRe">
                            <label>Please select the wallet: </label><br/>
                            <input type='radio' id='radio_earned' name='earnedRadio' value='SystemWallet' checked="checked"/>System Wallet
                            <input type='radio' id='radio_earned' name='earnedRadio' value='ExchangeWallet' />Exchange Earned Wallet
                            <input type='radio' id='radio_earned' name='earnedRadio' value='Vault' />Vault
                        </div>
                    </form>
                 
                    <br><br>
                    <button class="btn btn-success" onclick=TransferEarned()>Confirm</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reinvestConfirmModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body">
            
            <div class="col-md-12 border">
                <h3>Please Confirm Reinvest Request. </h3>            
                <h5>Your Total in USD will be: <span class="text-danger" id="reinvestArb_USD"></span></h5>
            </div>
            
            <div class="col-md-12">
               <br>
                <button class="btn btn-success" onclick=reinvestFun()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            
            <script>
                 $('#reinvestArb_USD').text("$" + (<?php echo $earned;?> * arb_in_usd).toFixed(4));
            </script>
            
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="negativeModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="font-size:12px">
        <div class="modal-body">
            
            <div class="col-md-12 border">
                <h3>Negative Value is not allowed.</h3>
            </div>
            
        </div>
    </div>
    </div>
</div>

<div class="modal fade" id="LockAdminModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <h1>Your aBOT is currently blocked by Admin. </h1>                
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reinvestModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <h1>Your Coins are Reinvested. </h1>                
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reinvestModalZero" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <h1>You don't have enough coins to Reinvest. </h1>                
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reinvestAbotConfirmation">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12">
                    <p><h5>You will not be able to deactivate it untill <span id="newdate"></span></h5></p>
                    <form>
                        <div class="radioAutoRe">
                            <label>Please select the amount: </label><br/>
                            <input type='radio' id='radio_25' name='type' value='25' checked="checked"/>25% 
                            <input type='radio' id='radio_50' name='type' value='50' />50% 
                            <input type='radio' id='radio_75' name='type' value='75' />75% 
                            <input type='radio' id='radio_100' name='type' value='100' />100% 
                        </div>
                    </form>
                    <br><br>
                        <button class="btn btn-success" onclick=confirmReinvest()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
            <script>
                function confirmReinvest(){
                    $('#reinvestAbotConfirmation').modal('hide');
                    var percent = $('input[name="type"]:checked').val();
                    if(percent == "25" || percent == "50" || percent == "75" || percent == "100")
                    {
                        $.post( "<?php echo base_url(); ?>auto_reinvest", {percent: percent})
                          .done(function( data ) {
                            if(data == "Lock by admin")
                            {
                                $('#LockAdminModal').modal('show');
                            }
                            else
                            {  
                                if(data == 'activated')
                                {
                                    alert("Successfully Activated");
                                }
                               $('.autoReinvestBtn').html("Deactivate Auto Reinvest");
                               location.reload();
                            }   
                        });
                    }
                    else
                    {
                        alert("Error");
                    }
                }
            </script>
        </div>
    </div>
</div>


<?php $this->load->view('pages/backend/modal_lending') ?>
<?php $this->load->view('pages/backend/modal_profit') ?>

<script>
 var chart = AmCharts.makeChart( "chartdiv", {
  "type": "serial",
  "theme": "light",
  "dataProvider": [{
    "day": <?php echo $commiD[0]; ?>,
    "visits": <?php echo $commi[0]; ?>
  },
  {
    "day": <?php echo $commiD[1]; ?>,
    "visits": <?php echo $commi[1]; ?>
  }, {
    "day": <?php echo $commiD[2]; ?>,
    "visits": <?php echo $commi[2]; ?>
  }, {
    "day": <?php echo $commiD[3]; ?>,
    "visits": <?php echo $commi[3]; ?>
  }, {
    "day": <?php echo $commiD[4]; ?>,
    "visits": <?php echo $commi[4]; ?>
  }, {
    "day": <?php echo $commiD[5]; ?>,
    "visits": <?php echo $commi[5]; ?>
  }, {
    "day": <?php echo $commiD[6]; ?>,
    "visits": <?php echo $commi[6]; ?>
  }, {
    "day": <?php echo $commiD[7]; ?>,
    "visits": <?php echo $commi[7]; ?>
  }, {
    "day": <?php echo $commiD[8]; ?>,
    "visits": <?php echo $commi[8]; ?>
  }, {
    "day": <?php echo $commiD[9]; ?>,
    "visits": <?php echo $commi[9]; ?>
  }, {
    "day": <?php echo $commiD[10]; ?>,
    "visits": <?php echo $commi[10]; ?>
  }, {
    "day": <?php echo $commiD[11]; ?>,
    "visits": <?php echo $commi[11]; ?>
  }, {
    "day": <?php echo $commiD[12]; ?>,
    "visits": <?php echo $commi[12]; ?>
  }, {
    "day": <?php echo $commiD[13]; ?>,
    "visits": <?php echo $commi[13]; ?>
  }, {
    "day": <?php echo $commiD[14]; ?>,
    "visits": <?php echo $commi[14]; ?>
  }, {
    "day": <?php echo $commiD[15]; ?>,
    "visits": <?php echo $commi[15]; ?>
  }, {
    "day": <?php echo $commiD[16]; ?>,
    "visits": <?php echo $commi[16]; ?>
  }, {
    "day": <?php echo $commiD[17]; ?>,
    "visits": <?php echo $commi[17]; ?>
  }, {
    "day": <?php echo $commiD[18]; ?>,
    "visits": <?php echo $commi[18]; ?>
  }, {
    "day": <?php echo $commiD[19]; ?>,
    "visits": <?php echo $commi[19]; ?>
  }, {
    "day": <?php echo $commiD[20]; ?>,
    "visits": <?php echo $commi[20]; ?>
  }, {
    "day": <?php echo $commiD[21]; ?>,
    "visits": <?php echo $commi[21]; ?>
  }, {
    "day": <?php echo $commiD[22]; ?>,
    "visits": <?php echo $commi[22]; ?>
  }, {
    "day": <?php echo $commiD[23]; ?>,
    "visits": <?php echo $commi[23]; ?>
  }, {
    "day": <?php echo $commiD[24]; ?>,
    "visits": <?php echo $commi[24]; ?>
  }, {
    "day": <?php echo $commiD[25]; ?>,
    "visits": <?php echo $commi[25]; ?>
  }, {
    "day": <?php echo $commiD[26]; ?>,
    "visits": <?php echo $commi[26]; ?>
  }, {
    "day": <?php echo $commiD[27]; ?>,
    "visits": <?php echo $commi[27]; ?>
  }, {
    "day": <?php echo $commiD[28]; ?>,
    "visits": <?php echo $commi[28]; ?>
  }, {
    "day": <?php echo $commiD[29]; ?>,
    "visits": <?php echo $commi[29]; ?>
  }],
  "valueAxes": [ {
    "gridColor": "#FFFFFF",
    "gridAlpha": 0.2,
    "dashLength": 0
  } ],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [ {
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "visits"
  } ],
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "day",
  "categoryAxis": {
    "gridPosition": "start",
    "gridAlpha": 0,
    "tickPosition": "start",
    "tickLength": 20
  },
  "export": {
    "enabled": true
  }

} );
 
</script>
