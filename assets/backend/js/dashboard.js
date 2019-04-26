    window.onload = function()
    {alert("dfs");}
    // function copyToClipboard(element) 
    // {
    //     var $temp = $("<input>");
    //     $("body").append($temp);
    //     $temp.val($(element).text()).select();
    //     document.execCommand("copy");
    //     $temp.remove();
    //     console.log($(element).text());
          
    //     var x = document.getElementById("snackbar");
    //     x.className = "show";
    //     setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    // }
        
    window.onload = function()
    {
        getbalance();
    }
    function getbalance(){
        $.post( "<?php echo base_url(); ?>getbalance")
        .done(function( data ) {
        });
    }

   
   function sendWithdrawArb(){
        $('#ArbwithdrawModal').modal('hide');
        var value = document.getElementById("wdrawamtArb").value;
        $.post( "<?php echo base_url(); ?>sendWithdraw", { currency: 'ARB', amount: value, wallet: "<?php echo $u_wallet ?>"  })
          .done(function( data ) {
             // console.log(data);
            $('#withdrawDoneModal').modal('show');
            location.reload();
          });
  
    }
    
    function sendWithdrawEth(){
        $('#EthwithdrawModal').modal('hide');
        var value = document.getElementById("wdrawamtEth").value;
        $.post( "<?php echo base_url(); ?>sendWithdraw", { currency: 'ETH', amount: value, wallet: "<?php echo $u_wallet ?>"  })
          .done(function( data ) {
            // console.log(data);
            $('#withdrawDoneModal').modal('show');
            location.reload();
          });
  
    }
    
    function wdrawamtArb(value){
        if(value > <?php echo $activeArb; ?>){
            $('#wdrawamtArb').val(<?php echo $activeArb; ?>);
        }
    }
        
    function wdrawamtEth(value){
        if(value > <?php echo $activeEth; ?>){
            $('#wdrawamtEth').val(<?php echo $activeEth; ?>);
        }
    }
    
    function sendArb (){
        var amount = document.getElementById("amountArb").value;
        var page = document.getElementById("pages-optionArb").value;
        
        if(page == "aBOT"){
            $.post( "<?php echo base_url(); ?>system_to_abot", { abot_amount: amount})
              .done(function( data ) {
                  if(data == "true")
                  {
                      
                    $('#sendAmountDoneModal').modal('show');
                    location.reload();
                  }
                  else if(data == "false")
                  {
                       $('#noAmountSendModal').modal('show');
                  }
          });
        }
        else if (page == "mBOT")
        {
            $.post( "<?php echo base_url(); ?>system_to_mbot", { mbot_amount: amount})
              .done(function( data ) {
                   if(data == "true")
                  {
                    $('#sendAmountDoneModal').modal('show');
                    location.reload();
                  }
                  else if(data == "false")
                  {
                       $('#noAmountSendModal').modal('show');
                  }
          });
        }
        else if (page == "ex"){
            $.post( "<?php echo base_url(); ?>system_to_exchange", { ex_amount: amount, currency:'ARB'})
              .done(function( data ) {
                                   // console.log(data);

                  if(data == "true")
                  {
                    $('#sendAmountDoneModal').modal('show');
                    location.reload();
                  }
                  else if(data == "false")
                  {
                       $('#noAmountSendModal').modal('show');
                  }
          });
        }
        
    }
    function sendEth (){
        var amount = document.getElementById("amountEth").value;
        var page = document.getElementById("pages-optionEth").value;
        
        if (page == "ex"){
            $.post( "<?php echo base_url(); ?>system_to_exchange", { ex_amount: amount, currency:'ETH'})
              .done(function( data ) {
                 // console.log(data);
                  if(data == "true")
                  {
                    $('#sendAmountDoneModal').modal('show');
                    location.reload();
                  }
                  else if(data == "false")
                  {
                       $('#noAmountSendModal').modal('show');
                       
                  }
          });
        }
        
    }
    
    // function socket (){
    //     var connection = new WebSocket('wss://www.arbitraging.co/platform_dev/market_history');
    //     connection.onopen = function () {
    //       connection.send('Ping'); // Send the message 'Ping' to the server
    //     };
        
    //     // Log errors
    //     connection.onerror = function (error) {
    //       console.log('WebSocket Error ' + error);
    //     };
        
    //     // Log messages from the server
    //     connection.onmessage = function (e) {
    //       console.log('Server: ' + e.data);
    //     };
    // }
    // socket();
    $(".content-wrapper").css("background-color", "lightgrey");

     document.getElementById("metamaskWalletAddress").innerHTML = web3.eth.coinbase;
