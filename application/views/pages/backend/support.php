<?php 
$t_active = 0;
$t_activeD = 0;
// echo "<pre>";
// print_r($tab_data);
// exit();
?>
<style>
    /*table{width: 100%;}*/
    /*thead tr{*/
    /*    background: #343940;*/
    /*    color: #fff;*/
    /*}*/
    /*td, th{border: 1px solid #ccc;padding: 5px;}*/
    
    /*.support_Tabs .nav.nav-tabs {*/
    /*    justify-content: space-around;*/
    /*    background: #343940;*/
    /*    border-radius: 10px 10px 0 0;*/
    /*}*/
</style>

<div class="container-fluid">
    <!--<div class="row userOldTicketsDiv">-->
    <!--    <div class="col-lg-12">-->
    <!--        <div class="userProfileDivSpan">USER OLD TICKETS</div>-->
    <!--        <div id="oldTickets"></div>-->
    <!--    </div>-->
    <!--</div>-->
    
    <div class="row">
        
            <div class="col-12 support_Tabs mt-5 mb-3">
                
              <ul class="nav nav-tabs">
                <li class=""><a id="slink_1" class="active" data-toggle="tab" href="#statusTab">Status Tab</a></li>
                <li><a id="slink_2" class="" data-toggle="tab" href="#supportTab">Support Tab</a></li>
              </ul>
            
              <div class="tab-content">
                <div id="statusTab" class="tab-pane active">
                    
                    <div class="userTx_boxDiv">
                        <div class="status_BarCollapsable">
                            
                              <div class="statusDIV">
                                  <!--<h2>Status Bar</h2>-->
                                  <span class="">
                                      <div>
                                            <span class="redStatus"></span> 
                                            <span>UNDER MAINTENANCE</span>
                                      </div>
                                      <div>
                                          <span class="orangeStatus"></span>
                                          <span>KNOWN ISSUE</span>
                                      </div>
                                      <div>
                                          <span class="greenStatus"></span>
                                          <span>LIVE</span>
                                      </div>
                                  </span>
                              </div>
                              
                              <div class="col-12 mt-3">
                                  <div class="row">
                                    <div class="nav flex-column nav-pills col-sm-4 col-md-4 col-lg-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <?php foreach($tab_data as $key => $t_d){ ?>
                                      <a class="nav-link <?php if( $t_d->details && $t_active == 0 && $t_d->status !="open" ) { echo "active";$t_active++; } ?>" id="v-pills-<?php echo $key; ?>-tab" data-toggle="pill" href="#v-pills-<?php echo $key; ?>" role="tab" aria-controls="v-pills-<?php echo $key; ?>" aria-selected="true">
                                        <div>
                                            <span class="<?php if($t_d->status == "Lock"){ echo "redStatus"; }else if( $t_d->status == "Maintenance" ){ echo "orangeStatus"; } else if($t_d->status == "Open") { echo "greenStatus";} else { echo "";} ?>"></span> <?php echo $t_d->tab_name; ?>    
                                        </div>
                                      </a>
                                      <?php } ?>
                                    </div>
                                    <div class="tab-content col-sm-8 col-md-8 col-lg-9" id="v-pills-tabContent">
                                        <?php foreach ($tab_data as $key => $t_d) { ?>
                                          <div class="tab-pane fade show <?php if( $t_d->details && $t_activeD == 0 && $t_d->status !="open" ) { echo "active";$t_active++; } ?>" id="v-pills-<?php echo $key; ?>" role="tabpanel" aria-labelledby="v-pills-<?php echo $key; ?>-tab">
                                            <div>
                                                <p><?php if($t_d->status !="open"){ echo $t_d->details;} else { echo "Works Fine.";} ?></p>
                                            </div>      
                                          </div>
                                        <?php } ?>
                                    </div>
                                  </div>
                              </div>
                        </div>
                    </div>
                </div>
                <div id="supportTab" class="tab-pane">
                    <div class="userTx_boxDiv">
                        <div class="row userOldTicketsDiv" style="/* display: none; */">
                            <div class="col-lg-12">
                                <div class="userProfileDivSpan">USER OLD TICKETS</div>
                                <div id="oldTickets"></div>
                            </div>
                        </div>
                        <div class="row statTopBoxSupportDiv" style="margin-right: 0;">
                            <div class="col-lg-6 col-md-7 col-sm-12">
                                <div class="userProfileDivSpan">USER SUPPORT</div>
                                <div class="userSupportForm">
                                    <form role="form" action="<?php echo base_url(); ?>generate_ticket" class="bv-form">
                                         <div class="form-group">
                                            <label>Subject</label>
                                            <input class="form-control" placeholder="Enter Subject" id="subjectSupp" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Category :</label>
                                              <select class="form-control" id="prioritySupp" required>
                                                <!--<option value="General" selected>General</option>-->
                                                <!--<option value="Critical">Critical</option>-->
                                                <!--<option value="Low/Informational">Low/Informational</option>-->
                                              </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Message</label>
                                            <textarea type="text" class="form-control" id="messageSupp" required></textarea>
                                        </div>
                                        <button type="button" class="btn userSupportBtn" disabled>Send</button>
                                        <!-- onclick="generateTicket();"-->
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-5 col-sm-12 imgDivSupport">
                                <div>
                                    <img src="<?php echo base_url()?>assets/backend/img/Question.png" class="questionImg">
                                </div>
                                <div class="imgDivSupportHead">
                                    WE'RE HERE TO HELP 
                                </div>   
                                <div>
                                    Our support team is here to help you. Please send us a message using the form.
                                </div>   
                            </div>
                        </div> <!-- -->
                        
                        <div class="row userSupportChatDiv">
                            <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1 col-sm-12">
                                <div class="userProfileDivSpan">Current Ticket</div>
                                <div class="subjectDiv">SUBJECT : <span id="subjectSpan"></span></div>
                                <div class="col-lg-12" id="chatbox">
                                    
                                </div>
                                <div class="row ReplaySection">
                                    <div class="col_75_messnger">
                                        <textarea id="replyMsg" name="replyMsg" placeholder="Enter Your Reply Here..."></textarea>
                                    </div>
                                    <div class="col_25_messnger">
                                        <button class="btn btn-reply" onclick="replySupport()">Reply</button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- -->
                    </div>
                </div>
              </div>
            </div>
    </div>
            <!--_________________________________________-->
    
</div> 

<!-- ///////////////////////////////////////////////////////    Modal       //////////////////////////////////////////////////////////// -->

    <div class="modal fade robotoRegular" id="openOldChatModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-header">
                    <h5 class="modal-title">Your Chat History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 id="chatOldHistorySub"></h5>
                    <div id="chatOldHistory"></div>
                </div>
            </div>
        </div>
    </div>

<script>
    $.get("<?php echo base_url(); ?>query_data", function( data ) {             //Check If any Ticket in Open or not
        if(data == "No Record Found")
        {
           $('.statTopBoxSupportDiv').css('display','flex');
        }
        else if(data == "Record Exist")
        {
            $('.userSupportChatDiv').css('display','block');
        }
    });
    
    $.get("<?php echo base_url(); ?>support_cat", function( data ) {             //Dropdown Categories
        var supCateg = JSON.parse(data);
        var CategArray = 0;
        
        $.each(supCateg, function(j)
        {
            CategArray = supCateg[j].cat_name;
            $('#prioritySupp').append("<option value="+ CategArray +">" + CategArray + "</option>");
        })
    });
    
    function generateTicket(){
        
        var subjectSupp = $('#subjectSupp').val();
        var prioritySupp = $('#prioritySupp').val();
        var messageSupp = $('#messageSupp').val();
        
        $.post("<?php echo base_url();?>generate_ticket", {subject:subjectSupp, category:prioritySupp, message:messageSupp})
        .done(function( data ) {
            location.reload();
        });   
    }
    
    var oldChatHistory = 0;
    var txNum = 0;
    
    function oldTicShow() {
        $.get("<?php echo base_url();?>all_tickets", function( data )
        {
            if(data == "No Record Found")
            {
                $('.userOldTicketsDiv').css('display','none');
            }
            
            oldChatHistory = JSON.parse(data);
            
            $.each(oldChatHistory, function(i)
            {
                txNum = oldChatHistory[i].index;
                
                $('#oldTickets').append("<button class='btn btn-warning mr-2 robotoRegular' onclick='openOldChatModal("+txNum+")'>Ticket # " + txNum + "</button>");
            })
        });
    }
    oldTicShow();
    
    $("#openOldChatModal").on("hidden.bs.modal", function(){                 //Empty Ticket Modals
        $("#chatOldHistory,#chatOldHistorySub").html("");
    });

    //$(document).ready(                                                 // Scrolling ChatBox
		//function (e) {
			$('#chatbox').animate({scrollTop: $(document).height()});
		//}
	//);
    
    function openOldChatModal(param) {
        var txId = param;
        var txData = 0;
        var txQuery = 0;
        var supSubject = 0;
        var responder = 0;
        var response = 0;
        var userClassSupp = 0;
        
        $.each(oldChatHistory, function(i)
        {
            if(txId == oldChatHistory[i].index)
            {
               txData = oldChatHistory[i].tickets;
               txQuery = oldChatHistory[i].query;
            }
        })
        
        supSubject = txQuery.subject;
        $('#chatOldHistorySub').append("<label>Subject: " + supSubject + "</label>");
        
        $.each(txData, function(j)
        {
            responder = txData[j].responder;
            response = txData[j].response;
            
            $('#openOldChatModal').modal('show');
            
            if(responder == "admin")
            {
                userClassSupp = "adminClass";
            }
            else
            {
                userClassSupp = "userClass";
            }
            $('#chatOldHistory').append("<div class=" + userClassSupp + "><p>" + response +"</p></div>");
        })
        $('#openOldChatModal').modal('show');
    }
        
    function chatboxCurTicShow() {
        
        $('#chatbox').html("");
        //$('textarea#replyMsg').val("");
        
        $.get("<?php echo base_url();?>complete_chat", function( data )
        {
            var chatHistory = JSON.parse(data); 
            var chatHistoryDetail = chatHistory.data;
            var chatHistSub = chatHistory.sub;
            
            $('#subjectSpan').html(chatHistSub);
            
            $.each(chatHistoryDetail, function(i)
            {
                var responderCurr = chatHistoryDetail[i].responder;
                var responseCurr = chatHistoryDetail[i].response;
                
                if(responderCurr == "admin")
                {
                    userClassSupp = "adminClass";
                }
                else
                {
                    userClassSupp = "userClass";
                }
                $('#chatbox').append("<div class=" + userClassSupp + "><p>" + responseCurr +"</p></div>");
            })
        });
    }
    chatboxCurTicShow();
    window.setInterval( function() {
        chatboxCurTicShow();
        $('#chatbox').animate({scrollTop: $(document).height()});
    }, 30000);
    
    function replySupport() {
        var replyMsg = $('textarea#replyMsg').val();
        
        if(replyMsg == "")
        {
            alert("Please Enter Some Message.")
        }
        else
        {
            $.post("<?php echo base_url();?>support_response", {response:replyMsg})
            .done(function( data ) {
                    chatboxCurTicShow();
                    $('#chatbox').animate({scrollTop: $(document).height()});
                    $('textarea#replyMsg').val("");
            });
        }    
    }

</script>