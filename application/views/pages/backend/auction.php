<?php
    $mtime = filemtime('/home/arbitrage/arbblock.com/test_beta/assets/backend/css/auction.css');
?>
<link href="<?php echo base_url()?>assets/backend/css/auction.css?v1=<?php echo $mtime; ?>" rel="stylesheet">

<div class="auction depositBtnExch ">
    <div class="row">
        <div class="col-sm-12">
            <div class="">
                <div class="ab_wrap scroll_">
                    <h3>Auction Book</h3>
                    <div class="text-right dropdownDiv">
                        <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Sort
                            <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                              <li class="dropdown-header">ASC</li>
                              <li class="dropdown-option"><a href="#" onclick="sortFunction('ASC', 'total_worth')">Total Worth</a></li>
                              <li class="dropdown-option"><a href="#" onclick="sortFunction('ASC', 'created_at')">Created At</a></li>
                              <li class="divider"></li>
                              <li class="dropdown-header">DESC</li>
                              <li class="dropdown-option"><a href="#" onclick="sortFunction('DESC', 'total_worth')">Total Worth</a></li>
                              <li class="dropdown-option"><a href="#" onclick="sortFunction('DESC', 'created_at')">Created At</a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="auctionBook" class="ab_boxWrap scroll_">
                        
                    </div>
                    <div id="PageBtnDiv">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    
    <div class="row userAuctionRow">
        <div class="col-sm-12">
            <div class="">
                <div class="ab_wrap scroll_">
                    <h3>My Auction</h3>
                    <div id="myAuctionBook" class="ab_boxWrap scroll_">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-sm-12">
            <div class="imgLogoMarg">
                <!--<div class="col-md-12">-->
                    <div class="">
                        <h3 class="acc_heading">My Open Bids</h3>
                    </div>
                    <div class="">
                        <div class="tableDivExchange">
                            <table class="table table-hover table-striped textAlignCenter">
                                <thead>
                                    <tr>
                                        <th>Account Worth</th>
                                        <th>My Bid</th>
                                        <th>Remaining Time</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody id="bidTable" class="textAlignCenter">
                                    <?php
                                        $countBid = 1;
                                        foreach($all_bids as $key => $a_b){ ?>
                                    <tr>
                                        <td><span class=""><?php echo $a_b['total_worth']; ?> ETH</span></td>
                                        <td><span class=""><?php echo $a_b['bid_value']; ?> ETH</span></td>
                                        <td><span class=""><?php echo $a_b['remaining_time']; ?></span></td>
                                        <td>
                                            <div class="acc_actions">
                                                <span class="">
                                                    <input id="rebid<?php echo $countBid; ?>" type="" value="" placeholder="Enter Value">
                                                    <button type="" class="" onclick="place_bid('rebid<?php echo $countBid; ?>','<?php echo $a_b['account_id']; ?>','<?php echo $a_b['bid_value']; ?>')">Re-Bid</button>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php $countBid++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <!--</div>-->
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div id="requestAccAuction" class="modal fade" role="dialog">
  <div class="modal-dialog ">       <!-- modal-lg -->
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header modalHeaderExchange">
        <h4 class="modal-title">Account Auction confirmation</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          
          <h6><strong>ARE YOU SURE YOU WISH TO ENTER YOUR ACCOUNT INTO ACCOUNT AUCTION?</strong></h6>
        <p>
        Arbitraging Team will respond withyour starting auction bid (based on many factors)After team has entered your starting bid, if accepted your 
        account will go into auction for 24 hours. During that auction the highest bidder will win and take ownership of your account. Your ETH from the 
        sale of your account will be instantly withdrawn to your External Wallet.All sales are finalDo you wish to proceed</p>
        <div class="">
            <div class="text-right">
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="proceedReq()"><span id="loaderGif" class="loader"></span>PROCEED</button>
            </div>
        </div>
      </div>
    </div>

  </div>
</div>
<div class="modal fade" id="ErrorModalGeneric" role="dialog">
   <div class="modal-dialog">
       <div class="modal-content">
          <div class="modal-header modalHeaderExchange">
            <h4 class="modal-title">Error</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
           <div class="modal-body">
               <h5 id="ErrorTextGeneric"></h5>
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
          <div class="modal-header modalHeaderExchange">
            <h4 class="modal-title" style="">Success</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
           <div class="modal-body">
               <h5 id="SuccessTextGeneric"></h5>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
           </div>
       </div>
   </div>
</div>
<!---->

<script>

    $.get("<?php echo base_url(); ?>get_user_auctions", function( data ) 
    {
        data = JSON.parse(data);
        if(data.success = 1) {
            
            var userAcution = data.result;
            if(userAcution.max_bid == ""){
                ethChart = "None";
            } else {
                ethChart = "ETH";
            }
            
            divUserRecord = `<div class="col-md-12">
                        <div class=""> 
                            <div class="ab_boxInner">
                            
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ETH Worth</th>
                                            <th>aBOT Worth</th>
                                            <th>Audit Percentage</th>
                                            <th>Hightest Bid</th>
                                            <th>State</th>
                                        </tr>    
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>`+ userAcution.total_worth +`</td>
                                            <td>`+ userAcution.abot_worth +`</td>
                                            <td>`+ userAcution.audit +` %</td>
                                            <td>`+ userAcution.max_bid +" "+ ethChart +`</td>
                                            <td>`+ userAcution.state +`</td>
                                        </tr>    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>`;
            $('#myAuctionBook').append(divUserRecord);
            $('.userAuctionRow').css('display', 'block');
        } else {
            $('.userAuctionRow').css('display', 'none');
        }
    });

    function proceedReq(){
        $("#loaderGif").css("display","inline-block");
        $.ajax({
          url: "<?php echo base_url(); ?>request_for_auction",
          type: "GET"
        })
        .done(function(data) {
            data = JSON.parse(data);
            $("#loaderGif").css("display","none");
            $("#requestAccAuction").modal("hide");
            if(data.success == "1"){
                $('#SuccessTextGeneric').html(data.msg);
                $('#SuccessModalGeneric').modal('show');
            }
            else if(data.error == "1"){
                $('#ErrorTextGeneric').html(data.msg);
                $('#ErrorModalGeneric').modal('show');
            }
        })
        .fail(function(data) {
            data = JSON.parse(data);
            console.log(data);
          });
    }

    if(<?php echo $total_auctions; ?> > 6) {
        $('#PageBtnDiv').css("display", "inline-block");
    }
    
    var totalPages = parseFloat(<?php echo $total_auctions; ?>);
    totalPages = totalPages / 6;
    totalPages = Math.ceil(totalPages);
    var btnDIv = "";

    for(var i = 1; i <= totalPages; i++) {
        if(i == 1) {activeClassPag = "active";} else {activeClassPag = "";}
        btnDIv = `<button id="page_`+i+`" type="button" class="btn btn-default myPagination `+activeClassPag+`" onclick="pageNoNext(`+i+`)">`+ i + `</button>`;
        $("#PageBtnDiv").append(btnDIv);
    }
    
    var page_no = 1;
    var sortingType = "ASC";
    var sortName = "total_worth";
    var minBid = '';
    var div33 = '';
    var count = 1;
    
    ////////////////////////////////////////////////
    function pageNoNext(num) {
        page_no = num;
        $(".myPagination").removeClass("active");
        $("#page_"+num).addClass("active");
        get_auctions();
    }
    ////////////////////////////////////////////////
    function sortFunction(sort, filed) {
        sortingType = sort;
        sortName = filed;
        get_auctions();
    }
    
    // <div class=""><h6><span class=""><i class="fas fa-check-circle"></i>Audit Percentage</span><span id="">`+ accBook[i].auction.audit +` %</span></h6></div>
    //                                     <div class=""><h6><span class=""><i class="fas fa-check-circle"></i>Pending</span><span id="">0</span></h6></div>
                                        
    ////////////////////////////////////////////////
    function get_auctions(){
        $("#auctionBook").html("");
        count = 1;
        $.post( "<?php echo base_url(); ?>get_auctions", {page_no:page_no, sort:sortingType, field:sortName})
           .done(function( data ) {
            var accBook = data;
            accBook = JSON.parse(accBook);   
            // console.log(accBook);
             $.each(accBook , function(i) {
                 
                if(accBook[i].max_bid_amount == ""){
                    ethChart = "None";
                } else {
                    ethChart = "ETH";
                }
                 
                div33 = `<div class="col33">
                            <div class="ab_box"> 
                                <div class="ab_boxInner">
                                    <div class="bidValue">
                                        <span id="" class="worthValue">`+ accBook[i].auction.total_worth +` ETH</span>    
                                    </div>
                                    <div class="accDetails">
                                        <div class=""><h6><span class=""><i class="fas fa-check-circle"></i>aBOT Active (aUSD)</span><span id="">`+ accBook[i].auction.abot_worth +`</span></h6></div>
                                        <div class="mt-4"><h6><span class="">Remaining Time</span><span id="">`+ accBook[i].remain_time +`</span></h6></div>
                                    </div>
                                    <div class="bidDetails">
                                        <div class="">
                                            <h6><span class="">Highest Bid</span>  <span id="">`+ accBook[i].max_bid_amount +" "+ ethChart +`</span></h6>
                                        </div>
                                        <div class="">
                                            <h6><span class="">aBOT Bid</span> <span id="">`+ accBook[i].min_bid_amount +` ETH</span></h6>
                                        </div>
                                    </div>
                                    <div class="acc_actions">
                                        <span class="">
                                            <input type="number" name="" id="bid`+ count +`" class="" placeholder="Enter your bid">
                                            <button class="" onclick="place_bid('bid`+ count +`','`+ accBook[i].auction.account_id +`','`+ accBook[i].min_bid_amount +`')">Bid</button>
                                        </span>
                                    </div>
                                </div>
                            </div> 
                        </div>`;
                $('#auctionBook').append(div33);
                count++;
            });
        });
    }
    get_auctions();
    
    //  onkeyup="minBid('bid`+ count +`','`+ accBook[i].min_bid_amount +`')"
    // function minBid(value,minBid){
    //     var input = input;
        
    //     if( ( $("#"+input).val() ) > (minBid+0.1) ){
            
    //         alert("OK");
            
    //     }else { alert("not OK"); }
    // }
    
    
    ////////////////////////////////
    function place_bid(input,value,minBid){
        var input = input;
        
        var minBid_ = minBid;
        var inputValue = $("#"+input).val();
        
        minBid_ = parseFloat(minBid_);
        minBid_ = parseFloat(minBid_ + 0.1);
        if( $("#"+input).val() >= minBid_ ){
            $.post( "<?php echo base_url(); ?>place_bid", {bid_amount:inputValue, account_id:value })
               .done(function( data ) {
                data = JSON.parse(data);
                if(data.success == "1"){
                    $('#SuccessTextGeneric').html(data.msg);
                    $('#SuccessModalGeneric').modal('show');
                    setInterval(function(){ location.reload(); }, 3000);
                }
                else if(data.error == "1"){
                    $('#ErrorTextGeneric').html(data.msg);
                    $('#ErrorModalGeneric').modal('show');
                }
           });
        }else { 
            alert("Less value than last bid not allowed.");
        }
    }
    
    ////////////////////////////////////////////
    
    // function countdown_bm(countDownDate){
    //     var x = setInterval(function() {

    //       // Get todays date and time
    //       var now = new Date().getTime();
        
    //       // Find the distance between now and the count down date
    //       var distance = countDownDate - now;
        
    //       // Time calculations for days, hours, minutes and seconds
    //       var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    //       var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
       
    //       // Display the result in the element with id="demo"
    //       if (distance < 30000) {
    //           $("#demoo").css('color', 'red');
    //           document.getElementById("demoo").innerHTML = minutes + "m:" + seconds + "s";
    //       }
    //       else {
    //           $("#demoo").css('color', '#daaf2a');
    //           document.getElementById("demoo").innerHTML = minutes + "m:" + seconds + "s";
    //       }
    //       // If the count down is finished, write some text 
    //       if (distance < 0) {
    //         clearInterval(x);
    //         document.getElementById("demoo").innerHTML = "00:00"; tflagg = false;
    //       }
    //     }, 1000);
    // }
 </script>