<link rel="stylesheet" href="<?php echo base_url()?>assets/backend/css/support.css">

<div class="container-fluid depositBtnExch">
    <div class="row userOldTicketsDiv">
        <div class="col-lg-12">
            <div class="userProfileDivSpan">USER OLD TICKETS</div>
            <div id="oldTickets"></div>
        </div>
    </div>
    <div class="row statTopBoxSupportDiv">
        <div class="col-lg-6 col-md-7 col-sm-12">
            <div class="userProfileDivSpan" style="margin-top:0px !important">USER SUPPORT</div>
            <div class="userSupportForm">
                <form role="form" action="<?php echo base_url(); ?>generate_ticket" class="bv-form">
                    <div class="form-group">
                        <label>Subject</label>
                        <input class="form-control" placeholder="Enter Subject" id="subjectSupp" required  disabled>
                    </div>
                    <div class="form-group">
                        <label>Category :</label>
                          <select class="form-control" id="prioritySupp" onchange="changeCat();" required  disabled>
                            <!--<option value="General" selected>General</option>-->
                            <!--<option value="Critical">Critical</option>-->
                            <!--<option value="Low/Informational">Low/Informational</option>-->
                          </select>
                    </div>
                    <!--<div class="form-group">-->
                    <!--    <label>Support Pin</label>-->
                    <!--    <input class="form-control" placeholder="Enter Your Support Pin" id="supportPin" required>-->
                    <!--</div>-->
                    <div id="txHashInpDiv" class="form-group">
                        <label>TX Hash</label>
                        <input class="form-control" placeholder="Enter TX Hash" id="txHashInp" required  disabled>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea type="text" class="form-control" id="messageSupp" required  disabled></textarea>
                    </div>
                    <!--onclick="saveTicket();"-->
                    <button type="button" class="btn userSupportBtn" disabled>Send</button>
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
    </div>
    
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
    </div>
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
    
    <div id="errorTextModal" class="modal fade">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Error</h5>
                </div>
                <div class="modal-body">
                    <p id="errorTextP"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    

<script>
    $.get("<?php echo base_url(); ?>query_data", function( data ) {             //Check If any Ticket in Open or not
    console.log(data);
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
    
    var optionCat = 0;
    
    function changeCat(){
        optionCat = $('#prioritySupp').val();
        if(optionCat == "Deposit")
        {
            $('#txHashInpDiv').css("display", "block");
        }
        else
        {
            $('#txHashInp').val("");
            $('#txHashInpDiv').css("display", "none");
        }
    }
    
    function saveTicket(){
        var subjectSupp = $('#subjectSupp').val();
        var prioritySupp = $('#prioritySupp').val();
        var messageSupp = $('#messageSupp').val();
        // var support_pin = $('#supportPin').val();
        var messageTxHash = $('#txHashInp').val();
        var supportIdMsg = 0;
        
        if(optionCat == "Deposit") {
            messageTxHash = jQuery.trim(messageTxHash);
            if(messageTxHash == ""){
                $('#errorTextP').html("Please Enter your TX #");
                $('#errorTextModal').modal('show');
            }
            else {
                supportIdMsg = messageSupp+ " TX: "+ messageTxHash;
                $.post("<?php echo base_url();?>generate_ticket", {subject:subjectSupp, category:prioritySupp, message:supportIdMsg})
                .done(function( data ) {
                    if(data == "Your Support pin does not match")
                    {
                        $('#errorTextP').html(data);
                        $('#errorTextModal').modal('show');
                    }
                    else if(data == "Your Support pin does not exist")
                    {
                        $('#errorTextP').html(data);
                        $('#errorTextModal').modal('show');
                    }
                    else
                    {
                        location.reload();
                    }  
                });
            }
        }
        else {
            $.post("<?php echo base_url();?>generate_ticket", {subject:subjectSupp, category:prioritySupp, message:messageSupp})
            .done(function( data ) {
                if(data == "Your Support pin does not match")
                {
                    $('#errorTextP').html(data);
                    $('#errorTextModal').modal('show');
                }
                else if(data == "Your Support pin does not exist")
                {
                    $('#errorTextP').html(data);
                    $('#errorTextModal').modal('show');
                }
                else
                {
                    location.reload();
                }    
            });
        }    
    }
    
    var oldChatHistory = 0;
    var txNum = 0;
    var txSubb = 0;
    
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
                txSubb = oldChatHistory[i].query.subject;
                txNum = oldChatHistory[i].index;
                
                $('#oldTickets').append("<button class='btn btn-warning mr-2 robotoRegular' onclick='openOldChatModal("+txNum+")'>Subject: " + txSubb + "</button>");
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
            
            if(responder == "Admin")
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
                
                if(responderCurr == "Admin")
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
        $('#chatbox').animate({scrollTop: $(document).height()});
    }
    chatboxCurTicShow();
    window.setInterval( function() {chatboxCurTicShow();}, 15000);
        
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
                $('textarea#replyMsg').val("");
            });
        }    
    }
</script>