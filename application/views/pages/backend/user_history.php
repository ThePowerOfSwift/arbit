<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css">

<link href="<?php echo base_url()?>assets/backend/css/transactions.css" rel="stylesheet">

<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

<br><br><br>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="userTx_">
                
              <ul class="nav nav-tabs">
                <li class=""><a id="link_1" class="" data-toggle="tab" href="#transcetionTab">Transactions</a></li>
                <li><a id="link_2" class="" data-toggle="tab" href="#orderTab">Order History</a></li>
                <li><a id="link_3" class="" data-toggle="tab" href="#vaultTab">Vault History</a></li>
              </ul>
            
              <div class="tab-content">
                <div id="transcetionTab" class="tab-pane fade">
                    <div class="userTx_boxDiv">
                        <div class="linkButtons">
                            <strong>Span:</strong> <span><button id="btn_getTransection" onclick="getTransactions('before')" title="Before 1st Dec 2018">1</button></span> | <span><button id="btn_getTransAfter" onclick="getTransactions('after')" title="After 1st Dec 2018">2</button></span>
                            <span id="indicator" style="display: none;"><i class="fa fa-spinner fa-spin" style="font-size:24px"></i></span>
                        </div>
                        <div class="userTx_tableWrap">
                          <div class="panel-body">
                            <table id="userTransactionTable" class="table table-striped table-bordered table-list styledTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tx Type</th>
                                        <th>Credit/Debit</th>
                                        <th>Value</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody id="userTransactionTbody">
                                    
                                </tbody>
                            </table>
                          </div>
                        </div>
                        <br>
                        <div id="vaultDiv" class="mt-1 mb-1 mx-auto text-center" style="display:none">
                            <span style="color: #343940;"><strong>Vault Share Distribution:</strong></span> &nbsp; <strong><span id="totalVault"></span></strong>
                        </div>
                    </div>
                </div>
                <div id="orderTab" class="tab-pane fade">
                    <div class="userTx_boxDiv">
                        <div class="linkButtons">
                            <strong>Span:</strong> <span><button id="btn_getHistory" onclick="getOrders('before')" title="Before 1st Dec 2018">1</button></span> | <span><button id="btn_getHistoryAfter" onclick="getOrders('after')" title="After 1st Dec 2018">2</button></span>
                            <span id="indicatorOrders" style="display: none;"><i class="fa fa-spinner fa-spin" style="font-size:24px"></i></span>
                        </div>
                        <div class="userTx_tableWrapOrders">
                          <div class="panel-body">
                            <table id="userOrdersTable" class="table table-striped table-bordered table-list styledTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Price</th>
                                        <th>Amount</th>
                                        <th>Order Type</th>
                                        <th>TimeStamp</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="userOrdersTbody">
                                    
                                </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
                <div id="vaultTab" class="tab-pane fade">
                    <div class="userTx_boxDiv">
                        <div class="linkButtons">
                            <strong>Span:</strong> <span><button id="btn_getVault" onclick="getVaultRecord()">1</button></span>
                            <span id="indicatorVault" style="display: none;"><i class="fa fa-spinner fa-spin" style="font-size:24px"></i></span>
                        </div>
                        <div class="userTx_tableWrapVault">
                          <div class="panel-body">
                            <table id="userVaultTable" class="table table-striped table-bordered table-list styledTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tx Type</th>
                                        <th>Value</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody id="userVaultTbody">
                                    
                                </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>

<script>
    $( document ).ready(function() {
        var localData = localStorage.getItem('casheData_ExchangeOrders');
        if(localData == ""){
            $("#link_1").addClass("active");
            $("#transcetionTab").addClass("in");
            $("#transcetionTab").addClass("active");
            $("#transcetionTab").addClass("show");
             
            // $("#btn_getTransAfter").click();
            // $("#btn_getTransAfter").addClass("activeCXTM");
        }
        else if(localData == "Orders"){
            $("#link_2").addClass("active");    
            $("#orderTab").addClass("in");
            $("#orderTab").addClass("active");
            $("#orderTab").addClass("show");
            
            $("#btn_getHistoryAfter").click();
            $("#btn_getHistoryAfter").addClass("activeCXTM");
            
            localStorage.setItem("casheData_ExchangeOrders","");
        } else {
            $("#link_1").addClass("active");
            $("#transcetionTab").addClass("in");
            $("#transcetionTab").addClass("active");
            $("#transcetionTab").addClass("show");
        }
        
        
        $("#btn_getTransection").click(function(){
            $("#btn_getTransection").addClass("activeCXTM");
            $("#vaultDiv").css("display","none");
            $("#btn_getTransAfter").removeClass("activeCXTM");
        });
        $("#btn_getTransAfter").click(function(){
            
            $("#btn_getTransAfter").addClass("activeCXTM");
            $("#vaultDiv").css("display","none");
            $("#btn_getTransection").removeClass("activeCXTM");
        });
        
        
        $("#btn_getHistory").click(function(){
            $("#btn_getHistory").addClass("activeCXTM");
            $("#btn_getHistoryAfter").removeClass("activeCXTM");
        });
        $("#btn_getHistoryAfter").click(function(){
            
            $("#btn_getHistoryAfter").addClass("activeCXTM");
            $("#btn_getHistory").removeClass("activeCXTM");
        });
        
        $("#btn_getVault").click(function(){
            $("#btn_getVault").addClass("activeCXTM");
        });
        
    });

    
    function getTransactions(type) {
        $('#indicator').css('display', 'block');
        var transactionData = 0;
        var transId = 0;
        var transType = 0;
        var transValue = 0;
        var transCreated_at = 0;
        var transCreditDebit = 0;
        var totalVault = 0;
        $.fn.dataTable.ext.errMode = 'none';
        // var tablee = '';
        
        var tablee = $('#userTransactionTable').DataTable({
            lengthMenu: [ 10, 25, 50, 75 ],
            dom: 'lfrtBp',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
        tablee.clear();

        $("#userTransactionTbody").text("");
        
        if(type == 'before')
        {
            $.get("<?php echo base_url()?>get_logs_backup", function( data ) {
                transactionData = JSON.parse(data);
                if(transactionData.error == 1) {
                    alert(data.msg);
                }
                else { 
                    
                    $('.userTx_tableWrap').css('display', 'block');
                    $('#indicator').css('display', 'none');
                    $.each(transactionData, function(i)
                    {
                        transId = transId + 1;
                        transType = transactionData[i].type;
                        transValue = transactionData[i].value;
                        transCreated_at = transactionData[i].created_at;
                        if(transValue >= 0){transCreditDebit = 'Credit';}else if(transValue < 0){transCreditDebit = 'Debit';}
                        tablee.row.add([transId, transType, transCreditDebit, transValue, transCreated_at ])
                        
                    });

                    tablee.draw();
                   
                }    
            });
        }
        else {
            $.get("<?php echo base_url()?>get_current_logs", function( data ) {
                transactionData = JSON.parse(data);
                if(data.error == 1) {
                    alert(data.msg);
                }
                else {
                    $('.userTx_tableWrap').css('display', 'block');
                    $('#indicator').css('display', 'none');
                    $.each(transactionData, function(i)
                    {
                       
                        transType = transactionData[i].type;
                        transValue = transactionData[i].value;
                        transCreated_at = transactionData[i].created_at;
                        if(transValue >= 0){transCreditDebit = 'Credit';}else if(transValue < 0){transCreditDebit = 'Debit';}
                        
                         if (transType.includes("Vault Share Distribution")) {
                            totalVault += parseFloat(transValue);
                        }
                        else {
                            transId = transId + 1;
                            tablee.row.add([transId, transType, transCreditDebit, transValue, transCreated_at ])
                        }
                    });
                    
                    tablee.draw();
                }    
            });
        }    
    }    
    
    function getVaultRecord() {
        $('#indicatorVault').css('display', 'block');
        var vaultData = 0;
        var vaultId = 0;
        var vaultType = 0;
        var vaultValue = 0;
        var vaultCreated_at = 0;
        $.fn.dataTable.ext.errMode = 'none';
        
        var tableVault = $('#userVaultTable').DataTable({
            lengthMenu: [ 10, 25, 50, 75 ],
            dom: 'lfrtBp',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
        tableVault.clear();

        $("#userVaultTbody").text("");
        
        $.get("<?php echo base_url()?>get_vault_log", function( data ) {
            vaultData = JSON.parse(data);
            if(vaultData.error == 1) {
                alert(data.msg);
            }
            else { 
                
                $('.userTx_tableWrapVault').css('display', 'block');
                $('#indicatorVault').css('display', 'none');
                $.each(vaultData, function(i)
                {
                    vaultId = vaultId + 1;
                    vaultType = vaultData[i].type;
                    vaultValue = vaultData[i].value;
                    vaultCreated_at = vaultData[i].created_at;
                    tableVault.row.add([vaultId, vaultType, vaultValue, vaultCreated_at ])
                    
                });

                tableVault.draw();
            }    
        });  
    }    
    
    function getOrders(type){
        $('#indicatorOrders').css('display', 'block');
        var OrderData = 0;
        var OrderId = 0;
        
        $.fn.dataTable.ext.errMode = 'none';
        var tableOrder = $('#userOrdersTable').DataTable({
            lengthMenu: [ 10, 25, 50, 75 ],
            dom: 'lfrtBp',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ]
        });
        $("#userOrdersTbody").text("");
        tableOrder.clear();
        
        if(type == 'before')
        {
            $.get("<?php echo base_url()?>get_orders_backup", function( data ) {
                OrderData = JSON.parse(data);
                if(data.error == 1) {
                    alert(data.msg);
                }
                else 
                {
                    
                    $('.userTx_tableWrapOrders').css('display', 'block');
                    $('#indicatorOrders').css('display', 'none');
                    $.each(OrderData, function(i)
                    {
                        total = 0;
                        OrderId = OrderId + 1;
                        price = OrderData[i].price;
                        priceT = parseFloat(price);
                        priceT = Math.abs(priceT);
                        amount = OrderData[i].amount;
                        amountT = parseFloat(amount);
                        amountT = Math.abs(amountT);
                        total = price * amount;
                        total = total.toFixed(8);
                        order = OrderData[i].order_type;
                        date = OrderData[i].created_at;
                        remark = OrderData[i].remark;
        
                        if(remark == "")
                        {
                            re = "<i class='fa fa-check text-success'> </i>";
                        }
                        else
                        {
                            re = "<i class='fa fa-ban text-danger'> </i>";
                        }
                        tableOrder.row.add([OrderId, priceT, amountT, order, date, total, re ])
                    });
                    
                    tableOrder.draw();
                }
            });    
        }
        else
        {
            $.get("<?php echo base_url()?>all_user_orders", function( data ) {
                OrderData = JSON.parse(data);
                if(data == 'false') {
                    alert("This function is currenctly Disable");
                }
                else 
                {
                    $('.userTx_tableWrapOrders').css('display', 'block');
                    $('#indicatorOrders').css('display', 'none');
                    $.each(OrderData, function(i)
                    {
                        total = 0;
                        OrderId = OrderId + 1;
                        price = OrderData[i].price;
                        priceT = parseFloat(price);
                        priceT = Math.abs(priceT);
                        amount = OrderData[i].amount;
                        amountT = parseFloat(amount);
                        amountT = Math.abs(amountT);
                        total = price * amount;
                        total = total.toFixed(8);
                        order = OrderData[i].order_type;
                        date = OrderData[i].created_at;
                        remark = OrderData[i].remark;
        
                        if(remark == "")
                        {
                            re = "<i class='fa fa-check text-success'> </i>";
                        }
                        else
                        {
                            re = "<i class='fa fa-ban text-danger'> </i>";
                        }
                        tableOrder.row.add([OrderId, priceT, amountT, order, date, total, re ])
                    });
                    
                    tableOrder.draw();
                }    
            });
        }    
    }

</script>
