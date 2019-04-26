<?php
foreach($code as $c){
    $a_code = $c['a_code'];
    $u_email = $c['u_email'];
	$u_username =$c['u_username'];
//	$u_wallet = $c['u_wallet'];
}

// foreach ($affiliate as $a)
// {
//     $chil_u_id = $a['child_u_id'];
//     foreach($all_user as $a)
//     {
//         if(empty($chil_u_id)){
//         }
//         else
//         {
// 			if($chil_u_id == $a['u_id'])
// 			{
// 			    $a_affCount[] = $a['u_email'];
			    
// 			}
// 		}
// 	}
// }

if($a_code == "" || $a_code == NULL){
 $a_code = "jvlKdrUN";
}

?>
<style>
.backgroundWhite
{
    background-color: white;
    border: 2px solid darkgoldenrod;
}
.affliateDiv
{
    padding: 20px;
    font-family: serif;
    font-size: 20px;
}
.affiliatCount
{
    font-size: 120px;
    margin: 20px;
    font-family: initial;
}
.affiliateText
{
    font-size: 20px;
    font-family: serif;
    text-decoration: underline;
}
.height_400
{
    height:400px;
}
.modal-body p
{
    font-size:25px;
    font-weight:700;
}
#affiliateTable tbody tr td
{
    font-size:16px;
}

</style>


<div class="container" style="margin-top: 40px;">
    <div class="row backgroundWhite rowDataBot2">
        <div class="affliateDiv">
            <p></p>
            <p> Your Affiliate Code is: <?php echo base_url('register/affiliate/'.$a_code)?></p>
            <p></p>
            <p>Affiliates Earning: <?php echo number_format((float)$aff_earn, 3, '.', '');?> ARB</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 offset-4 textAlignCenter">
            <span class="affiliateText">Your Total Affiliates</span>
            <p class="affiliatCount"><?php echo sizeof($affiliate)  ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 offset-4">
            <button class="btn btn-warning btn-block" id="shade" data-toggle="modal" data-target="#affiliatesModal" <?php if (sizeof($affiliate) == '0'){ ?> disabled <?php } ?> >Your Affiliates</button>
        </div>
    </div>
</div>

<!--///////////////////////////////////////////////////////////////////////// Modal ///////////////////////////////////////////////////////////////-->

<div class="modal fade" id="affiliatesModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="font-size:12px">
            <div class="modal-body">
                
                <p>Your Affiliates</p>
                
                <div class="table-responsive height_400">
                    <table class="table table-striped table-bordered" id="affiliateTable" width="100%">
                        <thead>
                        <tr>
        			            <th>Affiliate Email <span style='float:right'>aBOT Investment</span></th>
        			    </tr>
        			    </thead>
                      <tbody>
        				<?php 
        					foreach($affiliate as $a){
        			    ?>
        			        
    						<tr>
    						  <td><?php echo $a['email'] ?>
    						  <?php if($a['investment']==1){ ?><i style='float:right' class="fa fa-check text-success"> </i><?php }else{ ?><i style='float:right' class="fa fa-ban text-danger"> </i><?php }?></td>
    						</tr>
    					<?php 
        				 } 
                        ?>
                     </tbody> 
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".content-wrapper").css("background-color", "lightgrey");
</script>