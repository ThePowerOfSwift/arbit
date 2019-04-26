<?php 
    foreach($code as $c){
        $u_wallet = $c['u_wallet'];
        $a_code = $c['a_code'];
    }
    if(!isset($a_code)){
     if($a_code == "" || $a_code == NULL) { $a_code = "jvlKdrUN"; }
    }
?>
<style>
.addressSpan {margin: 8% 0 5%;}
/* The container */
.container1 {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 22px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
/* Hide the browser's default checkbox */
.container1 input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}
/* Create a custom checkbox */
.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
}
/* On mouse-over, add a grey background color */
.container1:hover input ~ .checkmark {
    background-color: #ccc;
}
/* When the checkbox is checked, add a blue background */
.container1 input:checked ~ .checkmark {
    background-color: #daa521;
}
/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}
/* Show the checkmark when checked */
.container1 input:checked ~ .checkmark:after {
    display: block;
}
/* Style the checkmark/indicator */
.container1 .checkmark:after {
    left: 9px;
    top: 3px;
    width: 7px;
    height: 16px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
.rowAddressDeposit{
    background-color: #f5f5f5  !important;
    border-radius: 10px !important;
    padding: 10px;
    box-shadow: 0px 0px 10px #888;
}
#resultDivi > h3{margin-botom: 20px;}
#modalButton, .buttonGold{
    background: #daa521;
    outline: 0;
    cursor: pointer;
    box-shadow: none;
    text-transform: uppercase;
    transition: 0.3s ease all;
}
#modalButton:hover, .buttonGold:hover{
    background: #f9b200;
}
#resultDivi h4{margin-bottom: 20px;}
@media screen and ( max-width: 767px ){
    .depositHeading{font-size: 24px;}
    .rowAddressDeposit{font-size: 20px;word-break: break-all;background: #a9a9a9;display: block;}
    .addressSpan{padding: 10px;margin: 20px;}
    #myModal .text-danger{font-size: 18px;}
    .container1{font-size: 18px;}
    #modalButton{font-size: 18px;padding: 10px;}
    #resultDivi h4{font-size: 18px;}
    #myModal ul{padding-left: 10px;text-align: justify;}
}
@media screen and ( max-width: 600px ){
    .depositHeading{font-size: 18px;}
    .rowAddressDeposit{font-size: 16px;word-break: break-all;}
    .addressSpan{padding: 10px;margin: 10px;}
    #myModal.text-danger{font-size: 16px;}
    .container1{font-size: 16px;}
    #modalButton{font-size: 16px;padding: 5px;}
    #resultDivi h4{font-size: 16px;}
}
</style>
<div>
    <div class="row margin_top60">
        <div class="col-md-12 textAlignCenter">
            <h2 class="depositHeading">Your PLATFORM REGISTERED WALLET Address:</h2>
            <h3 class="rowAddressDeposit">  <?php echo $u_wallet; ?> </h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8 addressSpan textAlignCenter">
              <button id="modalButton" type="button" class="btn btn-info btn-lg buttonGold" data-toggle="modal" data-target="#myModal">Show Deposit Address</button>
              <div id="resultDivi"></div>
              <!-- Modal -->
              <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-body" style="text-align: left;">
                      <!--<p><h3 class="text-danger"></BRl></h3></p>-->
                      <h2 class="text-danger">Warning...</h2>
                      <ul>
                          <li>
                              <h4 class="text-danger">ONLY DEPOSIT TO THIS ADDRESS FROM YOUR PLATFORM REGISTERED WALLET OR YOUR DEPOSIT WILL BE LOST.</h4>
                          </li>
                          <li>
                              <h4 class="text-danger">DO NOT DEPOSIT FROM EXCHANGES, FIRST SEND TO YOUR PLATFORM REGISTERED WALLET THEN SEND TO ARB PLATFORM.</h4> 
                          </li>
                          <li>
                              <h3 class="text-danger">IF YOU HAVE MULTIPLE ARB ACCOUNTS WITH THE SAME ETHER WALLET ADDRESS YOUR DEPOSIT WILL NOT SHOW UP.</h3>
                          </li>
                          <li>
                              <h3 class="text-danger">ALL TRANSACTIONS MADE BY USER ERROR WILL HAVE UP TO &#36;10 or 10% SERVICE FEE TO LOCATE AND FIX YOUR ERROR.</h3>
                          </li>
                          <li>
                              <h3 class="text-danger">Deposit fees are 0.0015ETH for ETH deposits and 0.5 ARB for ARB deposits.</h3>
                          </li>
                      </ul>
                        <div class="descpBOT2">
                        </div>
                        <div class="col-12">
                            <div class="pull-left">
                                <label class="container1">I agree, continue.
                                  <input id="agreeCheckBox" type="checkbox" class="form-check-input">
                                  <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary buttonGold" onclick="myFunction()">Continue..</button>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<script>
     // Get IP Address
     function getIpAddress(){
        $.get("https://ipapi.co/json/")
            .done(function( data ) {
                 var ip_Address = data.country;
                 if(ip_Address == "US")
                 {
                     $('#modalButton').prop('disabled', true);
                 }
             });
     }
     getIpAddress();
    
    function myFunction() {
        if( $("#agreeCheckBox").is(':checked')){
            
        $("#myModal").modal("hide");
        $("#modalButton").hide();
        $("#resultDivi").append(`<h4>USE THIS DEPOSIT ADDRESS FOR ARB AND ETH.</h4>
                        <h3><span class="rowAddressDeposit" id=address>0x6705120db9Fb682deC120cDEcC8220385D25fC50</span></h3>
                        <a href="#" onclick="copyToClipboard('#address')">Copy Address</a>
                        <div id="snackbar">Address Copy</div>
                        `);
        }
        else{
            alert("Agree to Continue..");
        }
    }
    //
	$(".content-wrapper").css("background-color", "lightgrey");
    function deposit()
    {
         $('#address').html('searching...');
         $('#addressAlready').html('searching...');
        var currency = document.getElementById("pagesOption").value;
        if(currency == "ARB"){
            $("#depsiteDivNoData").css("display", "block");
            $("#CurrDepositDiv").css("display", "none");
            // $.ajax({
            //     type: "POST",
            //     url: "<?php echo base_url(); ?>get_deposit_address",
            //     data: {
            //         currency : currency
            //     },
            //     success: function(data){
                    
            //         var res = data.split("#");
            //         $("#depsiteDivNoData").css("display", "block");
            //         if(res[0] == "please wait")
            //         {
            //             $('#depsiteDivAready').css("display", "none");
            //             $("#depsiteDivARB").css("display", "none");
            //             $("#CurrDepositDiv").css("display", "none");
            //             $("#depsiteDivNoData").css("display", "block");
            //         }
            //         else if(res[0] == "Already Allocated")
            //         {
            //             $("#depsiteDivARB").css("display", "none");
            //             $("#depsiteDivNoData").css("display", "none");
            //             $("#CurrDepositDiv").css("display", "none");
            //             $("#depsiteDivAready").css("display", "block");
            //             $('#addressAlready').html(res[1]);
            //         }
            //         else
            //         {
            //             $('#depsiteDivNoData').css("display", "none");
            //             $('#depsiteDivAready').css("display", "none");
            //             $("#CurrDepositDiv").css("display", "none");
            //             $("#depsiteDivARB").css("display", "block");
            //             $('#address').html(data);
            //         }
            //     }
            // });
        }
        else if (currency == "ETH"){
            $("#depsiteDivNoDataETH").css("display", "block");
            $("#CurrDepositDiv").css("display", "none");
            // $.ajax({
            //     type: "POST",
            //     url: "<?php echo base_url(); ?>get_deposit_address",
            //     data: {
            //         currency : currency
            //     },
            //     success: function(data){
                    
            //         var res = data.split("#");
                    
            //         if(res[0] == "please wait")
            //         {
            //             $('#depsiteDivAreadyETH').css("display", "none");
            //             $("#depsiteDivETH").css("display", "none");
            //             $("#CurrDepositDiv").css("display", "none");
            //             $("#depsiteDivNoDataETH").css("display", "block");
            //         }
            //         else if(res[0] == "Already Allocated")
            //         {
            //             $("#depsiteDivETH").css("display", "none");
            //             $("#depsiteDivNoDataETH").css("display", "none");
            //             $("#CurrDepositDiv").css("display", "none");
            //             $("#depsiteDivAreadyETH").css("display", "block");
            //             $('#addressAlreadyETH').html(res[1]);
            //         }
            //         else
            //         {
            //             $('#depsiteDivNoDataETH').css("display", "none");
            //             $('#depsiteDivAreadyETH').css("display", "none");
            //             $("#CurrDepositDiv").css("display", "none");
            //             $("#depsiteDivETH").css("display", "block");
            //             $('#addressETH').html(data);
            //         }
            //     }
            // });
        }
    }
    
    function copyToClipboard(element) 
    {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
        console.log($(element).text());
          
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    }
    
</script>