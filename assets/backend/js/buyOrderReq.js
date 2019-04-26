function buyOrderReq(ajaxSellOrder, buyAmount, price) {
    $.ajax({
        type: 'POST',
        url: ajaxSellOrder,
        data: {
            order_type: 'Buy',
            amount: buyAmount.toString(),
            price: price.toString(),
            wallet: "exchange",
            check_flag: "0"
        },
        success: function(data)
        {
            console.log(data);
            data = JSON.parse(data);
            
            if(data.error == '1')
            {
                $('#ErrorModalGeneric').modal('show');
                $('#ErrorTextGeneric').html(data.msg);
                setInterval(function(){ $('#ErrorModalGeneric').modal('hide'); }, 10000);
            }
            else if(data.success == '1')
            {
                $('#buyPrice').val('0.0000');
                $('#buyAmount').val('0.0000');
                $('#buyTotal').val('0.0000');
                getOrderBook();
                getBalnaceARBETH();
                
                var x = document.getElementById("snackbarBuy");
                x.className = "show";
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
            }
        }
    });
}