function sellOrderReq(ajaxSellOrder, sellAmount_check, price, walSelected, flagValue) {
    $.ajax({
        type: 'POST',
        url: ajaxSellOrder,
        data: {
            order_type: 'Sell',
            amount: sellAmount_check.toString(),
            price: price,
            wallet:walSelected,
            check_flag: flagValue
        },
        success: function(data)
        {
            data = JSON.parse(data);
            
            if(data.error == '1')
            {
                $('#ErrorModalGeneric').modal('show');
                $('#ErrorTextGeneric').html(data.msg);
                setInterval(function(){ $('#ErrorModalGeneric').modal('hide'); }, 3000);
            }
            else if(data.success == '1')
            {
                $('#sellPrice').val('0.0000');
                $('#sellAmount').val('0.0000');
                $('#sellTotal').val('0.0000');
                getOrderBook();
                getBalnaceARBETH();
                
                var x = document.getElementById("snackbarSell");
                x.className = "show";
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
            }
        }
    });   
}