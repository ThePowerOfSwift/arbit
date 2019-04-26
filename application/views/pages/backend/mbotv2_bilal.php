<link href="<?php echo base_url()?>assets/backend/css/mbotv2.css" rel="stylesheet">

    <!--////////////////////////////////////////////////////// Charts /////////////////////////////////////////-->
    
    <div ng-app="my-app">
        <div ng-controller="MainController">
          <div class="row mt-5">
            <div class="col-md-5">
              <h2>Drop</h2>
              <div class="boxDiv">
                <div>
                  <h4 style="display: flex;justify-content: space-between;">
                    <span>Step 1</span>
                    <span class="">
                      <button id="empty"><i class="fas fa-trash"></i> Remove all</button>
                    </span>
                  </h4>
                </div>
                <div class="dropExchangeWraper">
                  <div id="fromDiv" class="dropableDiv">
                    <strong>From</strong>
                    <br><br>
                    <div id="asdf" class="div1 dropBox" droppable="true">
                      <div class="dragableDiv" ng-class="{'displayNone': from.exchange == ''}" ng-repeat="from in fromItems">{{from.exchange}}</div>
                    </div>
                    <strong>Pair</strong>
                    <br><br>
                    <div class="div1 dropBox" droppable="true">
                      <div class="dragableDiv" ng-repeat="fromP in fromItems">{{fromP.pair}}</div>
                    </div>
                  </div>
                  <div id="toDiv" class="dropableDiv">
                    <strong>To</strong>
                    <br><br>
                    <div class="div1 dropBox" droppable="true">
                      <div class="dragableDiv" ng-repeat="to in toItems">{{to.exchange}}</div>
                    </div>
                    <strong>Pair</strong>
                    <br><br>
                    <div class="div1 dropBox" droppable="true">
                      <div class="dragableDiv" ng-repeat="toP in toItems">{{toP.pair}}</div>
                    </div>
                  </div>
    
                  <span class="profitSpan">
                    <div class="profitdiv"><input type="text" name="" value="" placeholder="$2000"></div>
                    <div class="profitArrow">
                      <i class="fas fa-long-arrow-alt-right"></i>
                    </div>
                    <div class="profitPrecentdiv"><input type="text" name="" value="" placeholder="5%" readonly=""></div>
                  </span>
    
                </div>
              </div>          
              <hr>
              <div class="boxDiv">
                <h4>Step 2</h4>
    
                <div class="dropExchangeWraper">
                  <div class="dropableDiv">
                    <strong>From</strong>
                    <br><br>
                    <div class="dragableDiv">
                      <h5>Kraken</h5>
                    </div>
                  </div>
                  <div class="dropableDiv">
                    <strong>To</strong>
                    <br><br>
                    <div class="dragableDiv">
                      <h5>Bithumb</h5>                
                    </div>
                  </div>
                  <span class="profitSpan">
                    <div class="profitdiv"><input type="text" name="" value="" placeholder="$2000"></div>
                    <div class="profitArrow">
                      <i class="fas fa-long-arrow-alt-right"></i>
                    </div>
                    <div class="profitPrecentdiv"><input type="text" name="" value="" placeholder="5%"></div>
                  </span>
                </div>
              </div>
              <hr>
    
              <div class="boxDiv">
                <h4>Step 3</h4>
    
                <div class="dropExchangeWraper">
                  <div class="dropableDiv">
                    <strong>From</strong>
                    <br><br>
                    <div class="dragableDiv">
                      <h5>Kraken</h5>
                    </div>
                  </div>
                  <div class="dropableDiv">
                    <strong>To</strong>
                    <br><br>
                    <div class="dragableDiv">
                      <h5>Bithumb</h5>                
                    </div>
                  </div>
                  <span class="profitSpan">
                    <div class="profitdiv"><input type="text" name="" value="" placeholder="$2000"></div>
                    <div class="profitArrow">
                      <i class="fas fa-long-arrow-alt-right"></i>
                    </div>
                    <div class="profitPrecentdiv"><input type="text" name="" value="" placeholder="5%"></div>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-7">
              <h2>Drag</h2>
              <div class="boxDiv">
                <div class="dragExchangeWraper">
                  <div class="dragableDiv exchange" draggable="true" ng-repeat="drag_type in drag_types" droppable="true">{{drag_type}}</div>
                </div>
              </div>
    
              <hr>
              <div class="boxDiv">
                <div class="dragExchangeWraper">
                  <div class="dragableDiv" draggable="true" ng-repeat="pair_type in pair_types" droppable="true">{{pair_type.name}}</div>
                </div>
              </div>
    
            </div>
          </div>
        </div>
    </div>
      

<!--////////////////////////////////////////////////////// Script /////////////////////////////////////////-->

<script>
    var app = angular.module('my-app', []);
    
    app.directive('draggable', function () {
      return {
        restrict: 'A',
        link: function (scope, element, attrs) {
          element[0].addEventListener('dragstart', scope.handleDragStart, false);
          element[0].addEventListener('dragend', scope.handleDragEnd, false);
        }
      }
    });
    
    app.directive('droppable', function () {
      return {
        restrict: 'A',
        link: function (scope, element, attrs) {
          element[0].addEventListener('drop', scope.handleDrop, false);
          element[0].addEventListener('dragover', scope.handleDragOver, false);
        }
      }
    });
    
    app.controller('MainController', function($scope, $http) {
        
        $scope.drag_types = ["Kraken","Bithumb","BTCMarkets","Poloniex","Binance","Bittrex","HitBTC","Huobi","Livecoin","Exmo"];
        // var url = "https://api.kraken.com/0/public/AssetPairs";
        // $http({
        //     method: 'JSONP',
        //     url: url
        // }).
        // success(function(status) {
        //     //console.log(status);
        // }).
        // error(function(status) {
        //     //console.log(status);
        // });
    
        // $http.get("https://api.kraken.com/0/public/AssetPairs")
        // .then(function(response) {
        //     console.log(response);
        // });
    
        $scope.pair_types = [
            {name: "USD/ETH"},
            {name: "USD/BTC"},
            {name: "USD/LTC"},
            {name: "USD/XRP"},
            {name: "USD/BCH"},
            {name: "USD/ADA"},
            {name: "USD/XMR"},
            {name: "USD/XLM"}
        ];
    
        $scope.fromItems = [];
        $scope.toItems = [];
        $scope.ExCountFrom = 0; $scope.PairCountFrom = 0;
        $scope.ExCountTo = 0; $scope.PairCountTo = 0;
    
        $scope.handleDragStart = function(e){
            this.style.opacity = '0.4';
            e.dataTransfer.setData('text', this.innerHTML);
        };
        
        $scope.handleDragEnd = function(e){
            this.style.opacity = '1.0';
        };
        
        $scope.handleDrop = function(e){
            e.preventDefault();
            e.stopPropagation();
    
            var dataText = e.dataTransfer.getData('text');
            $scope.$apply(function() {
                if(e.target.parentNode.id == "fromDiv"){                        //Check If its From Exchange Or To Exchange
    
                    if($scope.drag_types.indexOf(dataText) != -1)                             //Check If its Pair Or Exchange
                    {
                        if($scope.ExCountFrom < 1)                             //Check For One Exchange only
                        {
                            $scope.fromItems.push({'exchange': dataText});
                            $scope.ExCountFrom = 1;
                        }else {
                            alert("More than one Exchange is not allowed.")
                        }
                    }
                    else
                    {
                        if($scope.PairCountFrom < 1)                            //Check For One Pair only
                        {
                            $scope.fromItems.push({'pair': dataText});
                            $scope.PairCountFrom = 1;
                        }else {
                            alert("More than one Pair is not allowed.")
                        }
                    }
                    console.log($scope.fromItems);
                }
                else if(e.target.parentNode.id == "toDiv") {
                    
                    if($scope.drag_types.indexOf(dataText) != -1)                          //Check If its Pair Or Exchange
                    {
                        if($scope.ExCountTo < 1)                             //Check For One Exchange only
                        {
                            $scope.toItems.push({'exchange': dataText});
                            $scope.ExCountTo = 1;
                        }else {
                            alert("More than one Exchange is not allowed.")
                        }
                    }
                    else
                    {
                        if($scope.PairCountTo < 1)                            //Check For One Pair only
                        {
                            $scope.toItems.push({'pair': dataText});
                            $scope.PairCountTo = 1;
                        }else {
                            alert("More than one Pair is not allowed.")
                        }
                    }
                    console.log($scope.toItems);
                }
                
            });
        };
        
        $scope.handleDragOver = function (e) {
            e.preventDefault(); // Necessary. Allows us to drop.
            e.dataTransfer.dropEffect = 'move';  // See the section on the DataTransfer object.
            return false;
        };
        
        $scope.sayHi = function() {
            console.log($scope.fromItems);
        };
    });

</script>
