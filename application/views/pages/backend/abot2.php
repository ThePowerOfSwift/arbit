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

//$old_user = 1;
//$audit_per = -1;
?>

<div class="container">
    <div class='row margin_Top_50'>
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
    <div class="row rowDataBot2">
        <div class="col-lg-abot boxDivaBot3">
            <span class="lggFontBOT2">$<?php echo round($pending, 2); ?></span></br>
            <span class="lgFontBOT2">Pending (Over $250 activate at Group Time)</span></br>
        </div>
        <div class="col-lg-abot boxDivaBot3">
            <span class="lggFontBOT2">$<?php echo round($active, 2); ?></span></br>
            <span class="lgFontBOT2">aBOT Active</span></br>
        </div>
        <div class="col-lg-abot boxDivaBot3">
            <span class="lggFontBOT2"><?php echo round($profit, 2); ?></span></br>
            <span class="lgFontBOT2">Today's Profit (ARB)</span>
        </div>
        
        <!--<div class="col-lg-abot boxDivaBot3">-->
        <!--    <span class="lggFontBOT2"><?php //echo round($eth_earned, 2); ?></span></br>-->
        <!--    <span class="lgFontBOT2">Total Earned (ETH)</span>-->
        <!--</div>-->
        <div class="col-lg-abot boxDivaBot3">
            <span class="lggFontBOT2"><?php echo round($earned, 2); ?></span></br>
            <span class="lgFontBOT2">Total Earned (ARB)</span></br>
        </div>
        
        <div class="col-lg-abot boxDivaBot3">
            <span class="lggFontBOT2">$<?php echo (round($pending, 2) + round($active, 2)); ?></span></br>
            <span class="lgFontBOT2">Total aUSD</span></br>
        </div>
    </div>
    <!--  -->
    
    <!--<div class="row rowDataBot2">-->
        <!--<div class="col-sm-6 col-md-6 col-lg-5 mx-auto text-center mx-auto">-->
            <!--<div class="">-->
                <!--<button class="btn btn-warning btn-block" id="shade" disabled>Pay in ETH</button>-->
            <!--</div>-->
        <!--</div>-->
    <!--</div>-->
    
    <!--  -->
    <div class="row">
        <?php 
        $current = date('Y-m-d H:i:s');
        if($lock_status == 1 && $block_time > $current){
        ?>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" disabled>Transfer Active To aBOT Wallet (STOP ABOT)</button>
        </div>
        
        <?php }else{ ?>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" <?php if($pending_date > $before_24hour){?> disabled <?php }else{?> onclick="check2faStatus()" <?php }?>>Transfer Active To aBOT Wallet (STOP ABOT)</button>
        </div> 
        <?php }?>
        
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#transferEarnedWalletModal">Transfer Earned To Wallet</button>
        </div> 
        <?php
        if($old_user == 0){
        ?>
            <div class="col-sm textAlignCenter mb-3">
                <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#profitModal">PROFIT CALCULATOR</button>
            </div>
        <?php }else{ 
        if($audit_per == -1){
        ?>
            <div class="col-sm textAlignCenter mb-3">
                <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#auditAbotModal">aBOT Audit</button>
            </div>
            
            <?php }else{ ?>
            <div class="col-sm textAlignCenter mb-3">
                <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#profitModal">PROFIT CALCULATOR</button>
            </div>
        <?php } }?>
    </div>
    
    <div class="row">
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block btnFont" id="shade" data-toggle="modal" data-target="#reinvestConfirmModal">Reinvest Earned (Only balances over $250 pending in aBOT activate)</button>
        </div>
        <div class="col-sm textAlignCenter mb-3">
            <button class="btn btn-warning btn-block autoReinvestBtn" id="shade" onclick="ReinvestActivateFun()"></button>
        </div>
        <!--<div class="col-sm textAlignCenter mb-3">-->
        <!--    <button class="btn btn-warning btn-block payOutEthBtn" id="shade" onclick="payOutEth()"></button>-->
        <!--</div>-->
    </div>
    
    <div class="row rowDataBot2">
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2">
            <script>
                var cccom = <?php print_r(json_encode($commission)); ?>;
                document.write(cccom[0].value);
            </script>
             %</span></br>
            <span>Today Profit</span></br>
        </div>
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2"><?php echo $activeEth_abot." ETH";?></span></br>
            <span> Reinvest Bonus </span></br>
        </div>
        
        <div class="col-md-4 textAlignCenter">
            <span class="lggFontBOT2"><?php echo $com_sum;?>%</span></br>
            <span>Profit Last 30 Days</span></br>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 textAlignCenter">
            <span>$ = aUSD</span>
        </div>
    </div>
    
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
    
    $(".content-wrapper").css("background-color", "#ececec");
    
    function calcTime(offset, dd) {
        var d = new Date(dd); 
        d.setHours((d.getHours()+1) + offset);
        return d.toLocaleString();
    }

    var userID = 0;                                                         //Get Last Digit of UserID
    userID = "<?php echo $u_id; ?>";
    var userIDLastDig = userID.toString().split('').pop();
    $('#userId').html(userIDLastDig);
    
    var timeCalculated = 0;                                                 // Get Time of aBOT 
    offset = new Date().getTimezoneOffset() / 60;

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
        }
    });

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
        var selectedStopRadio = $('input[name=stopRadio]:checked', '#myForm').val();

        if( selectedStopRadio == 1 )
        {
            $.post( "<?php echo base_url(); ?>abot_active_to_stop_abot_wallet", {stop_percent: selectedStopRadio })
              .done(function( data ) {
                  
                data = $.parseJSON(data);
                $('#apiResponseDiv').text(data.msg);
                $('#apiResponseModal').modal('show');
                setInterval(function(){ location.reload(); }, 3000);
            });
        }
        else if( selectedStopRadio == 10 )
        {
            $.post( "<?php echo base_url(); ?>abot_active_to_stop_abot_wallet", {stop_percent: selectedStopRadio })
              .done(function( data ) {
                data = $.parseJSON(data);
                $('#apiResponseDiv').text(data.msg);
                $('#apiResponseModal').modal('show');
                setInterval(function(){ location.reload(); }, 3000);
            });
        }
        else {
            $('#apiResponseModal').modal('show');
            $('#apiResponseDiv').html("Please Select the Correct Option.");
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
    
    // function TransferEarnedETH(){
    //     $('#transferEarnedWalletModal').modal('hide');
    //     var valueETHTransfer = document.getElementById("wdrawamtearnedeth").value;
        
    //     if(valueETHTransfer <= 0)
    //     {
    //         $('#apiResponseModal').modal('show');
    //         $('#apiResponseDiv').html("Less than 0 amount is not allowed.");
    //     }
    //     else if(valueETHTransfer > <?php //echo $eth_earned;?>) {
    //         $('#apiResponseModal').modal('show');
    //         $('#apiResponseDiv').html("You don't have sufficient balance.");
    //     }
    //     else
    //     {
    //         $.post( "<?php //echo base_url(); ?>eth_earned_to_systemWallet", {eth_earned_transfer_amount:valueETHTransfer})
    //           .done(function( data ) {
    //             data = JSON.parse(data);
    //             if(data.success == '1') {
    //                 $('#apiResponseModal').modal('show');
    //                 $('#apiResponseDiv').html(data.msg);
    //                 setInterval(function(){ location.reload(); }, 3000);
    //             } 
    //             else if(data.error == '1') {
    //                 $('#apiResponseModal').modal('show');
    //                 $('#apiResponseDiv').html(data.msg);
    //             } 
    //         });
    //     }    
    // }
    
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
    
    function wdrawamtearnedeth(value){
        if(value > <?php echo $eth_earned; ?>){
            $('#wdrawamtearnedeth').val(<?php echo $eth_earned; ?>);
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

    ///////////////////////////////////////////// ETH Payout /////////////////////////////////////
    
    if(<?php echo $eth_payout;?> == 1)
    {
        $('.payOutEthBtn').html("Deactivate ETH Payout");
    }
    else
    {
        $('.payOutEthBtn').html("Activate ETH Payout");
    }
    
    function payOutEth() {
        if(<?php echo $eth_payout;?> == 0) {
            $('#ethPayoutModal').modal('show');
            if(<?php echo $auto_reinvest;?> == 1) {
                $("#ethPayoutReinvestCheck").attr('checked', true);
            }
        }
        else {
            $('#ethPayoutModalDeactivate').modal('show');
        }
    }
    
    function activateEthPayout(typ) {
        if(typ == "activate") {
            
            if($('#ethPayoutReinvestCheck').prop("checked") == false) {
                $('#showPayoutReinvestError').css('display', 'block');
            }
            else if($('#ethPayoutFeeCheck').prop("checked") == false) {
                $('#showPayoutFeeError').css('display', 'block');
            }
            else if($('#ethPayoutReinvestCheck').prop("checked") == false && $('#ethPayoutFeeCheck').prop("checked") == false) {
                $('#showPayoutReinvestError').css('display', 'block');
                $('#showPayoutFeeError').css('display', 'block');
            }
            else if(<?php echo $auto_reinvest;?> == '0') {
                $('#showPayoutReinvestError').css('display', 'block');
            }
            else {
                $('#ethPayoutModal').modal('hide');
                $.get("<?php echo base_url(); ?>eth_payout_status", function( data ) {
                    data = JSON.parse(data);
                    if(data.success == '1') {
                        $('#apiResponseModal').modal('show');
                        $('#apiResponseDiv').html(data.msg);
                        setInterval(function(){ location.reload(); }, 2000);
                    } 
                    else if(data.error == '1') {
                        $('#apiResponseModal').modal('show');
                        $('#apiResponseDiv').html(data.msg);
                    } 
                });
            }    
        }
        else if(typ == "deactivate") {
            $('#ethPayoutModalDeactivate').modal('hide');
            $.get("<?php echo base_url(); ?>eth_payout_status", function( data ) {
                data = JSON.parse(data);
                if(data.success == '1') {
                    $('#apiResponseModal').modal('show');
                    $('#apiResponseDiv').html(data.msg);
                    setInterval(function(){ location.reload(); }, 2000);
                } 
                else if(data.error == '1') {
                    $('#apiResponseModal').modal('show');
                    $('#apiResponseDiv').html(data.msg);
                } 
            });
        }   
    }
    
    function underMainFun() {
        $('#apiResponseDiv').html("Under Maintenance");
        $('#apiResponseModal').modal('show');
    }
    
    ///////////////////////////////////////////// aBOT Audit /////////////////////////////////////
    var auditPercent = 0;
    function auditProfitReq() {
        $("#auditAbotModal").modal("hide");
        
        auditPercent = $('input[name="audit_radio"]:checked').val();
        if(auditPercent == 0 || auditPercent == 10 || auditPercent == 25 || auditPercent == 33 || auditPercent == 50) {
            $.post( "<?php echo base_url(); ?>user_select_audit", {percentage:auditPercent})
              .done(function( data ) {
                data = JSON.parse(data);
                if(data.error == '1')  
                {
                    $('#apiResponseDiv').html(data.msg);
                    $('#apiResponseModal').modal('show');
                }
                else if(data.success == '1')
                {
                    $('#apiResponseDiv').html(data.msg);
                    $('#apiResponseModal').modal('show');
                    setInterval(function(){ location.reload(); }, 2000);
                }
            });
        }
        else {
            $('#apiResponseDiv').html("Please select some option");
            $('#apiResponseModal').modal('show');
        }
    }
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
                    <h3>This will Transfer your Active Investment into your aBOT Wallet.</h3>            
                </div>
                <h6 class="text-danger ml-3">Select how much you wanna transfer.</h6>
                <div class="row radioStopaBot">
                    <form id="myForm">
                        <div class="col-md-12">
                            <div class="checkboxWrapper">
                                <label class="containerRadio" style="width:100%">1%
                                  <input type='radio' name='stopRadio' value='1' onclick="stopAbotCheckPer('1')" />
                                  <span class="radioCheckmark"></span>
                                </label>
                            </div>
                            <div class="checkboxWrapper" style="width:100%">
                                <label class="containerRadio">10%
                                  <input type='radio' name='stopRadio' value='10' onclick="stopAbotCheckPer('10')" />
                                  <span class="radioCheckmark"></span>
                                </label>
                            </div>
                            <div class="checkboxWrapper" style="width:100%">
                                <label class="containerRadio">100% (Send to auction)
                                  <input type='radio' id='myRadioDisabled' />
                                  <span class="radioCheckmark"></span>
                                </label>
                            </div>
                        </div>
                    </form>    
                </div>
                
                <div id="stopAbotCheckText" class="auditNote"></div>
                
                <div class="col-md-12">
                    <button class="btn btn-success" onclick=TransferActive()>Confirm</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div> 
            </div>
            <script>
            
                document.getElementById("myRadioDisabled").disabled = true;
            
                function stopAbotCheckPer(per) {
                    if(per == '1'){
                        $('#stopAbotCheckText').html("Can be used 1 time per 24 hours. (Max stop value $2,000)");
                    }
                    else if(per == '10'){
                        $('#stopAbotCheckText').html("Can be used 1 time per 30 days, this requires your aBOT to re-activate and you get your first earning 48 hours after you stopped. (Max stop value $20,000)");
                    }
                    else {
                        $('#stopAbotCheckText').html("");
                    }
                }
            </script>
        </div>
    </div>
</div>

<div class="modal fade" id="auditAbotModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">aBOT Audit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-4">
                <form>
                    <div class="radioAutoRe">
                        <label>Please select the amount: </label>
                        <div class="text-center">
                            <input type='radio' id='audit_25' name='audit_radio' value='0' onclick="auditCheck('0')" />0%
                            <input type='radio' id='audit_25' name='audit_radio' value='10' onclick="auditCheck('10')" />10% 
                            <input type='radio' id='audit_50' name='audit_radio' value='25' onclick="auditCheck('25')" />25% 
                            <input type='radio' id='audit_25' name='audit_radio' value='33' onclick="auditCheck('33')" />33% 
                            <input type='radio' id='audit_50' name='audit_radio' value='50' onclick="auditCheck('50')" />50% 
                        </div>    
                    </div>
                </form>
                <div id="auditCheckText" class="auditNote"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=auditProfitReq()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
    <script>
    
        var auditValChecked;
        function auditCheck(val) {
            auditValChecked = val;
            
            if(val == '0'){
                $('#auditCheckText').html("");
            }
            else if(val == '10'){
                $('#auditCheckText').html("10% deduct on daily profit for 90 days.");
            }
            else if(val == '25'){
                $('#auditCheckText').html("25% deduct on daily profit for 90 days.");
            }
            else if(val == '33'){
                $('#auditCheckText').html("33% deduct on daily profit for 90 days.");
            }
            else if(val == '50'){
                $('#auditCheckText').html("50% deduct on daily profit for 90 days.");
            }
            else {
                $('#auditCheckText').html("");
            }
        }   
    </script>
</div>

<div class="modal fade" id="transferEarnedWalletModal" tabindex="-1" role="dialog" aria-labelledby="profitModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            
            <div class="modal-header modalHeaderExchange">
                <h4>This will Transfer your Earned Investment.</h4>
            </div>
            <div class="modal-body">
                <!--<div class="col-md-12 mb-3">-->
                <!--    <div>-->
                        <!-- 1 -->
                <!--        <div class="checkboxWrapper">-->
                <!--            <label class="containerRadio">ARB-->
                <!--              <input type='radio' id='radio_earnedCurr' name='earnedRadioCurr' value='ARB' checked="checked" onclick="checkCurrEarned()" />-->
                <!--              <span class="radioCheckmark"></span>-->
                <!--            </label>-->
                <!--        </div>-->
                        <!-- 2 -->
                <!--        <div class="checkboxWrapper">-->
                <!--            <label class="containerRadio">ETH-->
                <!--              <input type='radio' id='radio_earnedCurr' name='earnedRadioCurr' value='ETH' onclick="checkCurrEarned()"/>-->
                <!--              <span class="radioCheckmark"></span>-->
                <!--            </label>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                
                <div id="earnedWalletArbDiv" class="col-md-12">
                    <div>
                        <input id="wdrawamtearned" onkeyup="wdrawamtearned(this.value);" class="form-control" type="number"  value='<?php echo $earned; ?>' min="1" max='<?php echo $earned; ?>'>
                    </div><br>
                    <form>
                        <div class="radioAutoRe">
                            <label>Please select the wallet: </label><br/>
                            
                            <div class="">
                                <!-- 1 -->
                                <div class="checkboxWrapper cbWF">
                                    <label class="containerRadio">System Wallet
                                      <input type='radio' id='radio_earned' name='earnedRadio' value='SystemWallet' checked="checked"/>
                                      <span class="radioCheckmark"></span>
                                    </label>
                                </div>
                                <!-- 2 -->
                                <div class="checkboxWrapper cbWF">
                                    <label class="containerRadio">Exchange Earned Wallet
                                      <input type='radio' id='radio_earned' name='earnedRadio' value='ExchangeWallet' />
                                      <span class="radioCheckmark"></span>
                                    </label>
                                </div>
                                <!-- 3 -->
                                <div class="checkboxWrapper cbWF">
                                    <label class="containerRadio">Vault
                                      <input type='radio' id='radio_earned' name='earnedRadio' value='Vault' />
                                      <span class="radioCheckmark"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                    </form>
                 
                    <br><br>
                    <button class="btn btn-success" onclick=TransferEarned()>Confirm</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
                
                <!--<div id="earnedWalletEthDiv" class="col-md-12 displayNone">-->
                <!--    <div>-->
                <!--        <input id="wdrawamtearnedeth" onkeyup="wdrawamtearnedeth(this.value);" class="form-control" type="number" value='<?php //echo $eth_earned; ?>' max='<?php //echo $eth_earned; ?>'>-->
                <!--    </div>-->
                 
                <!--    <br><br>-->
                <!--    <button class="btn btn-success" onclick=TransferEarnedETH()>Confirm</button>-->
                <!--    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>-->
                <!--</div>-->
                
            </div>
        </div>
        <script>
            // var earnedWalletCurr = 0;
            // function checkCurrEarned() {
            //     earnedWalletCurr = $('input[name="earnedRadioCurr"]:checked').val();
            //     if(earnedWalletCurr == "ARB") {
            //         $('#earnedWalletEthDiv').css("display", "none");
            //         $('#earnedWalletArbDiv').css("display", "block");
            //     }
            //     else if(earnedWalletCurr == "ETH") {
            //         $('#earnedWalletArbDiv').css("display", "none");
            //         $('#earnedWalletEthDiv').css("display", "block");
            //     }
            // }
        </script>
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
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">Auto Reinvest</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-4">
                <p><h5>You will not be able to deactivate it untill <span id="newdate"></span></h5></p>
                <form>
                    <div class="radioAutoRe">
                        <label>Please select the amount: </label><br/>
                        <input type='radio' id='radio_25' name='type' value='25' checked="checked" onclick="autoReCheck('25')" />25% 
                        <input type='radio' id='radio_50' name='type' value='50' onclick="autoReCheck('50')" />50% 
                        <input type='radio' id='radio_75' name='type' value='75' onclick="autoReCheck('75')" />75% 
                        <input type='radio' id='radio_100' name='type' value='100' onclick="autoReCheck('100')" />100% 
                    </div>
                </form>
                
                <div id="autoReCheck" class="checkbox displayNone">
                    <label><input type="checkbox" id="checkBoxReinvest">Activate Your Reinvest Bonus (ETH), bonus released after lock period.</label>
                </div>
                
                <form id="checkDaysDiv" class="displayNone">
                    <div class="radioAutoRe">
                        <label>Please select the Days: </label><br/>
                        <input type='radio' id='typeDays_30' name='typeDays' value='30' checked="checked" />30
                        <input type='radio' id='typeDays_60' name='typeDays' value='60' />60
                        <input type='radio' id='typeDays_90' name='typeDays' value='90' />90
                    </div>
                    <div id="amountBonusDiv"></div>
                </form>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=confirmReinvest()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
    <script>
        var percent = 0;
        var lock_reinvest = 0;
        var checkBoxCheck = 0;
        
        function confirmReinvest(){
            $('#reinvestAbotConfirmation').modal('hide');
            percent = $('input[name="type"]:checked').val();
            checkBoxCheck = $('input[type="checkbox"]:checked').val();
            
            if(percent == '25') {
                $.post( "<?php echo base_url(); ?>auto_reinvest", {percent: percent})
                  .done(function( data ) {
                    data = JSON.parse(data);
                    if(data.success == '1')
                    {
                        $('#apiResponseModal').modal('show');
                        $('#apiResponseDiv').html(data.msg);
                        $('.autoReinvestBtn').html("Deactivate Auto Reinvest");
                        setInterval(function(){ location.reload(); }, 2000);
                    } 
                    else if(data.error == '1')
                    {
                        $('#apiResponseModal').modal('show');
                        $('#apiResponseDiv').html(data.msg);
                    }
                });
            }
            else if(percent == '50' || percent == '75' ||percent == '100'){
                
                if(checkBoxCheck == "on") {
                    lock_reinvest = 1;
                }
                
                var radioDaysVal = $('input[name="typeDays"]:checked').val();
                if(radioDaysVal == '30' || radioDaysVal == '60' || radioDaysVal == '90') {
                    $.post( "<?php echo base_url(); ?>auto_reinvest", {percent: percent, reinvest_lock_days: radioDaysVal, lock_reinvest: lock_reinvest})
                      .done(function( data ) {
                        data = JSON.parse(data);
                        if(data.success == '1')
                        {
                            $('#apiResponseModal').modal('show');
                            $('#apiResponseDiv').html(data.msg);
                            $('.autoReinvestBtn').html("Deactivate Auto Reinvest");
                            setInterval(function(){ location.reload(); }, 3000);
                        } 
                        else if(data.error == '1')
                        {
                            $('#apiResponseModal').modal('show');
                            $('#apiResponseDiv').html(data.msg);
                        }
                    });
                }
                else {
                    $('#apiResponseModal').modal('show');
                    $('# ').html("Select the Right Value.");
                }
            }
            else {
                $('#apiResponseModal').modal('show');
                $('#apiResponseDiv').html("Select the Right Value.");
            }
        }
        var checkBoxValChecked;
        function autoReCheck(val) {
            checkBoxValChecked = val;
            if(val != '25') {
                $('#checkBoxReinvest').prop('checked', false); 
                $('#autoReCheck').css('display', 'block');
                $('#checkDaysDiv').css('display', 'none');
            }
            else {
                $('#checkBoxReinvest').prop('checked', false); 
                $('#autoReCheck').css('display', 'none');
                $('#checkDaysDiv').css('display', 'none');
            }
        }   
        
        $('input[type="checkbox"]').click(function(){
            if($(this).prop("checked") == true){
                $('#checkDaysDiv').css('display', 'block');
                if(checkBoxValChecked == "50") {
                    $('#amountBonusDiv').html("30 (Base) 60 (+10%) , 90 (+20%)");
                }
                else if(checkBoxValChecked == "75") {
                    $('#amountBonusDiv').html("30 (+10%) 60 (+20%) , 90 (+25%)");
                }
                else if(checkBoxValChecked == "100") {
                    $('#amountBonusDiv').html("30 (+20%) 60 (+25%) , 90 (+30%)");
                }
                else {
                    $('#amountBonusDiv').html("");
                }
            }
            else if($(this).prop("checked") == false){
                $('#checkDaysDiv').css('display', 'none');
            }
        });
    </script>
</div>

<div class="modal fade" id="ethPayoutModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">Activate ETH Payout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-4">
                <p><h5>Are you sure you want to activate ETH payout?</h5></p>
                
                <label><input id="ethPayoutReinvestCheck" type="checkbox"> Enabled Auto Reinvest (Minimum 25%)</label><br>
                <label><input type="checkbox" id="ethPayoutFeeCheck"> 25% ETH conversion fee. (Fee is based on the total profit paid, example: if you would have earned $60 in ARB today, you will get $45 in ETH instead automatically.)<br><br> You will get your first $10,000 active aBOT payout in ETH, all active aBOT balance over $10,000 will payout in ARB. <br>(If your aBOT is $20,000 you will get 50% ETH and 50% ARB, $50,000 you will get 20% ETH "$10,000" and 80% ARB "$40,000")</label>
                <div>(ETH will arrive in your System Wallet) <br><br> Hint: If you select 50% Reinvest ETH bonus, at the end of you term you get a nice ETH bonus that helps cover the ETH conversion fee. <br><br>You can deactivate this feature at anytime.<br><br>* Note that no fees are taken from any part of your payout beyond 10k. 25% fee only applies to the first part of the payment in ETH. The rest is paid in ARB and has no additional fee</div>
                <div id="showPayoutFeeError" class="text-danger" style="display:none;font-size: 16px;">You must agree on 25% conversion fee to activate your ETH payout.</div>
                <div id="showPayoutReinvestError" class="text-danger" style="display:none;font-size: 16px;">You must enable your Reinvest to activate your ETH payout.</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=activateEthPayout('activate')>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
    <script>
        function checkboxCheckedReinvest() {
            if(<?php echo $auto_reinvest;?> == 1) {
                $("#ethPayoutReinvestCheck").attr('checked', true);
            }
        }
         
    </script>
</div>

<div class="modal fade" id="ethPayoutModalDeactivate">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">Deactivate ETH Payout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-4">
                <p><h5>Are you sure to deactivate your ETH payout?</h5></p>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=activateEthPayout('deactivate')>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
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
    "valueField": "visits",
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

});
 
</script>