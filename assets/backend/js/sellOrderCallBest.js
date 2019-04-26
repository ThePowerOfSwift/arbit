function sellOrderFunBest(ajaxSellOrder, walletSelect, sellAmount_check, price) {
    $.ajax({
        type: 'POST',
        url: ajaxSellOrder,
        data: {
            amount: sellAmount_check.toString(),
            price: price,
            wallet:walletSelect,
            koibe:123
            // captcha: captcha
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
                $('#buyPrice').val('0.0000');
                $('#buyAmount').val('0.0000');
                $('#buyTotal').val('0.0000');
                getOrderBook();
                getBlocksData();
                getBalnaceARBETH();
                
                var x = document.getElementById("snackbarBuy");
                x.className = "show";
                setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
                // location.reload(true);
            }
        }
    });   
}