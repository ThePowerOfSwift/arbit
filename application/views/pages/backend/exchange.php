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

$arbfee = $arbfee;
$ethfee = $ethfee;
$order_type_sell = "Sell";
$order_type_buy = "Buy";
$currencyARB = "ARB";
$currencyETH = "ETH";
$userId = $user_id;

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

if(isset($allow_pin)){
    $allow_pin = $allow_pin;
}else{
    $allow_pin = 0;
}
?>

<div>
    
    <!--<div class="modal" tabindex="-1" role="dialog" id="onLoadModal">-->
    <!--  <div class="modal-dialog" role="document">-->
    <!--    <div class="modal-content">-->
    <!--      <div class="modal-header">-->
    <!--        <h5 class="modal-title">Announcement</h5>-->
    <!--        <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
    <!--          <span aria-hidden="true">&times;</span>-->
    <!--        </button>-->
    <!--      </div>-->
    <!--      <div class="modal-body">-->
    <!--        <p>Dear users,-->
    <!--            <br>We are working on exchange. It will be live and fully functional soon.-->
    <!--            <br>Thank you for your patience, understanding and support.-->
    <!--            <br><br>The Arbitraging Team-->
    <!--        </p>-->
    <!--      </div>-->
    <!--      <div class="modal-footer">-->
    <!--        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</div>-->
    
    <!--////////////////////////////////////////////////////// Wallet Button /////////////////////////////////////////-->
    <div class="row depositBtnExch">
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
            <div class="text-center mb-2">
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
    </div>
    
    <!--////////////////////////////////////////////////////// TABS /////////////////////////////////////////-->
    <div>
        <div id="flexDivID" class="col-md-12" style="display: flex">
            <!--////////////////////////////////////////////////////// BLOCKS /////////////////////////////////////////-->
            <div class="width45">
                <div class="row" style="font-weight: 700;">
                    <div class="col-md-12">Buy Orders</div>
                </div>
                <div class="">
                    <div class="exDivHeading">
                        <div class="col-sm-12">
                            <div class="row balanceDiv">
                                <div class="col-sm-12">
                                    $ <span id="actETHMain"></span> (<span style="font-size:12px" id="ethValueActiveMain"></span> ETH)    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="tableDivExchange">
                            <table class="table table-hover table-striped textAlignCenter">
                                <thead>
                                <tr>
                                    <th>Price</th>
                                    <th>ARB</th>
                                    <th>ETH</th>
                                </tr>
                                </thead>
                                <tbody id="buySideOrders" class="textAlignCenter">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>    
            <div class="width30">
                <div id="lastBlockExecuted">
                    <span id="demoo"></span>
                </div>
                <div id="eth_sold_market"></div>
            </div>
            <div class="width45">
                <div class="row" style="font-weight: 700;">
                    <div class="col-md-6">Sell Orders</div>
                </div>
                <div class="">
                    <div class="exDivHeading">
                        <div class="col-sm-12">
                            <div class="row balanceDiv">
                                <div class="col-sm-12">
                                    <span id="arbValueActiveMain"></span> ARB
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="tableDivExchange">
                            <table class="table table-hover table-striped textAlignCenter">
                                <thead>
                                <tr>
                                    <th>Price</th>
                                    <th>ARB</th>
                                    <th>ETH</th>
                                </tr>
                                </thead>
                                <tbody id="sellSideOrders" class="textAlignCenter">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    
    <!--////////////////////////////////////////////////////// Order History /////////////////////////////////////////-->
    <div class="col-md-12 imgLogoMarg">
        <div class="col-md-12">
            <div class="row exDivHeading">
                <div class="col-md-12"><span class="fa fa-list-ol"></span> Order History</div>
            </div>
            <div class="row">
                <div id="old_blocksDiv" class="ex_blocksHistoryWrap"></div>
            </div>
        </div>
    </div>
    
    <!--////////////////////////////////////////////////////// Open Orders /////////////////////////////////////////-->
    <div class="col-md-12 imgLogoMarg">
        <div class="col-md-12">
            <!--<div class="col-md-12">-->
                <div class="row exDivHeading">
                    <div class="col-md-12"><span class="fa fa-list-ol"></span> Your Open Orders</div>
                </div>
                <div class="row">
                    <div class="tableDivExchange tDE_openOrders">
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
            <!--</div>-->
        </div>
    </div>

    <!--////////////////////////////////////////////////////// Close Orders /////////////////////////////////////////-->
    <div class="col-md-12 imgLogoMarg">
        <div class="col-md-12">
            <!--<div class="col-md-12">-->
                <div class="row exDivHeading">
                    <div class="col-md-12"><span class="fa fa-clone"></span> Your Order History</div>
                </div>
                <div class="row">
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
            <!--</div>-->
        </div>
        <div class="col-md-12">
            <div class="showAllRecord">
                <a href="<?php echo base_url(); ?>admin/history" style="color: #daa522;" onclick="histroyPageOrders()">Show All Record History</a>
            </div>
        </div>
    </div>
    <!--////////////////////////////////////////////////////// Snackbars /////////////////////////////////////////-->
    <div id="snackbarSell">Your order is placed.</div>
    <div id="snackbarFalseSell">Your order is not placed.</div>
    <div id="snackbarOrder">
        Your trade is successfully completed. 
        <br><b>Your remaning ARB are: <span id="newSnackBarARB"> </span></b>
        <br><b>Your remaning ETH are: <span id="newSnackBarETH"> </span></b>
    </div>
    <div id="snackbarOrderCancel">Your order is cancelled.</div>

</div>

<!--////////////////////////////////////////////////////// Script /////////////////////////////////////////-->
<script>
    // $('#onLoadModal').modal('show');
    var arbValue;
    var ethValue;
    var actETH;
    var check_len;
    var cal_time = 'false';
    var priceT;
    var amountT;
    var totalT;
    var walletSelect = 0;
    var walSelected = 0;
    var limitDollar = 0;
    var ajaxBuyOrder = '';
    var ajaxSellOrder = '';
    var ajaxSellOrderBest = '';
    
    walletSelect= "exchangeWallet";
    
    function selectedValue(val){
        walletSelect = val;
        getBalnaceARBETH();
    }

    function getBalnaceARBETH(){
        $.get("<?php echo base_url(); ?>exchange_wallet_balance_beta", function( data ) 
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
                    
                    document.getElementById("arbValueActiveNextBlock").innerHTML = arbValue;
                    document.getElementById("ethValueActive").innerHTML = ethValue;
                    document.getElementById("arbValueActiveMain").innerHTML = arbValue;
                    document.getElementById("ethValueActiveMain").innerHTML = ethValue;
                    document.getElementById("limitDollarNextBlock").innerHTML = limitDollar;
                    document.getElementById("newSnackBarARB").innerHTML = arbValue;
                    document.getElementById("newSnackBarETH").innerHTML = ethValue;
                    
                    $('#transferModalBtn').css('display', 'block');
                    $('#transferModalBtnAbot').css('display', 'none');
                    
                    var dollarArb_value = aeth * ethValue;
                    dollarArb_value = dollarArb_value.toFixed(2);
                    $('#actETH, #actETHMain').text(dollarArb_value);
                    
                    $("#exchangeWalletBtn").addClass("active");
                    $("#exchangeEarnedWalletBtn, #aBotWalletBtn").removeClass("active");
                }
                else if(walletSelect == "exchangeEarnedWallet")
                {
                    data = $.parseJSON(data);
                    arbValue = data.activeArb_earned;
                    ethValue = data.activeEth;
                    limitDollar = data.ex_er_limit;
                    
                    document.getElementById("arbValueActiveNextBlock").innerHTML = arbValue;
                    document.getElementById("ethValueActive").innerHTML = ethValue;
                    document.getElementById("arbValueActiveMain").innerHTML = arbValue;
                    document.getElementById("ethValueActiveMain").innerHTML = ethValue;
                    document.getElementById("limitDollarNextBlock").innerHTML = limitDollar;
                    document.getElementById("newSnackBarARB").innerHTML = arbValue;
                    document.getElementById("newSnackBarETH").innerHTML = ethValue;
                    
                    $('#transferModalBtn').css('display', 'block');
                    $('#transferModalBtnAbot').css('display', 'none');
                    
                    $('#actETH').text(aeth * ethValue);
                    
                    
                    $("#exchangeEarnedWalletBtn").addClass("active");
                    $("#exchangeWalletBtn, #aBotWalletBtn").removeClass("active");
                }
                else if(walletSelect == "aBotWallet")
                {
                    data = $.parseJSON(data);
                    arbValue = data.activeArb_stop_abot;
                    ethValue = data.activeEth;
                    limitDollar = data.ex_limit;
                    
                    document.getElementById("arbValueActiveNextBlock").innerHTML = arbValue;
                    document.getElementById("ethValueActive").innerHTML = ethValue;
                    document.getElementById("arbValueActiveMain").innerHTML = arbValue;
                    document.getElementById("ethValueActiveMain").innerHTML = ethValue;
                    document.getElementById("limitDollarNextBlock").innerHTML = limitDollar;
                    document.getElementById("newSnackBarARB").innerHTML = arbValue;
                    document.getElementById("newSnackBarETH").innerHTML = ethValue;
                    
                    $('#transferModalBtn').css('display', 'none');
                    $('#transferModalBtnAbot').css('display', 'block');
                    
                    $('#actETH').text(aeth * ethValue);
                    
                    $("#aBotWalletBtn").addClass("active");
                    $("#exchangeWalletBtn, #exchangeEarnedWalletBtn").removeClass("active");
                }
            }
        });
    }
    getBalnaceARBETH();
    window.setInterval( function() {getBalnaceARBETH();}, 58000);

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
         
   //////////////////////////////////////////////////////////////       Wallets / Transfer Amount    ////////////////////////////////////////////////////////////
    function getaddress() {
        $.post( "<?php echo base_url(); ?>deposit")
            .done(function( data ) {
                $('#address').text(data);
            });
    }
    
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
                else if(checkStopAbotWall == "externalWallet") {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>stop_abot_wallet_to_external_wallet",
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
    
    <!--////////////////////////////////////////////////////// Get Blocks Data /////////////////////////////////////////-->
    
    var close_blocksArray = [];
    var open_blocksArray = [];
    var sell_sideArray = [];
    var buy_sideArray = [];
    var tdsell;
    var tflagg = false; 
    var tdbuy;
    var eth_sold_market = 0;
    
    function getBlocksData() {
        close_blocksArray = [];
        open_blocksArray = [];
        sell_sideArray = [];
        buy_sideArray = [];
        tdsell = "";
        tdbuy = "";
        
        $.get("<?php echo base_url(); ?>get_blocks_data", function( data ) {
            data = JSON.parse(data);
            oldBlocksCateg = data.close_blocks;
            open_blocksArray = data.open_blocks;
            
            $.each(open_blocksArray, function(j) {
                if(open_blocksArray[j].flag == "sell_side"){
                    sell_sideArray.push(open_blocksArray[j]);
                }
                
                if(open_blocksArray[j].flag == "buy_side"){
                    buy_sideArray.push(open_blocksArray[j]);
                }
            });    
            
            <!--////////////////////////////////////////////////////// Sell Blocks /////////////////////////////////////////-->
            
            $.each(sell_sideArray, function(j) {
                price = sell_sideArray[j].price;
                current_arb_size = sell_sideArray[j].current_arb_size;
                current_arb_size = parseFloat(current_arb_size);
                current_arb_size = Math.floor(current_arb_size * 1000) / 1000;
                arb_size = sell_sideArray[j].arb_size;
                arb_size = parseFloat(arb_size);
                arb_size = Math.floor(arb_size * 1000) / 1000;
                current_eth_size = sell_sideArray[j].current_eth_size;
                eth_size = sell_sideArray[j].eth_size;
                
                if(arb_size == current_arb_size) {
                    placeBuy = 2;
                }
                else if(sell_sideArray[j].current_arb_size != '0') {
                    placeBuy = 1;
                }
                else {
                    placeBuy = 1;
                }  
                
                tdsell += "<tr onclick='clickARBPrice(" + current_arb_size + "," + price + "," + arb_size + "," + sell_sideArray[j].current_arb_size  + "," + placeBuy + "," + current_eth_size +  "," + eth_size + ")'><td>" + price + "</td><td>" + current_arb_size+"/"+arb_size + "</td><td>" + current_eth_size+"/"+eth_size + "</td></tr>";
            });
            $('#sellSideOrders').html(tdsell);
            
            <!--////////////////////////////////////////////////////// Buy Blocks /////////////////////////////////////////-->
            
            buy_sideArray = buy_sideArray.reverse();
            
            $.each(buy_sideArray, function(j) {
                price = buy_sideArray[j].price;
                current_arb_size = buy_sideArray[j].current_arb_size;
                current_arb_size = parseFloat(current_arb_size);
                current_arb_size = Math.floor(current_arb_size * 1000) / 1000;
                arb_size = buy_sideArray[j].arb_size;
                arb_size = parseFloat(arb_size);
                arb_size = Math.floor(arb_size * 1000) / 1000;
                current_eth_size = buy_sideArray[j].current_eth_size;
                eth_size = buy_sideArray[j].eth_size;
                
                if(eth_size == current_eth_size) {
                    placeSell = 2;
                }
                else if( buy_sideArray[0].current_eth_size != '0' ) {
                    placeSell = 1;
                }
                else {
                    placeSell = 1;
                }  
                
                tdbuy += "<tr onclick='clickETHPrice(" + current_eth_size + "," + price + "," + eth_size + "," + buy_sideArray[j].current_eth_size + "," + placeSell + "," + current_arb_size +  "," + arb_size + ")'><td>" + price + "</td><td>" + current_arb_size+"/"+arb_size + "</td><td>" + current_eth_size+"/"+eth_size + "</td></tr>";
            });
            $('#buySideOrders').html(tdbuy);
            
            <!--////////////////////////////////////////////////////// Close Blocks /////////////////////////////////////////-->
            
            $('#old_blocksDiv').empty();
            
            $.each(oldBlocksCateg, function(j)
            {
                priceArray = oldBlocksCateg[j].price;
                dollarEthBlock = parseFloat(priceArray * aeth).toFixed(3);
                
                $('#old_blocksDiv').append("<span class='expriceOld'>"+ priceArray +" / $"+ dollarEthBlock + " || " + oldBlocksCateg[j].complete_at +"</span>");
            })
            
            d = new Date();
            xtime = data.market_time_diff;
            xtime = xtime.split(":");
            
            d.setMinutes( d.getMinutes() + parseInt(xtime[0]) );
            d.setSeconds( d.getSeconds() + parseInt(xtime[1]) );
            diff_date = d.getFullYear() + "/" + (d.getMonth()+1) + "/" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() ;
            
            diff_date = new Date(diff_date).getTime();
            if(!tflagg) {  countdown_bm(diff_date); tflagg = true; }
            
            eth_sold_market = parseFloat(data.eth_sold_market).toFixed(2);
            $('#eth_sold_market').html("Traded "+ eth_sold_market + " ETH");
        });
    }    
    getBlocksData();
    window.setInterval( function() {getBlocksData();}, 15000);
    
    function countdown_bm(countDownDate){
        var x = setInterval(function() {

          // Get todays date and time
          var now = new Date().getTime();
        
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
        
          // Time calculations for days, hours, minutes and seconds
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
       
          // Display the result in the element with id="demo"
          if (distance < 30000) {
               $("#demoo").css('color', 'red');
               document.getElementById("demoo").innerHTML = minutes + "m:" + seconds + "s";
          }
          else {
               $("#demoo").css('color', '#daaf2a');
              document.getElementById("demoo").innerHTML = minutes + "m:" + seconds + "s";
          }
          // If the count down is finished, write some text 
          if (distance < 0) {
            clearInterval(x);
            document.getElementById("demoo").innerHTML = "00:00"; tflagg = false;
          }
        }, 1000);
    }
   
    <!--////////////////////////////////////////////////////// OnClick Block /////////////////////////////////////////-->
    var arb_max_sellSide = 0;
    
    function clickARBPrice(currentArb, price, total, checkEmptyBlocks, placeBuy, current_eth_size, eth_size) {
        
        var remainingArb = parseFloat(total - currentArb).toFixed(3);
        var checkEmptyBlock = parseFloat(total.toFixed(3) - checkEmptyBlocks.toFixed(3)).toFixed(3);
        var remainingETHSell = parseFloat(eth_size - current_eth_size).toFixed(3);
        arb_max_sellSide = parseFloat(remainingArb);
        $('#buyFeeDetailModal').html("3% Bonus");
        $('#sellFeeDetailModal').html("");
        
        if(placeBuy == 0) {                                                        // Sell Order Option
        
            if(remainingArb == 0) {
                $('#amountFullModal').modal('show');
            }
            else {
                $('#remainingARBLeftDropdown').html(remainingArb);
                $('#sellPriceNextBlock').val(price);
                $('#sellPriceInDollarBuy').html((price * aeth).toFixed(2));
                
                $('#buyAmountSellSideModal a').addClass('active');
                
                //sellside block
                $('#buyAmountBuySideModal, #buyAmountBuySideDiv').css('display', 'none');
                $('#buyAmountSellSideModal, #buyAmountSellSideDiv').css('display', 'block');
                
                $('#buyAmountBuySideDiv').removeClass('active');
                $('#buyAmountBuySideDiv').removeClass('show');
                $('#buyAmountSellSideDiv').addClass('active');
                $('#buyAmountSellSideDiv').addClass('show');
                $('#buySidePInput').val(price);
                $('#sellSidePInput').val(price);
                $('#buySellAmountModal').modal('show');
            }    
        }
        else if(placeBuy == 1) {                                                        // Buy / Sell Order Option
            $('#remainingETHLeft').html(remainingETHSell);
            $('#buyPrice').val(price);
            $('#buyPriceInDollar').html((price * aeth).toFixed(2));
            
            $('#remainingARBLeftDropdown').html(remainingArb);
            $('#sellPriceNextBlock').val(price);
            $('#sellPriceInDollarBuy').html((price * aeth).toFixed(2));
            
            $('#buyAmountSellSideModal > a').addClass('active');
            $('#buyAmountSellSideDiv').addClass('active');
            $('#buyAmountSellSideDiv').addClass('show');
            $('#buyAmountBuySideDiv, #buyAmountBuySideModal').css('display', 'block');
            
            $("#buyAmountSellSideModal").click(function(){
                $("#buyAmountBuySideDiv").css("display","none");
                $("#buyAmountSellSideDiv").css("display","block");
            });
            
            $("#buyAmountBuySideModal").click(function(){
                $("#buyAmountSellSideDiv").css("display","none");
                $("#buyAmountBuySideDiv").css("display","block");
            });
            
            if( $('#buyAmountSellSideModal > a').hasClass('active') ){
                $('#buyAmountBuySideModal a').removeClass('active');
                
                $('#buyAmountSellSideDiv').css('display', 'block');
                $('#buyAmountSellSideDiv').addClass('active');
                $('#buyAmountSellSideDiv').addClass('show');
                $('#buyAmountBuySideDiv').css('display', 'none');
            }
            
            if( $('#buyAmountBuySideModal > a').hasClass('active') ){
                $('#buyAmountSellSideModal a').removeClass('active');
                $('#buyAmountBuySideDiv').css('display', 'block');
                $('#buyAmountBuySideDiv').addClass('active');
                $('#buyAmountBuySideDiv').addClass('show');
                $('#buyAmountSellSideDiv').css('display', 'none');
            }
            
            $('#buyAmountSellSideModal').css('display', 'block');
            $('#buySidePInput').val(price);
            $('#sellSidePInput').val(price);
            $('#buySellAmountModal').modal('show');
        }
        else if(placeBuy == 2) {                                                        // Buy Order Option
            $('#remainingETHLeft').html(remainingETHSell);
            $('#buyPrice').val(price);
            $('#buyPriceInDollar').html((price * aeth).toFixed(2));
            
            $('#buyAmountBuySideModal, #buyAmountBuySideDiv').css('display', 'block');
            if( $('#buyAmountSellSideModal > a').removeClass('active') ){
                $('#buyAmountBuySideModal a').hasClass('active');
            }
            $('#buyAmountBuySideModal a').addClass('active');
            $('#buyAmountBuySideModal').addClass('active');
            $('#buyAmountBuySideModal').addClass('show');
            
            $('#buyAmountSellSideModal, #buyAmountSellSideDiv').css('display', 'none');
            $('#buySidePInput').val(price);
            $('#sellSidePInput').val(price);
            $('#buySellAmountModal').modal('show');
        }
    };
    
    function clickETHPrice(currentEth, price, total, checkEmptyBlocksBuy, placeSell, current_arb_size, arb_size) {

        var remainingETH = parseFloat(total - currentEth).toFixed(3);
        var remainingARBBuy = parseFloat(arb_size - current_arb_size).toFixed(3);
        arb_max_sellSide = parseFloat(remainingARBBuy);
        $('#buyFeeDetailModal').html("1% Fee");
        $('#sellFeeDetailModal').html("5% Fee");
        
        if(placeSell == 0) {                                                        // Buy Order Option
        
            if(remainingETH == 0) {
                $('#amountFullModal').modal('show');
            }
            else {
                $('#remainingETHLeft').html(remainingETH);
                $('#buyPrice').val(price);
                $('#buyPriceInDollar').html((price * aeth).toFixed(2));
                
                $('#buyAmountBuySideModal a').addClass('active');
                
                //buyside block
                $('#buyAmountBuySideModal, #buyAmountBuySideDiv').css('display', 'block');
                $('#buyAmountSellSideModal, #buyAmountSellSideDiv').css('display', 'none');
                
                $('#buyAmountBuySideDiv').addClass('active');
                $('#buyAmountBuySideDiv').addClass('show');
                $('#buyAmountSellSideDiv').removeClass('active');
                $('#buyAmountSellSideDiv').removeClass('show');
                $('#buySidePInput').val(price);
                $('#sellSidePInput').val(price);
                $('#buySellAmountModal').modal('show');
            }    
        }
        else if(placeSell == 1) {                                                        // Buy / Sell Order Option
            $('#remainingETHLeft').html(remainingETH);
            $('#buyPrice').val(price);
            $('#buyPriceInDollar').html((price * aeth).toFixed(2));
            
            $('#remainingARBLeftDropdown').html(remainingARBBuy);
            $('#sellPriceNextBlock').val(price);
            $('#sellPriceInDollarBuy').html((price * aeth).toFixed(2));
            
            $('#buyAmountBuySideModal > a').addClass('active');
            $('#buyAmountBuySideDiv').addClass('active');
            $('#buyAmountBuySideDiv').addClass('show');
            $('#buyAmountBuySideDiv, #buyAmountBuySideModal').css('display', 'block');
            
            $("#buyAmountSellSideModal").click(function(){
                $("#buyAmountBuySideDiv").css("display","none");
                $("#buyAmountSellSideDiv").css("display","block");
            });
            
            $("#buyAmountBuySideModal").click(function(){
                $("#buyAmountSellSideDiv").css("display","none");
                $("#buyAmountBuySideDiv").css("display","block");
            });
            
            if( $('#buyAmountSellSideModal > a').hasClass('active') ){
                $('#buyAmountBuySideModal a').removeClass('active');
                
                $('#buyAmountSellSideDiv').css('display', 'block');
                $('#buyAmountSellSideDiv').addClass('active');
                $('#buyAmountSellSideDiv').addClass('show');
                $('#buyAmountBuySideDiv').css('display', 'none');
            }
            
            if( $('#buyAmountBuySideModal > a').hasClass('active') ){
                $('#buyAmountSellSideModal a').removeClass('active');
                $('#buyAmountBuySideDiv').css('display', 'block');
                $('#buyAmountBuySideDiv').addClass('active');
                $('#buyAmountBuySideDiv').addClass('show');
                $('#buyAmountSellSideDiv').css('display', 'none');
            }
            
            $('#buyAmountSellSideModal').css('display', 'block');
            $('#buySidePInput').val(price);
            $('#sellSidePInput').val(price);
            $('#buySellAmountModal').modal('show');
        }
        else if(placeSell == 2) {                                                        // Sell Order Option
            $('#remainingARBLeftDropdown').html(remainingARBBuy);
            $('#sellPriceNextBlock').val(price);
            $('#sellPriceInDollarBuy').html((price * aeth).toFixed(2));
            
            $('#buyAmountSellSideModal, #buyAmountSellSideDiv').css('display', 'block');
            if( $('#buyAmountSellSideModal > a').hasClass('active') ){
                $('#buyAmountBuySideModal a').removeClass('active');
            }
            $('#buyAmountSellSideModal a').addClass('active');
            $('#buyAmountSellSideDiv').addClass('active');
            $('#buyAmountSellSideDiv').addClass('show');
            
            $('#buyAmountBuySideModal, #buyAmountBuySideDiv').css('display', 'none');
            $('#buySidePInput').val(price);
            $('#sellSidePInput').val(price);
            $('#buySellAmountModal').modal('show');
        }
    };
    
    
    $("#buyAmountSellSideModal, #buyAmountSellSideModal a").click(function(){
        alert("asdadad");
        $("#buyAmountBuySideDiv").css("display","none");
        
        $("#buyAmountSellSideDiv").css("display","block");
        
    });
    /////////////////////////////////////////////////////////////////////OrderBook///////////////////////////////////////////////////////////////////
    function getOrderBook() {
        var orderBook = [];
        var openOrders = [];
        var closeOrders = [];
        var tds = "";
        var tdss = "";
        var total = 0;

        $.get("<?php echo base_url(); ?>user_orders_beta", function( data )
        {
            orderBook = JSON.parse(data);
            jQuery('#sellTable,#buyTable').empty();

            openOrders = orderBook.openOrders;
            closeOrders = orderBook.closeOrders;

            /////////////////////////////////////////////////////////////////////Open Orders///////////////////////////////////////////////////////////////////
            $.each(openOrders, function(i)
            {
                price = openOrders[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT);
                amount = openOrders[i].amount;
                amountT = parseFloat(amount);
                amountT = Math.abs(amountT);
                total = price * amount;
                total = total.toFixed(8);
                order = openOrders[i].order_type;
                date = openOrders[i].created_at;
                orderID = openOrders[i].id;

                tds += "<tr><td>" + priceT + "</td><td>" + amountT + "</td><td>" + order + "</td><td>" + date + "</td><td>" + total + "</td><td><div style='cursor: pointer' class='fa fa-times text-danger' onclick='cancelButton(" + orderID + ");'></div></td></tr>";
            });
            $('#openTable').html(tds);

            //////////////////////////////////////////////////////////    Close Orders    ///////////////////////////////////////
            $.each(closeOrders, function(i)
            {
                total = 0;
                
                price = closeOrders[i].price;
                priceT = parseFloat(price);
                priceT = Math.abs(priceT);
                amount = closeOrders[i].amount;
                amountT = parseFloat(amount);
                amountT = Math.abs(amountT);
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
        });
    }
    getOrderBook();
    window.setInterval( function() {getOrderBook();}, 30000);

    <!--//////////////////////////////////////////////////////       Sell Order Buttons      /////////////////////////////////////////-->
    function submitSellOrder() {
        var sellAmount_check = 0; var callWallet = '';
        
        $('#buySellAmountModal').modal('hide');
        sellAmount_check = parseFloat($('#sellAmountNextBlock').val());
        price = parseFloat($('#sellSidePInput').val());
        
        if (sellAmount_check  < 10 && sellAmount_check  > 500)
        {
            $('#ErrorTextGeneric').html("Please Enter a Higher Value.");
            $('#ErrorModalGeneric').modal('show');
        }
        else if(sellAmount_check  < 10)
        {
            $('#ErrorTextGeneric').html("Sell Order shouldn't be less than 10");
            $('#ErrorModalGeneric').modal('show');
        }
        else if(sellAmount_check  > 500)
        {
            $('#ErrorTextGeneric').html("Please Enter Valid Amount.");
            $('#ErrorModalGeneric').modal('show');
        }
        else if (sellAmount_check  > arbValue)
        {
            $('#ErrorTextGeneric').html("You don't have Sufficient Amount of ARB");
            $('#ErrorModalGeneric').modal('show');
        }
        else
        {
            if(walletSelect =="exchangeWallet") {
                callWallet = 'exchange';
                ajaxSellOrder = '<?php echo base_url(); ?>sellOrder_exchange';
                sellOrderFun(ajaxSellOrder, callWallet, sellAmount_check, price);
            } 
            else if(walletSelect =="exchangeEarnedWallet") {
                callWallet = 'exchange_earned';
                ajaxSellOrder = '<?php echo base_url(); ?>sellOrder_exchange_earned';
                sellOrderFun(ajaxSellOrder, callWallet, sellAmount_check, price);
            }
            else if(walletSelect =="aBotWallet") {
                callWallet = 'stop_abot';
                ajaxSellOrder = '<?php echo base_url(); ?>sellOrder_stop_abot';
                sellOrderFun(ajaxSellOrder, callWallet, sellAmount_check, price);
            }
        }   
    }
    
    <!--//////////////////////////////////////////////////////       Buy Order Buttons      /////////////////////////////////////////-->

    function submitBuyOrder() {
        $('#buySellAmountModal').modal('hide');
        buyPrice_check = parseFloat($('#buySidePInput').val());
        
        if ($('#buyAmount').val()  < 10)
        {
            $('#ErrorTextGeneric').html("Please Enter a Higher Value.");
            $('#ErrorModalGeneric').modal('show');
        }
        else if ($('#buyAmount').val()  < 10)
        {
            $('#ErrorTextGeneric').html("Buy Order Amount shouldn't be less than 10");
            $('#ErrorModalGeneric').modal('show');
        }
        else if (buyPrice_check  > ethValue)
        {
            $('#ErrorTextGeneric').html("You don't have Sufficient Amount of Ethereum");
            $('#ErrorModalGeneric').modal('show');
        }
        else
        {
            ajaxBuyOrder = '<?php echo base_url(); ?>place_buy_order';
            buyOrderFun(ajaxBuyOrder, $('#buyAmount').val(), buyPrice_check);
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
                url: "<?php echo base_url(); ?>close_order_by_id_beta",
                data: {
                    order_id: id
                },
                success: function(data){
                    data = JSON.parse(data);
                    if(data.success == 1)
                    {
                        getOrderBook();
                        getBlocksData();
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
    
    function histroyPageOrders() {
        localStorage.setItem('casheData_ExchangeOrders','Orders');
    }
    
    <!--////////////////////////////////////////////////////// Auto Buy / Sell /////////////////////////////////////////-->
    var autoBuyQuantity = 0;
    var autoBuyRadio = 0;
    
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
           $('#activate_Modal').modal('show');
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
                            alert("Not Available");
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
<div class="modal fade" id="activate_Modal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12">
                    <p><h3>Please Activate your 2FA First</h3></p>
                    
                </div>
            </div>
        </div>
    </div>
</div>

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

<div class="modal fade" id="previousBlockEmptyModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p><h5>Previous Block is still not full. Please put your order on that block first.</h5></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="amountFullModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                <div class="col-md-12 border">
                    <p><h5>This block is full, please place your order in next available block.</h5></p>
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
                                <option id="InpValueExWall" value="externalWallet">External Wallet</option>
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

<div class="modal fade" id="buySellAmountModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-header modalHeaderExchange">
                <h5 class="modal-title">Place Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs buyModalUl">
                  <li id="buyAmountBuySideModal" class="active"><a class="active" data-toggle="tab" href="#buyAmountBuySideDiv">Buy Order </a></li>
                  <li id="buyAmountSellSideModal" style="display:none"><a data-toggle="tab" href="#buyAmountSellSideDiv">Sell Order</a></li>
                </ul>
                
                <div class="tab-content">
                  <div id="buyAmountBuySideDiv" class="tab-pane fade in active show">
                    <div class="col-md-12">
                        <div class="row boldTextPadding textAlignCenter">
                            <div class="col-md-6 textAlignLeft textStyles">
                                $ <span id="actETH"></span> (<span style="font-size:12px" id="ethValueActive"></span> ETH)
                            </div>
                            <div class="col-md-6 textAlignRight textStyles">
                               <span class="titledSpan" id="buyFeeDetailModal"></span>
                            </div>
                        </div>
                        <div class="remainingAmountText">Remaining ETH left in this block: <span id="remainingETHLeft"></span></div>
                        <div class="remainingAmountText">Selected price in $: <span id="buyPriceInDollar"></span></div>
                        <input id="buySidePInput" type="hidden">
                        <div class="row marginTop20">
                            <div class="col-sm-6 fullwidth_579">
                                <div class="form-group">
                                    <label class="form-text"><b>Amount</b></label>
                                    <input name="amount" id="buyAmount" class="input inputOrdExc form-control" type="number" step=".0001" value="0">
                                </div>
                            </div>
                            <div class="col-sm-6 fullwidth_579">
                                <div class="form-group sellBuyOrderInp fullwidth_579">
                                    <label class="form-text"><b>Price</b></label>
                                    <input id="buyPrice" class="input inputOrdExc form-control" readonly>
                                    <b style="font-size: 12px;display: -webkit-inline-box;margin-left: 20px;">(10 MIN ARB/ORDER)</b>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 offset-lg-3 col-lg-6 col-sm-12 margin_3px fullwidth_579">
                                <div class="input-group sellBuyOrderInp width_100">
                                    <input type="text" class="input inputOrdExc form-control" id="buyTotal" oninput="totalETH(this.value);" value="0">
                                    <div id="maxETHBtn" class="input-group-btn">
                                        <button class="btn" type="button" onclick="maxEthValueSet()">Max Eth</button>
                                    </div>
                                    <span class="totalEth_Exchange">
                                        <span >(Total ETH)</span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 margin_3px">
                                <button class="btn btn-block set_max_width_300px inputOrdExc btn-success" type="button" onclick="submitBuyOrder()">Buy ARB</button>
                            </div>
                        </div>    
                    </div>
                  </div>
                  <div id="buyAmountSellSideDiv"class="tab-pane fade">
                     <div class="col-md-12">
                        <div class="row textAlignCenter boldTextPadding">
                            <div class="col-md-6 textAlignLeft textStyles">
                            <span id="arbValueActiveNextBlock"></span> Available ARB
                        </div>
                        <div class="col-md-6 textAlignRight textStyles">
                            <span class="titledSpan" id="sellFeeDetailModal"></span>
                        </div>
                        </div>
                        <div class="remainingAmountText">Remaining ARB left in this block: <span id="remainingARBLeftDropdown"></span></div>
                        <div class="remainingAmountText">Selected price in $: <span id="sellPriceInDollarBuy"></span></div>
                        <input id="sellSidePInput" type="hidden">
                        <div class="row marginTop20">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 fullwidth_579">
                                <div class="form-group">
                                    <label class="form-text"><b>Amount</b></label>
                                    <input name="amount" id="sellAmountNextBlock" value="0" type="number" step=".01" class="input inputOrdExc form-control"  oninput="totalARB(this.value);">
                                    <b style="font-size: 12px;display: -webkit-inline-box;">(<span id="limitDollarNextBlock"></span> ARB Sell Remaining Limit)</b>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 fullwidth_579">
                                <div class="form-group sellBuyOrderInp fullwidth_579">
                                    <label class="form-text"><b>Price</b></label>
                                    <input type="text" id="sellPriceNextBlock" class="input inputOrdExc form-control" readonly/>
                                    <b style="font-size: 12px;display: -webkit-inline-box;">(10 MIN, 500 MAX ARB/ORDER)</b>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 offset-lg-3 col-lg-6 col-sm-12 col-xs-12 fullwidth_579">
                                <div class="input-group sellBuyOrderInp width_100">
                                    <input type="text" class="input inputOrdExc form-control" id="sellTotalNextBlock" value="0" readonly>
                                    <span class="totalEth_Exchange">
                                        (Total ETH)
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 margin_3px">
                                <button class="btn btn-block set_max_width_300px inputOrdExc btn-danger" type="button" onclick="submitSellOrder()">Sell ARB</button>
                            </div>
                        </div>   
                    </div>
                  </div>
                </div>
            </div>
            <script>
                
                <!--////////////////////////////////////////////////////// OnChange / Keyup Buy /////////////////////////////////////////-->
                $("#buyTotal").on('change', function (){
                    buyprice = $("#buyPrice").val();
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                    
                    if($("#buyTotal").val() != 0) {
                        $("#buyAmount").val($("#buyTotal").val() / buyprice );
                    }
                });
            
                $("#buyTotal").on('keyup', function (){
                    buyprice = $("#buyPrice").val();
                    
                    if($("#buyTotal").val() != 0) {
                        $("#buyAmount").val($("#buyTotal").val() / buyprice );
                    }
                });
            
                $("#buyAmount").on('change', function (){
                    buyamount = $("#buyAmount").val();
                    buyprice = $("#buyPrice").val();
                    
                    var minArbSell = parseInt($(this).attr('min'));
                    if ($(this).val() < minArbSell)
                    {
                        $(this).val(minArbSell);
                    }       
                    
                    $("#buyAmount").val(parseFloat($("#buyAmount").val()).toFixed(8)).trigger('keyup'); //need in onchange
                    if( (buyamount * buyprice) > ethValue ){
                        $("#buyPrice").val( ethValue / buyamount);
                    }
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                    // this.value = Number(this.value).toFixed(4);
                    
                    $("#buyTotal").val($("#buyPrice").val() * buyamount );
                });
                
                $("#buyAmount").on('keyup', function (){
                    buyamount = $("#buyAmount").val();
                    buyprice = $("#buyPrice").val();
                     
                    // this.value = Number(this.value).toFixed(4);
                    
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
            
                function totalETH(value)
                {
                    value = parseFloat(value);
                    if(value > ethValue)
                    { 
                        $('#buyTotal').val(ethValue);
                    }
                }
                
                function maxEthValueSet() {
                    $('#buyTotal').val(ethValue).trigger('change');
                }
                <!--////////////////////////////////////////////////////// OnChange / Keyup Sell Order /////////////////////////////////////////-->
            
                $("#sellAmountNextBlock").on('change', function (){
                    this.value = this.value.replace(/[^0-9\.]/g,'');
                    
                    // var minArbSell = parseInt($(this).attr('min'));
                    // if ($(this).val() > arb_max_sellSide)
                    // {
                    //     $(this).val(arb_max_sellSide);
                    // }
                    // else if ($(this).val() < minArbSell)
                    // {
                    //     $(this).val(minArbSell);
                    // }
                   
                    $("#sellAmountNextBlock").val(parseFloat($("#sellAmountNextBlock").val()).toFixed(8)).trigger('keyup');
                    $('#sellTotalNextBlock').val($('#sellAmountNextBlock').val() * $('#sellPriceNextBlock').val());
                });
                
                $("#sellAmountNextBlock").on('keyup', function (){
                    
                    // var minArbSell = parseInt($(this).attr('min'));
                    // if ($(this).val() > arb_max_sellSide)
                    // {
                    //     $(this).val(arb_max_sellSide);
                    // }
                    // else if ($(this).val() < minArbSell)
                    // {
                    //     $(this).val(minArbSell);
                    // }
                    
                    $('#sellTotalNextBlock').val($('#sellAmountNextBlock').val() * $('#sellPriceNextBlock').val());
                });
                
                function totalARB(value)
                {
                    value = parseFloat(value);
                    if(value > arbValue){
                        $('#sellAmountNextBlock').val(arbValue);
                    }
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
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="snackbarBuy">Your order is placed.</div>
<div id="snackbarOnClickNoBalance" class="snackbar">Your Balance is not sufficient.</div>

<!--////////////////////////////////////////////////////    Auto SELL/BUY Script   //////////////////////////////////////////////-->  
<script src="<?php echo base_url()?>assets/backend/js/autoSellBuyScript.js"></script>
<script src="<?php echo base_url()?>assets/backend/js/sellOrderCall.js"></script>
<script src="<?php echo base_url()?>assets/backend/js/buyOrderCall.js"></script>