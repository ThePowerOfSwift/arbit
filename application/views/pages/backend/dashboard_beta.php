<?php
foreach($code as $c){
    $a_code = $c['a_code'];
}


foreach($s_data as $t){
    if($t->activeArb < 0.0001){ $activeArbSys = 0; }else{ $activeArbSys = $t->activeArb; }
    if($t->activeEth < 0.00001){ $activeEthSys = 0; }else{ $activeEthSys = $t->activeEth; }
}
foreach($a_data as $t){
    if($t->active < 0.01){ $activeDollars = 0; }else{ $activeDollars = $t->active; }
}
foreach($e_data as $t){
    if($t->activeArb < 0.0001){ $activeArbEx = 0; }else{ $activeArbEx = $t->activeArb; }
    if($t->activeEth < 0.00001){ $activeEthEx = 0; }else{ $activeEthEx = $t->activeEth; }
}
foreach($ee_data as $t){
    if($t->activeArb < 0.0001){$activeArbWall = 0; }else{ $activeArbWall = $t->activeArb; }
}
foreach($stop_data as $sd){
    if($sd->activeArb < 0.0001){ $activeStopAbotArb = 0; }else{ $activeStopAbotArb = $sd->activeArb; }
}

$arb = "";
if($a_code == "" || $a_code == NULL){
 $a_code = "jvlKdrUN";
}

$ann = $ann;

?>

<div class="container" style="padding:0">
    <!--////////////////////////////////////////////////////////// Announcement /////////////////////////////////////////////////////////////////////--> 
    <div class="depositBtnExch">
        <div class="col-md-12 dashboardAnnoncDiv textAlignCenter">
            <h2 class="text-danger">ANNOUNCEMENT</h2>
            <h3 class="dashboardAnnonc">
               We want every user to enable Google 2FA as soon as you can, Go to your Account Tab to enable 2FA, This will protect your account. <br>
               PLEASE BEWARE: ARBITRAGING STAFF INCLUDING TELEGRAM ADMINS WILL NEVER ASK FOR YOUR: PASSWORDS, 2FA OR PERSONAL INFO. Please do not let anyone know your account login info. 
            </h3>
        </div>
    </div>
    
    <!--////////////////////////////////////////////////////////// ARB / ETH Values /////////////////////////////////////////////////////////////////////-->    
    <div class="valuesDashDiv">
       <div class="row rowDataBot2">
            <div class="col-md-4 textAlignCenter">
                <!--<span class="lggFontBOT2"><?php if(number_format($activeArb, 6) > 100){echo abs($activeArb);} else {echo number_format($activeArb, 6);} ?></span>-->
                <span class="lggFontDash"><?php  echo round($activeArbSys, 2); ?></span>
                <span class="lgFontBOT2"><?php echo $arb;?></span></br>
                <span class="lgFontDash">Available ARB In Wallet</span></br>
            </div>
            
            <?php if($pp_status == 1) {?>
                <div class="col-md-4 textAlignCenter">
                    <span class="lggFontBOT2"><?php echo round($pp_data, 3); ?></span></br>
                    <span class="lgFontBOT2">Available ARB In Plus+ Pool</span></br>
                </div>
            <?php } else {?>
                <div class="col-md-4 textAlignCenter">
                </div>
            <?php } ?>
            
            <div class="col-md-4 textAlignCenter">
                <span class="lggFontDash"><?php echo round($activeEthSys, 10); ?></span></br>
                <span class="lgFontDash">Available ETH In Wallet</span></br>
            </div>
        </div>
        <div class="row rowDataBot2">
            <div class="col-lg-3 col-md-4 col-sm-6 textAlignCenter">
                <span class="lggFontDash"><?php  echo round($activeArbEx, 4); ?></span>
                <span class="lgFontBOT2"><?php echo $arb;?></span></br>
                <span class="lgFontDash">Available ARB In Exchange</span></br>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 textAlignCenter">
                <span class="lggFontDash"><?php echo round($activeDollars, 2); ?></span>
                <span class="lgFontBOT2"><?php echo $arb;?> $</span></br>
                <span class="lgFontDash">Investment In aBot</span></br>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 textAlignCenter">
                <span class="lggFontDash"><?php echo round($activeEthEx, 8); ?></span></br>
                <span class="lgFontDash">Available ETH In Exchange</span></br>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 textAlignCenter">
                <span class="lggFontDash"><?php echo round($activeArbWall, 4); ?></span></br>
                <span class="lgFontDash">Available ARB In Earned Wallet</span></br>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 textAlignCenter">
                <span class="lggFontDash"><?php echo round($activeStopAbotArb, 4); ?></span></br>
                <span class="lgFontDash">Available ARB In Stop aBOT Wallet</span></br>
            </div>
        </div>
    </div>
    
    <!--////////////////////////////////////////////////////////// Chart /////////////////////////////////////////////////////////////////////-->  
    <div class="row descpBOT2">
        <div class="col-md-12">
            <div>
                <div id="container" style="min-width: 310px; height: 300px; margin: 0 auto; margin-top: 10px;"></div>
            </div>		
        </div>
    </div>
    
</div>
     
    <!-- Modal -->
    <div id="announceModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><b>Announcement: </b></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p id="annTextDiv"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    
      </div>
    </div>
    
    <div id="announceModal1" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><b>Announcement: </b></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <p>Hi ARBNation,<br><br>David will be doing a live stream @ 8pm CST on the 24th March ( 1am UTC ) This will be the biggest stream to date and an event that can’t be missed.<br>
                The live stream will be via the below link <a href="https://youtu.be/e7_GH_vn1mM" target="_blank">https://youtu.be/e7_GH_vn1mM</a><br><br>
                Thanks<br>
                Community Manager Ozi
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    
      </div>
    </div>
    

<!--<script src="<?php echo base_url(); ?>assets/backend/js/chart.js"></script>-->
<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>-->
<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>-->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<script>
    
    //$("#announceModal1").modal("show");
    
    $(".content-wrapper").css("background-color", "lightgrey");    

    if('<?php echo $ann; ?>' == 'false')
    {
        $('#announceModal').modal('hide');
    }
    else
    {
        $('#annTextDiv').html('<?php echo $ann; ?>');
        $('#announceModal').modal('show');
    }

<!--////////////////////////////////////////////////////////// Chart /////////////////////////////////////////////////////////////////////--> 

    function graph(){
        graph = [];
        $.get( "<?php echo base_url(); ?>arb_price_stats")
            .done(function( data ) {
                data = JSON.parse(data);
          	    //data = data.reverse();
          	    //data = parseFloat(data);
          	    
          	    //count = 1;
          	    //console.log(data);
          	    $.each(data, function(i){
          	     //   if(count <= 500)
          	     //   {
          	            graph.push(parseFloat(data[i]));
          	            //count ++;
          	     //   } 
          	    });
          	    //console.log(graph);
                graph = graph.reverse();
                
                Highcharts.chart('container', {
                    title: {
                        text: 'ARB Price History (Last 500 trades)',
                        style: {
                            color: '#daa521',
                            fontWeight: 'bold'
                        }
                    },
                    yAxis: {
                        title: {
                            text: ''
                        }
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },
                
                    plotOptions: {
                        series: {
                            label: {
                                connectorAllowed: false
                            },
                            color: '#daa521',
                            pointStart: 1
                        }
                    },
                
                    series: [{
                        name: 'Price',
                        data: graph
                    }],
                
                    responsive: {
                        rules: [{
                            condition: {
                                maxWidth: 500
                            },
                            chartOptions: {
                                legend: {
                                    layout: 'vertical',
                                    align: 'center',
                                    verticalAlign: 'top'
                                }
                            }
                        }]
                    }
                
                });
            });
}
graph();
 </script>