<?php
    $mtime = filemtime('/home/arbitrage/arbblock.com/test_beta/assets/backend/css/trading.css');
?>
<link href="<?php echo base_url()?>assets/backend/css/trading.css?v1=<?php echo $mtime; ?>" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<div ng-app="myApp" ng-controller="myCtrl" class="depositBtnExch">
  <section class="">
    <div class="pageWrap">
      <div class="pageTopDiv">
            <!--//////////////////////////////////////////////////          Main Div One          ////////////////////////////////////////////////-->
        <div class="pT_Div pT_DivOne">
          <div class="pt_divBox">
            <div class="pt_divInner">
              <div class="pt_divInnerTop">
                <span><h4>ORDER BOOK</h4></span>
                <span class="exchPair">
                  <span>{{currSymbol}}</span>
                </span>
              </div>
              <div class="tbl-header">
			    <table class="mb-2" style="width:100%">
			      <thead>
			        <tr class="row">
                        <th class="col-4">Price</th>
                        <th class="col-4">Amount</th>
                        <th class="col-4">Total</th>
                    </tr>
			      </thead>
			    </table>
			  </div>
              <div class="pt_dI_oBook">
                <div id="orderBookAsks" class="pt_oB pt_oB_sell" scroll="orderBookAsks">
                  <table>
                    <tbody>
                      <tr ng-repeat="orderBookAsk in orderBookAsks" class="row">
                        <td class="col-4">{{orderBookAsk.price}}</td>
                        <td class="col-4">{{orderBookAsk.qty}}</td>
                        <td class="col-4">{{orderBookAsk.price * orderBookAsk.qty | number : 6}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>   
              </div>
              <div class="pt_dI_price text-center">
                <span class="redColor">{{orderBookLastPrice}}<span ng-if="!orderBookLastPrice"><div class="loaderSmall"></div></span></span>
              </div>
              <div class="pt_dI_oBook">
                <div class="pt_oB pt_oB_buy">
                  <table>
                    <tbody>
                      <tr ng-repeat="orderBookBid in orderBookBids" class="row">
                        <td class="col-4">{{orderBookBid.price}}</td>
                        <td class="col-4">{{orderBookBid.qty}}</td>
                        <td class="col-4">{{orderBookBid.price * orderBookBid.qty | number : 6}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
            <!--//////////////////////////////////////////////////          Main Div Second          ////////////////////////////////////////////////-->
        <div class="pT_Div pT_DivTwo">
          <div class="pt_dTwo_box">
            <div class="pt_dTwoTop">
              <div class="pt_dTwoTop_left">
                <span class="dropdown pt_dTwoTop_dropDown">
                     <!--href="https://example.com" id="dropdown0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"-->
                    <a class="dropdown-toggle pt_dTwoTop_dropToggle" ng-click="autoScroll = !autoScroll"><span>{{selectedEx}}</span> <i class="fas fa-angle-down"></i></a>
                    
                    <div id="displayHideEx" class="dropdown-menu pt_dTwoTop_dropMenu" ng-class="autoScroll ? 'show1' : 'show_'">
                      <div id="accordionExample" class="divUl accordion">
                        <ul ng-repeat="exName in exNames">
                          <li>
                              <div class="exchDiv">
                                <div class="">
                                  <span class="exchName" ng-click="selectEx(exName); displayHideEx();">{{exName}}</span>
                                  <span class="exchCollapse"><a class="exchCollapse" ng-click="getEx(exName)" data-toggle="collapse" href="#collapseExample{{exName}}" role="button" aria-expanded="false" aria-controls="collapseExample">
                                      <i class="fas fa-plus"></i></a>
                                  </span>
                                </div>
                                <div class="collapse" id="collapseExample{{exName}}" data-parent="#accordionExample">
                                  <div class="card card-body">
                                    <div class="exchDiv_inner">
                                        <input id="api_{{exName}}" type="text" class="form-control" name="api_key" value="" placeholder="API KEY">
                                        <input id="sec_{{exName}}" type="text" class="form-control" name="api_key" value="" placeholder="SECRET KEY">
                                        <button type="submit" name="button" class="" ng-click="connectEx(exName)">CONNECT</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                </span>
                <!--  -->
                
                <!--  -->
                <span class="exchLastPrice">
                  <!-- <div class=""> -->
                    <h6>Last Price</h6>
                    <h5><span class="redColor">{{orderBookLastPrice}}<span ng-if="!orderBookLastPrice"><div class="loaderSmall"></div></span></span></h5>
                  <!-- </div> -->
                </span>
                
                <span class="mx-auto">
                    <ul class="nav nav-tabs" id="chartTabs" role="tablist">
                      <li class="nav-item" style="display: inline-block;">
                        <a class="nav-link " id="depth-tab" data-toggle="tab" href="#depth" role="tab" aria-controls="depth" aria-selected="true">Depth View</a>
                      </li>
                      <li class="nav-item" style="display: inline-block;">
                        <a class="nav-link active" id="trade-tab" data-toggle="tab" href="#trade" role="tab" aria-controls="trade" aria-selected="false">Trading View</a>
                      </li>
                    </ul>
                </span>
                
                <span class="withdrawBtnEx">
                  <!-- <div class=""> -->
                    <button class="btn" ng-click="withdarwExFun()">OTC Desk</button>
                  <!-- </div> -->
                </span>
              </div>
              <div class="pt_dTwoTop_right"></div>
            </div>
            
          </div>
          
          <div class="">
              
            <!---->
            <!---->
            <div class="tab-content" id="tableTabsContent">
              <!--///////////////////////////////////////////////////////     depth    ////////////////////////////////////////////-->
              <div class="tab-pane fade" id="depth" role="tabpanel" aria-labelledby="depth-tab">
                  <div class="">
                      <div id="depthChart" style="width: 100%; height: 400px;"></div>
                  </div>
                
              </div>
              <!--///////////////////////////////////////////////////////     trade    ////////////////////////////////////////////-->
              <div class="tab-pane fade show active" id="trade" role="tabpanel" aria-labelledby="trade-tab">
                <div class="">
                    <div class="tradingview-widget-container">
                        <div id="tradingview_b62cb"></div>
                        <!--<div class="tradingview-widget-copyright">-->
                        <!--    <a href="https://www.tradingview.com/symbols/COINBASE-BTCUSD/" rel="noopener" target="_blank">-->
                        <!--        <span class="blue-text">BTCUSD chart</span>-->
                        <!--    </a> by TradingView-->
                        <!--</div>-->
                        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                        <script type="text/javascript"></script>
                    </div>
                </div>
              </div>
            </div>
            
            
          </div>
        </div>
            <!--//////////////////////////////////////////////////          Main Div Third          ////////////////////////////////////////////////-->
        <div class="pT_Div pT_DivThree">
          <div class="pT_DivThreeTop">
            <div class="pT_DivTTop_inner">

              <ul class="nav nav-tabs" id="tableTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="pairs-tab" data-toggle="tab" href="#pairs" role="tab" aria-controls="pairs" aria-selected="true">Pairs</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="favo-tab" data-toggle="tab" href="#favo" role="tab" aria-controls="favo" aria-selected="false">Favorites</a>
                </li>
              </ul>
              <div class="tab-content" id="tableTabsContent">
                <div class="tab-pane fade show active" id="pairs" role="tabpanel" aria-labelledby="pairs-tab">
                  <div class="tabsContent">
                    <div class="pairsFilter">
                      <div class="">
                        <div class="pairsFilterSearch">
                          <input type="text" ng-model="search_pair" placeholder="Search">
                          <button type="submit" name=""><i class="fas fa-search"></i></button>
                        </div>
                      </div>
                      <div class="">
                        <div class="pairsFilterUl">
                          <ul>
                            <li ng-repeat="exchangeCurrency in exchangeCurrencies"><a href="#" ng-click="getPairsQuoteCall(exchangeCurrency)">{{exchangeCurrency}}</a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="pt_dI_oBook">
                      <div class="pt_oB pt_oB_sell">
                        <table>
                          <thead>
                            <tr>
                              <th>
                                <span> Pair </span>
                              </th>
                              <th>
                                <span> Base </span>
                              </th>
                              <th>
                                <span> Quote </span>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr ng-repeat="exchangePair in exchangePairs | filter:search_pair" ng-click="getOrderBook(exchangePair.symbol, exchangePair.base, exchangePair.quote); set_chart(); userdataEx();">
                              <td>
                                <span>
                                  <a href="#"><i class="fas fa-star" ng-click="saveFavPair(exchangePair.symbol, exchangePair.base, exchangePair.quote);"></i></a>
                                  <span>{{exchangePair.base}}/{{exchangePair.quote}}</span>
                                </span>
                              </td>
                              <td>
                                <span class="">
                                  <span>{{exchangePair.base}}</span>
                                </span>
                              </td>
                              <td>
                                <span class="">
                                  <span>{{exchangePair.quote}}</span>
                                </span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                  </div>
                </div>
                <!--  -->
                <div class="tab-pane fade" id="favo" role="tabpanel" aria-labelledby="favo-tab">
                  <div class="tabsContent">
                    <div class="pairsFilter">
                      <div class="">
                        <div class="pairsFilterSearch">
                          <input type="text" ng-model="search_pairFav" placeholder="Search">
                          <button type="submit" name=""><i class="fas fa-search"></i></button>
                        </div>
                      </div>
                    </div>

                    <div class="pt_dI_oBook">
                      <div class="pt_oB pt_oB_sell">
                        <table>
                          <thead>
                            <tr>
                              <th>
                                <span> Pair </span>
                              </th>
                              <th>
                                <span> Base </span>
                              </th>
                              <th>
                                <span> Quote </span>
                              </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr ng-repeat="exchangeFavPair in exchangeFavPairs" ng-click="getOrderBook(exchangeFavPair.symbol, exchangeFavPair.base, exchangeFavPair.quote); set_chart(); userdataEx();">
                              <td>
                                  <a href="#" style="color:#f7bc2a"><i class="fas fa-star" ng-click="saveFavPair(exchangeFavPair.symbol, exchangeFavPair.base, exchangeFavPair.quote); getFavPair();"></i></a>
                                  <span>{{exchangeFavPair.symbol}}</span>
                              </td>
                              <td>
                                  <span>{{exchangeFavPair.base}}</span>
                              </td>
                              <td>
                                  <span>{{exchangeFavPair.quote}}</span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="pT_DivThreeBottom"></div>
        </div>
      </div>
      <div class="pageBottomDiv">
          <div class="pt_dTwo_bottom">
              <!-- exchange buy sell  -->
              <div class="pT_DivTTop_inner">
                <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="limit-tab" data-toggle="tab" href="#limit" role="tab" aria-controls="limit" aria-selected="true">Limit Order</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="market-tab" data-toggle="tab" href="#market" role="tab" aria-controls="market" aria-selected="false">Market Order</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" ng-click="stopLossFun()">Stop Loss</a>
                  </li>
                </ul>
                
                <div class="tab-content pr-3 pb-3" id="tableTabsContent">
                  <!--///////////////////////////////////////////////////////     Limit Orders    ////////////////////////////////////////////-->
                  <div class="tab-pane fade show active" id="limit" role="tabpanel" aria-labelledby="limit-tab">
                    <div class="orderContent br1">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="text-right orderBlncDiv"><span>Balance Available:</span> <span>{{tradingCurrSelectedBalanceQuote}} {{tradingCurrSelectedQuote}}</span></div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="orderDiv">
                                <div class="orderInput form-row">
                                  <label for="" class="form-label col-sm-3">Price</label>
                                  <input type="number" ng-model="limitBuyPrice" class="form-control col-sm-9" placeholder="Price">
                                  <span class="currency">{{tradingCurrSelectedQuote}}</span>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="orderDiv">
                                <div class="orderInput form-row">
                                  <label for="" class="form-label col-sm-3">Amount</label>
                                  <input type="number" ng-model="limitBuyQty" class="form-control col-sm-9" placeholder="Quantity">
                                  <span class="currency">{{tradingCurrSelected}}</span>
                                </div>
                                <!--<div class="text-right">-->
                                <!--  <ul>-->
                                <!--    <li><a href="#">5%</a> </li>-->
                                <!--    <li><a href="#">20%</a> </li>-->
                                <!--    <li><a href="#">50%</a> </li>-->
                                <!--    <li><a href="#">75%</a> </li>-->
                                <!--    <li><a href="#">100%</a> </li>-->
                                <!--  </ul>-->
                                <!--</div>-->
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="orderDiv">
                                <div class="orderInput form-row">
                                  <label for="" class="form-label col-sm-3">Total</label>
                                  <input type="number"  class="form-control col-sm-9" value="{{ limitBuyPrice * limitBuyQty }}" disabled>
                                  <span class="currency">{{tradingCurrSelectedQuote}}</span>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="orderBtnWrap">
                                <button type="button" name="button" class="greenBtn" ng-click="placeBuyOrderLimit()">BUY {{tradingCurrSelected}}</button>
                              </div>
                            </div>
                          </div>
    
                        </div>
                        <div class="col-sm-6">
                          <div class="text-right orderBlncDiv"><span>Balance Available:</span> <span>{{tradingCurrSelectedBalance}} {{tradingCurrSelected}}</span></div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="orderDiv">
                                <div class="orderInput form-row">
                                  <label for="" class="form-label col-sm-3">Price</label>
                                  <input type="number" ng-model="limitSellPrice" class="form-control col-sm-9" placeholder="Price">
                                  <span class="currency">{{tradingCurrSelectedQuote}}</span>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="orderDiv">
                                <div class="orderInput form-row">
                                  <label for="" class="form-label col-sm-3">Amount</label>
                                  <input type="number" ng-model="limitSellQty" class="form-control col-sm-9" placeholder="Quantity">
                                  <span class="currency">{{tradingCurrSelected}}</span>
                                </div>
                                <!--<div class="text-right">-->
                                <!--  <ul>-->
                                <!--    <li><a href="#">5%</a> </li>-->
                                <!--    <li><a href="#">20%</a> </li>-->
                                <!--    <li><a href="#">50%</a> </li>-->
                                <!--    <li><a href="#">75%</a> </li>-->
                                <!--    <li><a href="#">100%</a> </li>-->
                                <!--  </ul>-->
                                <!--</div>-->
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="orderDiv">
                                <div class="orderInput form-row">
                                  <label for="" class="form-label col-sm-3">Total</label>
                                  <input type="number" value="{{ limitSellPrice * limitSellQty }}" class="form-control col-sm-9" disabled>
                                  <span class="currency">{{tradingCurrSelectedQuote}}</span>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-12">
                              <div class="orderBtnWrap">
                                <button type="button" name="button" class="redBtn" ng-click="placeSellOrderLimit()">Sell {{tradingCurrSelected}}</button>
                              </div>
                            </div>
                          </div>
    
                        </div>
    
                      </div>
                    </div>
                  </div>
                  <!--///////////////////////////////////////////////////////     Market Orders    ////////////////////////////////////////////-->
                  <div class="tab-pane fade" id="market" role="tabpanel" aria-labelledby="market-tab">
                    <div class="orderContent">
                      <div class="orderContent br1">
                        <div class="row">
                          <div class="col-sm-6">
                            <div class="text-right orderBlncDiv"><span>Balance Available:</span> <span>{{tradingCurrSelectedBalanceQuote}} {{tradingCurrSelectedQuote}}</span></div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="orderDiv">
                                  <div class="orderInput form-row">
                                    <label for="" class="form-label col-sm-3">Price</label>
                                    <input type="text" class="form-control col-sm-9" placeholder="Market Price" disabled>
                                    <span class="currency">{{tradingCurrSelectedQuote}}</span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="orderDiv">
                                  <div class="orderInput form-row">
                                    <label for="" class="form-label col-sm-3">Amount</label>
                                    <input type="number" ng-model="marketBuyQty" class="form-control col-sm-9" placeholder="Quantity">
                                    <span class="currency">{{tradingCurrSelected}}</span>
                                  </div>
                                  <!--<div class="text-right">-->
                                  <!--  <ul>-->
                                  <!--    <li><a href="#">5%</a> </li>-->
                                  <!--    <li><a href="#">20%</a> </li>-->
                                  <!--    <li><a href="#">50%</a> </li>-->
                                  <!--    <li><a href="#">75%</a> </li>-->
                                  <!--    <li><a href="#">100%</a> </li>-->
                                  <!--  </ul>-->
                                  <!--</div>-->
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="orderBtnWrap">
                                  <button type="button" name="button" class="greenBtn" ng-click="placeBuyOrderMarket()">BUY {{tradingCurrSelected}}</button>
                                </div>
                              </div>
                            </div>
    
                          </div>
                          <div class="col-sm-6">
                            <div class="text-right orderBlncDiv"><span>Balance Available:</span> <span>{{tradingCurrSelectedBalance}} {{tradingCurrSelected}}</span></div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="orderDiv">
                                  <div class="orderInput form-row">
                                    <label for="" class="form-label col-sm-3">Price</label>
                                    <input type="text" class="form-control col-sm-9" placeholder="Market Price" disabled>
                                    <span class="currency">{{tradingCurrSelectedQuote}}</span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="orderDiv">
                                  <div class="orderInput form-row">
                                    <label for="" class="form-label col-sm-3">Amount</label>
                                    <input type="number" ng-model="marketSellQty" class="form-control col-sm-9" placeholder="Quantity">
                                    <span class="currency">{{tradingCurrSelected}}</span>
                                  </div>
                                  <!--<div class="text-right">-->
                                  <!--  <ul>-->
                                  <!--    <li><a href="#">5%</a> </li>-->
                                  <!--    <li><a href="#">20%</a> </li>-->
                                  <!--    <li><a href="#">50%</a> </li>-->
                                  <!--    <li><a href="#">75%</a> </li>-->
                                  <!--    <li><a href="#">100%</a> </li>-->
                                  <!--  </ul>-->
                                  <!--</div>-->
                                </div>
                              </div>
                              <div class="col-md-12">
                                <div class="orderBtnWrap">
                                  <button type="button" name="button" class="redBtn" ng-click="placeSellOrderMarket()">Sell {{tradingCurrSelected}}</button>
                                </div>
                              </div>
                            </div>
    
                          </div>
    
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="pT_DivTop_bottom">
                <div class="pT_DivTTop_inner pT_bottom">
                  <ul class="nav nav-tabs" id="openOrdersabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="balance-tab" data-toggle="tab" href="#balance" role="tab" aria-controls="balance" aria-selected="false">BALANCE</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="openOrder-tab" data-toggle="tab" href="#openOrder" role="tab" aria-controls="openOrder" aria-selected="true">OPEN ORDERS</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="balance" aria-selected="false">Trade History</a>
                    </li>
                  </ul>
                  <div class="tab-content" id="openOrdersContent">
                    <div class="tab-pane fade show active" id="balance" role="tabpanel" aria-labelledby="balance-tab">
                      <div class="orderContent">
                        <div class="orderContent">
                          <div class="tabsContent">
                            <div class="pairsFilter">
                              <div class="">
                                <div class="pairsFilterSearch">
                                  <input type="text" ng-model="search_balances" placeholder="Search">
                                  <button type="submit" name=""><i class="fas fa-search"></i></button>
                                </div>
                              </div>
                            </div>
                            <div class="pt_dI_oBook">
                              <div class="pt_oB pt_oB_sell">
                                <table>
                                  <thead>
                                    <tr>
                                      <th>
                                        <span>
                                            Currency
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          Balances
                                        </span>
                                      </th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr ng-repeat="usersData_balance in usersData_balances | filter:search_balances">
                                      <td>
                                        <span>
                                          <span>{{usersData_balance.currency}}</span>
                                        </span>
                                      </td>
                                      <td>
                                        <span class="">{{usersData_balance.balance}}</span>
                                        </span>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
    
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="openOrder" role="tabpanel" aria-labelledby="openOrder-tab">
                      <div class="orderContent">
                        <div class="tabsContent">
                          <div class="pt_dI_oBook">
                            <div class="pt_oB pt_oB_sell">
                              <table>
                                <thead>
                                  <tr>
                                    <th>
                                      <span>
                                        Pair
                                      </span>
                                    </th>
                                    <th>
                                      <span>
                                        Type
                                      </span>
                                    </th>
                                    <th>
                                      <span>
                                        Side
                                      </span>
                                    </th>
                                    <th>
                                      <span>
                                        AMOUNT
                                      </span>
                                    </th>
                                    <th>
                                      <span>
                                        PRICE
                                      </span>
                                    </th>
                                    <th>
                                      <span>
                                        DATE
                                      </span>
                                    </th>
                                    <th>
                                      <span>
                                        CANCEL
                                      </span>
                                    </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr ng-repeat="tradesOpenOrder in tradesOpenOrders">
                                    <td>
                                      <span>
                                        <span>{{tradesOpenOrder.pair}}</span>
                                      </span>
                                    </td>
                                    <td>
                                      <span>{{tradesOpenOrder.type}}</span>
                                    </td>
                                    <td>
                                      <span>{{tradesOpenOrder.side}}</span>
                                    </td>
                                    <td>
                                      <span>{{tradesOpenOrder.qty}}</span>
                                    </td>
                                    <td>
                                      <span>{{tradesOpenOrder.price}}</span>
                                    </td>
                                    <td>
                                      <span>{{tradesOpenOrder.time}}</span>
                                    </td>
                                    <td>
                                      <span><i class="fa fa-times-circle text-danger" ng-click="deleteOrderOpen(tradesOpenOrder.order_id)"></i></span>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
    
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                      <div class="orderContent">
                        <div class="orderContent">
                          <div class="tabsContent">
                            <div class="pt_dI_oBook">
                              <div class="pt_oB pt_oB_sell">
                                <table>
                                  <thead>
                                    <tr>
                                      <th>
                                        <span>
                                          Pair
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          Type
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          Side
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          AMOUNT
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          PRICE
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          DATE
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                      <th>
                                        <span>
                                          Status
                                          <a href="#"><i class="fas fa-angle-down"></i></a>
                                        </span>
                                      </th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr ng-repeat="successOrder in successOrders">
                                      <td>
                                        <span>
                                          <span>{{successOrder.pair}}</span>
                                        </span>
                                      </td>
                                      <td>
                                        <span>{{successOrder.type}}</span>
                                      </td>
                                      <td>
                                        <span>{{successOrder.side}}</span>
                                      </td>
                                      <td>
                                        <span>{{successOrder.qty}}</span>
                                      </td>
                                      <td>
                                        <span>{{successOrder.price}}</span>
                                      </td>
                                      <td>
                                        <span>{{successOrder.time}}</span>
                                      </td>
                                      <td>
                                        <span>{{successOrder.status}}</span>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
    
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--  -->
                  </div>
                  <!--  -->
                </div>
              </div>
            </div>
      </div>
      
    </div>
  </section>
</div>

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
    <!--            <br>Trading will be live soon-->
    <!--            <br><br>The Arbitraging Team-->
    <!--        </p>-->
    <!--      </div>-->
    <!--      <div class="modal-footer">-->
    <!--        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</div>-->
    
<script src="https://www.amcharts.com/lib/4/core.js"></script>
<script src="https://www.amcharts.com/lib/4/charts.js"></script>
<script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

<script>
    // $('#onLoadModal').modal('show');
    var app = angular.module('myApp', ['ngAnimate', 'ngSanitize', 'ui.bootstrap']);             //Main App
    app.controller('myCtrl', function($uibModal, $scope, $http, $log, $interval, $window) {        //Main Controller
        
        $scope.exNames = ["Binance", "Kraken", "Poloniex", "Huobi", "Bittrex", "HitBtc", "Exmo", "Livecoin", "Coinbene"];
        $scope.PairQouteArray = [];
        $scope.exchangePairs = [];
        $scope.exchangePairsComp = [];
        $scope.exchangeCurrencies = [];
        $scope.currSymbol = "ETHBTC";
        $scope.tradingCurrSelected = "ETH";
        $scope.usersData_balances = [];
        $scope.exchangeFavPairs = [];
        
        $scope.selectEx = function(exName) { 
            $scope.selectedEx = exName;
            $scope.getPairsCall(exName);
            $scope.chartDepth = true;
            $scope.getFavPair();
        }
        
        $scope.saveFavPair = function (pair, base, quote){
            $http.post("<?php echo base_url()?>add_fav_pairs",{'exchange': $scope.selectedEx, 'pair': pair, 'base': base, 'quote':quote})
            .then(function(response) { });
            keepGoing = true;
            $scope.getFavPair();
            $window.location.reload(true);
        }
        
        $scope.getFavPair = function() {
            $http({
              method: 'GET',
              url: '<?php echo base_url()?>get_fav_pairs'
            }).then(function successCallback(response) {
                var keepGoing = true;
                angular.forEach(response.data, function(value, key) {
                    if(keepGoing == true) {
                        if($scope.selectedEx == key) {
                            $scope.exchangeFavPairs = value;
                            keepGoing = false;
                        } else {
                            $scope.exchangeFavPairs = "";
                        }
                    }    
                });
            });
        }
        
        $scope.connectEx = function(exName) { 
            // toastr.error("This Feature is Currently Disable");
            $scope.selEx = exName;
            // window.alert("hi!");
            $scope.apikey = $("#api_"+exName).val();
            $scope.seckey = $("#sec_"+exName).val();
            if( $scope.apikey != '' || $scope.seckey != '' ){
                $http.post("<?php echo base_url()?>mbot_cred_trading",{'exchange': $scope.selEx, 'apikey':$scope.apikey, 'seckey':$scope.seckey})
                .then(function(response) {
                    if(response.data.success == 1) {
                        toastr.success(response.data.msg);
                    }
                    else if($scope.selEx == "Binance")
                    {
                        toastr.warning(response.data);
                    }
                    else if(response.data.error == 1) {
                        toastr.error(response.data.msg);
                    }
                    setInterval(function(){ $window.location.reload(true); }, 5000); 
                }); 
            } else { window.alert("Value cannot be empty!"); }
        }
        
        $scope.getEx = function(exName) { 
            $scope.selEx = exName;
            $("#api_"+exName).val();
            $("#sec_"+exName).val();
                $http.post("<?php echo base_url()?>get_exchange_keys",{'exchange': $scope.selEx})
                .then(function(response) {
                    if(response.data.api == "") {
                        $("#api_"+exName).val(response.data.api);
                        $("#sec_"+exName).val(response.data.sec);
                    } else {
                        $("#api_"+exName).val("********");
                        $("#sec_"+exName).val("********");
                    }    
                }); 
        }
        
        $scope.deleteOrderOpen = function(id,pair){
            $http.post("<?php echo base_url()?>cancel_order_exchange",{'exchange': $scope.selectedEx, 'pair': $scope.currSymbol, 'orderId': id})
            .then(function(response) {
                if(response.data.success = 1) {
                    toastr.success(response.data.msg);
                    $scope.userdataEx();
                } else {
                    toastr.error(response.data.msg);
                }
            });
        }
        
        $scope.userdataEx = function() {                                              //Gets Users Data
        
            $scope.tradingCurrSelectedBalance = ""; 
            $scope.tradingCurrSelectedBalanceQuote = "";
            
            $("#api_"+$scope.selectedEx).val();
            $("#sec_"+$scope.selectedEx).val();
            
            $http.post("<?php echo base_url()?>user_exchanges_data",{'exchange': $scope.selectedEx, 'pair': $scope.currSymbol})
            .then(function(response) {
                if(response.data.balances != "") {
                    $scope.usersData_balances = response.data.balances;
                } else {
                    $scope.usersData_balances = "";
                }
                
                if(response.data.trades.success != "") {
                    $scope.successOrders = response.data.trades.success;
                }else {
                    $scope.successOrders = "";
                }
                
                if(response.data.open != "") {
                    $scope.tradesOpenOrders = response.data.trades.open;
                }else {
                    $scope.tradesOpenOrders = "";
                }
                
                angular.forEach($scope.usersData_balances, function(value, key) {
                    if($scope.tradingCurrSelected == value.currency) {
                        $scope.tradingCurrSelectedBalance = value.balance;
                    }
                    if($scope.tradingCurrSelectedQuote == value.currency) {
                        $scope.tradingCurrSelectedBalanceQuote = value.balance;
                    }
                });
            }); 
        }
        
        $scope.getPairsCall = function (exName){                                                //Get Pairs 
            $('#loadingmessage').show();
            $http.post("<?php echo base_url()?>exchanges_pairs",{'exchange': exName})
            .then(function(response) {
                $scope.exchangePairs = response.data.pairs;
                $scope.exchangePairsComp = response.data.pairs;
                $scope.exchangeCurrencies = response.data.markets;
                $scope.getOrderBook($scope.exchangePairs[0].symbol, $scope.exchangePairs[0].base, $scope.exchangePairs[0].quote);
                $scope.currSymbol = $scope.exchangePairs[0].symbol;
                $scope.tradingCurrSelected = $scope.exchangePairs[0].base;
                $scope.userdataEx();
                $scope.set_chart();
                $('#loadingmessage').hide();
            });
        }
        
        $scope.set_chart = function(){
            if($scope.selectedEx == "Poloniex") {
                var myReplacedString = $scope.currSymbol.split('_');
                myReplacedString = myReplacedString[1] + myReplacedString[0];
                new TradingView.widget(
                {
                    "symbol": $scope.selectedEx + ':' + myReplacedString,
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "Light",
                    "style": "1",
                    "width": "100%",
                    "height": "420px",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "allow_symbol_change": true,
                    "container_id": "tradingview_b62cb"
                });
                $scope.chartDepth = true;
            } 
            else if($scope.selectedEx == "Bittrex") {
                var myReplacedString = $scope.currSymbol.split('-');
                myReplacedString = myReplacedString[1] + myReplacedString[0];
                new TradingView.widget(
                {
                    "symbol": $scope.selectedEx + ':' + myReplacedString,
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "Light",
                    "style": "1",
                    "width": "100%",
                    "height": "420px",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "allow_symbol_change": true,
                    "container_id": "tradingview_b62cb"
                });
                $scope.chartDepth = true;
            } 
            else if($scope.selectedEx == "Exmo" || $scope.selectedEx == "Livecoin" || $scope.selectedEx == "CoinBasePro" || $scope.selectedEx == "Coinbene") {
                $("#trade-tab").removeClass('active');
                $("#depth-tab").addClass('active');
                
                $("#trade").removeClass('show , active');
                $("#depth").addClass('show , active');
                
                $scope.chartDepth = true;
            }
            else {
                new TradingView.widget(
                {
                    "symbol": $scope.selectedEx + ':' + $scope.currSymbol,
                    "interval": "D",
                    "timezone": "Etc/UTC",
                    "theme": "Light",
                    "style": "1",
                    "width": "100%",
                    "height": "420px",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "allow_symbol_change": true,
                    "container_id": "tradingview_b62cb"
                });
                $scope.chartDepth = true;
            }    
        }
        
        $scope.displayHideEx = function(){
            angular.element(document.querySelector("#displayHideEx")).removeClass("show1");
        }
        
        $scope.stopLossFun = function() {
            toastr.warning("Feature Not Available Yet");
        }
        
        $scope.withdarwExFun = function(){
            toastr.warning("Coming Soon: OTC desk for converting to and from FIAT on all TradePro exchanges - More info will release soon.");
        }
        
        $scope.getPairsQuoteCall = function (market){                                                //Get Pairs According to Market
        
            $scope.PairQouteArray = [];
            angular.forEach($scope.exchangePairsComp, function(value, key) {
                if(market == value.quote) {
                    $scope.PairQouteArray.push(value);
                }
            });
            $scope.exchangePairs = $scope.PairQouteArray;
        }
        
        $scope.getOrderBook = function(symbol, base, quote) {
            $scope.currSymbol = symbol;
            $http.post("<?php echo base_url()?>exchanges_books",{'exchange': $scope.selectedEx, 'pair': symbol})
            .then(function(response) {
                $scope.orderBookAsks = response.data.asks;
                $scope.orderBookAsks = $scope.orderBookAsks.reverse();
                $scope.orderBookBids = response.data.bids;
                $scope.orderBookLastPrice = response.data.last_price;
                $scope.tradingCurrSelected = base;
                $scope.tradingCurrSelectedQuote = quote;
                
                var resultBids =  $scope.orderBookBids.map(function (obj) {
                    var subArrr = Object.keys(obj).slice(0).map(function(key) {
                        return obj[key];
                    });
                    return subArrr;
                });
                
                var resultAsks =  $scope.orderBookAsks.map(function (obj) {
                    var subArr = Object.keys(obj).slice(0).map(function(key) {
                        return obj[key];
                    });
                    return subArr;
                });
                
                if($scope.chartDepth == true) {
                    // Create chart instance
                    var chart = am4core.create("depthChart", am4charts.XYChart);
                    
                    //Add data
                    chart.dataSource.url = "";
                    // chart.dataSource.reloadFrequency = 1000000;
                    chart.dataSource.adapter.add("parsedData", function(data) {
                      
                      // Function to process (sort and calculate cummulative volume)
                      function processData(list, type, desc) {
                        // Convert to data points
                        for(var i = 0; i < list.length; i++) {
                          list[i] = {
                            value: Number(list[i][0]),
                            volume: Number(list[i][1]),
                          }
                        }
                    
                        // Sort list just in case
                        list.sort(function(a, b) {
                          if (a.value > b.value) {
                            return 1;
                          }
                          else if (a.value < b.value) {
                            return -1;
                          }
                          else {
                            return 0;
                          }
                        });
                    
                        // Calculate cummulative volume
                        if (desc) {
                          for(var i = list.length - 1; i >= 0; i--) {
                            if (i < (list.length - 1)) {
                              list[i].totalvolume = list[i+1].totalvolume + list[i].volume;
                            }
                            else {
                              list[i].totalvolume = list[i].volume;
                            }
                            var dp = {};
                            dp["value"] = list[i].value;
                            dp[type + "volume"] = list[i].volume;
                            dp[type + "totalvolume"] = list[i].totalvolume;
                            res.unshift(dp);
                          }
                        }
                        else {
                          for(var i = 0; i < list.length; i++) {
                            if (i > 0) {
                              list[i].totalvolume = list[i-1].totalvolume + list[i].volume;
                            }
                            else {
                              list[i].totalvolume = list[i].volume;
                            }
                            var dp = {};
                            dp["value"] = list[i].value;
                            dp[type + "volume"] = list[i].volume;
                            dp[type + "totalvolume"] = list[i].totalvolume;
                            res.push(dp);
                          }
                        }
                    
                      }
                    
                      // Init
                      var res = [];
                      processData(resultBids, "bids", true);
                      processData(resultAsks, "asks", false);
                    
                      return res;
                    });
                    
                    // Set up precision for numbers
                    chart.numberFormatter.numberFormat = "#,###.####";
                    
                    // Create axes
                    var xAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                    xAxis.dataFields.category = "value";
                    //xAxis.renderer.grid.template.location = 0;
                    xAxis.renderer.minGridDistance = 50;
                    xAxis.title.text = $scope.currSymbol;
                    
                    var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
                    yAxis.title.text = "Volume";
                    
                    // Create series
                    var series = chart.series.push(new am4charts.StepLineSeries());
                    series.dataFields.categoryX = "value";
                    series.dataFields.valueY = "bidstotalvolume";
                    series.strokeWidth = 2;
                    series.stroke = am4core.color("#0f0");
                    series.fill = series.stroke;
                    series.fillOpacity = 0.1;
                    series.tooltipText = "Ask: [bold]{categoryX}[/]\nTotal volume: [bold]{valueY}[/]\nVolume: [bold]{bidsvolume}[/]"
                    
                    var series2 = chart.series.push(new am4charts.StepLineSeries());
                    series2.dataFields.categoryX = "value";
                    series2.dataFields.valueY = "askstotalvolume";
                    series2.strokeWidth = 2;
                    series2.stroke = am4core.color("#f00");
                    series2.fill = series2.stroke;
                    series2.fillOpacity = 0.1;
                    series2.tooltipText = "Ask: [bold]{categoryX}[/]\nTotal volume: [bold]{valueY}[/]\nVolume: [bold]{asksvolume}[/]"
                    
                    var series3 = chart.series.push(new am4charts.ColumnSeries());
                    series3.dataFields.categoryX = "value";
                    series3.dataFields.valueY = "bidsvolume";
                    series3.strokeWidth = 0;
                    series3.fill = am4core.color("#000");
                    series3.fillOpacity = 0.2;
                    
                    var series4 = chart.series.push(new am4charts.ColumnSeries());
                    series4.dataFields.categoryX = "value";
                    series4.dataFields.valueY = "asksvolume";
                    series4.strokeWidth = 0;
                    series4.fill = am4core.color("#000");
                    series4.fillOpacity = 0.2;
                    
                    // Add cursor
                    chart.cursor = new am4charts.XYCursor();
                    $scope.chartDepth = false;
                }    
            });
        }
        
        $scope.selectEx("Binance");
        // $scope.connectEx();
        $scope.getOrderBook($scope.currSymbol,$scope.tradingCurrSelected, $scope.tradingCurrSelectedQuote);
        $interval(function (){$scope.getOrderBook($scope.currSymbol, $scope.tradingCurrSelected, $scope.tradingCurrSelectedQuote);}, 10000);  
        $scope.userdataEx();
        $scope.set_chart();

        
        $scope.placeBuyOrderLimit = function() {
            if($scope.limitBuyPrice == null || $scope.limitBuyQty == null){
                toastr.error('Please Enter Proper Data');
            }
            else if($scope.limitBuyPrice == null){
                toastr.error('Please Enter Price');
            }
            else if($scope.limitBuyQty == null) {
                toastr.error('Please Enter Quantity');
            }
            else {
                //  toastr.error("Trading is Currently Disable");
                $http.post("<?php echo base_url()?>place_trade_in_exchanges",{'exchange': $scope.selectedEx, 'pair': $scope.currSymbol, 'side': "Buy", 'type': "limit", 'price': $scope.limitBuyPrice, 'qty': $scope.limitBuyQty})
                .then(function(response) {
                    if(response.data.error == 1)
                    {
                        toastr.error(response.data.msg);
                    }
                    else if(response.data.success == 1)
                    {
                        toastr.success(response.data.msg);
                        $scope.userdataEx();
                    }
                    else if($scope.selectedEx == "Binance" && response.data.indexOf("signedRequest error") != -1)
                    {
                        toastr.error("Something Went Wrong");
                    }
                });
            }    
        }
        
        $scope.placeSellOrderLimit = function() {
            if($scope.limitSellPrice == null || $scope.limitSellQty == null){
                toastr.error('Please Enter Proper Data');
            }
            else if($scope.limitSellPrice == null){
                toastr.error('Please Enter Price');
            }
            else if($scope.limitSellQty == null) {
                toastr.error('Please Enter Quantity');
            }
            else {
                // toastr.error("Trading is Currently Disable");
                $http.post("<?php echo base_url()?>place_trade_in_exchanges",{'exchange': $scope.selectedEx, 'pair': $scope.currSymbol, 'side': "Sell", 'type': "limit", 'price': $scope.limitSellPrice, 'qty': $scope.limitSellQty})
                .then(function(response) {
                    if(response.data.error == 1)
                    {
                        toastr.error(response.data.msg);
                    }
                    else if(response.data.success == 1)
                    {
                        toastr.success(response.data.msg);
                        $scope.userdataEx();
                    }
                    else if($scope.selectedEx == "Binance" && response.data.indexOf("signedRequest error") != -1)
                    {
                        toastr.error("Something Went Wrong");
                    }
                });
            }    
        }
        
        $scope.placeBuyOrderMarket = function() {
            if($scope.marketBuyQty == null) {
                toastr.error('Please Enter Quantity');
            }
            else {
                // toastr.error("Trading is Currently Disable");
                $http.post("<?php echo base_url()?>place_trade_in_exchanges",{'exchange': $scope.selectedEx, 'pair': $scope.currSymbol, 'side': "Buy", 'type': "market", 'qty': $scope.marketBuyQty})
                .then(function(response) {
                    if(response.data.error == 1)
                    {
                        toastr.error(response.data.msg);
                    }
                    else if(response.data.success == 1)
                    {
                        toastr.success(response.data.msg);
                        $scope.userdataEx();
                    }
                    else if($scope.selectedEx == "Binance" && response.data.indexOf("signedRequest error") != -1)
                    {
                        toastr.error("Something Went Wrong");
                    }
                });
            }    
        }
        
        $scope.placeSellOrderMarket = function() {
            if($scope.marketSellQty == null) {
                toastr.error('Please Enter Quantity');
            }
            else {
                // toastr.error("Trading is Currently Disable");
                $http.post("<?php echo base_url()?>place_trade_in_exchanges",{'exchange': $scope.selectedEx, 'pair': $scope.currSymbol, 'side': "Sell", 'type': "market", 'qty': $scope.marketSellQty})
                .then(function(response) {
                    if(response.data.error == 1)
                    {
                        toastr.error(response.data.msg);
                    }
                    else if(response.data.success == 1)
                    {
                        toastr.success(response.data.msg);
                        $scope.userdataEx();
                    }
                    else if($scope.selectedEx == "Binance" && response.data.indexOf("signedRequest error") != -1)
                    {
                        toastr.error("Something Went Wrong");
                    }
                });
            }   
        }
        
        // $scope.checkAmountBase = function(e,amt) {
        //     if(amt > $scope.tradingCurrSelectedBalance){
        //         $scope.limitSellQty = parseFloat($scope.tradingCurrSelectedBalance);
        //         $scope.marketSellQty = parseFloat($scope.tradingCurrSelectedBalance);
        //         e.preventDefault();
        //     }
        // };
        
        // $scope.checkAmountQuote = function(e,amt) {
        //     if(amt > $scope.tradingCurrSelectedBalanceQuote){
        //         $scope.limitBuyQty = parseFloat($scope.tradingCurrSelectedBalanceQuote);
        //         $scope.marketBuyQty = parseFloat($scope.tradingCurrSelectedBalanceQuote);
        //         e.preventDefault();
        //     }
        // };
        
        toastr.options = {
          "closeButton": true,
          "debug": false,
          "newestOnTop": false,
          "progressBar": false,
          "positionClass": "toast-bottom-right",
          "preventDuplicates": false,
          "onclick": null,
          "showDuration": "400",
          "hideDuration": "1000",
          "timeOut": "3000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
        }
    });
    
    app.directive('scroll', function($timeout) {
      return {
        restrict: 'A',
        link: function(scope, element, attr) {
          scope.$watchCollection(attr.scroll, function(newVal) {
            $timeout(function() {
             element[0].scrollTop = element[0].scrollHeight;
            });
          });
        }
      }
    });
</script>