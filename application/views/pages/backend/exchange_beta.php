<?php
    $mtime = filemtime('/home/arbitrage/arbblock.com/test_beta/assets/backend/css/exchange_beta.css');
?>
<link href="<?php echo base_url()?>assets/backend/css/exchange_beta.css?v1=<?php echo $mtime; ?>" rel="stylesheet">

<?php
foreach($exData as $data){
    $activeArb = $data->activeArb;
    $activeEth = $data->activeEth;
}

foreach($code as $c){ 
	$allow_pin = $c['allow_pin'];
	$username = $c['u_username'];
}

if(isset($auto_buy_status)){
    $auto_buy_status = $auto_buy_status;
}else{
    $auto_buy_status = 0;
}

if(isset($auto_sell_status)){
    $auto_sell_status = $auto_sell_status;
}else{
    $auto_sell_status = 0;
}

if(!isset($allow_pin)){
    $allow_pin = 0;
}

?>

    <!--////////////////////////////////////////////////////// Charts /////////////////////////////////////////-->
    <div class="row depositBtnExch">
        <div class="col-md-12">
            <div id="container" style="min-width: 310px; height: 300px; margin: 0 auto"></div>
        </div>
    </div>
    <hr>
    <!--////////////////////////////////////////////////////// Wallet Button /////////////////////////////////////////-->
    <div class="row">
        <div class="col-md-7 walletBtnWrap">
            <div class="form-group col-12">
                <button id="exchangeWalletBtn" class="btn walletBtns active" onclick="selectedValue('exchangeWallet')">Exchange Wallet</button>
                <button id="exchangeEarnedWalletBtn" class="btn walletBtns" onclick="selectedValue('exchangeEarnedWallet')">Exchange Earned Wallet</button>
                <button id="aBotWalletBtn" class="btn walletBtns" onclick="selectedValue('aBotWallet')">Stop aBOT Wallet</button>
            </div>
        </div>
        
        <div id="exchangeToWalletDiv" class="col-md-3 text-center">
            <button class="btn btnMarginRightExc exWalletBtns" id="transferModalBtn" style="display:none" data-toggle="modal" data-target="#transferModal">Transfer To Wallet</button>
            <button class="btn btnMarginRightExc exWalletBtns" id="transferModalBtnAbot" style="display:none" data-toggle="modal" data-target="#transfer_aBOT_Modal">Transfer</button>
        </div>
        
        <div class="col-md-2">
            <div class="autobuyWrap">
                <button id="auto_buy" class="btn exWalletBtns">Auto Buy</button>
                <div class="autoBuyDropDown">
                    
                    <div class="">
                        <div class="autobuyCheckbox">
                            <span>Enable Auto Buy</span>
                            <label class="container1">
                                <?php
                                if($auto_buy_status == 1)
                                {
                                    echo '<input type="checkbox" id="checkboxAutoBuy" value="" checked>';
                                }
                                else 
                                    echo '<input type="checkbox" id="checkboxAutoBuy" value="">';
                                ?> 
                              <span class="checkmark"></span>
                            </label>
                        </div>
                        <div>
                            <label>TIME (Minutes)</label>
                            <div class="custom-select" >
                              <select id="autoBuyTime">
                                <option <?php if($auto_buy_status == 1 && $auto_buy_min == 20){ echo 'selected';}?> value="20">20</option>
                                <option <?php if($auto_buy_status == 1 && $auto_buy_min == 40){ echo 'selected';}?> value="40">40</option>
                                <option <?php if($auto_buy_status == 1 && $auto_buy_min == 60){ echo 'selected';}?> value="60">60</option>
                              </select>
                            </div>
                        </div>
                        <div>
                            <label>AMOUNT (ARB)</label>
                            <div class="custom-select">
                              <select id="autoBuyQuan">
                                <option <?php if($auto_buy_status == 1 && $auto_buy_amount == 50){ echo 'selected';}?> value="50">50</option>
                                <option <?php if($auto_buy_status == 1 && $auto_buy_amount == 100){ echo 'selected';}?> value="100">100</option>
                                <option <?php if($auto_buy_status == 1 && $auto_buy_amount == 150){ echo 'selected';}?> value="150">150</option>
                                <option <?php if($auto_buy_status == 1 && $auto_buy_amount == 200){ echo 'selected';}?> value="200">200</option>
                                <option <?php if($auto_buy_status == 1 && $auto_buy_amount == 250){ echo 'selected';}?> value="250">250</option>
                              </select>
                          </div>
                        </div>
                    </div>
                    <div class="pull-right">
                        <button class="btn btn-default btnASAVE" onclick="autoBuy('Buy')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--////////////////////////////////////////////////////// Sell / Order ARB /////////////////////////////////////////-->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="col-md-12">
                <div class="row exDivHeading textAlignCenter">
                    <div class="col-sm-6 textAlignLeft" style="width:50%;display:inline-block;">
                        <span id="arbValueActive"></span> ARB
                    </div>
                    <div class="col-sm-6 textAlignRight">
                       <span class="titledSpan">Sell ARB = 0.5% fee (i)</span>
                    </div>
                </div>
                <div class="row exDivBody">
                    <div class="col-lg-4 col-md-6 col-sm-6 fullwidth_579">
                        <div class="input-group">
                            <input name="amount" id="sellAmount" value="0" max="500" type="text" step=".01" class="input inputOrdExc form-control"  oninput="totalARB(this.value);">
                            <span class="input-group-btn">
                                <button class="btn inputOrdExc btn-secondary" type="button" id="getSellAmount"> ARB </button>
                            </span>
                        </div>
                        <b style="font-size: 11px;display: -webkit-inline-box;">(<span id="limitDollar"></span> ARB Sell Remaining Limit)</b>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 fullwidth_579">
                        <div class="input-group sellBuyOrderInp fullwidth_579">
                            <input name="price" id="sellPrice" type="text" step=".0001" class="input inputOrdExc form-control" value="0">
                            <span class="input-group-btn">
                                <button class="btn inputOrdExc btn-secondary" id="getSellPrice"> Price </button>
                            </span>
                        </div><br>
                        <b style="font-size: 11px;display: -webkit-inline-box;margin-left: 20px;">(10 MIN, 500 MAX ARB/ORDER)</b>
                    </div>
                    <div class="col-lg-4 col-md-6 margin_3px fullwidth_579">
                        <span class='hiddden_insmall custom_icon'>=</span>
                        <div class="input-group sellBuyOrderInp width_100">
                            <input type="text" class="input inputOrdExc form-control" id="sellTotal" value="0" readonly>
                            <span class="totalEth_Exchange">
                                (Total ETH)
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 margin_3px">
                        <button class="btn btn-block set_max_width_300px inputOrdExc btn-danger" data-toggle="modal" data-target="#submitSellOrder" onclick="sellSubmitValues()" type="button">Sell ARB</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="col-md-12">
                <div class="row exDivHeading textAlignCenter">
                    <div class="col-sm-6 textAlignLeft" style="width:50%;display:inline-block;">
                        $ <span id="actETH"></span> (<span style="font-size:12px" id="ethValueActive"></span> ETH)
                    </div>
                    <div class="col-sm-6 textAlignRight">
                       <span class="titledSpan">Buy ARB = 0.5% fee (i)</span>
                    </div>
                </div>
                <div class="row exDivBody">
                    <div class="col-lg-4 col-md-6 col-sm-6 fullwidth_579">
                        <div class="input-group">
                            <input name="amount" id="buyAmount" class="input inputOrdExc form-control" type="text" step=".0001" oninput="totalETH(this.value);" value="0">
                            <span class="input-group-btn">
                                <button class="btn inputOrdExc btn-secondary" type="button" id="getBuyAmount"> ARB </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 fullwidth_579">
                        <div class="input-group sellBuyOrderInp fullwidth_579">
                            <input name="price" id="buyPrice" class="input inputOrdExc form-control" type="text" step=".0001" value="0" min="0" oninput="totalETH(this.value);">
                            <span class="input-group-btn">
                                <button class="btn inputOrdExc btn-secondary" id="getBuyPrice"> Price </button>
                            </span>
                        </div><br>
                        <b style="font-size: 11px;display: -webkit-inline-box;margin-left: 20px;">(10 MIN ARB/ORDER)</b>
                    </div>
                    <div class="col-lg-4 col-md-6 margin_3px fullwidth_579">
                        <span class='hiddden_insmall custom_icon'>=</span>
                        <div class="input-group sellBuyOrderInp width_100">
                            <input type="text" class="input inputOrdExc form-control" id="buyTotal" value="0">
                            <span class="totalEth_Exchange">
                                (Total ETH)
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 margin_3px">
                        <button class="btn btn-block set_max_width_300px inputOrdExc btn-success" data-toggle="modal" data-target="#submitBuyOrder" onclick="buySubmitValues()" type="button">Buy ARB</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--////////////////////////////////////////////////////// SELL / BUY Orders /////////////////////////////////////////-->
    <div class="row imgLogoMarg">
        <div class="col-md-6">
            <div class="col-md-12">
                <div class="row exDivHeading">
                    <div class="col-sm-4" style="width:40%;display:inline-block;"><span class="fab fa-sellsy"></span> Sell Orders</div>
                    <!--<div class="col-sm-8 text-right titledSpan"> (Tip: Buying from this side gives you a 3% bonus!)</div>-->
                </div>
                <div class="row">
                    <div class="tableDivExchange">
                        <table class="table table-hover table-hover table-striped textAlignCenter">
                            <thead>
                            <tr>
                                <th>Price (ETH)</th>
                                <th>Amount (ARB)</th>
                                <th>Total (ETH)</th>
                                <th>Sum (ETH)</th>
                            </tr>
                            </thead>
                            <tbody id="sellTable" class="textAlignCenter">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12">
                <div class="row exDivHeading">
                    <div class="col-sm-6" style="width:40%;display:inline-block;"><span class="fa fa-credit-card"></span> Buy Orders</div>
                    <div class="col-sm-6 text-right titledSpan"></div>
                </div>
                <div class="row">
                    <div class="tableDivExchange">
                        <table class="table table-hover table-striped textAlignCenter">
                            <thead>
                            <tr>
                                <th>Price (ETH)</th>
                                <th>Amount (ARB)</th>
                                <th>Total (ETH)</th>
                                <th>Sum (ETH)</th>
                            </tr>
                            </thead>
                            <tbody id="buyTable" class="textAlignCenter">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--////////////////////////////////////////////////////// Market History /////////////////////////////////////////-->
    <div class="row imgLogoMarg">
        <div class="col-md-12">
            <div class="exDivHeading">
                <div class="col-md-12"><span class="fa fa-history"></span> Market History</div>
            </div>
            <div class="tableDivExchange">
                <table class="table table-hover table-striped textAlignCenter">
                    <thead>
                    <tr>
                        <th>Price (ETH)</th>
                        <th>Amount (ARB)</th>
                        <th>Timestamps</th>
                        <th>Type</th>
                        <th>Total </th>
                        <th>Sum</th>
                    </tr>
                    </thead>
                    <tbody id="marketHistory">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--////////////////////////////////////////////////////// Open Orders /////////////////////////////////////////-->
    <div class="row imgLogoMarg">
        <div class="col-md-12">
            <div class="exDivHeading">
                <div class="col-md-12"><span class="fa fa-list-ol"></span> Your Open Orders</div>
            </div>
            <div class="tableDivExchange">
                <table class="table table-hover table-hover table-striped textAlignCenter">
                    <thead>
                    <tr>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>Order Type</th>
                        <th>TimeStamp</th>
                        <th>Total</th>
                        <th>Cancel</th>
                    </tr>
                    </thead>
                    <tbody id="openTable" class="textAlignCenter">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--////////////////////////////////////////////////////// Close Orders /////////////////////////////////////////-->
    <div class="row imgLogoMarg">
        <div class="col-md-12">
            <div class="exDivHeading">
                <div class="col-md-12"><span class="fa fa-clone"></span> Your Order History</div>
            </div>
            <div class="tableDivExchange">
                <table class="table table-hover table-striped textAlignCenter">
                    <thead>
                    <tr>
                        <th>Price</th>
                        <th>Amount</th>
                        <th>Order Type</th>
                        <th>TimeStamp</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody id="closeTable" class="textAlignCenter">

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12">
            <div class="showAllRecord">
                <a href="<?php echo base_url(); ?>admin/history" style="color: #daa522;" onclick="histroyPageOrders()">Show All Record History</a>
            </div>
        </div>
    </div>

<!--////////////////////////////////////////////////////// Script /////////////////////////////////////////-->
<script>
 
    var arbValue;
    var ethValue;
    var actETH;
    var check_len;
    var cal_time = 'false';
    var tableBuyOrd = $("#buyTable");
    var tableSellOrd = $("#sellTable");
    var priceT;
    var amountT;
    var totalT;
    var priceTT;
    var amountTT;
    var totalTT;
    var coinEth = 1;
    var setonclickSell = true;
    var priceBuyArray = 0;
    var walletSelect = 0;
    var walSelected = 0;
    var limitDollar = 0;
    var ajaxBuyOrder = '';
    var ajaxSellOrder = '';
    var flagValue = 0;
    
    walletSelect = "exchangeWallet";
    
    function selectedValue(val){
        walletSelect = val;
        getBalnaceARBETH();
    }

    function getBalnaceARBETH(){
        $.get("<?php echo base_url(); ?>exchange_wallet_balance", function( data ) 
        {
            if(data == "needlogin")
            {
                window.location.replace("<?php echo base_url(); ?>logout");
            }
            else
            {
                if(walletSelect == "exchangeWallet")
                {
                    data = $.parseJSON(data);
                    arbValue = data.activeArb;
                    ethValue = data.activeEth;
                    limitDollar = data.ex_limit;
                    document.getElementById("arbValueActive").innerHTML = arbValue;
                    document.getElementById("ethValueActive").innerHTML = ethValue;
                    document.getElementById("limitDollar").innerHTML = limitDollar;
                    document.getElementById("newSnackBarARB").innerHTML = arbValue;
                    document.getElementById("newSnackBarETH").innerHTML = ethValue;
                    
                    $('#transferModalBtn').css('display', 'block');
                    $('#transferModalBtnAbot').css('display', 'none');
                    
                    $('#actETH').text((aeth * ethValue).toFixed(2));
                    
                    $("#exchangeWalletBtn").addClass("active");
                    $("#exchangeEarnedWalletBtn, #aBotWalletBtn").removeClass("active");
                }
                else if(walletSelect == "exchangeEarnedWallet")
                {
                    data = $.parseJSON(data);
                    arbValue = data.activeArb_earned;
                    ethValue = data.activeEth;
                    limitDollar = data.ex_er_limit;
                    document.getElementById("arbValueActive").innerHTML = arbValue;
                    document.getElementById("ethValueActive").innerHTML = ethValue;
                    document.getElementById("limitDollar").innerHTML = limitDollar;
                    document.getElementById("newSnackBarARB").innerHTML = arbValue;
                    document.getElementById("newSnackBarETH").innerHTML = ethValue;
                    
                    $('#transferModalBtn').css('display', 'block');
                    $('#transferModalBtnAbot').css('display', 'none');
                    
                    $('#actETH').text((aeth * ethValue).toFixed(2));
                    
                    $("#exchangeEarnedWalletBtn").addClass("active");
                    $("#exchangeWalletBtn, #aBotWalletBtn").removeClass("active");
                }
                else if(walletSelect == "aBotWallet")
                {
                    data = $.parseJSON(data);
                    arbValue = data.activeArb_stop_abot;
                    ethValue = data.activeEth;
                    limitDollar = data.ex_limit;
                    
                    document.getElementById("arbValueActive").innerHTML = arbValue;
                    document.getElementById("ethValueActive").innerHTML = ethValue;
                    document.getElementById("limitDollar").innerHTML = limitDollar;
                    document.getElementById("newSnackBarARB").innerHTML = arbValue;
                    document.getElementById("newSnackBarETH").innerHTML = ethValue;
                    
                    $('#transferModalBtn').css('display', 'none');
                    $('#transferModalBtnAbot').css('display', 'block');
                    
                    $('#actETH').text((aeth * ethValue).toFixed(2));
                    
                    $("#aBotWalletBtn").addClass("active");
                    $("#exchangeWalletBtn, #exchangeEarnedWalletBtn").removeClass("active");
                }
            } 
        });
    }
    getBalnaceARBETH();
    window.setInterval( function() {getBalnaceARBETH();}, 60000);
    
    /////////////////////////////////////////////////////////////    Stop Abot Price ////////////////////////////////////////
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

    <!--//////////////////////////////////////////////////////    Transfer Wallet Amount    /////////////////////////////////////////-->
    function transferWallet () {
        $('#transferModal').modal('hide');
        var amount = parseFloat($("#withdrawValue").val());
        var page = $("#pagesOption").val();
        
        if(page == "ARB"){
            if(amount == 0)
            {
                $('#ErrorTextGeneric').html("Please Enter Some Value.");
                $('#ErrorModalGeneric').modal('show');
            }
            else if(arbValue >= amount)
            {
                if(walletSelect == "exchangeWallet")
                {    
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>exchange_to_wallet",
                        data: {
                            currency : page,
                            amount : amount
                        },
                        success: function(data){
                            data = JSON.parse(data);
                            if(data.success == 1)
                            {
                                $('#SuccessModalGeneric').modal('show');
                                $('#SuccessTextGeneric').html(data.msg);
                                location.reload();
                            }
                            else if(data.error == 1)
                            {
                                $('#ErrorModalGeneric').modal('show');
                                $('#ErrorTextGeneric').html(data.msg);
                            }
                        }
                    });
                }
                else if(walletSelect == "exchangeEarnedWallet") {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>exEarned_to_system_wallet",
                        data: {
                            exearned_transfer_amount : amount
                        },
                        success: function(data){
                            data = JSON.parse(data);
                            if(data.success == 1)
                            {
                                $('#SuccessModalGeneric').modal('show');
                                $('#SuccessTextGeneric').html(data.msg);
                                location.reload();
                            }
                            else if(data.error == 1)
                            {
                                $('#ErrorModalGeneric').modal('show');
                                $('#ErrorTextGeneric').html(data.msg);
                            }
                        }
                    });
                }
                else if(walletSelect == "aBotWallet")
                {
                    $('#ErrorModalGeneric').modal('show');
                    $('#ErrorTextGeneric').html("Select Exchange Wallet to transfer your amount to Main Wallet.");
                }
            } 
            else {
                $('#ErrorModalGeneric').modal('show');
                $('#ErrorTextGeneric').html("You don't have sufficient balance.");
            }
        }
        else if (page == "ETH"){
            if(amount == 0)
            {
                $('#ErrorTextGeneric').html("Please Enter Some Value.");
                $('#ErrorModalGeneric').modal('show');
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>exchange_to_wallet",
                    data: {
                        currency : page,
                        amount : amount
                    },
                    success: function(data){
                        data = JSON.parse(data);
                        if(data.success == 1)
                        {
                            $('#SuccessModalGeneric').modal('show');
                            $('#SuccessTextGeneric').html(data.msg);
                            location.reload();
                        }
                        else if(data.error == 1)
                        {
                            $('#ErrorModalGeneric').modal('show');
                            $('#ErrorTextGeneric').html(data.msg);
                        }
                    }
                });
            }
        }
        else if (page == ""){
            $('#ErrorTextGeneric').html("Your Transfer is Failed.");
            $('#ErrorModalGeneric').modal('show');
        }
    }
    
    function transferWalletABot ()
    {
        $('#transfer_aBOT_Modal').modal('hide');
        var amountAbot = document.getElementById("withdrawValueAbot").value;
        var checkStopAbotWall = document.getElementById('pagesOptionStopAbot').value;
        
        if(amountAbot <= 0)
        {
            $('#ErrorTextGeneric').html("Please Enter Some Value.");
            $('#ErrorModalGeneric').modal('show');
        }
        else
        {
            if(walletSelect == "exchangeWallet" || walletSelect == "exchangeEarnedWallet")
            {    
                $('#ErrorModalGeneric').modal('show');
                $('#ErrorTextGeneric').html("Select Stop aBOT Wallet to transfer your amount to aBOT.");
            }
            else if(walletSelect == "aBotWallet")
            {
                if(checkStopAbotWall == "stopAbot") {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>stop_abot_wallet_to_abot",
                        data: {
                            amount : amountAbot
                        },
                        success: function(data){
                            data = JSON.parse(data);
                            if(data.success == 1)
                            {
                                $('#SuccessModalGeneric').modal('show');
                                $('#SuccessTextGeneric').html(data.msg);
                                location.reload();
                            }
                            else if(data.error == 1)
                            {
                                $('#ErrorModalGeneric').modal('show');
                                $('#ErrorTextGeneric').html(data.msg);
                            }
                        }
                    });
                }
                // else if(checkStopAbotWall == "externalWallet") {
                //     $.ajax({
                //         type: "POST",
                //         url: "<?php //echo base_url(); ?>stop_abot_wallet_to_external_wallet",
                //         data: {
                //             amount : amountAbot
                //         },
                //         success: function(data){
                //             data = JSON.parse(data);
                //             if(data.success == 1)
                //             {
                //                 $('#SuccessModalGeneric').modal('show');
                //                 $('#SuccessTextGeneric').html(data.msg);
                //                 location.reload();
                //             }
                //             else if(data.error == 1)
                //             {
                //                 $('#ErrorModalGeneric').modal('show');
                //                 $('#ErrorTextGeneric').html(data.msg);
                //             }
                //         }
                //     });
                // }
                else {
                    $('#ErrorModalGeneric').modal('show');
                    $('#ErrorTextGeneric').html("Select an option.");
                }
            }
            else {
                $('#ErrorTextGeneric').html("Your Transfer is Failed.");
                $('#ErrorModalGeneric').modal('show');  
            }
        }
    }
           
    <!--////////////////////////////////////////////////////// OnChange / Keyup Buy /////////////////////////////////////////-->
    $("#buyPrice").on('change', function (){
        buyprice = $("#buyPrice").val();
        this.value = this.value.replace(/[^0-9\.]/g,'');
        
        if($("#buyTotal").val() != 0) {
            $("#buyAmount").val($("#buyTotal").val() / buyprice );
        }
    });

    $("#buyPrice").on('keyup', function (){
        buyprice = $("#buyPrice").val();
        
        if($("#buyTotal").val() != 0) {
            $("#buyAmount").val($("#buyTotal").val() / buyprice );
        }
    });

    $("#buyAmount").on('change', function (){
        buyamount = $("#buyAmount").val();
        buyprice = $("#buyPrice").val();
        this.value = this.value.replace(/[^0-9\.]/g,'');
        
        var minArbSell = parseInt($(this).attr('min'));
        if ($(this).val() < minArbSell)
        {
            $(this).val(minArbSell);
        }       
        
        $("#buyAmount").val(parseFloat($("#buyAmount").val()).toFixed(8)).trigger('keyup'); //need in onchange
        if( (buyamount * buyprice) > ethValue ){
            $("#buyPrice").val( ethValue / buyamount);
        }
        
        $("#buyTotal").val($("#buyPrice").val() * buyamount );
    });
    
    $("#buyAmount").on('keyup', function (){
        buyamount = $("#buyAmount").val();
        buyprice = $("#buyPrice").val();
        
        var minArbSell = parseInt($(this).attr('min'));
        if ($(this).val() < minArbSell)
        {
            $(this).val(minArbSell);
        } 
        
        if( (buyamount * buyprice) > ethValue ){
            $("#buyPrice").val( ethValue / buyamount);
        }
        $("#buyTotal").val($("#buyPrice").val() * buyamount );

    });
    
    $("#buyTotal").on('change', function (){
        buyamount = $("#buyAmount").val();
        buyprice = $("#buyPrice").val();

        if(parseFloat($("#buyTotal").val()) > ethValue) {
            $(this).val(ethValue);
        } 
        this.value = this.value.replace(/[^0-9\.]/g,'');
        $("#buyAmount").val($("#buyTotal").val() / buyprice );
    });
    
    $("#buyTotal").on('keyup', function (){
        buyamount = $("#buyAmount").val();
        buyprice = $("#buyPrice").val();

        if(parseFloat($("#buyTotal").val()) > ethValue) {
            $(this).val(ethValue);
        } 
        
        $("#buyAmount").val($("#buyTotal").val() / buyprice );
    });

           
           <!--////////////////////////////////////////////////////// OnChange / Keyup Sell /////////////////////////////////////////-->
    $("#sellPrice").on('keyup', function (){
        $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());
    });
    
    $("#sellPrice").on('change', function (){
        this.value = this.value.replace(/[^0-9\.]/g,'');
        $("#sellPrice").val(parseFloat($("#sellPrice").val()).toFixed(8)).trigger('keyup');
        $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());
    });

    $("#sellAmount").on('change', function (){
        this.value = this.value.replace(/[^0-9\.]/g,'');
        
        var maxArbSell = parseInt($(this).attr('max'));
        var minArbSell = parseInt($(this).attr('min'));
        if ($(this).val() > maxArbSell)
        {
            $(this).val(maxArbSell);
        }
        else if ($(this).val() < minArbSell)
        {
            $(this).val(minArbSell);
        }       
       
        $("#sellAmount").val(parseFloat($("#sellAmount").val()).toFixed(8)).trigger('keyup');
        $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());
    });
    
    $("#sellAmount").on('keyup', function (){
        
        var maxArbSell = parseInt($(this).attr('max'));
        var minArbSell = parseInt($(this).attr('min'));
        if ($(this).val() > maxArbSell)
        {
            $(this).val(maxArbSell);
        }
        else if ($(this).val() < minArbSell)
        {
            $(this).val(minArbSell);
        }
        
        $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());
    });

           
    <!--//////////////////////////////////////////////////////      Click To Get Value Buttons      /////////////////////////////////////////-->
    function clickSellTr(priceT,amountT,totalT){

        priceT = priceT;
        amountT = amountT;
        totalT = totalT;

        $('#buyAmount').val(amountT).trigger('change');
        $('#buyPrice').val(priceT).trigger('change');
    };

    function clickBuyTr(priceT,amountT) {
        priceTT = parseFloat(priceT);
        amountTT = parseFloat(amountT);

        if( arbValue >=  amountTT){
            $('#sellPrice').val(priceTT).trigger('change');
            $('#sellAmount').val(amountTT).trigger('change');
        }
        else {
            $(".snackbar").addClass("show");
            setTimeout(function(){ $(".snackbar").removeClass("show"); }, 3000);
        }
    };

    function converEth(){
        $.get( "https://api.coinmarketcap.com/v2/ticker/1027/")
            .done(function( data ) {
                aeth = data.data.quotes.USD.price;
                //aeth = aeth.toFixed(2);
                coinEth = aeth;
            });
    }
    converEth();

    function sellValues_change()
    {
        $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());
        var buyAmountArbChange = parseFloat($('#sellAmount').val());
    }

    function buyValues_change()
    {
        $('#buyTotal').val($('#buyAmount').val() * $('#buyPrice').val());
        var buyPriceEthChange = parseFloat($('#buyTotal').val());
    }

    /////////////////////////////////////////////////////////////////////OrderBook///////////////////////////////////////////////////////////////////
    function getOrderBook()
    {
        var orderBook = [];
        var sellArray = [];
        var buyArray = [];
        var openOrders = [];
        var closeOrders = [];
        var marketHistory = [];
        var tds = "";
        var tdss = "";
        var tdh = "";
        var total = 0;
        var totalPreSell = 0;
        var totalPreBuy = 0;
        var totalPreMark = 0;
        var totalPrice = 0;
        
        var sum = 0;

        $.get("<?php echo base_url(); ?>orderBook", function( data )
        {

            orderBook = JSON.parse(data);
            jQuery('#sellTable,#buyTable').empty();

            sellArray = orderBook.sellArray;
            buyArray = orderBook.buyArray;
            openOrders = orderBook.openOrders;
            closeOrders = orderBook.closeOrders;
            marketHistory = orderBook.marketHistory;

            /////////////////////////////////////////////////////////////////////Sell OrderBook///////////////////////////////////////////////////////////////////
            sellArray.sort(function(a, b) {
                if (a.price == b.price) {
                    return a.row == b.row ? 0 : +a.row > +b.row ? 1 : -1;
                }
                return +a.price > +b.price ? 1 : -1;
            });

            $.each(sellArray, function(i)
            {
                total = 0;
                
                price = sellArray[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT);
                //priceT = priceT.replace(/\.0+$/,'');
                amount = sellArray[i].amount;
                amountT = parseFloat(amount);
                amountT = Math.abs(amountT);
                //amountT = amountT.replace(/\.0+$/,'');
                total = price * amount;
                total = total.toFixed(8);
                totalPrice = total;
                totalPreSell += parseFloat(totalPrice);
                //totalPreSell = totalPreSell.toFixed(8); 
                if(i == 0){ $("#getBuyMarketValue").click(function(){ clickSellTr(sellArray[0].price , sellArray[0].amount ); }); }
                $('#sellTable').append("<tr onclick='clickSellTr(" + priceT + "," + amountT + ");'><td>" + priceT + "</td><td>" + amountT + "</td><td>" + total + "</td><td>" + totalPreSell.toFixed(6) + "</td></tr>");
            });

            /////////////////////////////////////////////////////////////////////Buy OrderBook///////////////////////////////////////////////////////////////////
            buyArray.sort(function(a, b) {
                if (a.price == b.price) {
                    return a.row == b.row ? 0 : +a.row > +b.row ? 1 : -1;
                }
                return +a.price < +b.price ? 1 : -1;
            });

            $.each(buyArray, function(i)
            {
                total = 0;
                
                price = buyArray[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT)
                //priceT = priceT.replace(/\.0+$/,'');
                amount = buyArray[i].amount;
                amountT = parseFloat(amount);
                amountT = Math.abs(amountT)
                //amountT = amountT.replace(/\.0+$/,'');
                total = price * amount;
                total = total.toFixed(6);
                totalPrice = total;
                totalPreBuy += parseFloat(totalPrice);
                
                priceBuyArray = buyArray[0].price;
                $('#buyTable').append("<tr><td>" + priceT + "</td><td>" + amountT + "</td><td>" + total + "</td><td>" + totalPreBuy.toFixed(6) + "</td></tr>");
            });

            if(setonclickSell){
                $("#getSellMarketValue").click(function(){ clickBuyTr(buyArray[0].price , buyArray[0].amount );$('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val()); });
                setonclickSell = false;
            }
            /////////////////////////////////////////////////////////////////////Sell Price Amount///////////////////////////////////////////////////////////////////

            $('#getSellAmount').click(function ()
            {
                var sellAmountInput = buyArray[0].amount;
                sellAmountInputFloat = parseFloat(sellAmountInput);
                sellAmountInputFloat = Math.abs(sellAmountInputFloat);

                $('#sellAmount').val(sellAmountInputFloat);
                $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());
            });
            $('#getSellPrice').click(function ()
            {
                var sellPriceInput = buyArray[0].price;
                
                sellPriceInputFloat = parseFloat(arb_eth_p);
                sellPriceInputFloat = Math.abs(sellPriceInputFloat);
                
                sellPriceInputFloat1 = Math.abs((sellPriceInputFloat*10)/100);
                
                $('#sellPrice').val(sellPriceInputFloat + sellPriceInputFloat1);
                $('#sellTotal').val($('#sellAmount').val() * $('#sellPrice').val());

            });

            /////////////////////////////////////////////////////////////////////Buy Price Amount///////////////////////////////////////////////////////////////////

            $('#getBuyAmount').click(function ()
            {
                var buyAmountInput = sellArray[0].amount;
                buyAmountInputFloat = parseFloat(buyAmountInput);
                buyAmountInputFloat = Math.abs(buyAmountInputFloat);
                $('#buyAmount').val(buyAmountInputFloat);
                $('#buyTotal').val($('#buyAmount').val() * $('#buyPrice').val());

            });
            $('#getBuyPrice').click(function ()
            {
                var buyPriceInput = sellArray[0].price;
                buyPriceInputFloat = parseFloat(buyPriceInput);
                buyPriceInputFloat = Math.abs(buyPriceInputFloat);

                $('#buyPrice').val(buyPriceInputFloat);
                $('#buyTotal').val($('#buyAmount').val() * $('#buyPrice').val());
            });

            /////////////////////////////////////////////////////////////////////Open Orders///////////////////////////////////////////////////////////////////
            $.each(openOrders, function(i)
            {
                price = openOrders[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT);
                //priceT = priceT.replace(/\.0+$/,'');
                amount = openOrders[i].amount;
                amountT = parseFloat(amount);
                amountT = Math.abs(amountT);
                //amountT = amountT.replace(/\.0+$/,'');
                total = price * amount;
                total = total.toFixed(8);
                order = openOrders[i].order_type;
                date = openOrders[i].created_at;
                orderID = openOrders[i].id;

                tds += "<tr><td>" + priceT + "</td><td>" + amountT + "</td><td>" + order + "</td><td>" + date + "</td><td>" + total + "</td><td><div style='cursor: pointer' class='fa fa-times text-danger' onclick='cancelButton(" + orderID + ");'></div></td></tr>";
            });
            $('#openTable').html(tds);

            /////////////////////////////////////////////////////////////////////Close Orders///////////////////////////////////////////////////////////////////
            $.each(closeOrders, function(i)
            {
                total = 0;
                
                price = closeOrders[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT);
                //priceT = priceT.replace(/\.0+$/,'');
                amount = closeOrders[i].amount;
                amountT = parseFloat(amount);
                amountT = Math.abs(amountT);
                //amountT = amountT.replace(/\.0+$/,'');
                total = price * amount;
                total = total.toFixed(8);
                order = closeOrders[i].order_type;
                date = closeOrders[i].created_at;
                remark = closeOrders[i].remark;

                if(remark == "")
                {
                    re = "<i class='fa fa-check text-success'> </i>";
                }
                else
                {
                    re = "<i class='fa fa-ban text-danger'> </i>";
                }

                tdss += "<tr><td>" + priceT + "</td><td>" + amountT + "</td><td>" + order + "</td><td>" + date + "</td><td>" + total + "</td><td>" + re + "</td></tr>";
            });
            $('#closeTable').html(function (){
                if(check_len < closeOrders.length && closeOrders[0].remark == '' && cal_time == 'true'){
                    check_len = closeOrders.length;
                    if(closeOrders[0].remark == '')
                    {
                        orderSnackbar();
                    }
                    else if(closeOrders[0].remark == 'cancel')
                    {
                        orderCancelSnackbar();
                    }
                }
                else{
                    check_len = closeOrders.length;
                    cal_time = 'true';
                }

                return tdss;
            });
            
            /////////////////////////////////////////////////////////////////////Market History///////////////////////////////////////////////////////////////////
            $.each(marketHistory , function(i)
            {
                total = 0;
               
                price = marketHistory[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT);
                //priceT = priceT.replace(/\.0+$/,'');
                volume = marketHistory[i].amount;
                volumeT = parseFloat(volume);
                volumeT = Math.abs(volumeT);
                //volumeT = volumeT.replace(/\.0+$/,'');

                sum =  price * volume;
                total += sum;
                sum = sum.toFixed(8);

                time = marketHistory[i].created_at;
                mode = marketHistory[i].order_type;
                
                totalPrice = sum;
                if(mode == 'Buy'){
                    totalPreMark += parseFloat(totalPrice);
                    tdh += "<tr><td>" + priceT + "</td><td>" + volumeT + "</td><td>" + time + "</td><td>" + 'Trade' + "</td><td>" + sum + "</td><td>" + totalPreMark + "</td><td>";
                }  
            });
            $('#marketHistory').html(tdh);
        });
    }
    getOrderBook();
    window.setInterval( function() {getOrderBook();}, 30000);

    <!--//////////////////////////////////////////////////////       Sell Order Buttons      /////////////////////////////////////////-->
    function submitSellOrder()
    {
        $('#submitSellOrder').modal('hide');    
                
        sellAmount_check = parseFloat($('#sellAmount').val());
        // if($('#sellPrice').val() < priceBuyArray)
        // {
        //     $('#ErrorTextGeneric').html("You can't sell less than " + priceBuyArray);
        //     $('#ErrorModalGeneric').modal('show');
        // }
        
        if ($('#sellAmount').val()  < 10 && $('#sellAmount').val()  > 500 && $('#sellPrice').val() < 0.0001 )
        {
            $('#ErrorTextGeneric').html("Enter a valid amount please.");
            $('#ErrorModalGeneric').modal('show');
        }
        else if($('#sellPrice').val() < 0.0001 )
        {
            $('#ErrorTextGeneric').html("Price should be greater.");
            $('#ErrorModalGeneric').modal('show');
        }
        else if($('#sellAmount').val()  < 10)
        {
            $('#ErrorTextGeneric').html("You can't sell less than 10 ARB");
            $('#ErrorModalGeneric').modal('show');
        }
        else if($('#sellAmount').val()  > 500)
        {
            $('#ErrorTextGeneric').html("You can't sell more than 500 ARB");
            $('#ErrorModalGeneric').modal('show');
        }
        else if (sellAmount_check  > arbValue)
        {
            $('#ErrorTextGeneric').html("You don't have Sufficient Amount of ARB");
            $('#ErrorModalGeneric').modal('show');
        }
        else
        {
            // $('#ErrorModalGeneric').modal('show');
            // $('#ErrorTextGeneric').html("Dear users, we are installing an exchange update, approximate downtime is 30 minutes. Thank you for understanding.");
            
            if(walletSelect =="exchangeWallet") {
                walSelected = 'exchange';
            } 
            else if(walletSelect =="exchangeEarnedWallet") {
                walSelected = 'exchange_earned';
            }
            else if(walletSelect =="aBotWallet") {
                walSelected = 'stop_abot';
            }
            ajaxSellOrder = '<?php echo base_url(); ?>saveOrder';
            sellOrderReq(ajaxSellOrder, sellAmount_check, $('#sellPrice').val(), walSelected, flagValue);
        }   
    }
    
    <!--//////////////////////////////////////////////////////       Buy Order Buttons      /////////////////////////////////////////-->

    function submitBuyOrder()
    {
        $('#submitBuyOrder').modal('hide');
        
        buyPrice_check = parseFloat($('#buyPrice').val());
        if ($('#buyAmount').val()  < 10 && $('#buyPrice').val()  < 0.0001)
        {
            $('#ErrorTextGeneric').html("Please Enter a Higher Value.");
            $('#ErrorModalGeneric').modal('show');
        }
        else if ($('#buyAmount').val()  < 10)
        {
            $('#ErrorTextGeneric').html("Amount should be more than 10 ARB");
            $('#ErrorModalGeneric').modal('show');
        }
        else if ($('#buyPrice').val() < 0.0001)
        {
            $('#ErrorTextGeneric').html("Price should be greater than 0.0001");
            $('#ErrorModalGeneric').modal('show');
        }
        else if (buyPrice_check  > ethValue)
        {
            $('#ErrorTextGeneric').html("You don't have Sufficient Amount of Ethereum");
            $('#ErrorModalGeneric').modal('show');
        }
        else
        {
            // $('#ErrorModalGeneric').modal('show');
            // $('#ErrorTextGeneric').html("Dear users, we are installing an exchange update, approximate downtime is 30 minutes. Thank you for understanding.");
            
            ajaxBuyOrder = '<?php echo base_url(); ?>saveOrder';
            buyOrderReq(ajaxBuyOrder, $('#buyAmount').val(), buyPrice_check);
        }
    }

    function orderSnackbar(){
        var x = document.getElementById("snackbarOrder");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    function orderCancelSnackbar(){
        var x = document.getElementById("snackbarOrderCancel");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }

    function cancelButton(id){
        if(confirm("Do you want to cancel this order?"))
        {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>close_order",
                data: {
                    order_id: id
                },
                success: function(data){
                    data = JSON.parse(data);
                    if(data.success == 1)
                    {
                        getOrderBook();
                        getBalnaceARBETH();
                    }
                    else if(data.error == 1)
                    {
                        $('#ErrorModalGeneric').modal('show');
                        $('#ErrorTextGeneric').html(data.msg);
                    }
                }
            });
        }
    }

    function totalETH(value)
    {
        value = parseInt(value);
        if(value > ethValue)
        {
            $('#buyTotal').val( $('#buyPrice').val() /  $('#buyAmount').val());
        }
    }

    function totalARB(value)
    {
        value = parseInt(value);
        if(value > arbValue){
            $('#sellAmount').val(arbValue);
        }
    }
    
    function histroyPageOrders() {
        localStorage.setItem('casheData_ExchangeOrders','Orders');
    }
    
    
    <!--////////////////////////////////////////////////////// Auto Buy / Sell /////////////////////////////////////////-->
    var autoBuyQuantity = 0;
    var autoBuyRadio = 0;
    
    var autoSellQuantity = 0;
    var autoSellRadio = 0;
    
    function autoBuy(checkbox) 
    {
        if(<?php echo $allow_pin ;?> == 1)
        {
            autoBuyQuantity = $('#autoBuyQuan option:selected').text();
            autoBuyRadio = $('#autoBuyTime option:selected').text();
            
            if($('#checkboxAutoBuy:checkbox:checked').length > 0 )
            {
                $('#save2faAutoBuyModal').modal('show');
                $('#save2faAutoHidden').val(checkbox);
            }
            else
            {
                if(<?php echo $auto_buy_status ;?> == 1)
                {
                    $('#deAutoBuy').modal('show');
                    $('#inputAutoDeActivateHidden').val(checkbox);
                }
                else
                {
                    alert("Enable Auto Buy First.");
                }    
            }
        }
        else
        {
            
            $('#ErrorTextGeneric').html("Please Activate your 2FA first.");
            $('#ErrorModalGeneric').modal('show');
        }
    }
    
    function save2FaAutoBuy() {
        
        var checkAutoTrade = $('#save2faAutoHidden').val();
        $('#save2faAutoBuyModal').modal('hide');
        var authenticationCodeAutoBuy = $('#authenticationCodeAutoBuy').val();
        
        $.post( "<?php echo base_url(); ?>verify2fa", {code:authenticationCodeAutoBuy})
        .done(function( data ) {
            if(data == "true") {
                if(checkAutoTrade == "Buy")
                {
                    $.post( "<?php echo base_url(); ?>activate_auto_buy", {amount:autoBuyQuantity, min:autoBuyRadio})
                    .done(function( data ) {
                        if(data == 'activated')
                        {
                            $('#SuccessTextGeneric').html("Your Auto Trade Is Activate Now.");
                            $('#SuccessModalGeneric').modal('show');
                            setInterval(function(){ location.reload(); }, 3000);
                        }
                    });
                }
                else if(checkAutoTrade == "Sell") {
                    $.post( "<?php echo base_url(); ?>activate_auto_sell", {amount:autoSellQuantity, min:autoSellRadio})
                    .done(function( data ) {
                        if(data == 'activated')
                        {
                            $('#SuccessTextGeneric').html("Your Auto Trade Is Activate Now.");
                            $('#SuccessModalGeneric').modal('show');
                            setInterval(function(){ location.reload(); }, 3000);
                        }
                    });
                }
            }
            else 
            {
                alert("Wrong Key");
            }
        });        
    }
    
    function deAutoBuy() {
        var checkDecactiveAutoTrade = $('#inputAutoDeActivateHidden').val();
        $('#deAutoBuy').modal('hide');
        var deactiCodeAutoBuy = $('#deactiCodeAutoBuy').val();
        
        $.post( "<?php echo base_url(); ?>verify2fa", {code:deactiCodeAutoBuy})
        .done(function( data ) {
            if(data == "true")
            {
                if(checkDecactiveAutoTrade == "Buy") {
                    $.get("<?php echo base_url(); ?>deactivate_auto_buy", function( data ) {
                        if(data == "deactivated")
                        {
                            $('#SuccessTextGeneric').html("Successfully Deactivated.");
                            $('#SuccessModalGeneric').modal('show');
                            setInterval(function(){ location.reload(); }, 3000);
                        }
                    });
                }
                else if(checkDecactiveAutoTrade == "Sell") {
                    $.get("<?php echo base_url(); ?>deactivate_auto_sell", function( data ) {
                        if(data == "deactivated")
                        {
                            $('#SuccessTextGeneric').html("Successfully Deactivated.");
                            $('#SuccessModalGeneric').modal('show');
                            setInterval(function(){ location.reload(); }, 3000);
                        }
                    });
                }
            }
            else
            {
                alert("Wrong Key");
            }
        });        
    }
</script>

<!--////////////////////////////////////////////////////// Modal Divs /////////////////////////////////////////-->

<div class="modal fade" id="save2faAutoBuyModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p><h3>Enter your Google 2FA Pin for Auto Trade.</h3></p>
                    <input type="hidden" id="save2faAutoHidden">
                     <div class="col-md-12">
                        <input id="authenticationCodeAutoBuy" class="form-control" type="number" placeholder="Enter the Google 2FA pin code for Auto Trade.">
                        <br><br>
                        <button class="btn btn-success" onclick=save2FaAutoBuy()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deAutoBuy">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p><h3>Enter your Google 2FA Pin for Deactivate your Auto Trade.</h3></p>
                    <input type="hidden" id="inputAutoDeActivateHidden">
                     <div class="col-md-12">
                        <input id="deactiCodeAutoBuy" class="form-control" type="number" placeholder="Enter the Google 2FA pin code for Deactivate your Auto Trade.">
                        <br><br>
                        <button class="btn btn-success" onclick=deAutoBuy()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="transferModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">Transfer your ARB / ETH into your main Wallet.</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-5 pb-5">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label for="pages-option" class="col-4 col-form-label"><h5>Transfer: </h5></label>
                        <div class="col-8">
                            <select class="form-control" id="pagesOption" onchange="changeValue();">
                                <option value="" selected>Select One</option>
                                <option id="arbInpValue" value="ARB">ARB</option>
                                <option id="ethInpValue" value="ETH">ETH</option>
                            </select>
                        </div>
                    </div>
                    <h5> Withdraw Amount: </h5>
                    <input id="withdrawValue" class="form-control" > <!-- onkeyup="valueConfirmation();" -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=transferWallet()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            
            <script>
                $('#transferModalBtn').click(function() {
                    $('#pagesOption').val("");
                    $('#withdrawValue').val("");
                });

                jQuery('#withdrawValue').keyup(function () {
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                });

                function changeValue(){
                    var option = document.getElementById('pagesOption').value;
                    if(option=="ETH")
                    {
                        document.getElementById('withdrawValue').value=ethValue;

                    }
                    else if (option=="ARB")
                    {
                        document.getElementById('withdrawValue').value=arbValue;
                    }
                    else
                    {
                        document.getElementById('withdrawValue').value="";
                    }
                }

                $( "#withdrawValue" ).keyup(function() {
                    var option = document.getElementById('pagesOption').value;
                    var inputval = parseFloat($("#withdrawValue").val());
                    if(option=="ETH")
                    {
                        if (inputval > ethValue)
                        {
                            $("#withdrawValue").val(ethValue);
                        }
                    }
                    else if (option=="ARB")
                    {
                        if (inputval > arbValue)
                        {
                            $("#withdrawValue").val(arbValue);
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>    

<div class="modal fade" id="transfer_aBOT_Modal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">Transfer your Stop aBOT ARB</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-5 pb-5">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-4 col-form-label"><h5>Transfer To: </h5></label>
                        <div class="col-8">
                            <select class="form-control" id="pagesOptionStopAbot">
                                <option value="" selected>Select One</option>
                                <option id="InpValueStopAbot" value="stopAbot">aBOT </option>
                                <!--<option id="InpValueExWall" value="externalWallet">External Wallet</option>-->
                            </select>
                        </div>
                    </div>
                    <h5> Transfer Amount: </h5>
                    <input id="withdrawValueAbot" class="form-control" >
                    <span id="dollarSign" class="error text-danger"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick=transferWalletABot()>Confirm</button>
                <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
            
            <script>
                var inputvalaBot = 0;
                var optionStop = 0;
                
                $('#transferModalBtnAbot').click(function() {
                    $('#pagesOptionStopAbot').val("");
                    $('#withdrawValueAbot').val(arbValue);
                });
                
                $('#withdrawValueAbot').keyup(function () {
                    inputvalaBot = parseFloat($("#withdrawValueAbot").val());
                    if(inputvalaBot > arbValue)
                    {
                        $("#withdrawValueAbot").val(arbValue);
                    }
                    
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                    optionStop = document.getElementById('pagesOptionStopAbot').value;
                    
                    if(optionStop == "stopAbot") {
                        $('#dollarSign').text('Total in USD: ' + ($('#withdrawValueAbot').val() * userAbotPrice).toFixed(4));
                    } 
                    else if (optionStop == "externalWallet") {
                        $('#dollarSign').text("");
                    }
                });
            </script>
        </div>
    </div>
</div> 

<div class="modal fade" id="submitBuyOrder">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p>
                        <h3>Your Buy Order is: </h3>
                        <span>ARB Amount : <b id="modelBuyAmount"></b></span><br/>
                        <span>Price In ETH: <b id="modelBuyPrice"></b></span><br/>
                        <span>Price In USD : $ <b id="modelBuyPriceDollar"></b></span><br/>
                        <span>Total ETH: <b id="modelBuyTotal"></b> ($ <span id="modelBuyTotalDollar"></span>)</span>
                    </p>
                    <p>
                        <div class="col-md-12 textAlignCenter">
                            <button class="btn btn-success" onclick=submitBuyOrder()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </p>    
                </div>
            </div>
            <script>
                var buyAmountModel;
                var buyPriceModel;
                var buyTotalModel;
                
                function buySubmitValues()
                {
                    buyAmountModel = $('#buyAmount').val();
                    buyPriceModel = $('#buyPrice').val();
                    buyTotalModel = $('#buyTotal').val();
                    
                    $('#modelBuyAmount').text(buyAmountModel);
                    $('#modelBuyPrice').text(buyPriceModel);
                    $('#modelBuyTotal').text(buyTotalModel);    
                
                    $.get( "https://api.coinmarketcap.com/v1/ticker/ethereum/")
                    .done(function( data ) {
                        aeth = data[0].price_usd;
                        $('#modelBuyPriceDollar').text(aeth * buyPriceModel);
                        $('#modelBuyTotalDollar').text(aeth * buyTotalModel);
                    });
                }
            </script>
        </div>
    </div>
</div>   
    
<div class="modal fade" id="submitSellOrder">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p>
                        <h3>Your Sell Order is: </h3>
                        <span>ARB Amount : <b id="modelSellAmount"></b></span><br/>
                        <span>Price In ETH : <b id="modelSellPrice"></b></span><br/>
                        <span>Price In USD : $ <b id="modelSellPriceDollar"></b></span><br/>
                        <span>Total ETH: <b id="modelSellTotal"></b> ($ <span id="modelSellTotalDollar"></span>)</span><br/>
                        <p class="noteLossModelParagraph">
                            <span id="noteLossModel" class="text-danger"></span>
                            <div class="text-center">
                                <span id="noteLossSecondModel" class="text-danger" style="font-size: 40px;"></span></br>
                            </div>
                            <div class="text-center noteLossModelParagraph">
                                <button class="btn btn-warning mt-3" onclick="betterPriceSellOrder()">Choose Better Price!</button>
                            </div> 
                            <span id="noteFeeDeductModel" class="text-danger noteLossModelParagraph"></span>
                        </p>
                    </p>
                    <div class="text-danger border" style="padding:10px;font-size: 15px;text-align: center;font-weight: 700;">Your Order will expire after 24 hours if not filled.</div>
                    <p>
                        <div class="col-md-12 textAlignCenter">
                            <button class="btn btn-success" onclick=submitSellOrder()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </p>    
                </div>
            </div>
            <script>
                    
                function sellSubmitValues()
                {
                    var sellAmountModel = 0;
                    var sellPriceModel = 0;
                    var sellTotalModel = 0;
                    var tradeLossCount = 0;
                    var percentCheckArbValue = 0;
                    
                    sellAmountModel = $('#sellAmount').val();
                    sellPriceModel = $('#sellPrice').val();
                    sellTotalModel = $('#sellTotal').val();
                    
                    $('#modelSellAmount').text(sellAmountModel);
                    $('#modelSellPrice').text(sellPriceModel);
                    $('#modelSellTotal').text(sellTotalModel);
                    
                    var eth_usd = arb_p/arb_eth_p;
                    var dollarPriceARB = 0;
                    dollarPriceARB = eth_usd * sellPriceModel;
                    dollarPriceARB = dollarPriceARB.toFixed(4);
                    
                    $('#modelSellPriceDollar').text(dollarPriceARB);
                    $('#modelSellTotalDollar').text(eth_usd * sellTotalModel);
                    
                    tradeLossCount = ((arb_p - dollarPriceARB) * sellAmountModel);
                    tradeLossCount = tradeLossCount.toFixed(4);

                    // priceOnePercentIncr = (arb_p / 100) + arb_p;
                    
                    percentCheckArbValue = arb_eth_p-((arb_eth_p / 100)* 0.25);
                    
                    if(sellPriceModel < arb_eth_p )
                    {
                        if(sellPriceModel < percentCheckArbValue)
                        {
                            $('.noteLossModelParagraph').css('display', 'block');
                            $('#noteLossModel').text("Currently ARB price is "+arb_p+" and you are selling for "+dollarPriceARB+ ". Your trade will give you LOSS of ");
                            $('#noteFeeDeductModel').text("Warning: This trade is UNDERCUTTING YOUR OWN PROFITS and you will pay HIGHER FEE to place or cancel this order.");
                            $('#noteLossSecondModel').text("$"+tradeLossCount);
                            flagValue = 1;
                        }
                        else {
                            $('.noteLossModelParagraph').css('display', 'block');
                            $('#noteLossModel').text("Currently ARB price is "+arb_p+" and you are selling for "+dollarPriceARB+ ". Your trade will give you LOSS of ");
                            $('#noteFeeDeductModel').text("");
                            $('#noteLossSecondModel').text("$"+tradeLossCount);   
                            flagValue = 0;
                        }
                    }
                    else
                    {
                        $('.noteLossModelParagraph').css('display', 'none');
                        $('#noteLossModel').text("");
                        $('#noteFeeDeductModel').text("");
                        $('#noteLossSecondModel').text("");
                        flagValue = 0;
                    }
                }
                
                function betterPriceSellOrder(){
                    
                    var priceOnePercent = arb_eth_p / 100;
                    var priceOnePercentIncr = parseFloat(priceOnePercent) + parseFloat(arb_eth_p);
                    flagValue = 0;
                    $('#sellPrice').val(priceOnePercentIncr);
                    submitSellOrder();
                }
            </script>
        </div>
    </div>
</div>

<div class="modal fade" id="ErrorModalGeneric" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title text-danger">Error</h1>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h3 id="ErrorTextGeneric"></h3>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="SuccessModalGeneric" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3 id="SuccessTextGeneric"></h3>
            </div>
        </div>
    </div>
</div>

<div id="snackbarBuy">Your order is placed.</div>
<div id="snackbarOnClickNoBalance" class="snackbar">Your Balance is not sufficient.</div>
<div id="snackbarSell">Your order is placed.</div>
<div id="snackbarFalseSell">Your order is not placed.</div>
<div id="snackbarOrder">
    Your trade is successfully completed. 
    <br><b>Your remaning ARB are: <span id="newSnackBarARB"> </span></b>
    <br><b>Your remaning ETH are: <span id="newSnackBarETH"> </span></b>
</div>
<div id="snackbarOrderCancel">Your order is cancelled.</div>

<!--////////////////////////////////////////////////////    Auto SELL/BUY Script   //////////////////////////////////////////////-->  
<script src="<?php echo base_url()?>assets/backend/js/autoSellBuyScript.js"></script>
<script src="<?php echo base_url()?>assets/backend/js/sellOrderReqq.js"></script>
<script src="<?php echo base_url()?>assets/backend/js/buyOrderReq.js"></script>

<!--////////////////////////////////////////////////////// Graph /////////////////////////////////////////-->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>
    function graph(){
        graph = [];
        $.get( "<?php echo base_url(); ?>arb_price_stats")
            .done(function( data ) {
                data = JSON.parse(data);
          	    $.each(data, function(i){
      	            graph.push(parseFloat(data[i]));
          	    });
                graph = graph.reverse();
                
                Highcharts.chart('container', {
                    title: {
                        text: 'ARB Price History (Last 500 trades)',
                        color: '#daa521',
                    },
                
                    yAxis: {
                        title: {
                            text: ''
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },
                
                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            },
                            color: '#daa521',
                            pointStart: 1
                        }
                    },
                
                    series: [{
                        name: 'Price',
                        data: graph
                    }],
                
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'vertical',
                                    align: 'center',
                                    verticalAlign: 'top'
                                }
                            }
                        }]
                    }
                
                });
            });
    }
    graph();
</script>