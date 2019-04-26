<?php
if(isset($_GET['base_currency'])){ $base_currency = $_GET['base_currency'];}else{$base_currency = 'USD';}

$u_id = $this->session->userdata('u_id');

?>

<link rel="stylesheet" href="<?php echo base_url()?>assets/backend/css/mBOT.css">

<div ng-app="myApp" ng-controller="myCtrl">
    <div class="container-fluid rowDataBot2" ng-show="mbotTablesContentDiv">
        <div class="row limitSetters">
            <div class="col-md-4 text-center">
                <h3>Trade Value <img ng-src="<?php echo base_url("assets/backend/img/angularImgs/{{currSignText+'.png'}}") ?>" class="currLogoClass"></h3>
                <span id="btc_dollarWrap">
                    <input type="number" ng-model="trade_value" ng-change="numberToSlider()" class="sliderInput" min="0" max="{{max_trade_value}}"/>
                    <span id="btc_dollar">{{currSignText}}</span>
                </span>
            </div>
            <div class="col-md-4 text-center borderLeftHeader pt-4">
                <h3>
                    Selected Pair: <b>{{selectedMainCurr}} / {{keyCurrSelected}}</b>
                </h3>
            </div>
            <div class="col-md-4 text-center borderLeftHeader">
                <h3>Spread Value </h3>
                    <input type="number" ng-model="spread_value" class="sliderInput" min="0" max="1000"/>
                </span>
            </div>
            <div class="col-md-12  mt-3">
                <div class="progressBarBg">
                    <span class="progressBar_Text">GAS TANK <?php echo $gas; ?>%</span>
                    <div class="progressBar  text-center" style="width:<?php echo $gas; ?>%"></div>
                </div>
            </div>  
        </div>
        
        <div class="row tabsRow">
            <div class="col-md-10">
                <div class="tab">
                    <div ng-repeat="(key, value) in coins">
                        <button class="tablinks" ng-click="tableShow(key)">{{key}}</button>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="tab floatRightBtn" ng-show="transferBtnCoin">
                    <button class="tablinks borderLeftNone" ng-click='coinTransfer()'>Coins Transfer</button>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <hr>
            </div>
        </div>

        <div class="container-fluid">
        <!--//////////////////////////////////////////////////////////     Table    ////////////////////////////////////////////////////-->
            <div class="row" ng-repeat="(key, value) in coins"  ng-show="isActiveShow(key)">
                
                <div class="col-md-12 tableColumn">
                    <div class="row">
                        <div class="col-md-12 tableHeader">
                            {{key}}
                        </div>
                    </div>
                    <div class="row overflowX border01">
                        <table class="table">
                            <thead>
                                <th></th>
                                <th ng-repeat="i in coins[key].Markets track by $index">
                                    <img ng-src="<?php echo base_url("assets/backend/img/angularImgs/{{i.name+'.png'}}") ?>" ng-click="getBalancesEx(i.name)" class="exchangeLogoHeader"><br>
                                    {{i.name}}
                                </th>
                            </thead>
                            <tbody>
                    <!--////////////////////////////////////////////////////     Row    //////////////////////////////////////////////-->
                                <tr ng-repeat="c in coins[key].Markets track by $index">
                                    <th><img ng-src="<?php echo base_url("assets/backend/img/angularImgs/{{c.name+'.png'}}") ?>" class="exchangeLogo"><br>{{c.name}}</th>
                                    <td id="btcToBtcExBTC" ng-repeat="m in c.sell_to track by $index">
                                        <table class="innerTd_table" ng-dblclick='keysModal(key, c.name, m.exchange, c.buy_from, m.value)'>
                                            <tr>
                                                <td colspan="2" ng-class="{silverColor: c.name == m.exchange , redColor: calculate(c.buy_from, m.value) < 0 , blackColor: calculate(c.buy_from, m.value) > 0 , darkGreenColor: calculate(c.buy_from, m.value) > spread_value }">{{calculate(c.buy_from, m.value) | number : 4 }}</td>
                                            </tr>
                                            <tr>
                                                <td class="tablle_borderRight" title="Buy From">{{c.buy_from | number : 6 }}</td>
                                                <td title="Sell To">{{m.value | number : 6 }}</td>
                                            </tr>
                                        </table>
                                    </td> 
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!--////////////////////////////////////////////////////     Status Div   //////////////////////////////////////////////-->   
    <div class="col-md-12 rowDataBot2" ng-show="currTradeStatusDiv">
        <div class="col-md-8 offset-2" style="border:1px solid grey;">
            <div class="row backgroundColorAB">
                <div class="col-md-12"><span class="fa fa-clone"></span> Your Current Trade</div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-striped textAlignCenter">
                        <tbody class="textAlignCenter">
                            <tr>
                                <th>Current Status: </th>
                                <th>{{currentStatusTd}}</th>
                            </tr>
                            <tr>
                                <th>Buy Exchange: </th>
                                <th>{{buyExTd}}</th>
                            </tr>
                            <tr>
                                <th>Sell Exchange: </th>
                                <th>{{sellExTd}}</th>
                            </tr>
                            <tr>
                                <th>Trade Currency: </th>
                                <th>{{currTd}}</th>
                            </tr>
                            <tr>
                                <th>Volume: </th>
                                <th>{{volumeTd}}</th>
                            </tr>
                            <tr>
                                <th>Cancel Trade: </th>
                                <th><i class="fa fa-times text-danger pointerCur" ng-click='cancelmBotTrade()'></i></th>
                            </tr>
                            <tr>
                                <div style="responseThDiv">
                                    <th colspan="2" class="pt-4" style="word-break: break-all;">{{apiResponseTd}}</th>
                                </div>    
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-12 rowDataBot2" ng-show="currTransferStatusDiv">
        <div class="col-md-8 offset-2" style="border:1px solid grey;">
            <div class="row backgroundColorAB">
                <div class="col-md-12"><span class="fa fa-clone"></span> Your Current Transfer</div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-striped textAlignCenter">
                        <tbody class="textAlignCenter">
                            <tr>
                                <th>Current Status: </th>
                                <th>{{currentCoinStatusTd}}</th>
                            </tr>
                            <tr>
                                <th>Source Exchange: </th>
                                <th>{{fromExTd}}</th>
                            </tr>
                            <tr>
                                <th>Destination Exchange: </th>
                                <th>{{toExTd}}</th>
                            </tr>
                            <tr>
                                <th>Trade Currency: </th>
                                <th>{{currenTd}}</th>
                            </tr>
                            <tr>
                                <th>Volume: </th>
                                <th>{{volumeCoinTd}}</th>
                            </tr>
                            <tr>
                                <th>Cancel Trade: </th>
                                <th><i class="fa fa-times text-danger pointerCur" ng-click='cancelmBotTransfer()'></i></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!--////////////////////////////////////////////////////     History Table    //////////////////////////////////////////////-->   
    <div class="row rowDataBot2">
        <div class="col-md-8 offset-2" style="border:1px solid grey;">
            <div class="row backgroundColorAB">
                <div class="col-md-12"><span class="fa fa-clone"></span> Your Trade History</div>
            </div>
            <div class="row">
                <div class="col-md-12 tableDivExchange">
                    <table class="table table-hover textAlignCenter">
                        <thead>
                            <tr>
                                <th>Buy Exchange</th>
                                <th>Sell Exchange</th>
                                <th>Trade Currency</th>
                                <th>Buy</th>
                                <th>Sell</th>
                                <th>Volume</th>
                            </tr>
                        </thead>
                        <tbody class="textAlignCenter" ng-repeat="t in tradeHistory">
                            <td>{{t.data.buy_exchange}}</td>
                            <td>{{t.data.sell_exchange}}</td>
                            <td>{{t.data.trade_currency}}</td>
                            <td>{{t.data.bid}}</td>
                            <td>{{t.data.ask}}</td>
                            <td>{{t.data.volume}}</td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row rowDataBot2">
        <div class="col-md-8 offset-2" style="border:1px solid grey;">
            <div class="row backgroundColorAB">
                <div class="col-md-12"><span class="fa fa-clone"></span> Your Coin Transfer History</div>
            </div>
            <div class="row">
                <div class="col-md-12 tableDivExchange">
                    <table class="table table-hover textAlignCenter">
                        <thead>
                            <tr>
                                <th>From Exchange</th>
                                <th>To Exchange</th>
                                <th>Currency</th>
                                <th>Volume</th>
                            </tr>
                        </thead>
                        <tbody class="textAlignCenter" ng-repeat="s in transferHistory">
                            <td>{{s.data.from_exchange}}</td>
                            <td>{{s.data.to_exchange}}</td>
                            <td>{{s.data.currency}}</td>
                            <td>{{s.data.volume}}</td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row rowDataBot2">
        <div class="col-md-12 text-right">
            <span class="mBOT_views">
                <a href="https://discordapp.com/invite/YXwPY5R" target="_blank">Contact mBOT Support</a>
            </span>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 text-right">
            <input type="checkbox" id="switch" onclick="stopLogoutFun()"/><label class="switchLabel" for="switch"></label>
        </div>
    </div>
</div>

<script>

    function stopLogoutFun() {
         window.setInterval( function() {resetTimer();}, 50000);
    }
    
    var app = angular.module('myApp', ['ngAnimate', 'ngSanitize', 'ui.bootstrap']);             //Main App
    app.controller('myCtrl', function($uibModal ,$scope, $http, $log, $interval, $window) {     //Main Controller

        $scope.slider_value = 1;
        $scope.trade_value = 1;
        $scope.currNames = ["USD", "USDT", "BTC", "ETH"];
        $scope.selectedMainCurrBase = "<?php echo $base_currency; ?>";
        $scope.userID = "<?php echo $u_id; ?>";
        
        if($scope.selectedMainCurrBase == "USD")
        {
            $scope.slider_value = 200;
            $scope.trade_value = 200;
            $scope.currSignText = "$";
            $scope.selectedMainCurr = "USD";
            $scope.max_trade_value = 1000000;
        }
        else if($scope.selectedMainCurrBase == "USDT")
        {
            $scope.slider_value = 200;
            $scope.trade_value = 200;
            $scope.currSignText = "USDT";
            $scope.selectedMainCurr = "USDT";
            $scope.max_trade_value = 1000000;
        }
        else if($scope.selectedMainCurrBase == "BTC")
        {
            $scope.slider_value = 1;
            $scope.trade_value = 1;
            $scope.currSignText = "BTC";
            $scope.selectedMainCurr = "BTC";
            $scope.max_trade_value = 500;
        }
        else if($scope.selectedMainCurrBase == "ETH")
        {
            $scope.slider_value = 1;
            $scope.trade_value = 1;
            $scope.currSignText = "ETH";
            $scope.selectedMainCurr = "ETH";
            $scope.max_trade_value = 100000;
        }
        else if($scope.selectedMainCurrBase == "USD-USDT")
        {
            $scope.slider_value = 200;
            $scope.trade_value = 200;
            $scope.currSignText = "$";
            $scope.selectedMainCurr = "USD-USDT";
            $scope.max_trade_value = 1000000;
        }
        else
        {
            $scope.slider_value = 200;
            $scope.trade_value = 200;
            $scope.currSignText = "$";
            $scope.selectedMainCurr = "USD";
            $scope.max_trade_value = 1000000;
        }

        $scope.isActiveShow = function(key){
            return $scope.isActive === key;
        }
        
        $scope.tableShow = function(key) { 
            $scope.isActive = key;
            $scope.keyCurrSelected = key;
        }
        
        $scope.numberToSlider = function() {
            $scope.slider_value = $scope.trade_value;
            //$scope.apiCall();
        }
        
        $scope.apiCall = function (){                                                           //Call Api Route(Getting Data)
            if($scope.trade_value < $scope.max_trade_value + 1)
            {
                // console.log($scope.userID);
                // return;
                $http.get("https://www.arbitraging.co/platform/testing_call?currency="+ $scope.selectedMainCurr +"&value="+ $scope.trade_value +"&id="+$scope.userID)
                .then(function(response) {
                    $scope.coins = response.data.coin[0];
                });
            }
            else
            {
                alert("Not Allowed More Than "+$scope.max_trade_value);
                $scope.trade_value = $scope.max_trade_value;
            }    
        }  
        $scope.apiCall();
        $interval(function (){$scope.apiCall();}, 5000);
        
        $scope.calculate = function (buyFrom, sellTo){
            if(buyFrom == 0 || sellTo == 0)
            {
                return (0);
            }
            else
            {
                return (((sellTo - buyFrom) / buyFrom) * 100);
            }    
        }
        
        $scope.TradeCoinStatus = function (){                                                   //Trade Coin Status Check
            $http.get("<?php echo base_url();?>user_current_trade")
            .then(function(response) {
                if(response.data == 'no record found')
                {
                    $scope.mbotTablesContentDiv = true;
                    $scope.currTradeStatusDiv = false;
                }
                else
                {
                    // resetTimer();
                    $scope.mbotTablesContentDiv = false;
                    $scope.currTradeStatusDiv = true; 
                    
                    $scope.currentStatusTd = response.data.current_state;
                    $scope.buyExTd = response.data.data.buy_exchange;
                    $scope.sellExTd = response.data.data.sell_exchange;
                    $scope.currTd = response.data.data.trade_currency;
                    $scope.volumeTd = response.data.data.volume;
                    $scope.apiResponseTd = response.data.api_res;
                }
            });
        }  
        $scope.TradeCoinStatus();
        
        $scope.checkCurrentStatus = function () {
            if($scope.currTradeStatusDiv == true)
            {
                $scope.TradeCoinStatus();
            }
        }
        $interval(function (){$scope.checkCurrentStatus();}, 30000);
        
        $scope.TransferCoinStatus = function (){                                                //Transfer Coin Status Check
            $http.get("<?php echo base_url();?>user_current_transfer")
            .then(function(response) {
                if(response.data == 'no record found')
                {
                    $scope.transferBtnCoin = true;
                }
                else
                {
                    resetTimer();
                    $scope.transferBtnCoin = false;
                    $scope.currTransferStatusDiv = true;
                    
                    $scope.currentCoinStatusTd = response.data.current_state;
                    $scope.fromExTd = response.data.data.from_exchange;
                    $scope.toExTd = response.data.data.to_exchange;
                    $scope.currenTd = response.data.data.currency;
                    $scope.volumeCoinTd = response.data.data.from_blnc;
                }
            });
        }  
        $scope.TransferCoinStatus();
        
        $scope.tradeHistory = function (){                                                      //Append User mBot Trade History
            $http.get("<?php echo base_url();?>user_mbot_history")
            .then(function(response) {
                $scope.tradeHistory = response.data;
            });
        }
        $scope.tradeHistory();
        
        $scope.transferHistory = function (){                                                   //Append User mBot Transfer Coin History
            $http.get("<?php echo base_url();?>user_transfer_history")
            .then(function(response) {
                $scope.transferHistory = response.data;
            });
        }  
        $scope.transferHistory();
        
        // $scope.keysModal = function (){         //Td Click Function To open Modal
           
        //     var tm = this;
        //     var modalInstanceTest = $uibModal.open({                                                //Api Modal Instant 
        //         animation: true,
        //         ariaLabelledBy: 'modal-title',
        //         ariaDescribedBy: 'modal-body',
        //         templateUrl: '<?php echo base_url();?>assets/backend/modals/testModal.html',
        //         controller: 'myCtrlTest',
        //         controllerAs: 'tm',
        //         resolve: {
        //             data: function () {
        //             return null;
        //             }
        //         }
        //     });
        
        //     modalInstanceTest.result.then(function () {
        //     });
        // }
        
        $scope.keysModal = function (curr, buyExchange, sellExchange, buyFrom, sellTo){         //Td Click Function To open Modal
            var pc = this;
            pc.items = {
                curr: curr,
                buyExchange:buyExchange,
                sellExchange:sellExchange,
                buyFrom:buyFrom,
                sellTo:sellTo,
                slider_value:$scope.slider_value,
                mainCurr:$scope.selectedMainCurr
            };
           
            var modalInstance = $uibModal.open({                                                //Api Modal Instant 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/acceptTrade.html',
                controller: 'myCtrl1',
                controllerAs: 'pc',
                resolve: {
                    data: function () {
                    return pc.items;
                    }
                }
            });
        
            modalInstance.result.then(function () {
            });
        }
        
        $scope.cancelmBotTrade = function () {                                                    // Cancel Trade On mBOT
            $http.get("<?php echo base_url();?>cancel_trade")
            .then(function(response) {
                if(response.data == "deleted")
                {
                    alert("Deleted");
                    $window.location.reload(true);
                }
            });
        }
        
        $scope.cancelmBotTransfer = function () {                                                    // Cancel Coin Transfer On mBOT
            $http.get("<?php echo base_url();?>cancel_transfer")
            .then(function(response) {
                if(response.data == "deleted")
                {
                    alert("Deleted");
                    $window.location.reload(true);
                }
            });
        }
        
        $scope.coinTransfer = function(){
            var ct = this;
            var modalInstanceCoinTransfer = $uibModal.open({                                                //Api Modal Instant 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/coinTransfer.html',
                controller: 'myCtrlCT',
                controllerAs: 'ct',
                resolve: {
                }
            });
        
            modalInstanceCoinTransfer.result.then(function () {
            });
        }
        
        $scope.getBalancesEx = function(ex) {
            $('#loadingmessage').show();
            var gb = this;
            
            gb.errorModalGetBaln = function (){                                                           //Error Api Response Modal
            var ep = this;
            var modalInstanceApiErrGetbal = $uibModal.open({ 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/errorApiModal.html',
                controller: 'myCtrlErrModal',
                controllerAs: 'ep',
                resolve: {
                    data: function () {
                    return ep.response;
                    }
                }
            });
        
            modalInstanceApiErrGetbal.result.then(function () {
                });
            }
            
            gb.ModalGetBaln = function (){                                                           //Show Exchange Data
            var gb = this;
            var modalInstanceApiGetBaln = $uibModal.open({ 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/getBalanceModal.html',
                controller: 'myCtrlgetBalance',
                controllerAs: 'gb',
                resolve: {
                    data: function () {
                    return gb.response;
                    }
                }
            });
        
            modalInstanceApiGetBaln.result.then(function () {
                });
            }
            
            $http.post("<?php echo base_url()?>exchanges_blnc",{'exchange': ex})
            .then(function(response) {
                if(response.data == "User have no exchange data")
                {
                    gb.response = {errorApi: response.data};
                    gb.errorModalGetBaln();
                }
                else if(response.data == "empty")
                {
                    gb.response = {errorApi: "Invalid Apikey"};
                    gb.errorModalGetBaln();
                }
                else if(ex == "Binance" && response.data.indexOf("signedRequest error") != -1)
                {
                    gb.response = {errorApi: "Invalid Apikey"};
                    gb.errorModalGetBaln();
                }
                else
                {
                    gb.response = response.data;
                    gb.ModalGetBaln();
                }
            });
        }
    });
    
    app.controller('myCtrlTest', function($uibModalInstance, $http, data) {                           //Get Balances Of Exchange Modal
        var tm = this;
        // gb.data = data;
        
        tm.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    });
    
    app.controller('myCtrl1', function($uibModal, $uibModalInstance, $window, $http, data) {         //Second Controller To Open Api Modal
        var pc = this;
        pc.data = data;
        pc.buyFromEx = pc.data.buyExchange;
        pc.sellToEx = pc.data.sellExchange;
        pc.currSelected = pc.data.curr;
        pc.mainCurr = pc.data.mainCurr;
        pc.volume = pc.data.slider_value / pc.data.buyFrom;
        
        pc.errorModalFun = function (){                                                            //Error Api Response Modal
            var ep = this;
            var modalInstanceApiErr = $uibModal.open({ 
                animation: true,
                backdrop  : 'static',
                keyboard  : false,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/errorApiModal.html',
                controller: 'myCtrlErrModal',
                controllerAs: 'ep',
                resolve: {
                    data: function () {
                    return ep.response;
                    }
                }
            });
        
            modalInstanceApiErr.result.then(function () {
            });
        }  
        
        pc.confirmTrade = function () {                                                         //Confirm Trade Function
            if(pc.data.sellTo == 0 || pc.data.buyFrom == 0)
            {
                pc.response = {errorApi: "Trade on 0 is not allow."};
                pc.errorModalFun();
            }
            else
            {
                $http.post("<?php echo base_url()?>auto_hit_trade",{'buy_exchange': pc.buyFromEx, 'sell_exchange': pc.sellToEx, 'currency': pc.currSelected, 'volume': pc.volume, 'mainCurrency': pc.mainCurr})
                .then(function(response) {
                    if(response.data.error == "true")
                    {
                        pc.response = {errorApi: response.data.msg};
                        pc.errorModalFun();
                    }
                    if(response.data.login == "false")
                    {
                        window.location.replace("<?php echo base_url();?>login");
                    }
                    else if(response.data.success == "true")
                    {
                        pc.response = {errorApi: response.data.msg};
                        pc.errorModalFun();
                        $window.location.reload(true);
                    }
                });
            }
            $uibModalInstance.close();
        };
            
        pc.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    });
    
    app.controller('myCtrlErrModal', function($uibModalInstance, $window, $http, data) {                             //Error Modal
        var ep = this;
        ep.data = data;
        ep.errorApi = ep.data.errorApi;
        
        ep.cancel = function () {
            $http.get("<?php echo base_url();?>user_current_trade")
            .then(function(response) {
                if(response.data == 'no record found')
                {
                    $uibModalInstance.dismiss('cancel');
                }
                else
                {
                    $window.location.reload(true);
                }
            });
        };
    });
    
    app.controller('myCtrlgetBalance', function($uibModalInstance, $http, data) {                           //Get Balances Of Exchange Modal
        var gb = this;
        $('#loadingmessage').hide();
        gb.data = data;
        
        gb.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    });
    
                                                                                            //Transfer Coin Controller
    app.controller('myCtrlCT', function($uibModal, $uibModalInstance, $window, $http) { 
        var ct = this;
        ct.exchangesNames = ["BTCMarket", "Bithumb", "Kraken", "Binance","Bittrex", "Hitbtc", "Huobi", "Poloniex", "Livecoin", "Exmo"];
        ct.selectedFromExchange = "BTCMarket";
        ct.selectedToExchange = "BTCMarket";
        ct.currNamesTrans = ["USDT", "BTC", "ETH","LTC", "BCH", "XRP", "EOS","XMR", "XLM", "ADA", "DASH"]
        ct.selectedCurrTrans = "ETH";
        
        ct.errorModalFun = function (){                                                           //Error Api Response Modal
            var ep = this;
            var modalInstanceApiErr = $uibModal.open({ 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/errorApiModal.html',
                controller: 'myCtrlErrModal',
                controllerAs: 'ep',
                resolve: {
                    data: function () {
                    return ep.response;
                    }
                }
            });
        
            modalInstanceApiErr.result.then(function () {
            });
        }
        
        ct.transferCoinModalFun = function (){                                                           //Transfer Coins
            var ctn = this;
            var modalInstanceApiBalnce = $uibModal.open({ 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/balanceCoinTransfer.html',
                controller: 'myCtrlBalanceCoinModal',
                controllerAs: 'ctn',
                resolve: {
                    data: function () {
                    return ctn.response;
                    }
                }
            });
        
            modalInstanceApiBalnce.result.then(function () {
            });
        }
        
        ct.confirmTransfer = function (fromEx, toEx, Curr) {
            $http.post("<?php echo base_url()?>transfercoin_initial",{'from_exchange': fromEx, 'to_exchange': toEx, 'currency': Curr})
            .then(function(response) {
                if(response.data.hasOwnProperty('balance'))
                {
                    ct.response = {balanceApiGet: response.data.balance};
                    ct.transferCoinModalFun();
                }
                else
                {
                    ct.response = {errorApi: response.data};
                    ct.errorModalFun();
                    // $window.location.reload(true);
                }    
            });
            
            $uibModalInstance.close();
        };
        
        ct.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    });
    
    app.controller('myCtrlBalanceCoinModal', function($uibModal,$window, $uibModalInstance, $http, data) { 
        var ctn = this;
        ctn.data = data;
        ctn.balanceApiGet = ctn.data.balanceApiGet;
        ctn.balanceApiMax = ctn.data.balanceApiGet;
        
        ctn.errorModalFun = function (){                                                           //Error Api Response Modal
            var ep = this;
            var modalInstanceApiErr = $uibModal.open({ 
                animation: true,
                ariaLabelledBy: 'modal-title',
                ariaDescribedBy: 'modal-body',
                templateUrl: '<?php echo base_url();?>assets/backend/modals/errorApiModal.html',
                controller: 'myCtrlErrModal',
                controllerAs: 'ep',
                resolve: {
                    data: function () {
                    return ep.response;
                    }
                }
            });
        
            modalInstanceApiErr.result.then(function () {
            });
        }
        
        ctn.confirmTransferBalance = function (volume) {
            if(volume == 0)
            {
                alert("Volume must be greater than 0");
            }
            else if(volume > ctn.balanceApiMax)
            {
                alert("You don't have enough balance");
            }
            else
            {
                $http.post("<?php echo base_url()?>transfercoin",{'volume': volume})
                .then(function(response) {
                    ctn.response = {errorApi: response.data};
                    ctn.errorModalFun();
                    $window.location.reload(true);
                });
                $uibModalInstance.close();
            }    
        };
        
        ctn.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    });

</script>