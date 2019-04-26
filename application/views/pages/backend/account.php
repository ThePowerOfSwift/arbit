<?php
foreach($code as $c){
    $a_code = $c['a_code'];
    $u_email = $c['u_email']; 
    $u_id = $c['u_id'];
    $u_username =$c['u_username'];
    $u_wallet = $c['u_wallet']; 
    $allow_pin = $c['allow_pin'];
}

if($a_code == "" || $a_code == NULL) { $a_code = "jvlKdrUN"; }

    // $pasob_trns_to_ex      = $pp_auto_selling_or_buying->trns_to_ex;
    $pasob_sell_or_buy     = $pp_auto_selling_or_buying->auto_sell_per; 
    $pasob_trns_to_sw      = $pp_auto_selling_or_buying->to_sw;
    $pasob_withdraw        = $pp_auto_selling_or_buying->withdraw;
    $pasob_status          = $pp_auto_selling_or_buying->status;
    // $pasob_trns_to_abot    = $pp_auto_selling_or_buying->trns_to_abot;

    // $pasob_eth_deposit_in_abot = $pp_auto_selling_or_buying->eth_deposit_in_abot;
    
    $pp_arb_value = $pp_auto_abot_active->arb_value;
    $pp_arb_amount = $pp_auto_abot_active->arb_amount;
    
    
    $pp_stop_value          = $pp_auto_stop_abot->arb_value;
    $pp_stop_active_arb_per = $pp_auto_stop_abot->active_arb_per;

?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link href="<?php echo base_url()?>assets/backend/css/account_new.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>

<div class="container-fluid" style="background: #f7f7f7;">
    <div class="row">
        <div class="col-12  pt-5 pb-5">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">mBOT</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="membership-tab" data-toggle="tab" href="#membership" role="tab" aria-controls="membership" aria-selected="true">Membership</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="voting-tab" data-toggle="tab" href="#voting" onclick="votingNotiCheck()" role="tab" aria-controls="voting" aria-selected="true">Voting <i id="votingNotiDiv" class="badge badge-warning"><?php echo $votingNoti; ?></i></a>
          </li>
          <?php if($user_current_package == "Pro" || $user_current_package == "Advance") {?>
          <li class="nav-item">
            <a class="nav-link " id="accountAccess-tab" data-toggle="tab" href="#accountAccess" role="tab" aria-controls="accountAccess" aria-selected="true">Account Access</a>
          </li>
          <? }?> 
          <?php if($user_current_add_ons[0]->add_on_name == 'Pro+') { ?>
          <li class="nav-item">
            <a class="nav-link " id="proPlus-tab" data-toggle="tab" href="#proPlus" role="tab" aria-controls="proPlus" aria-selected="true">Plus <i class="fa fa-plus"></i> </a>
          </li>
          <? }?>
          
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="col-md-12 pt-3 pb-3">
                <div class="card">
                    <div class="card-header">
                         <i class="fa fa-user"></i> User profile
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Referral</label>
                            <input type="text" class="form-control" name="u_referal" value="<?php echo base_url('register/affiliate/'.$a_code)?>" readonly>
                        </div>
                       <div class="form-group">
                            <label>MEW/Metamask Wallet  - Mandatory</label>
                            <input type="text" class="form-control" id="u_wallet" name="u_wallet" value="<?php echo $u_wallet; ?>"> 
                            <label id="mewError" class="error text-danger" style="display:none">Enter a Valid MEW/METAMASK Address</label>
                        </div>                
                        <div class="form-group">
                            <label>Email</label>
                            <input id="u_email" type="email" class="form-control" name="u_email" value="<?php echo $u_email; ?>" readonly>
                            
                        </div>  
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="u_nom" value="<?php echo $u_username ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                           <button type="button" class="btn btn-lg btn-success" onclick=saveWallet();>Save</button>
                        </div>
                    </div>
                </div>   
                <div class="card mt-4">
                    <div class="card-header">
                         <i class="fa fa-user-secret"></i> Google Authorization
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label></label>
                            <div class="checkbox">
                                <label style="font-size: 2.5em">
                                    <?php
                                    if($allow_pin == 1)
                                    {
                                        echo '<input type="checkbox" onchange="handleChange(this);" id="checkbox" value="" checked>';
                                    }
                                    else  
                                        echo '<input type="checkbox" onchange="handleChange(this);" id="checkbox" value="">';
                                    ?>
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <div class="googleAuthText">Add Google 2 Factor Authorization</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <i class="fa fa-key"></i> User password
                    </div>
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" id="pre_password" name="pre_password" required> 
                        </div>                
                        <div class="form-group">
                            <label>New Password: </label>
                            <input type="password" class="form-control" name="new_password" id="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password: </label>
                            <input type="password" class="form-control" name="new_password1" id="new_password1" required>
                            <span id='message'></span>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-lg btn-success" onclick=updatepwd()>Update</button>
                        </div>
                    </div>
                </div>

                
                <div class="card mt-4">
                    <div class="card-header">
                        <i class="fa fa-key"></i> Terms of Services
                    </div>
                    <div class="card-body">
                        <div class="checkbox">
                            <label style="font-size: 2.5em">
                                <input type="checkbox" onchange="handleChangeTos();" id="checkboxTos" value="" checked>
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span><a href="https://www.arbitraging.co/index_files/docs/ArbitragingTOS.pdf" target=_blank>TOS</a>
                            </label>
                        </div>
                    </div>
                </div>
                
                
                <div class="card mt-4">
                    <div class="card-header">
                        <i class="fa fa-thumbtack"></i> Support Pin
                    </div>
                    
                    <div class="card-body" id="pinSupportCheck">
                        <label>Pin: </label>
                        <div class="input-group">
                            <input type="password" class="form-control" aria-describedby="basic-addon1" value="******" readonly>
                            <div class="input-group-prepend showSupp" onclick="suppPinShow()">
                                <span class="input-group-text" id="basic-addon1"><i class="far fa-eye"></i></span>
                            </div>
                        </div>   
                    </div>
                    
                    <div class="card-body" id="pinSupportDiv">
                        <div class="form-group">
                            <label>Pin: </label>
                            <input type="password" class="form-control" id="supp_pin" min="6" max="6" required> 
                        </div>                
                        <div class="form-group">
                            <label>Confirm Pin: </label>
                            <input type="password" class="form-control" id="supp_pin_confirm"  min="6" max="6" required>
                        </div>
                        <div class="form-group">
                            <span class="text-danger">*Pin must be of 6 characters.*</span>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-lg btn-success" onclick=suppPinSave()>Save</button>
                        </div>
                    </div>
                </div>
                
            </div>
          </div>
          
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
              <div class="col-md-12 pt-3 pb-3">
                  <div class="exchangesSpreadWrap">
                      <h3>Select Exchanges to show spread</h3>
                      <div class="exchangesContainer">
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Kraken
                              <input type="checkbox" <?php if($ex_checkbox_data->Kraken->checked == 1){?>checked="checked"<?php }?> id="show_kraken">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Bithumb
                              <input type="checkbox" <?php if($ex_checkbox_data->Bithumb->checked == 1){?>checked="checked"<?php }?> id="show_bithumb">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">BTCMarkets
                              <input type="checkbox" <?php if($ex_checkbox_data->BtcMarket->checked == 1){?>checked="checked"<?php }?> id="show_btcmarket">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Poloniex
                              <input type="checkbox" <?php if($ex_checkbox_data->Poloniex->checked == 1){?>checked="checked"<?php }?> id="show_poloniex">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Binance
                              <input type="checkbox" <?php if($ex_checkbox_data->Binance->checked == 1){?>checked="checked"<?php }?> id="show_binance">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Bittrex
                              <input type="checkbox" <?php if($ex_checkbox_data->Bittrex->checked == 1){?>checked="checked"<?php }?> id="show_bittrex">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Hitbtc
                              <input type="checkbox" <?php if($ex_checkbox_data->HitBtc->checked == 1){?>checked="checked"<?php }?> id="show_hitbtc">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Huobi
                              <input type="checkbox" <?php if($ex_checkbox_data->Huobi->checked == 1){?>checked="checked"<?php }?> id="show_huobi">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Livecoin
                              <input type="checkbox" <?php if($ex_checkbox_data->Livecoin->checked == 1){?>checked="checked"<?php }?> id="show_livecoin">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Exmo
                              <input type="checkbox" <?php if($ex_checkbox_data->Exmo->checked == 1){?>checked="checked"<?php }?> id="show_exmo">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Bitstamp
                              <input type="checkbox" <?php if($ex_checkbox_data->Bitstamp->checked == 1){?>checked="checked"<?php }?> id="show_bitstamp">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">KuCoin
                              <input type="checkbox" <?php if($ex_checkbox_data->KuCoin->checked == 1){?>checked="checked"<?php }?> id="show_kucoin">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                          <div class="checkboxWrapper">
                            <label class="checkboxContainer">Bitsane
                              <input type="checkbox" <?php if($ex_checkbox_data->Bitsane->checked == 1){?>checked="checked"<?php }?> id="show_bitsane">
                              <span class="radioCheckmark"></span>
                            </label>
                          </div>
                      </div>
                      <div class="text-right"><button class="btn btn-lg btn-success" onClick='save_spread_excahnges()'>Save</button></div>
                  </div>
              </div>
              <div class="col-md-12 pt-3 pb-3">
                  <div class="exchangesSpreadWrap">
                      <h3>Whitelist IPs <span class="text-danger" style="margin-left: 15px;font-size: 16px;">*Use these IPs on every exchange but don't use any IP on BITHUMB.</span></h3>
                      <div class="row pt-3">
                          <div class="col-md-3 text-center">
                              <h4>69.167.187.165</h4>
                          </div>
                          <div class="col-md-3 text-center">
                              <h4>216.229.19.236</h4>
                          </div>
                          <div class="col-md-3 text-center">
                              <h4>69.167.185.200</h4>
                          </div>
                          <div class="col-md-3 text-center">
                              <h4>69.167.185.201</h4>
                          </div>
                      </div>
                  </div>
              </div>
              
<script>
function save_spread_excahnges(){
     var spread= new Object();
     $('#loadingmessage').show();
     if($('#show_kraken').is(':checked')){spread.Kraken= {'checked':1};}else{spread.Kraken = {'checked':0};}
     if($('#show_bithumb').is(':checked')){spread.Bithumb = {'checked':1};}else{spread.Bithumb = {'checked':0};}
     if($('#show_btcmarket').is(':checked')){spread.BtcMarket = {'checked':1};}else{spread.BtcMarket = {'checked':0};}
     if($('#show_poloniex').is(':checked')){spread.Poloniex = {'checked':1};}else{spread.Poloniex = {'checked':0};}
     if($('#show_binance').is(':checked')){spread.Binance = {'checked':1};}else{spread.Binance = {'checked':0};}
     if($('#show_bittrex').is(':checked')){spread.Bittrex = {'checked':1};}else{spread.Bittrex = {'checked':0};}
     if($('#show_hitbtc').is(':checked')){spread.HitBtc = {'checked':1};}else{spread.HitBtc = {'checked':0};}
     if($('#show_huobi').is(':checked')){spread.Huobi = {'checked':1};}else{spread.Huobi = {'checked':0};}
     if($('#show_livecoin').is(':checked')){spread.Livecoin = {'checked':1};}else{spread.Livecoin = {'checked':0};}
     if($('#show_exmo').is(':checked')){spread.Exmo = {'checked':1};}else{spread.Exmo = {'checked':0};}
     if($('#show_bitstamp').is(':checked')){spread.Bitstamp = {'checked':1};}else{spread.Bitstamp = {'checked':0};}
     if($('#show_kucoin').is(':checked')){spread.KuCoin = {'checked':1};}else{spread.KuCoin = {'checked':0};}
     if($('#show_bitsane').is(':checked')){spread.Bitsane = {'checked':1};}else{spread.Bitsane = {'checked':0};}
     
     
     
    //  http://www.arbblock.com/test_beta/get_exchanges
     
     $.post( "<?php echo base_url(); ?>get_exchanges", {exchanges:spread})
      .done(function( data ) {
        $('#loadingmessage').hide();
        if(data == "success"){
            $('#successKeysModal').modal('show');
            setInterval(function(){ $('#successKeysModal').modal('hide'); }, 3000);
        }
        else{$('#failedModal').modal('show');
            setInterval(function(){ $('#failedModal').modal('hide'); }, 3000);
            }
      });   
}
</script>
              
              
              
              <div class="col-md-12 pt-3 pb-3">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-user"></i> Exchange Keys
                    </div>
                    
                    <div class="card-body">
                        <div class="container">
                            <div id="accordion">
                                
                                    <div class="" id="headingOne">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                          <h5 class="mb-0">Kraken  <span id="checked_mark" class="pull-right"><?php if(isset($mbot_data['kraken']['apikey']) && isset($mbot_data['kraken']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="krakenEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyKraken" class="form-control exInput" value = '<?php if(isset($mbot_data['kraken']['apikey'])){echo $mbot_data['kraken']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <div class="Ex_addressTags">
                                            </div>
                                            <input type="text" id="secKeyKraken" class="form-control exInput" value = '<?php if(isset($mbot_data['kraken']['seckey'])){echo $mbot_data['kraken']['seckey'];}?>'>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>You must have to save your sell exchange deposit addresses in kraken with key of this format.
                                                  <br>
                                                  <strong>mbot_(sell exhange name in Lower Case)_deposit_(your coin name in Upper Case)</strong>
                                                  <br>
                                                  Like if you trade from kraken to binance in ETH then your format is like this. <strong>mbot_binance_deposit_ETH</strong>
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='kraken_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XMR</span>
                                                          <!--<input id="kraken_xmr_tag" type="text" name="coinTag[]" placeholder="Coin Tag" value = '<?php if(isset($mbot_data['kraken']['coins']['XMR']['tag'])){echo $mbot_data['kraken']['coins']['XMR']['tag'];}?>'>-->
                                                          <input id="kraken_xmr_address" type="text" name="coinAddress[]" placeholder="Coin Address" value = '<?php if(isset($mbot_data['kraken']['coins']['XMR']['address'])){echo $mbot_data['kraken']['coins']['XMR']['address'];}?>'>
                                                        </li>
                                                        <li>
                                                          <span>XLM</span>
                                                          <input id="kraken_xlm_tag" type="text" name="coinTag[]" placeholder="Coin Tag" value = '<?php if(isset($mbot_data['kraken']['coins']['XLM']['tag'])){echo $mbot_data['kraken']['coins']['XLM']['tag'];}?>'>
                                                          <input id="kraken_xlm_address" type="text" name="coinAddress[]" placeholder="Coin Address" value = '<?php if(isset($mbot_data['kraken']['coins']['XLM']['address'])){echo $mbot_data['kraken']['coins']['XLM']['address'];}?>'>
                                                        </li>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="kraken_eos_tag" type="text" name="coinTag[]" placeholder="Coin Tag" value = '<?php if(isset($mbot_data['kraken']['coins']['EOS']['tag'])){echo $mbot_data['kraken']['coins']['EOS']['tag'];}?>'>
                                                          <input id="kraken_eos_address" type="text" name="coinAddress[]" placeholder="Coin Address" value = '<?php if(isset($mbot_data['kraken']['coins']['EOS']['address'])){echo $mbot_data['kraken']['coins']['EOS']['address'];}?>'>
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="kraken_xrp_tag" type="text" name="coinTag[]"  placeholder="Coin Tag" value = '<?php if(isset($mbot_data['kraken']['coins']['XRP']['tag'])){echo $mbot_data['kraken']['coins']['XRP']['tag'];}?>'>
                                                          <input id="kraken_xrp_address" type="text" name="coinAddress[]" placeholder="Coin Address" value = '<?php if(isset($mbot_data['kraken']['coins']['XRP']['address'])){echo $mbot_data['kraken']['coins']['XRP']['address'];}?>'>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('kraken');"> Save </button>
                                            </p>    
                                        </div> 
                                      </div>
                                    </div>
                                </div>

                                    <div class="" id="headingTwo">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                          <h5 class="mb-0">Bithumb  <span id="checked_bithumbmark" class="pull-right"><?php if(isset($mbot_data['bithumb']['apikey']) && isset($mbot_data['bithumb']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="bithumbEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyBithumb" class="form-control exInput"  value = '<?php if(isset($mbot_data['bithumb']['apikey'])){echo $mbot_data['bithumb']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyBithumb" class="form-control exInput"  value = '<?php if(isset($mbot_data['bithumb']['seckey'])){echo $mbot_data['bithumb']['seckey'];}?>'>
                                            <br>    
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api key and these coins deposit addresses.
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='bithumb_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XMR</span>
                                                          <!--<input id="bithumb_xmr_tag" type="text" name="coinTag[]"  value ='<?php if(isset($mbot_data['bithumb']['coins']['XMR']['tag'])){echo $mbot_data['bithumb']['coins']['XMR']['tag'];}?>' placeholder="Coin Tag">-->
                                                          <input id="bithumb_xmr_address" type="text" name="coinAddress[]"  value ='<?php if(isset($mbot_data['bithumb']['coins']['XMR']['address'])){echo $mbot_data['bithumb']['coins']['XMR']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="bithumb_eos_tag" type="text" name="coinTag[]"  value ='<?php if(isset($mbot_data['bithumb']['coins']['EOS']['tag'])){echo $mbot_data['bithumb']['coins']['EOS']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="bithumb_eos_address" type="text" name="coinAddress[]"  value ='<?php if(isset($mbot_data['bithumb']['coins']['EOS']['address'])){echo $mbot_data['bithumb']['coins']['EOS']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="bithumb_xrp_tag" type="text" name="coinTag[]"  value ='<?php if(isset($mbot_data['bithumb']['coins']['XRP']['tag'])){echo $mbot_data['bithumb']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="bithumb_xrp_address" type="text" name="coinAddress[]"  value ='<?php if(isset($mbot_data['bithumb']['coins']['XRP']['address'])){echo $mbot_data['bithumb']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('bithumb');"> Save </button>
                                            </p>    
                                        </div>   
                                      </div>
                                    </div>
                                </div>
                                
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                          <h5 class="mb-0">BTCMarkets  <span id="checked_btcmark" class="pull-right"><?php if(isset($mbot_data['btcmarkets']['apikey']) && isset($mbot_data['btcmarkets']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="btcMarketEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyBtcm" name="btc_coin_label" class="form-control exInput" value = '<?php if(isset($mbot_data['btcmarkets']['apikey'])){echo $mbot_data['btcmarkets']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyBtcm" class="form-control exInput"  value = '<?php if(isset($mbot_data['btcmarkets']['seckey'])){echo $mbot_data['btcmarkets']['seckey'];}?>'>
                                            <br>   
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api key and these coins deposit addresses.
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='btmfield_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                            <span>BTC</span>
                                                          <input id="btcmarkets_btc_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['btcmarkets']['coins']['BTC']['address'])){echo $mbot_data['btcmarkets']['coins']['BTC']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>ETH</span>
                                                          <input id="btcmarkets_eth_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['btcmarkets']['coins']['ETH']['address'])){echo $mbot_data['btcmarkets']['coins']['ETH']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>LTC</span>
                                                          <input id="btcmarkets_ltc_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['btcmarkets']['coins']['LTC']['address'])){echo $mbot_data['btcmarkets']['coins']['LTC']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>BCH</span>
                                                          <input id="btcmarkets_bch_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['btcmarkets']['coins']['BCH']['address'])){echo $mbot_data['btcmarkets']['coins']['BCH']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                          <span>XRP</span>
                                                          <input id="btcmarkets_xrp_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['btcmarkets']['coins']['XRP']['tag'])){echo $mbot_data['btcmarkets']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="btcmarkets_xrp_address" type="text" name="coinAddress[]" value = '<?php if(isset($mbot_data['btcmarkets']['coins']['XRP']['address'])){echo $mbot_data['btcmarkets']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('btcmarkets');"> Save </button>
                                            </p>    
                                        </div>  
                                      </div>
                                    </div>
                                </div>
                                

                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#PoloniexThree" aria-expanded="false" aria-controls="PoloniexThree">
                                          <h5 class="mb-0">Poloniex  <span id="checked_poloniexmark" class="pull-right"><?php if(isset($mbot_data['poloniex']['apikey']) && isset($mbot_data['poloniex']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="PoloniexThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="poloniexEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyPolo" class="form-control exInput" value = '<?php if(isset($mbot_data['poloniex']['apikey'])){echo $mbot_data['poloniex']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyPolo" class="form-control exInput" value = '<?php if(isset($mbot_data['poloniex']['seckey'])){echo $mbot_data['poloniex']['seckey'];}?>'>
                                            <br>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses.
                                                  <br>
                                                  Please generate all your deposit addresses before trade.
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='poloniex_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XLM (STR)</span>
                                                          <input id="poloniex_xlm_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['poloniex']['coins']['XLM']['tag'])){echo $mbot_data['poloniex']['coins']['XLM']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="poloniex_xlm_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['poloniex']['coins']['XLM']['address'])){echo $mbot_data['poloniex']['coins']['XLM']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="poloniex_eos_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['poloniex']['coins']['EOS']['tag'])){echo $mbot_data['poloniex']['coins']['EOS']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="poloniex_eos_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['poloniex']['coins']['EOS']['address'])){echo $mbot_data['poloniex']['coins']['EOS']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <!--<input id="poloniex_xrp_tag" type="text" name="coinTag[]" value="" placeholder="Coin Tag">-->
                                                          <input id="poloniex_xrp_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['poloniex']['coins']['XRP']['address'])){echo $mbot_data['poloniex']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('poloniex');"> Save </button>
                                            </p>    
                                        </div>   
                                      </div>
                                    </div>
                                </div>
                             
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#BinanceThree" aria-expanded="false" aria-controls="BinanceThree">
                                          <h5 class="mb-0">Binance  <span id="checked_binancemark" class="pull-right"><?php if(isset($mbot_data['binance']['apikey']) && isset($mbot_data['binance']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="BinanceThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="binanceEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyBin" class="form-control exInput" value = '<?php if(isset($mbot_data['binance']['apikey'])){echo $mbot_data['binance']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyBin" class="form-control exInput" value = '<?php if(isset($mbot_data['binance']['seckey'])){echo $mbot_data['binance']['seckey'];}?>'>
                                            <br>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses.
                                                  <br>
                                                  Please generate all your deposit addresses before trade.
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='binance_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XMR</span>
                                                          <input id="binance_xmr_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['binance']['coins']['XMR']['tag'])){echo $mbot_data['binance']['coins']['XMR']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="binance_xmr_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['binance']['coins']['XMR']['address'])){echo $mbot_data['binance']['coins']['XMR']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                          <span>XLM</span>
                                                          <input id="binance_xlm_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['binance']['coins']['XLM']['tag'])){echo $mbot_data['binance']['coins']['XLM']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="binance_xlm_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['binance']['coins']['XLM']['address'])){echo $mbot_data['binance']['coins']['XLM']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="binance_eos_tag" type="text" name="coinTag[]" value = '<?php if(isset($mbot_data['binance']['coins']['EOS']['tag'])){echo $mbot_data['binance']['coins']['EOS']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="binance_eos_address" type="text" name="coinAddress[]" value = '<?php if(isset($mbot_data['binance']['coins']['EOS']['address'])){echo $mbot_data['binance']['coins']['EOS']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="binance_xrp_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['binance']['coins']['XRP']['tag'])){echo $mbot_data['binance']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="binance_xrp_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['binance']['coins']['XRP']['address'])){echo $mbot_data['binance']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('binance');"> Save </button>
                                            </p>    
                                        </div>    
                                      </div>
                                    </div>
                                </div>
                                
                                
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#BittrexThree" aria-expanded="false" aria-controls="BittrexThree">
                                          <h5 class="mb-0">Bittrex  <span id="checked_bittrexmark" class="pull-right"><?php if(isset($mbot_data['bittrex']['apikey']) && isset($mbot_data['bittrex']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="BittrexThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="bittrexEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyBit" class="form-control exInput" value = '<?php if(isset($mbot_data['bittrex']['apikey'])){echo $mbot_data['bittrex']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyBit" class="form-control exInput" value = '<?php if(isset($mbot_data['bittrex']['seckey'])){echo $mbot_data['bittrex']['seckey'];}?>'>
                                            <br>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses.                                              
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='bittrex_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XMR</span>
                                                          <input id="bittrex_xmr_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['bittrex']['coins']['XMR']['tag'])){echo $mbot_data['bittrex']['coins']['XMR']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="bittrex_xmr_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['bittrex']['coins']['XMR']['address'])){echo $mbot_data['bittrex']['coins']['XMR']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="bittrex_xrp_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['bittrex']['coins']['XRP']['tag'])){echo $mbot_data['bittrex']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="bittrex_xrp_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['bittrex']['coins']['XRP']['address'])){echo $mbot_data['bittrex']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('bittrex');"> Save </button>
                                            </p>    
                                        </div>    
                                      </div>
                                    </div>
                                </div>
                                
                                
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#HitBTCThree" aria-expanded="false" aria-controls="HitBTCThree">
                                          <h5 class="mb-0">HitBTC  <span id="checked_hitbtcmark" class="pull-right"><?php if(isset($mbot_data['hitbtc']['apikey']) && isset($mbot_data['hitbtc']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="HitBTCThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="hitBTCEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyHit" class="form-control exInput" value = '<?php if(isset($mbot_data['hitbtc']['apikey'])){echo $mbot_data['hitbtc']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyHit" class="form-control exInput" value = '<?php if(isset($mbot_data['hitbtc']['seckey'])){echo $mbot_data['hitbtc']['seckey'];}?>'>
                                            <br>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses..
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='hitbtc_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XMR</span>
                                                          <!--<input id="hitbtc_xmr_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['XMR']['tag'])){echo $mbot_data['hitbtc']['coins']['XMR']['tag'];}?>' placeholder="Coin Tag">-->
                                                          <input id="hitbtc_xmr_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['XMR']['address'])){echo $mbot_data['hitbtc']['coins']['XMR']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                          <span>XLM</span>
                                                          <input id="hitbtc_xlm_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['XLM']['tag'])){echo $mbot_data['hitbtc']['coins']['XLM']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="hitbtc_xlm_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['XLM']['address'])){echo $mbot_data['hitbtc']['coins']['XLM']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="hitbtc_eos_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['EOS']['tag'])){echo $mbot_data['hitbtc']['coins']['EOS']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="hitbtc_eos_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['EOS']['address'])){echo $mbot_data['hitbtc']['coins']['EOS']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="hitbtc_xrp_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['XRP']['tag'])){echo $mbot_data['hitbtc']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="hitbtc_xrp_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['hitbtc']['coins']['XRP']['address'])){echo $mbot_data['hitbtc']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('hitbtc');"> Save </button>
                                            </p>    
                                        </div>    
                                      </div>
                                    </div>
                                </div>
                                
                                
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#HuobiThree" aria-expanded="false" aria-controls="HuobiThree">
                                          <h5 class="mb-0">Huobi  <span id="checked_huobimark" class="pull-right"><?php if(isset($mbot_data['huobi']['apikey']) && isset($mbot_data['huobi']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="HuobiThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="huobiEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyHuobi" class="form-control exInput"  value = '<?php if(isset($mbot_data['huobi']['apikey'])){echo $mbot_data['huobi']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyHuobi" class="form-control exInput"  value = '<?php if(isset($mbot_data['huobi']['seckey'])){echo $mbot_data['huobi']['seckey'];}?>'>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses.
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='Huobi_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                            <span>BTC</span>
                                                          <input id="huobi_btc_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['BTC']['address'])){echo $mbot_data['huobi']['coins']['BTC']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>ETH</span>
                                                          <input id="huobi_eth_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['ETH']['address'])){echo $mbot_data['huobi']['coins']['ETH']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>LTC</span>
                                                          <input id="huobi_ltc_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['LTC']['address'])){echo $mbot_data['huobi']['coins']['LTC']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>BCH</span>
                                                          <input id="huobi_bch_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['BCH']['address'])){echo $mbot_data['huobi']['coins']['BCH']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="huobi_eos_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['EOS']['tag'])){echo $mbot_data['huobi']['coins']['EOS']['tag'];}?>' value = '<?php if(isset($mbot_data['kraken']['coins']['EOS']['tag'])){echo $mbot_data['kraken']['coins']['EOS']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="huobi_eos_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['EOS']['address'])){echo $mbot_data['huobi']['coins']['EOS']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="huobi_xrp_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['XRP']['tag'])){echo $mbot_data['huobi']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="huobi_xrp_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['huobi']['coins']['XRP']['address'])){echo $mbot_data['huobi']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('huobi');"> Save </button>
                                            </p>    
                                        </div>      
                                      </div>
                                    </div>
                                </div>
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#LivecoinThree" aria-expanded="false" aria-controls="LivecoinThree">
                                          <h5 class="mb-0">Livecoin  <span id="checked_livecoinmark" class="pull-right"><?php if(isset($mbot_data['livecoin']['apikey']) && isset($mbot_data['livecoin']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">
                                    <div id="LivecoinThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="livecoinEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyLc" class="form-control exInput" value = '<?php if(isset($mbot_data['livecoin']['apikey'])){echo $mbot_data['livecoin']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyLc" class="form-control exInput" value = '<?php if(isset($mbot_data['livecoin']['seckey'])){echo $mbot_data['livecoin']['seckey'];}?>'>
                                            <div class="Ex_addressTags">
                                                
                                            </div>
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses.
                                                </p><br>
                                            </div>
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='liveCoin_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                            <span>EOS</span>
                                                          <input id="liveCoin_eos_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['livecoin']['coins']['EOS']['tag'])){echo $mbot_data['livecoin']['coins']['EOS']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="liveCoin_eos_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['livecoin']['coins']['EOS']['address'])){echo $mbot_data['livecoin']['coins']['EOS']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('livecoin');"> Save </button>
                                            </p>    
                                        </div>        
                                      </div>
                                    </div>
                                </div>
                                
                                
                                    <div class="" id="headingThree">
                                        <a class="exChange_name collapsed" data-toggle="collapse" data-target="#ExmoThree" aria-expanded="false" aria-controls="ExmoThree">
                                          <h5 class="mb-0">Exmo  <span id="checked_exmomark" class="pull-right"><?php if(isset($mbot_data['exmo']['apikey']) && isset($mbot_data['exmo']['seckey'])){echo '<i class="fas fa-check-circle greentick"></i>';}?></span></h5>
                                        </a>
                                    </div>
                                    <br>
                                <div class="card">    
                                    <div id="ExmoThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                      <div class="card-body">
                                        <div id="exmoEx" class="content">
                                            <label>Api Key:</label>
                                            <input type="text" id="apiKeyExmo" class="form-control exInput" value = '<?php if(isset($mbot_data['exmo']['apikey'])){echo $mbot_data['exmo']['apikey'];}?>'>
                                            <label>Secret Key:</label>
                                            <input type="text" id="secKeyExmo" class="form-control exInput" value = '<?php if(isset($mbot_data['exmo']['seckey'])){echo $mbot_data['exmo']['seckey'];}?>'>
                                            <br>                                            
                                            <div class="formInnerDiv">
                                                <p class="alert alert-danger" role="alert">
                                                  <strong>Note:</strong><br>
                                                  Please add your Api keys and these coins deposit addresses.
                                                </p><br>
                                            </div>
                                            <div class="Ex_addressTags">
                                            <div class="formInnerDiv jqueryWrappDiv">
                                                <div id='exmo_wrapper' class="field_wrapper">
                                                    <ul>
                                                        <li>
                                                          <span>XLM</span>
                                                          <input id="exmo_xlm_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['exmo']['coins']['XLM']['tag'])){echo $mbot_data['exmo']['coins']['XLM']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="exmo_xlm_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['exmo']['coins']['XLM']['address'])){echo $mbot_data['exmo']['coins']['XLM']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XRP</span>
                                                          <input id="exmo_xrp_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['exmo']['coins']['XRP']['tag'])){echo $mbot_data['exmo']['coins']['XRP']['tag'];}?>' placeholder="Coin Tag">
                                                          <input id="exmo_xrp_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['exmo']['coins']['XRP']['address'])){echo $mbot_data['exmo']['coins']['XRP']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                        <li>
                                                            <span>XMR</span>
                                                          <!--<input id="exmo_xmr_tag" type="text" name="coinTag[]"  value = '<?php if(isset($mbot_data['exmo']['coins']['XMR']['tag'])){echo $mbot_data['exmo']['coins']['XMR']['tag'];}?>' placeholder="Coin Tag">-->
                                                          <input id="exmo_xmr_address" type="text" name="coinAddress[]"  value = '<?php if(isset($mbot_data['exmo']['coins']['XMR']['address'])){echo $mbot_data['exmo']['coins']['XMR']['address'];}?>' placeholder="Coin Address">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div><br>
                                            <p class="mt-2 textAlignRight">
                                                <button type="button" class="btn btn-lg btn-success" onclick="saveExchangeKeys('exmo');"> Save </button>
                                            </p>    
                                        </div>         
                                      </div>
                                    </div>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
        
          <div class="tab-pane fade" id="membership" role="tabpanel" aria-labelledby="membership-tab">
                <div class="col-md-12 pt-3 pb-3">
                    <div class="card">
                        <div class="card-header">
                             <i class="fa fa-user-circle"></i></i> Membership Packages
                        </div>
                        <div class="card-body">
                            <div class="membershipWrap">
                                <div class="">
                                    <div class="card-deck mb-3 text-center">
                                        <?php foreach($packages as $pkg){ 
                                            if(!($pkg->name == "Pro+")){
                                        ?>
                                        
                                        <div class="card mb-4 box-shadow" style="border: 1px solid #343a40;">
                                          <div class="card-header">
                                            <h4 class="my-0 font-weight-normal"><?php echo $pkg->name; ?></h4>
                                          </div>
                                          <div class="card-body">
                                            <h1 class="card-title pricing-card-title"><?php echo $pkg->fee;?><small class="text-muted">$</small></h1>
                                            <ul class="list-unstyled mt-3 mb-4">
                                              <li><?php echo $pkg->allow_orders;?> Order Cancel/Day <small>(only Sell)</small></li>
                                              <li>$<?php echo $pkg->exchange_sell_limit;?> ARB/Day <small>(Exchange Limit)</small></li>
                                              <!--<li>$<?php //echo $pkg->exchange_er_sell_limit;?> ARB/Hour <small>(Earned Limit)</small></li>-->
                                              <li><?php echo $pkg->fee_discount;?>% Discount on Exchange Fees</li>
                                              <li><?php echo $pkg->mbot_limit;?>$ mBOT Limit/Day</li>
                                              <?php if($pkg->name == 'Pro'){ ?>
                                              <li>Account access upto 5 users</li>
                                              <?php }else if($pkg->name == 'Advance'){?>
                                              <li>Account access to 1 Pro user</li>
                                              <?php }else{?>
                                              <li>----</li>
                                              <?php } ?>
                                              
                                              <li><strong>Lifetime License</strong></li>
                                              
                                            </ul>
                                            <?php if($pkg->name == 'Basic' && ($user_current_package == 'Advance' || $user_current_package == 'Pro')){?>
                                            <button type="button" disabled class="btn btn-lg btn-block btn-outline-primary "/>
                                            <?php }else if($pkg->name == 'Advance' && $user_current_package == 'Pro'){?>
                                            <button type="button" disabled class="btn btn-lg btn-block btn-outline-primary "/>
                                            <?php }else{ ?>
                                            <button type="button" <?php if($pkg->name == $user_current_package){?> disabled <?php }else{?>onclick="activate_pkg('<?php echo $pkg->name;?>')"<?php }?> class="btn btn-lg btn-block btn-outline-primary <?php if($pkg->name == $user_current_package){?> btn-primary <?php }?>">
                                            <?php }?>
                                            <?php if($pkg->name == $user_current_package){?>Activated<?php }else{?>Activate<?php }?>
                                            </button>
                                          </div>
                                        </div>
                                        
                                        <?php }}?>
                                      </div>
                                    <!---->
                                </div>
                            </div>
                            <!---->
                            <div class="accfcs">
                                <h5><i class="fas fa-award"></i> Add-ons</h5>
                                <div class="accfcs_flex col-sm-4">
                                    <?php foreach($add_ons as $add_on){
                                                $addon_active = 0;
                                            foreach($user_current_add_ons as $one){
                                                    if($one->add_on_name == $add_on->add_on_name){
                                                        $addon_active = 1; 
                                                    }
                                            } ?>
                                    <div class="card mb-4 box-shadow" style="border: 1px solid #343a40;">
                                      <div class="card-header">
                                        <h4 class="my-0 font-weight-normal">
                                            <?php if($add_on->add_on_name == 'Pro+'){echo 'Plus +';} ?>
                                            <span class="" style="float:right;font-size: 20px;">
                                                <h1 class="card-title pricing-card-title">
                                                    <?php echo $add_on->fee;?><small class="text-muted">$</small>
                                                </h1>
                                            </span>    
                                        </h4>
                                      </div>
                                      <div class="card-body" style="display:flex;justify-content:space-between;">
                                        <div>
                                            <ul class="list-unstyled mt-3 mb-4">
                                              <li><strong>Monthly License</strong></li>
                                            </ul>    
                                        </div>
                                        <div>
                                            <button type="button" <?php if($addon_active == 1){?> disabled <?php }else{?>onclick="addOnActivate('<?php echo $add_on->add_on_name;?>')"<?php }?> class="btn btn-lg btn-block btn-outline-primary <?php if($addon_active == 1){?> btn-primary <?php }?>">
                                            <?php if($addon_active == 1){?>Activated<?php }else{?>Activate<?php }?>
                                            </button>
                                        </div>
                                        
                                      </div>
                                    </div>
                                    
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>   
                </div>
          </div>
            
          <div class="tab-pane fade" id="voting" role="tabpanel" aria-labelledby="voting-tab">
            <div class="col-md-12 pt-3 pb-3">
                <div class="card">
                    <div class="card-header">
                         <i class="fa fa-poll"></i> Active Voting
                    </div>
                    <div class="card-body votingTab">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="voting_left">
                                    <div class="active_votesWrap">
                                        <?php foreach($open_votings as $open){?>
                                            <div class="voteBoxWrap">
                                                <div class="card">
                                                    <div class="card-header">
                                                          <h5 class="mb-0">
                                                              <?php  echo $open['subject']; ?>
                                                              <span class="expireDate" style="float:right;padding-top: 8px;">Expire At: <?php  echo $open['expire']; ?></span>
                                                          </h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="voteBox">
                                                            <p><?php echo $open['discription'];?></p>
                                                            <div class="Vote_options">
                                                              <?php $hide_vote_btn = false; ?> 
                                                              <?php foreach($open['options'] as $opt){?>
                                                              <div class="voteRadioboxWrapper">
                                                                  <?php if($open['total'] > 0){$per = ($opt['count']/$open['total'])*100;}else{$per = 0;}?>
                                                                  <div class="qwer_wrap" style="position:relative;">
                                                                      
                                                                    <strong class="radioGasVote"><?php echo $opt['count'].' of '. $open['total'];?></strong>
                                                                    <label class="checkboxContainer">
                                                                        <?php if($opt['option'] != 'other'){ ?>
                                                                        <span class="radioGasWrap">
                                                                            <strong class="radioGasText"><?php echo number_format($per, 2, '.', '').' %';?></strong>
                                                                            <span class="radioGas" style="width:<?php echo $per.'%';?>; text-align:center; font-size:12px;"></span>
                                                                        </span>
                                                                        <?php if($opt['option']){ ?><p><?php echo $opt['option'];?></p><?php } ?>
                                                                        <?php }?>
                                                                            
                                                                      <input type="radio" <?php if(isset($opt['checked']) && $opt['checked'] == 1){ $hide_vote_btn = true; ?>checked <?php }?> value='<?php echo $opt['option'];?>' name="vote_<?php echo $open['topic_id'];?>">
                                                                      
                                                                      <span class="radioCheckmark"></span>
                                                                      
                                                                      <?php if($opt['davidchoice'] == '1') {?>
                                                                        <strong class="radioGasDavid" style="display:none">David voted for this option.</strong>    
                                                                      <?php } ?>
                                                                    </label>
                                                                      <?php if($opt['option']){ ?><p>
                                                                      <?php if($opt['option'] == 'other'){ ?>
                                                                        <div class="col-md-6 col-sm-10 autocomplete ml-5 pl-0">
                                                                            <input id="myInputSuggestion_<?php echo $open['topic_id'];?>" class="form-control otherOptionInput" onclick="suggestionInput(<?php echo $open['topic_id'];?>)" type='text' max='16' <?php if(isset($opt['other_text'])){ ?> value="<?php echo $opt['other_text'];?>" readonly <?php }?> name="user_option_<?php echo $open['topic_id'];?>"/>
                                                                        </div>
                                                                      <?php }?>
                                                                      </p>
                                                                      <?php } ?>
                                                                </div>
                                                              </div>
                                                              <?php }
                                                              if(!$hide_vote_btn && $voting_status == 1){
                                                              ?>
                                                              <div class="text-right">
                                                                <button type='button' class='btn' onClick="cast_vote(<?php echo $open['topic_id'];?>)">Vote</button>    
                                                              </div>
                                                              <?php }?>
                                                              
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }?>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="voting_right">
                                    <div class="voting_right_Top">
                                        <div class="votingSubscription">
                                            <button class="btn btn-warning" id="votingSubscriptionBtn" data-toggle="modal" data-target="#activateVotingModal"></button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="voting_right_bottom">
                                        <div class="old_voteings">
                                            <div class="active_votesWrap old_voteBox">
                                                <?php if(sizeof($close_votings) > 0){?>
                                                <h5>Old Votings</h5>
                                                <?php }?>
                                                <div class="voteBoxWrap allOldVotes">
                                                  <?php foreach($close_votings as $close){?>
                                                  <div class="old_vote_box">
                                                      <div class="card">
                                                        <div class="card-header" id="headingOne">
                                                              <h5 class="mb-0"><?php  echo $close['subject']; ?></h5>
                                                        </div>
                                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#votingExample">
                                                          <div class="card-body">
                                                            <div class="voteBox">
                                                                <?php if($close['discription']){ ?><p><?php echo $close['discription'];?></p><?php } ?>
                                                                <div class="">
                                                                  <?php foreach($close['options'] as $opt){?>
                                                                  <div class="voteRadioboxWrapper">
                                                                      <?php if($close['total'] > 0){$per = ($opt['count']/$close['total'])*100;}else{$per = 0;}?>
                                                                    <label class="checkboxContainer">
                                                                <span class="radioGasWrap">
                                                                    <strong class="radioGasText"><?php echo number_format($per, 2, '.', '').' %';?></strong>
                                                                    <span class="radioGas" style="width:<?php echo $per.'%';?>; text-align:center; font-size:12px;"></span>
                                                                </span>
                                                                        
                                                                      <?php if($opt['option']){ ?><p><?php echo $opt['option'];?></p> <?php } ?>
                                                                      <!--<input type="radio" <?php if(isset($opt['checked']) && $opt['checked'] == 1){ $hide_vote_btn = true; ?>checked <?php }?> value='<?php echo $opt['option'];?>' name="vote_<?php echo $close['topic_id'];?>">-->
                                                                    </label>
                                                                  </div>
                                                                  <?php }?>
                                                                </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                  </div>
                                                 <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>   
            </div>
        </div>
        
          <?php if($user_current_package == "Pro" || $user_current_package == "Advance") {?>
          <div class="tab-pane fade" id="accountAccess" role="tabpanel" aria-labelledby="accountAccess-tab">
            <div class="col-md-12 pt-3 pb-3">
                <div class="card">
                    <div class="card-header">
                         <i class="fas fa-award"></i> Account Access
                    </div>
                    <div class="card-body">
                        <div class="accountAccessWrapper">
                            <div id="AC_First" style="display: none">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Enter Email</label>
                                    <input type="email" class="form-control" id="accessAccountEmail" placeholder="Enter email" required>
                                    <small class="form-text text-muted">Enter email you want to give access to.</small>
                                </div>
                                <button type="submit" class="save_btn" onclick="sendAccessEmail()"><i id="loader_access" class="fa fa-spinner fa-spin" style="display:none"></i>  <i class="fas fa-save"></i>  SEND</button>
                                
                            </div>
                            <div id="AC_Second" style="display: none">
                                <div class="form-group">
                                    <label for="exampleInputPin">Enter Pin</label>
                                    <input type="text" class="form-control" id="accessAccountPin" placeholder="Enter pin" required>
                                    <small class="form-text text-muted">Pin should be 6 characters.</small>
                                </div>
                                <button type="submit" class="save_btn" onclick="checkGoogleAuth('accessAccount')"><i id="loader_accessPin" class="fa fa-spinner fa-spin" style="display:none"></i>  <i class="fas fa-save"></i>  SEND</button>
                                
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="AC_MyAccountAssess" style="display: none">
                                        <div>
                                            <h3 class="h3HeadingAccess">Access of your Account:</h3>
                                            <label>Access of your Account is given to:</label> <strong><?php echo $access_given_to;?> <i class="fas fa-times deleteAccessAcc" onclick="cancelAccessAcc()"></i></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="AC_AssessOfOthersAccount" style="display: none">
                                        <div>
                                            <h3 class="h3HeadingAccess">Access for Others Account:</h3>
                                            <table class="table table-hover table-striped textAlignCenter">
                                                <thead>
                                                <tr>
                                                    <th>Email</th>
                                                    <th>Username</th>
                                                </tr>
                                                </thead>
                                                <tbody id="AssessOfOthersTable" class="textAlignCenter">
                    
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
          <?php }?>
          
          <?php if($user_current_add_ons[0]->add_on_name == 'Pro+') { ?>
            <div class="tab-pane fade" id="proPlus" role="tabpanel" aria-labelledby="proPlus-tab">
                <div class="col-md-12 pt-3 pb-3">
                    <div class="card">
                        <div class="card-header">
                             <i class="fas fa-award"></i> <strong>Plus </strong><i class="fa fa-plus"></i>
                        </div>
            
                        <div class="card-body">
                            
                            <script>
                                $(function () {
                                  // Smooth Scroll
                                  $('a[href*=#]').bind('click', function(e){
                                    var anchor = $(this);
                                    $('html, body').stop().animate({
                                      scrollTop: $(anchor.attr('href')).offset().top
                                    }, 1000);
                                    e.preventDefault();
                                  });
                                });
                                
                                (function ($) {
                                  // Color Changer
                                  $('.color-changer li a').on('click', function(e){
                                    var color = $(this).data('bgcolor');
                                    $('.dc-menu .dc-list').css({'background-color': color});
                                    e.preventDefault();
                                  });
                                  $(document).ready(function() {
                                    $('.ac-list > li.expanded > a').on('click', function(e) {
                                      e.preventDefault();
                                      if($(this).next('ul.sub-menu').is(':visible')) {
                                          $(this).removeClass('open');
                                          $(this).next('ul.sub-menu').slideUp();
                                      } else {
                                          $('.ac-list > li.expanded > a').removeClass('open');
                                          $(this).addClass('open');
                                          $('.ac-list > li.expanded > a').next('ul.sub-menu').slideUp();
                                          $(this).next('ul.sub-menu').slideToggle();
                                      }
                                    });
                                    
                                    var $menu = $('.bc-list');
                                    $menu.find('li.expanded > a').on('click', function(e) {
                                      e.preventDefault();
                                      if($(this).next('ul.sub-menu').is(':visible')) {
                                          $(this).removeClass('open');
                                          $(this).next('ul.sub-menu').slideUp();
                                      } else {
                                          $menu.find('li.expanded > a').removeClass('open');
                                          $(this).addClass('open');
                                          $menu.find('li.expanded > a').next('ul.sub-menu').slideUp();
                                          $(this).next('ul.sub-menu').slideToggle();
                                      }
                                    });
                                    
                                    var $cmenu = $('.cc-list');
                                    $cmenu.find('li.expanded > a').on('click', function(e) {
                                      e.preventDefault();
                                      if($(this).next('ul.sub-menu').is(':visible')) {
                                          $(this).removeClass('open');
                                          $(this).next('ul.sub-menu').slideUp();
                                      } else {
                                          $cmenu.find('li.expanded > a').removeClass('open');
                                          $(this).addClass('open');
                                          $cmenu.find('li.expanded > a').next('ul.sub-menu').slideUp();
                                          $(this).next('ul.sub-menu').slideToggle();
                                      }
                                    });
                                    
                                    var $dmenu = $('.dc-list');
                                    $dmenu.find('li.expanded > a').on('click', function(e) {
                                      e.preventDefault();
                                      if($(this).next('ul.sub-menu').is(':visible')) {
                                          $(this).removeClass('open');
                                          $(this).next('ul.sub-menu').slideUp();
                                      } else {
                                          $dmenu.find('li.expanded > a').removeClass('open');
                                          $(this).addClass('open');
                                          $dmenu.find('li.expanded > a').next('ul.sub-menu').slideUp();
                                          $(this).next('ul.sub-menu').slideToggle();
                                      }
                                    });
                                    
                                    var $emenu = $('#three-one');
                                    $emenu.find('li.expanded > a').on('click', function(e) {
                                      e.preventDefault();
                                      if($(this).next('ul.sub-menu').is(':visible')) {
                                          $(this).parent().removeClass('open');
                                          $(this).next('ul.sub-menu').slideUp();
                                      } else {
                                          $emenu.find('li.expanded').removeClass('open');
                                          $(this).parent().addClass('open');
                                          $emenu.find('li.expanded').children('ul.sub-menu').slideUp();
                                          $(this).next('ul.sub-menu').slideToggle();
                                      }
                                    });
                                    
                                    $(".ec-list > .expanded > a").click(function() {
                                      var e = $(this).next(".sub-menu")
                                        , a = ".ec-list > li.expanded > .sub-menu";
                                      0 === $(".page-sidebar-minified").length && ($(a).not(e).slideUp(function() {
                                        $(this).closest("li").removeClass("open")
                                      }),
                                      $(e).slideToggle(function() {
                                        var e = $(this).closest("li");
                                        $(e).hasClass("open") ? $(e).removeClass("open") : $(e).addClass("open")
                                      }))
                                    }),
                                    $(".ec-list > .expanded .sub-menu li.expanded > a").click(function() {
                                      if (0 === $(".page-sidebar-minified").length) {
                                        var e = $(this).next(".sub-menu");
                                        $(e).slideToggle()
                                      }
                                    });
                                  });
                                })(jQuery);
                            </script>
                            <div class="proPlusWrapper">
                                
                                <div>
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="proheading1-tab" data-toggle="tab" href="#innerPro1" role="tab" aria-controls="innerPro1" aria-selected="true">Setting</a>
                                      </li>
                                      <!--<li class="nav-item">-->
                                      <!--  <a class="nav-link" id="proheading2-tab" data-toggle="tab" href="#innerPro2" role="tab" aria-controls="innerPro2" aria-selected="false">Affiliate Selling ETH</a>-->
                                      <!--</li>-->
                                    </ul>
                                    <!--___________________________________________________________________________________________________________________________________________-->
                                    
                                    <div class="tab-content" id="myTabContent">
                                      <div class="tab-pane fade show active p-3" id="innerPro1" role="tabpanel" aria-labelledby="innerPro1-tab">
                                          
                                        <div class="ac-menu">
                                          <ul class="ac-list">
                                          <li class="expanded">
                                              <a href="javascript: void(0);">Automate Selling of ARB</a>
                                              <ul class="sub-menu">
                                                <!--<li>-->
                                                <!--  <div class="checkboxWrapper">-->
                                                <!--    <label class="checkboxContainer">Deposit to exchange Wallet-->
                                                <!--      <input id="dtew_1" type="checkbox" <?php //if( $pasob_trns_to_ex == 1){ echo "checked='checked'";} ?> >-->
                                                <!--      <span class="radioCheckmark"></span>-->
                                                <!--    </label>-->
                                                <!--  </div>-->
                                                <!--</li>-->
                                                <!--<li>-->
                                                <!--  <div class="checkboxWrapper">-->
                                                <!--    <label class="checkboxContainer">Auto Sell-->
                                                <!--      <input id="sell_arb_1" type="checkbox" <?php //if( $pasob_sell_or_buy == 1){ echo "checked='checked'";} ?> >-->
                                                <!--      <span class="radioCheckmark"></span>-->
                                                <!--    </label>-->
                                                <!--  </div>-->
                                                <!-- </li>-->
                                                <li>
                                                    <div class="text-left sm_innerDiv">
                                                        <span>
                                                            <label style="width: 100%;">Auto Sell Percentage of ARB earned in aBOT (after reinvest):</label>
                                                            <select id="auto_sell_per" class="round" vlaue="<?php if($pasob_sell_or_buy) echo $pasob_sell_or_buy; ?>" placeholder="">
                                                                <option value="25" <?php if($pasob_sell_or_buy == 25)  echo "selected"; ?> >25</option>
                                                                <option value="50" <?php if($pasob_sell_or_buy == 50) echo "selected"; ?>>50</option>
                                                                <option value="75" <?php if($pasob_sell_or_buy == 75) echo "selected"; ?>>75</option>
                                                                <option value="100" <?php if($pasob_sell_or_buy == 100) echo "selected"; ?>>100</option>
                                                            </select>
                                                        </span>
                                                        <span style="font-size: 12px;color: #18bc9c;"><strong>
                                                            <!--(We charge a very genius fee up to 2.5% to use this feature!  We use it to suplement buying during slow hours when volume needs assistance. So you always end up getting it back!)</strong></span>-->
                                                        (We charge a very fair fee up to 2.5% to use this feature! We use it to supplement buying during
slow hours when volume needs assistance, returning you this fee overtime.)</strong></span>
                                                        <br>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="">
                                                      <div class="checkboxWrapper">
                                                        <label class="checkboxContainer">Withdraw ETH
                                                          <input id="withdraw_1" type="checkbox" <?php if( $pasob_withdraw == 1){ echo "checked='checked'";} ?> >
                                                          <span class="radioCheckmark"></span>
                                                        </label>
                                                      </div>
                                                    </div>
                                                </li>
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Transfer to system wallet
                                                      <input id="m_eth_to_sw" type="checkbox" <?php if( $pasob_trns_to_sw == 1 ){ echo "checked='checked'";} ?> >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                </li>
                                                <li>
                                                    <div id="actionsDiv1" class="actionsDiv aD1">
                                                        <!--<a id="edit_1" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>-->
                                                        <button id="deactivate_1" type="submit" class="deactivateBtn"><i id="deactive_i" class="fa fa-spinner fa-spin" style="display:none"></i>Deactivate</button>
                                                        <button id="save_1" type="submit" class="save_btn"><i id="loader_i" class="fa fa-spinner fa-spin" style="display:none"></i>  <i class="fas fa-save"></i>  SAVE</button>
                                                    </div>
                                                </li>
                                              </ul>
                                            </li>
                                          <li class="expanded">
                                            <a href="javascript:void(0);">Automate deposit in aBOT
                                              <!--<div class="checkboxWrapper">-->
                                              <!--  <label class="checkboxContainer">Automate stop aBot-->
                                              <!--    <input id="" type="checkbox" checked="checked">-->
                                              <!--    <span class="radioCheckmark"></span>-->
                                              <!--  </label>-->
                                              <!--</div>-->
                                            </a>
                                            <ul class="sub-menu">
                                                <li>
                                                    <div class="checkboxWrapper">
                                                        <label class="checkboxContainer">Deposit to Exchange Wallet
                                                          <input id="eth_deposit_in_abot1" class="eth_deposit_in_abot" type="checkbox" <?php if( $pasob_eth_deposit_in_abot == 1){ echo "checked='checked'";} ?> >
                                                          <span class="radioCheckmark"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                                
                                                <!---->
                                                
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Sell ETH
                                                      <input id="eth_deposit_in_abot2" class="eth_deposit_in_abot" type="checkbox" <?php if( $pasob_eth_deposit_in_abot == 1){ echo "checked='checked'";} ?> >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                </li>
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Move ARB to system wallet
                                                      <input id="eth_deposit_in_abot3" class="eth_deposit_in_abot" type="checkbox" <?php if( $pasob_eth_deposit_in_abot == 1){ echo "checked='checked'";} ?> >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                 </li>
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Invest in aBOT
                                                      <input id="eth_deposit_in_abot4" class="eth_deposit_in_abot" type="checkbox" <?php if( $pasob_eth_deposit_in_abot == 1){ echo "checked='checked'";} ?> >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                </li>
                                                <!---->
                                                
                                                <li>
                                                    <div class="actionsDiv  aD1">
                                                        <!--<a id="edit_1" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>-->
                                                        <button id="save_4" type="submit" class="save_btn"><i id="loader_4" class="fa fa-spinner fa-spin" style="display:none"></i>  <i class="fas fa-save"></i>  SAVE</button>
                                                    </div>
                                                </li>
                                            </ul>
                                          </li>
                                          <li class="expanded">    <!-- sub menu -->
                                            <a href="javascript:void(0);">
                                              Automate aBot active
                                              <!--<div class="checkboxWrapper">-->
                                              <!--  <label class="checkboxContainer">Automate aBot active-->
                                              <!--    <input id="" type="checkbox" checked="checked">-->
                                              <!--    <span class="radioCheckmark"></span>-->
                                              <!--  </label>-->
                                              <!--</div>  -->
                                            </a>
                                            <ul class="sub-menu">
                                              <li>
                                                  <div class="text-left sm_innerDiv">
                                                      <span>
                                                          <label>ARB price in aUSD</label>
                                                          <input id="automate_abote_active_value" type="number" name="" value="<?php if($pp_arb_value) echo $pp_arb_value; ?>" placeholder="value">
                                                      </span>
                                                      <span>
                                                          <label>Total ARBs</label>
                                                          <input id="automate_abote_active_arb" type="number" name="" value="<?php if($pp_arb_amount) echo $pp_arb_amount; ?>" placeholder="ARB">
                                                      </span>
                                                  </div>
                                              </li>
                                              <li>
                                                <div class="actionsDiv">
                                                    
                                                    <span id="noteSpan">
                                                        <!--<strong>NOTE: </strong>-->
                                                        Invest <span id="dollar_p"></span> ARBs in aBOT when price reaches $<span id="arb_value_p"></span>
                                                    </span>  
                                                    
                                                    
                                                    <!--<a id="edit_2" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>-->
                                                    <button id="save_2" type="submit" class="save_btn"><i id="loader_2" class="fa fa-spinner fa-spin" style="display:none"></i>  <i class="fas fa-save"></i> SAVE</button>
                                                </div>
                                              </li>
                                            </ul>
                                          </li>
                                          <li class="expanded">    <!-- sub menu -->
                                                    <a href="javascript:void(0);">Automate stop aBOT
                                                      <!--<div class="checkboxWrapper">-->
                                                      <!--  <label class="checkboxContainer">Automate stop aBot-->
                                                      <!--    <input id="" type="checkbox" checked="checked">-->
                                                      <!--    <span class="radioCheckmark"></span>-->
                                                      <!--  </label>-->
                                                      <!--</div>-->
                                                    </a>
                                                    <ul class="sub-menu">
                                                        <li>
                                                            <div class="text-left sm_innerDiv">
                                                                <span>
                                                                    <label>ARB price in aUSD</label> 
                                                                    <input id="dollarValue" type="text" name="" value="<?php if($pp_stop_value) echo $pp_stop_value; ?>" placeholder="$ value" />
                                                                </span>
                                                                <span>
                                                                    <label>Investment Percentage</label>
                                                                    <select id="investmentPercentage" class="round" vlaue="<?php if($pp_stop_active_arb_per) echo $pp_stop_active_arb_per; ?>" placeholder="">
                                                                        <option value="25" <?php if($pp_stop_active_arb_per == 25)  echo "selected"; ?> >25</option>
                                                                        <option value="50" <?php if($pp_stop_active_arb_per == 50) echo "selected"; ?>>50</option>
                                                                        <option value="75" <?php if($pp_stop_active_arb_per == 75) echo "selected"; ?>>75</option>
                                                                        <option value="100" <?php if($pp_stop_active_arb_per == 100) echo "selected"; ?>>100</option>
                                                                    </select>
                                                                </span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="actionsDiv">
                                                                
                                                            <span id="noteSpan2">
                                                                <!--<strong>NOTE: </strong>-->
                                                                Stop % <span id="arb_value_p2"></span> of active investment when price reaches <span id="dollar_p2"></span> ARBs
                                                                <!--Invest ARBs in aBOT when price reaches -->
                                                            </span>  
                                                                <!--<a id="edit_3" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>-->
                                                                <button id="save_3" type="submit" class="save_btn"><i id="loader_3" class="fa fa-spinner fa-spin" style="display:none"></i>  <i class="fas fa-save"></i> SAVE</button>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </li>
                                              
                                              <!---->
                                          </ul>
                                        </div>
                                      </div>
                                      <div class="tab-pane fade" id="innerPro2" role="tabpanel" aria-labelledby="innerPro2-tab" style="display: none">
                                          
                                       <div class="ac-menu">
                                          <ul class="ac-list">
                                            <li class="expanded">
                                              <a href="javascript: void(0);">Automated Selling ABR</a>
                                              <ul class="sub-menu">
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Deposit to exchange Wallet
                                                      <input id="dtew_21" type="checkbox" >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                </li>
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Sell ARB
                                                      <input id="sell_arb_21" type="checkbox" >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                 </li>
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Move ETH to system wallet
                                                      <input id="m_eth_to_sw2" type="checkbox" >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                </li>
                                                <li>
                                                  <div class="checkboxWrapper">
                                                    <label class="checkboxContainer">Withdraw / (0.25 ETH)
                                                      <input id="withdraw_21" type="checkbox" >
                                                      <span class="radioCheckmark"></span>
                                                    </label>
                                                  </div>
                                                </li>
                                                <li>
                                                    <div class="actionsDiv aD1">
                                                        <a id="edit_21" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>
                                                        <button id="save_21" type="submit" class="save_btn"><i class="fas fa-save"></i> SAVE</button>
                                                    </div>
                                                </li>
                                              </ul>
                                            </li>
                                            <!--<li class="expanded">-->
                                            <!--    <a href="javascript: void(0);">-->
                                            <!--      <div class="checkboxWrapper">-->
                                            <!--        <label class="checkboxContainer">Automate aBot Earning ( no submenu )-->
                                            <!--          <input id="" type="checkbox" checked="checked">-->
                                            <!--          <span class="radioCheckmark"></span>-->
                                            <!--        </label>-->
                                            <!--      </div>-->
                                            <!--    </a>-->
                                            <!--</li>-->
                                            
                                            
                                              <li class="expanded">    <!-- sub menu -->
                                                <a href="javascript:void(0);">
                                                  Automate aBot active
                                                  <!--<div class="checkboxWrapper">-->
                                                  <!--  <label class="checkboxContainer">Automate aBot active-->
                                                  <!--    <input id="" type="checkbox" checked="checked">-->
                                                  <!--    <span class="radioCheckmark"></span>-->
                                                  <!--  </label>-->
                                                  <!--</div>  -->
                                                </a>
                                                <ul class="sub-menu">
                                                  <li>
                                                      <div class="text-left sm_innerDiv">
                                                          <span>
                                                              <label>$</label>
                                                              <input id="" type="number" name="" value="" placeholder="value">
                                                          </span>
                                                          <span>
                                                              <label>ARB</label>
                                                              <input id="" type="number" name="" value="" placeholder="ARB">
                                                          </span>
                                                      </div>
                                                  </li>
                                                  <li>
                                                    <div class="actionsDiv text-right">
                                                        <a id="edit_22" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>
                                                        <button id="save_22" type="submit" class="save_btn"><i class="fas fa-save"></i> SAVE</button>
                                                    </div>
                                                  </li>
                                                </ul>
                                              </li>
                                              
                                              
                                              <li class="expanded">    <!-- sub menu -->
                                                <a href="javascript:void(0);">Automate stop aBot
                                                  <!--<div class="checkboxWrapper">-->
                                                  <!--  <label class="checkboxContainer">Automate stop aBot-->
                                                  <!--    <input id="" type="checkbox" checked="checked">-->
                                                  <!--    <span class="radioCheckmark"></span>-->
                                                  <!--  </label>-->
                                                  <!--</div>-->
                                                </a>
                                                <ul class="sub-menu">
                                                    <li>
                                                        <div class="text-left sm_innerDiv">
                                                            <span>
                                                                <label>$</label>
                                                                <input id="" type="text" name="" value="" placeholder="$ value" />
                                                            </span>
                                                            <span>
                                                                <label>%</label>
                                                                <select class="round" vlaue="" >
                                                                    <option value="" disabled selected>%</option>
                                                                    <option value="25">25 %</option>
                                                                    <option value="50">50 %</option>
                                                                    <option value="75">75 %</option>
                                                                    <option value="100">100 %</option>
                                                                </select>
                                                            </span>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="actionsDiv text-right">
                                                            <a id="edit_33" class="edit_a" href="javascript:void(0);"><i class="fas fa-pencil-alt"></i> EDIT</a>
                                                            <button id="save_33" type="submit" class="save_btn"><i class="fas fa-save"></i> SAVE</button>
                                                        </div>
                                                    </li>
                                                </ul>
                                              </li>
                                          
                                          
                                          </ul>
                                        </div>
                                            
                                      </div>
                                    </div>
                                    
                                </div> <!-- end here -->
                                
                                <script>
                                    <?php if($pasob_status == 0){ ?>
                                        $("#deactivate_1").css("display","none");      
                                    <?php } ?>
                                    
                                    //deactivate
                                    $("#deactivate_1").click(function(){
                                        $("#deactive_i").css("display","inline-block");
                                            $.post("<?php echo base_url(); ?>deactive_auto_selling", {}
                                          ).done(function( data ) {
                                            $("#deactivated_Modal").modal("show");    
                                            setInterval(function(){  $("#deactivated_Modal").modal("hide"); }, 2000);
                                               $("#deactive_i").css("display","none");
                                            //   $("#m_eth_to_sw").prop('checked', false);
                                            //   $("#withdraw_1").prop('checked', false);
                                            //   $("#auto_sell_per").val(0);
                                               $("#deactivate_1").css("display","none");
                                           });  
                                    });
                                    //          2
                                    $("#save_1").click(function(){
                                        var m_eth_to_sw = withdraw_1 = auto_sell_per = 0;
                                        
                                       if($("#m_eth_to_sw").prop("checked") == true){
                                           m_eth_to_sw = 1;
                                       }
                                       else { m_eth_to_sw = 0; }
                                       
                                       if($("#withdraw_1").prop("checked") == true){
                                           withdraw_1 = 1;
                                       }
                                       else { withdraw_1 = 0; }
                                    
                                    var auto_sell_per     = $("#auto_sell_per").val();
                                    //   alert(dollarValue);
                                    //   alert(investmentPercentage);
                                       if( auto_sell_per > 0 && (withdraw_1 == 1 || m_eth_to_sw == 1) ){
                                            // alert("bbbbbbb")
                                            // console.log( investmentPercentage ,"investmentPercentage");
                                            $("#loader_i").css("display","inline-block");
                                            $.post("<?php echo base_url(); ?>auto_selling_or_buying", {auto_sell_per: auto_sell_per, to_sw: m_eth_to_sw, withdraw: withdraw_1 }
                                              ).done(function( data ) {
                                                  if(data == "updated"){
                                                    $("#updated_Modal").modal("show");    
                                                    setInterval(function(){  $("#updated_Modal").modal("hide"); }, 2000);
                                                    $("#deactivate_1").css("display","block");
                                                    // $("#actionsDiv1").prepend(`<button id="deactivate_1" type="submit" class="deactivateBtn"><i id="deactive_i" class="fa fa-spinner fa-spin" style="display:none"></i>Deactivate</button>`);
                                                  }
                                                  else {
                                                    $("#settingsSaved_Modal").modal("show");
                                                    setInterval(function(){  $("#settingsSaved_Modal").modal("hide"); }, 2000);
                                                    $("#deactivate_1").css("display","block");
                                                    // $("#actionsDiv1").prepend(`<button id="deactivate_1" type="submit" class="deactivateBtn"><i id="deactive_i" class="fa fa-spinner fa-spin" style="display:none"></i>Deactivate</button>`);
                                                  }
                                                   $("#loader_i").css("display","none");
                                               });  
                                       }
                                       else {
                                           alert("empty value not allowed");
                                       }
                                    });
                                    
                                    // save function
                                    $("#save_4").click(function(){
                                       var eth_deposit_in_abot = 0;
                                       
                                       if($("#eth_deposit_in_abot1").prop("checked") == true && $("#eth_deposit_in_abot2").prop("checked") == true  && $("#eth_deposit_in_abot3").prop("checked") == true  && $("#eth_deposit_in_abot4").prop("checked") == true ){
                                          eth_deposit_in_abot = 1; 
                                       }
                                       else { eth_deposit_in_abot = 0;}
                                       
                                    //   if( eth_deposit_in_abot > 0 ){
                                            // alert("bbbbbbb")
                                            // console.log( valuee ,"value");
                                            // console.log( arb ,"ARB");
                                            $("#loader_4").css("display","inline-block");
                                            $.post("<?php echo base_url(); ?>proplus_eth_setting", {eth_deposit_in_abot: eth_deposit_in_abot }
                                              ).done(function( data ) {
                                                  if(data == "updated"){
                                                    $("#updated_Modal").modal("show");    
                                                    setInterval(function(){  $("#updated_Modal").modal("hide"); }, 2000);
                                                  }
                                                  else {
                                                    $("#settingsSaved_Modal").modal("show");
                                                    setInterval(function(){  $("#settingsSaved_Modal").modal("hide"); }, 2000);
                                                  }
                                                   $("#loader_4").css("display","none");
                                               });   
                                    //   }
                                    //   else {
                                    //       alert("empty value not allowed");
                                    //   }
                                    //   console.log($("#automate_abote_active_value").val() , "valuie")
                                    });
                                    
                                    // 2nd save function
                                    $("#save_2").click(function(){
                                       var valuee = $("#automate_abote_active_value").val();
                                       var arb    = $("#automate_abote_active_arb").val();
                                       
                                       if( valuee > 0 && arb > 0 ){
                                            // alert("bbbbbbb")
                                            // console.log( valuee ,"value");
                                            // console.log( arb ,"ARB");
                                            $("#loader_2").css("display","inline-block");
                                            $.post("<?php echo base_url(); ?>auto_abot_active", {arb_value: valuee , arb_amount: arb }
                                              ).done(function( data ) {
                                                  if(data == "updated"){
                                                    $("#updated_Modal").modal("show");    
                                                    setInterval(function(){  $("#updated_Modal").modal("hide"); }, 2000);
                                                  }
                                                  else {
                                                    $("#settingsSaved_Modal").modal("show");
                                                    setInterval(function(){  $("#settingsSaved_Modal").modal("hide"); }, 2000);
                                                  }
                                                   $("#loader_2").css("display","none");
                                               });   
                                       }
                                       else {
                                           alert("empty value not allowed");
                                       }
                                    //   console.log($("#automate_abote_active_value").val() , "valuie")
                                    });
                                    
                                    // third save button
                                    $("#save_3").click(function(){
                                       var dollarValue              = $("#dollarValue").val();
                                       var investmentPercentage     = $("#investmentPercentage").val();
                                    //   alert(dollarValue);
                                    //   alert(investmentPercentage);
                                       if( dollarValue > 0 && investmentPercentage > 0 ){
                                            // alert("bbbbbbb")
                                            // console.log( dollarValue ,"dollarValue");
                                            // console.log( investmentPercentage ,"investmentPercentage");
                                            $("#loader_3").css("display","inline-block");
                                            $.post("<?php echo base_url(); ?>auto_stop_abot", {arb_value: dollarValue , active_arb_per: investmentPercentage }
                                              ).done(function( data ) {
                                                  if(data == "updated"){
                                                    $("#updated_Modal").modal("show");    
                                                    setInterval(function(){  $("#updated_Modal").modal("hide"); }, 2000);
                                                  }
                                                  else {
                                                    $("#settingsSaved_Modal").modal("show");
                                                    setInterval(function(){  $("#settingsSaved_Modal").modal("hide"); }, 2000);
                                                  }
                                                //   console.log(data,"data")
                                                   $("#loader_3").css("display","none");
                                               });   
                                       }
                                       else {
                                           alert("empty value not allowed");
                                       }
                                    //   console.log($("#automate_abote_active_value").val() , "valuie")
                                    });
                                    
                                    //      21
                                    $("#save_21").click(function(){
                                        var dtew_21 = sell_arb_21 = m_eth_to_sw2 = withdraw_21 = 0;
                                        
                                        
                                           if($("#dtew_21").prop("checked") == true){
                                              dtew_21 = 1; 
                                           }
                                           else { dtew_21 = 0;}
                                           
                                           
                                           if($("#sell_arb_21").prop("checked") == true){
                                               sell_arb_21 = 1;
                                           }
                                           else { sell_arb_21 = 0; }
                                           
                                           if($("#m_eth_to_sw2").prop("checked") == true){
                                               m_eth_to_sw2 = 1;
                                           }
                                           else { m_eth_to_sw2 = 0; }
                                           
                                           if($("#withdraw_21").prop("checked") == true){
                                               withdraw_21 = 1;
                                           }
                                           else { withdraw_21 = 0; }
                                           
                                    });
                                    
                                    $(document).ready(function(){
                                        //                              1
                                        // $('#dtew_1').click(function(){
                                        //     if($(this).is(":checked")){
                                        //         // alert("Checkbox is checked.Ali");
                                        //     }
                                        //     else if($(this).is(":not(:checked)")){
                                        //         // alert("Checkbox is unchecked.Ali");
                                        //         $("#sell_arb_1").prop('checked', false);
                                        //         $("#m_eth_to_sw").prop('checked', false);
                                        //         $("#withdraw_1").prop('checked', false);
                                        //         // $("#transfer_to_abot_1").prop('checked', false);
                                        //     }
                                        // });
                                        
                                        //                              2
                                        // $('#sell_arb_1').click(function(){
                                        //     if($(this).is(":checked")){
                                        //         // alert("Checkbox is checked.Ali");
                                        //         $("#dtew_1").prop('checked', true);
                                        //     }
                                        //     else if($(this).is(":not(:checked)")){
                                        //         // alert("Checkbox is unchecked.Ali");
                                        //         $("#m_eth_to_sw").prop('checked', false);
                                        //         $("#withdraw_1").prop('checked', false);
                                        //         // $("#transfer_to_abot_1").prop('checked', false);
                                        //     }
                                        // });
                                        //                              
                                        // $('#eth_deposit_in_abot1').click(function(){
                                        //     if($(".eth_deposit_in_abot").is(":checked")){
                                        //         // alert("Checkbox is checked.Ali");
                                        //         $(this).prop('checked', true);
                                        //     }
                                        //     else if($(".eth_deposit_in_abot").is(":not(:checked)")){
                                        //         $(".eth_deposit_in_abot").prop('checked', false);
                                        //     }
                                        // });
                                        
                                        
                                        //                             3
                                        $('#m_eth_to_sw').click(function(){
                                            if($(this).is(":checked")){
                                                // alert("Checkbox is checked.m_eth_to_sw");
                                                // $("#dtew_1").prop('checked', true);
                                                $("#withdraw_1").prop('checked', false);
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                // alert("Checkbox is unchecked.m_eth_to_sw");
                                                $("#withdraw_1").prop('checked', true);
                                                // $("#transfer_to_abot_1").prop('checked', false);
                                            }
                                        });
                                        
                                        //                              4.1
                                        $('#withdraw_1').click(function(){
                                            if($(this).is(":checked")){
                                                // alert("Checkbox is checked.withdraw_1");
                                                // $("#dtew_1").prop('checked', true);
                                                // $("#sell_arb_1").prop('checked', true);
                                                // $("#m_eth_to_sw").prop('checked', true);
                                                // $("#transfer_to_abot_1").prop('checked', false);
                                                $("#m_eth_to_sw").prop('checked', false);
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                // alert("Checkbox is unchecked.withdraw_1");
                                                $("#m_eth_to_sw").prop('checked', true);
                                            }
                                        });
                                        
                                        //                              4.2
                                        // $('#transfer_to_abot_1').click(function(){
                                        //     if($(this).is(":checked")){
                                        //         // alert("Checkbox is checked.Ali");
                                        //         $("#dtew_1").prop('checked', true);
                                        //         $("#sell_arb_1").prop('checked', true);
                                        //         $("#m_eth_to_sw").prop('checked', true);
                                        //         $("#withdraw_1").prop('checked', false);
                                        //     }
                                        //     else if($(this).is(":not(:checked)")){
                                        //         // alert("Checkbox is unchecked.Ali");
                                        //     }
                                        // });
                                        
                                        //
                                        
                                        
                                        //                              2
                                        $('#eth_deposit_in_abot1,#eth_deposit_in_abot2,#eth_deposit_in_abot3,#eth_deposit_in_abot4').click(function(){
                                            if($(this).is(":checked")){
                                                $("#eth_deposit_in_abot1").prop('checked', true);
                                                $("#eth_deposit_in_abot2").prop('checked', true);
                                                $("#eth_deposit_in_abot3").prop('checked', true);
                                                $("#eth_deposit_in_abot4").prop('checked', true);
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                $("#eth_deposit_in_abot1").prop('checked', false);
                                                $("#eth_deposit_in_abot2").prop('checked', false);
                                                $("#eth_deposit_in_abot3").prop('checked', false);
                                                $("#eth_deposit_in_abot4").prop('checked', false);
                                            }
                                        });
                                        //
                                        //                              21
                                        $('#dtew_21').click(function(){
                                            if($(this).is(":checked")){
                                                // alert("Checkbox is checked.Ali");
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                // alert("Checkbox is unchecked.Ali");
                                                $("#sell_arb_21").prop('checked', false);
                                                $("#m_eth_to_sw2").prop('checked', false);
                                                $("#withdraw_21").prop('checked', false);
                                            }
                                        });
                                        
                                        //                              22
                                        $('#sell_arb_21').click(function(){
                                            if($(this).is(":checked")){
                                                // alert("Checkbox is checked.Ali");
                                                $("#dtew_21").prop('checked', true);
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                // alert("Checkbox is unchecked.Ali");
                                                $("#m_eth_to_sw2").prop('checked', false);
                                                $("#withdraw_21").prop('checked', false);
                                            }
                                        });
                                        
                                        //                             23
                                        $('#m_eth_to_sw2').click(function(){
                                            if($(this).is(":checked")){
                                                // alert("Checkbox is checked.Ali");
                                                $("#dtew_21").prop('checked', true);
                                                $("#sell_arb_21").prop('checked', true);
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                // alert("Checkbox is unchecked.Ali");
                                                $("#withdraw_21").prop('checked', false);
                                            }
                                        });
                                        
                                        //                              24
                                        $('#withdraw_21').click(function(){
                                            if($(this).is(":checked")){
                                                // alert("Checkbox is checked.Ali");
                                                $("#dtew_21").prop('checked', true);
                                                $("#sell_arb_21").prop('checked', true);
                                                $("#m_eth_to_sw2").prop('checked', true);
                                            }
                                            else if($(this).is(":not(:checked)")){
                                                // alert("Checkbox is unchecked.Ali");
                                            }
                                        });
                                        
                                    });
                                </script>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
          <?php }?>
    </div>
</div>    
</div>

<!--///////////////////////////////////////////////////////////////////// Modals ///////////////////////////////////////////////////////////////-->

    <div class="modal fade" id="qrImgModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Scan this code from your mobile GoogleAuthenticator App to activate 2 factor authentication </h3></p>
                        <div id="imageDiv" class="textAlignCenter"></div> 
                         <div class="col-md-12">
                            <input id="code" class="form-control" type="number" placeholder="Enter the scan code for verification.">
                            <br><br>
                            <button class="btn btn-success" onclick=sendCode()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="activate_Modal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Please Activate your 2FA First</h3></p>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="reinvestAbotConfirmation">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <p><h5>Are you sure? You will not be able to deactivate it untill <span id="newdate"></span></h5></p>
                        <br><br>
                            <button class="btn btn-success" onclick=confirmReinvest()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="activate_pkgModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <p><h5>Are you sure to activate your <span id="activate_pkgType"></span> Package? <span id="activate_pkgDes"></span></h5></p>
                        <br>
                        <button class="btn btn-success" onclick=activate_pkgConfirm()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="ErrModalGeneric" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-danger">Error</h1>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h3 id="ErrTextGeneric"></h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="AlreadyActKeyModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Your 2FA Authentication is Already Activated</h3></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="codeModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>To Deactivate 2 factor authentication please enter the code.</h3></p>
                         <div class="col-md-12">
                            <input id="codeDe" class="form-control" type="number" placeholder="Enter the current code to deactivate.">
                            <br><br>
                            <button class="btn btn-success" onclick=closeCode()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="save2faModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Enter your Google 2FA Pin to save the record.</h3></p>
                         <div class="col-md-12">
                            <input id="codeSave2Fa" class="form-control" type="number" placeholder="Enter the Google 2FA pin code to change your record.">
                            <input type="hidden" id="hiddenInput2faModal">
                            <br><br>
                            <button class="btn btn-success" onclick=saveCode2Fa()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="successModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <h3>Successfully Authenticated.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="successKeysModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <h3>Successfully Added.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="updatedKeysModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <h3>Your API Keys Are Invalid.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="failedModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <h3>Failed</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="deactivateModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <h3>Successfully Deactivated.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="autoBuy_Modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><b>Auto Buy </b></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h5>This will Activate your Auto Buy Trade</h5>
                    <form>
                        <div class="radioAutoRe">
                            <label>Set Quantity: </label>
                            <div class="setQuantityDiv">
                                <span>
                                    <label class="container1">50 ARB
                                      <input type="radio" name='radioAutoBuyQuan' value='50'  checked="checked">
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <span>
                                    <label class="container1">100 ARB
                                      <input type="radio" name='radioAutoBuyQuan' value='100' >
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <span>
                                    <label class="container1">150 ARB
                                      <input type="radio" name='radioAutoBuyQuan' value='150' >
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <span>
                                    <label class="container1">200 ARB
                                      <input type="radio" name='radioAutoBuyQuan' value='200' >
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <span>
                                    <label class="container1">250 ARB
                                      <input type="radio" name='radioAutoBuyQuan' value='250' >
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                            </div>
                        </div>
                    </form>
                    <form>
                        <div class="radioAutoRe">
                            <label>Set Time For Trade: </label>
                            <div class="setQuantityDiv">
                                <span>
                                    <label class="container1">After 20 minute
                                      <input type="radio" name='radioAutoBuy' value='20' checked="checked">
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <span>
                                    <label class="container1">After 40 minute
                                      <input type="radio" name='radioAutoBuy' value='40'>
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <span>
                                    <label class="container1">After 60 minutes 
                                      <input type="radio" name='radioAutoBuy' value='60' >
                                      <span class="checkmark"></span>
                                    </label>
                                </span>
                                <!--<input type='radio' name='radioAutoBuy' value='20' checked="checked"/>After 20 minutes </br>-->
                                <!--<input type='radio' name='radioAutoBuy' value='40' />After 20 minute </br>-->
                                <!--<input type='radio' name='radioAutoBuy' value='60' />After 60 minutes -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick=confirmAutoBuy()>Confirm</button>
                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="save2faAutoBuyModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Enter your Google 2FA Pin for Auto Buy.</h3></p>
                         <div class="col-md-12">
                            <input id="authenticationCodeAutoBuy" class="form-control" type="number" placeholder="Enter the Google 2FA pin code for Auto Buy.">
                            <br><br>
                            <button class="btn btn-success" onclick=save2FaAutoBuy()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="autoBuySucc">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Your Auto Buy Is Activate Now.</h3></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="deAutoBuy">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Enter your Google 2FA Pin for Deactivate your Auto Buy.</h3></p>
                         <div class="col-md-12">
                            <input id="deactiCodeAutoBuy" class="form-control" type="number" placeholder="Enter the Google 2FA pin code for Deactivate your Auto Buy.">
                            <br><br>
                            <button class="btn btn-success" onclick=deAutoBuy()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="termOfServicesModal">
        <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title text-danger">Warning</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <p>Are you sure? This will deactivate your account from Arbitraging Platform.</p>
              </div>
              <div class="modal-footer">
                <button class="btn btn-warning" onclick=uncheckTos()>Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
        
          </div>
    </div>
    
    <div class="modal" id="getSuppPinModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Support Pin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Your Pin: <b><span id="getSuppPinInput"></span></b></p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="activateVotingModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-danger">Activate Voting</h3>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you wish to activate voting feature? It will deduct 1 ARB from your system wallet for lifetime voting membership.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning" onclick=votingSubs()>Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="successVotingActivateModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <h3>Your voting feature is successfully activated.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="successVoteModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <h3 id="successVoteHeading"></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="errorModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title text-danger">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <span id="errorModalDiv"></span>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="acceptAccessReqModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Enter Pin.</h3></p>
                         <div class="col-md-12">
                            <input id="acceptAccessReqPin" class="form-control" type="number" placeholder="Enter the pin.">
                            <br>
                            <button class="btn btn-success" onclick=acceptAccessReqPin()>Confirm</button>
                            <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
        <!-- *********** -->
    <div class="modal fade" id="updated_Modal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Settings Updated.</h3></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deactivated_Modal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Settings Deactivated.</h3></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="settingsSaved_Modal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12 border">
                        <p><h3>Settings Saved successfully.</h3></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- *********** -->
    
    <div class="modal fade" id="add_onModal">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="font-size:12px">
                <div class="modal-body">
                    <div class="col-md-12">
                        <p><h5>Are you sure to activate your <span id="add_onModalType"></span> Add On?</h5></p>
                        <br>
                        <button class="btn btn-success" onclick=add_onModalConfirm()>Confirm</button>
                        <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<script>
    
    $("#userList").click(function(){
        $(".dropdown-menu.DmRight").toggleClass("show");
    });
    
    $( "#u_wallet" ).keyup(function() {
         var value = document.getElementById('u_wallet').value;
         if (value.length < 42) {
           $("#mewError").css("display", "block");
         }
         else
         {
             $("#mewError").css("display", "none");
         }
    });
    
    function handleChange(checkbox) 
    {
        // $('#errorModalDiv').html("Activation and deactivation of 2FA is currently disable. Please try again later.");
        // $('#errorModal').modal('show');
        var image;
        if(checkbox.checked == true)
        {
            $.get("<?php echo base_url(); ?>getQrImage", function( data ) {
                if(data == "Already activated")
                {
                  $('#AlreadyActKeyModal').modal('show');
                }
                else
                {
                    $("#imageDiv").html(data);
                    $('#qrImgModal').modal('show');
                }
            });
        }    
        else 
        {
          $('#codeModal').modal('show');
        }
    }
    
    function handleChangeTos() {
        $('#termOfServicesModal').modal('show');
    }
    
    function uncheckTos() {
         $.get("<?php echo base_url(); ?>block_login", function( data ) {
            if(data == "success")
            {
                location.reload();
            }
        });
    }
    
    function sendCode(){
        code = document.getElementById('code').value;
        $.post( "<?php echo base_url(); ?>activate2fa", {code:code})
          .done(function( data ) {
              if(data == "true"){
                  $('#qrImgModal').modal('hide');
                  $('#successModal').modal('show');
                  location.reload();
              } 
              else if(data == "false") {
                    $('#errorModalDiv').html("Incorrect Code.");
                    $('#errorModal').modal('show');
              }
          });
    }
    
    function closeCode(){
        code = document.getElementById('codeDe').value;
        $.post( "<?php echo base_url(); ?>deactive2fa", {code:code})
          .done(function( data ) {
              if(data == "true"){
                  $('#codeModal').modal('hide');
                  $('#deactivateModal').modal('show');
                  location.reload();
              }
          });
    }
    
    function saveCode2Fa(){
        $('#save2faModal').modal('hide');
        $('#accessAccountGoogleAuth').modal('hide');
        code = document.getElementById('codeSave2Fa').value;
        
        var googleAuthCheck = document.getElementById('hiddenInput2faModal').value;
        
        $.post( "<?php echo base_url(); ?>verify2fa", {code:code})
          .done(function( data ) {
          if(data == "true")
          {
            if(googleAuthCheck == "supportPin")
            {
                $('#hiddenInput2faModal').val('');
                $.get("<?php echo base_url(); ?>get_support_pin", function( data ) {
                    $('#getSuppPinModal').modal('show');
                    $('#getSuppPinInput').html(data);
                }); 
            }
            else if(googleAuthCheck == "accessAccount") {
                var accessAccountPin = $('#accessAccountPin').val();
                
                if(accessAccountPin.length == 6) {
                    $.post("<?php echo base_url(); ?>generate_request", {pin: accessAccountPin, user_id:userAccessId })
                    .done(function( data ) {
                        data = JSON.parse(data);
                        if(data.error == "1") {
                            $('#errorModal').modal('show');
                            $('#errorModalDiv').html(data.msg);
                        }
                        else {
                            location.reload();
                        }   
                    });
                } else {
                    alert("Pin Should be 6 digits.");
                }
            } 
            else {
                var walletad = $('#u_wallet').val();
                var email = $('#u_email').val();
                if(walletad.length < 42){
                    alert("Please Input Valid Wallet Address");
                }
                else {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo base_url(); ?>update_wallet",
                        data: { wallet : walletad , email : email}, // pass it as POST parameter
                         success: function(data){
                            if(data == "false")
                            {
                                alert("Email is Not Valid.");
                                location.reload();
                            }
                            else
                            {
                                alert("New Data Saved");
                                location.reload();
                            }
                        }
                    });
                      // alert('New Address Saved');
                }
            }    
          }
          else
          {
              alert("Please Enter the Correct 2FA Pin.");
          }
      });
    }
    
    function saveWallet(){
        if(<?php if(isset($allow_pin)){ echo $allow_pin; }else{ echo 0;}?> == 1)
        {
            $('#save2faModal').modal('show');
        }
        else
        {
            $('#activate_Modal').modal('show');
        }
    }
        
    function updatepwd(){
        var oldpass = $('#pre_password').val();
        var newpass = $('#new_password').val();
        var confpass = $('#new_password1').val();
        if(newpass != confpass){
            alert("Password not matching. ");
        }
        else {
            $.ajax({
                type: "POST",
                  url: "<?php echo base_url(); ?>update_pwd",
                  data: { pre_password : oldpass, new_password: newpass }, // pass it as POST parameter
                  success: function(data){
                      data = JSON.parse(data);
                    alert(data.result);
                    if(data.flag=='1') location.reload();
                 }
             });
           // alert('New Address Saved');
        }
    }
    
    $('#new_password, #new_password1').on('keyup', function () 
    {
        if($('#new_password').val() == $('#new_password1').val()) 
        {
            $('#message').html('Matching').css('color', 'green');
        }
        else 
        {
            $('#message').html('Not Matching').css('color', 'red');
        }    
    });
    
    function toggle_visibility(id) {                                            //Exchange Keys Toggle Button
        var e = document.getElementById(id);
        if(e.style.display == 'block')
            e.style.display = 'none';
        else
            e.style.display = 'block';
    }
    


var ex;
var apikey;
var seckey;
  
var address_array = new Array();


// loops array push data
var btcM_address , btcM_coin_tag, btcM_coin_Label, houbi_address; 
// var btcM_coin, houbi_coin;    
    function saveExchangeKeys(id){                                                 //Saving Exchange Keys                
         // for kraken addresses
         $('#loadingmessage').show();
         ex = id;
        if(ex == "kraken"){
            
            if($('#apiKeyKraken').val() == ''){
                alert("Please add Kraken Api Key");
                return;
            }
            if($('#secKeyKraken').val() == ''){
                alert("Please add Kraken Secret Key");
                return;
            }
            apikey = $('#apiKeyKraken').val();
            seckey = $('#secKeyKraken').val();
            var kraken_addresses = new Object();
            if($("#kraken_xrp_address").val() != ''){
                if($("#kraken_xrp_tag").val() == ''){
                    alert("Kraken XRP tag required");
                    return;
                }else{
                    kraken_addresses.XRP =  {"address": $("#kraken_xrp_address").val(), "tag": $("#kraken_xrp_tag").val()};
                }
            }
            if($("#kraken_xlm_address").val() != ''){
                if($("#kraken_xlm_tag").val() == ''){
                    alert("Kraken XLM tag required");
                    return;
                }else{
                    kraken_addresses.XLM =  {"address": $("#kraken_xlm_address").val(), "tag": $("#kraken_xlm_tag").val()};
                }
            }
            if($("#kraken_xmr_address").val() != ''){
                // if($("#kraken_xmr_tag").val() == ''){
                //     alert("Kraken XMR tag required");
                //     return;
                // }else{
                    kraken_addresses.XMR =  {"address": $("#kraken_xmr_address").val(), "tag": ""};
                //}
            }
            if($("#kraken_eos_address").val() != ''){
                if($("#kraken_eos_tag").val() == ''){
                    alert("Kraken EOS tag required");
                    return;
                }else{
                    kraken_addresses.EOS =  {"address": $("#kraken_eos_address").val(), "tag": $("#kraken_eos_tag").val()};
                }
            }
            
            postData(ex, apikey, seckey, kraken_addresses);
            return;
        }
        else if(ex == "bithumb"){
            
            if($('#apiKeyBithumb').val() == ''){
                alert("Please add Bithumb Api Key");
                return;
            }
            if($('#secKeyBithumb').val() == ''){
                alert("Please add Bithumb Secret Key");
                return;
            }
            apikey = $('#apiKeyBithumb').val();
            seckey = $('#secKeyBithumb').val();
            var bithumb_addresses = new Object();
            if($("#bithumb_xrp_address").val() != ''){
                if($("#bithumb_xrp_tag").val() == ''){
                    alert("Bithumb XRP tag required");
                    return;
                }else{
                    bithumb_addresses.XRP =  {"address": $("#bithumb_xrp_address").val(), "tag": $("#bithumb_xrp_tag").val()};
                }
            }
            if($("#bithumb_xmr_address").val() != ''){
                // if($("#bithumb_xmr_tag").val() == ''){
                //     alert("Bithumb XMR tag required");
                //     return;
                // }else{
                    bithumb_addresses.XMR =  {"address": $("#bithumb_xmr_address").val(), "tag": ""};
                //}
            }
            if($("#bithumb_eos_address").val() != ''){
                if($("#bithumb_eos_tag").val() == ''){
                    alert("Bithumb EOS tag required");
                    return;
                }else{
                    bithumb_addresses.EOS =  {"address": $("#bithumb_eos_address").val(), "tag": $("#bithumb_eos_tag").val()};
                }
            }
            postData(ex, apikey, seckey, bithumb_addresses);
            return;
        }
        
        else if(ex == "btcmarkets"){
            if($('#apiKeyBtcm').val() == ''){
                alert("Please add BTC Market Api Key");
                return;
            }
            if($('#secKeyBtcm').val() == ''){
                alert("Please add Bithumb Secret Key");
                return;
            }
            apikey = $('#apiKeyBtcm').val();
            seckey = $('#secKeyBtcm').val();
            var btcmarkets_addresses = new Object();
            if($("#btcmarkets_xrp_address").val() != ''){
                if($("#btcmarkets_xrp_tag").val() == ''){
                    alert("Btcmarket XRP tag required");
                    return;
                }else{
                    btcmarkets_addresses.XRP =  {"address": $("#btcmarkets_xrp_address").val(), "tag": $("#btcmarkets_xrp_tag").val()};
                }
            }
            if($("#btcmarkets_btc_address").val() != ''){
                    btcmarkets_addresses.BTC =  {"address": $("#btcmarkets_btc_address").val(), "tag": ''};
            }
            
            if($("#btcmarkets_eth_address").val() != ''){
                    btcmarkets_addresses.ETH =  {"address": $("#btcmarkets_eth_address").val(), "tag": ''};
            }
            if($("#btcmarkets_ltc_address").val() != ''){
                    btcmarkets_addresses.LTC =  {"address": $("#btcmarkets_ltc_address").val(), "tag": ''};
            }
            if($("#btcmarkets_bch_address").val() != ''){
                    btcmarkets_addresses.BCH =  {"address": $("#btcmarkets_bch_address").val(), "tag": ''};
            }
            postData(ex, apikey, seckey, btcmarkets_addresses);
            return;
        }
        else if(ex == "poloniex"){
            if($('#apiKeyPolo').val() == ''){
                alert("Please add Poloniex Api Key");
                return;
            }
            if($('#secKeyPolo').val() == ''){
                alert("Please add Poloniex Secret Key");
                return;
            }
            apikey = $('#apiKeyPolo').val();
            seckey = $('#secKeyPolo').val();
            var poloniex_addresses = new Object();
            if($("#poloniex_xrp_address").val() != ''){
                // if($("#poloniex_xrp_tag").val() == ''){
                //     alert("poloniex XRP tag required");
                //     return;
                // }else{
                    poloniex_addresses.XRP =  {"address": $("#poloniex_xrp_address").val(), "tag":''};
                // }
            }
            if($("#poloniex_xlm_address").val() != ''){
                if($("#poloniex_xlm_tag").val() == ''){
                    alert("poloniex XLM (STR) tag required");
                    return;
                }else{
                    poloniex_addresses.XLM =  {"address": $("#poloniex_xlm_address").val(), "tag": $("#poloniex_xlm_tag").val()};
                }
            }
            if($("#poloniex_eos_address").val() != ''){
                if($("#poloniex_eos_tag").val() == ''){
                    alert("poloniex EOS tag required");
                    return;
                }else{
                    poloniex_addresses.EOS =  {"address": $("#poloniex_eos_address").val(), "tag": $("#poloniex_eos_tag").val()};
                }
            }
            
            postData(ex, apikey, seckey, poloniex_addresses);
            return;
        }
        else if(ex == "binance"){
            if($('#apiKeyBin').val() == ''){
                alert("Please add Binance Api Key");
                return;
            }
            if($('#secKeyBin').val() == ''){
                alert("Please add Binance Secret Key");
                return;
            }
            apikey = $('#apiKeyBin').val();
            seckey = $('#secKeyBin').val();
            var binance_addresses = new Object();
            if($("#binance_xrp_address").val() != ''){
                if($("#binance_xrp_tag").val() == ''){
                    alert("Binance XRP tag required");
                    return;
                }else{
                    binance_addresses.XRP =  {"address": $("#binance_xrp_address").val(), "tag": $("#binance_xrp_tag").val()};
                }
            }
            if($("#binance_xlm_address").val() != ''){
                if($("#binance_xlm_tag").val() == ''){
                    alert("Binance XLM tag required");
                    return;
                }else{
                    binance_addresses.XLM =  {"address": $("#binance_xlm_address").val(), "tag": $("#binance_xlm_tag").val()};
                }
            }
            if($("#binance_xmr_address").val() != ''){
                if($("#binance_xmr_tag").val() == ''){
                    alert("Binance XMR tag required");
                    return;
                }else{
                    binance_addresses.XMR =  {"address": $("#binance_xmr_address").val(), "tag": $("#binance_xmr_tag").val()};
                }
            }
            if($("#binance_eos_address").val() != ''){
                if($("#binance_eos_tag").val() == ''){
                    alert("Binance EOS tag required");
                    return;
                }else{
                    binance_addresses.EOS =  {"address": $("#binance_eos_address").val(), "tag": $("#binance_eos_tag").val()};
                }
            }
            postData(ex, apikey, seckey, binance_addresses);
            return;
        }
        else if(ex == "bittrex"){
            if($('#apiKeyBit').val() == ''){
                alert("Please add Bittrex Api Key");
                return;
            }
            if($('#secKeyBit').val() == ''){
                alert("Please add Bittrex Secret Key");
                return;
            }
            apikey = $('#apiKeyBit').val();
            seckey = $('#secKeyBit').val();
            var bittrex_addresses = new Object();
            if($("#bittrex_xrp_address").val() != ''){
                if($("#bittrex_xrp_tag").val() == ''){
                    alert("Bittrex XRP tag required");
                    return;
                }else{
                    bittrex_addresses.XRP =  {"address": $("#bittrex_xrp_address").val(), "tag": $("#bittrex_xrp_tag").val()};
                }
            }
            if($("#bittrex_xmr_address").val() != ''){
                if($("#bittrex_xmr_tag").val() == ''){
                    alert("Bittrex XMR tag required");
                    return;
                }else{
                    bittrex_addresses.XMR =  {"address": $("#bittrex_xmr_address").val(), "tag": $("#bittrex_xmr_tag").val()};
                }
            }
            postData(ex, apikey, seckey, bittrex_addresses);
            return;
        }
        else if(ex == "hitbtc"){
            if($('#apiKeyHit').val() == ''){
                alert("Please add Hitbtc Api Key");
                return;
            }
            if($('#secKeyHit').val() == ''){
                alert("Please add Hitbtc Secret Key");
                return;
            }
            apikey = $('#apiKeyHit').val();
            seckey = $('#secKeyHit').val();
            var hitbtc_addresses = new Object();
            if($("#hitbtc_xrp_address").val() != ''){
                if($("#hitbtc_xrp_tag").val() == ''){
                    alert("Hitbtc XRP tag required");
                    return;
                }else{
                    hitbtc_addresses.XRP =  {"address": $("#hitbtc_xrp_address").val(), "tag": $("#hitbtc_xrp_tag").val()};
                }
            }
            if($("#hitbtc_xlm_address").val() != ''){
                if($("#hitbtc_xlm_tag").val() == ''){
                    alert("Hitbtc XLM tag required");
                    return;
                }else{
                    hitbtc_addresses.XLM =  {"address": $("#hitbtc_xlm_address").val(), "tag": $("#hitbtc_xlm_tag").val()};
                }
            }
            if($("#hitbtc_xmr_address").val() != ''){
                // if($("#hitbtc_xmr_address").val() == ''){
                //     alert("hitbtc XMR address required");
                //     return;
                // }
                //else{
                     hitbtc_addresses.XMR =  {"address": $("#hitbtc_xmr_address").val(), "tag": ""};
                // }
            }
            if($("#hitbtc_eos_address").val() != ''){
                if($("#hitbtc_eos_tag").val() == ''){
                    alert("Hitbtc EOS tag required");
                    return;
                }else{
                    hitbtc_addresses.EOS =  {"address": $("#hitbtc_eos_address").val(), "tag": $("#hitbtc_eos_tag").val()};
                }
            }
            postData(ex, apikey, seckey, hitbtc_addresses);
            return;
        }
        else if(ex == "huobi"){
            if($('#apiKeyHuobi').val() == ''){
                alert("Please add Huobi Market Api Key");
                return;
            }
            if($('#secKeyHuobi').val() == ''){
                alert("Please add Huobi Secret Key");
                return;
            }
            apikey = $('#apiKeyHuobi').val();
            seckey = $('#secKeyHuobi').val();
            var huobi_addresses = new Object();
            if($("#huobi_xrp_address").val() != ''){
                if($("#huobi_xrp_tag").val() == ''){
                    alert("Huobi XRP tag required");
                    return;
                }else{
                    huobi_addresses.XRP =  {"address": $("#huobi_xrp_address").val(), "tag": $("#huobi_xrp_tag").val()};
                }
            }
            if($("#huobi_eos_address").val() != ''){
                if($("#huobi_eos_tag").val() == ''){
                    alert("Huobi EOS tag required");
                    return;
                }else{
                    huobi_addresses.EOS =  {"address": $("#huobi_eos_address").val(), "tag": $("#huobi_eos_tag").val()};
                }
            }
            if($("#huobi_btc_address").val() != ''){
                    huobi_addresses.BTC =  {"address": $("#huobi_btc_address").val(), "tag": ''};
            }
            if($("#huobi_eth_address").val() != ''){
                    huobi_addresses.ETH =  {"address": $("#huobi_eth_address").val(), "tag": ''};
            }
            if($("#huobi_ltc_address").val() != ''){
                    huobi_addresses.LTC =  {"address": $("#huobi_ltc_address").val(), "tag": ''};
            }
            if($("#huobi_bch_address").val() != ''){
                    huobi_addresses.BCH =  {"address": $("#huobi_bch_address").val(), "tag": ''};
            }
            
            postData(ex, apikey, seckey, huobi_addresses);
            return;
        }
        else if(ex == "livecoin"){
            if($('#apiKeyLc').val() == ''){
                alert("Please add Livecoin Market Api Key");
                return;
            }
            if($('#secKeyLc').val() == ''){
                alert("Please add Livecoin Secret Key");
                return;
            }
            apikey = $('#apiKeyLc').val();
            seckey = $('#secKeyLc').val();
            var livecoin_addresses = new Object();
            if($("#liveCoin_eos_address").val() != ''){
                if($("#liveCoin_eos_tag").val() == ''){
                    alert("Livecoin EOS tag required");
                    return;
                }else{
                    livecoin_addresses.EOS =  {"address": $("#liveCoin_eos_address").val(), "tag": $("#liveCoin_eos_tag").val()};
                }
            }
            
            postData(ex, apikey, seckey, livecoin_addresses);
            return;
        }
        else if(ex == "exmo"){
            if($('#apiKeyExmo').val() == ''){
                alert("Please add Exmo Api Key");
                return;
            }
            if($('#secKeyExmo').val() == ''){
                alert("Please add Exmo Secret Key");
                return;
            }
            apikey = $('#apiKeyExmo').val();
            seckey = $('#secKeyExmo').val();
            var exmo_addresses = new Object();
            if($("#exmo_xrp_address").val() != ''){
                if($("#exmo_xrp_tag").val() == ''){
                    alert("Exmo XRP tag required");
                    return;
                }else{
                    exmo_addresses.XRP =  {"address": $("#exmo_xrp_address").val(), "tag": $("#exmo_xrp_tag").val()};
                }
            }
            if($("#exmo_xlm_address").val() != ''){
                if($("#exmo_xlm_tag").val() == ''){
                    alert("Exmo XLM tag required");
                    return;
                }else{
                    exmo_addresses.XLM =  {"address": $("#exmo_xlm_address").val(), "tag": $("#exmo_xlm_tag").val()};
                }
            }
            if($("#exmo_xmr_address").val() != ''){
                // if($("#exmo_xmr_tag").val() == ''){
                //     alert("Exmo XMR tag required");
                //     return;
                // }else{
                    exmo_addresses.XMR =  {"address": $("#exmo_xmr_address").val(), "tag": ""};
                //}
            }
            postData(ex, apikey, seckey, exmo_addresses);
            return;
        }
        
    }
    
//Post Data function
 function postData(ex1, apikey1, seckey1, arr){ $.post( "<?php echo base_url(); ?>mbot_cred", {exchange:ex1, apikey:apikey1, seckey:seckey1, coins:arr})
      .done(function( data ) {
        $('#loadingmessage').hide();
        if(data == "success")
        {
            $('#successKeysModal').modal('show');
            setInterval(function(){ $('#successKeysModal').modal('hide'); }, 3000);
        }
        else if(data == "Api Key not valid")
        {
            $('#updatedKeysModal').modal('show');
            setInterval(function(){ $('#updatedKeysModal').modal('hide'); }, 3000);
        }
        else
        {
            // $('#successKeysModal').modal('show');
            
            $('#failedModal').modal('show');
            setInterval(function(){ $('#failedModal').modal('hide'); }, 3000);
            // location.reload();
        }
      });    
 }
 
    ////// Support Pin     ////////////////////////
     
    if(<?php echo $support_pin_status; ?> == 0)
    {
        $('#pinSupportDiv').css('display', 'block');
        $('#pinSupportCheck').css('display', 'none');
    }
    else
    {
        $('#pinSupportDiv').css('display', 'none');
        $('#pinSupportCheck').css('display', 'block');
    }
     
    function suppPinSave() {
        var suppPin = $('#supp_pin').val();
        var confirmSuppPin = $('#supp_pin_confirm').val();
        
        if(suppPin != confirmSuppPin)
        {
            alert("Pin Don't Match");
        }
        else if(suppPin.length != 6)
        {
            alert("Pin Must be 6 characters");
        }
        else
        {
            $.post( "<?php echo base_url(); ?>add_support_pin", {support_pin:suppPin})
            .done(function( data ) {
                if(data == "success")
                {
                    $('#successKeysModal').modal('show');
                    location.reload();
                }
            });    
        }
         
    } 
    
    function suppPinShow()
    {
        if(<?php if(isset($allow_pin)){ echo $allow_pin; }else{ echo 0;}?> == 1)
        {
            $('#save2faModal').modal('show');
            $('#hiddenInput2faModal').val("supportPin");
        }
        else
        {
            $('#activate_Modal').modal('show');
        }
    }
    
    ///////////////   Packages   ////////////////////////
    var activate_pkgVal = 0;
    
    function activate_pkg(name){
        activate_pkgVal = name;
        if(name == "Pro")
        {
            $('#activate_pkgType').html(activate_pkgVal);
            $('#activate_pkgDes').html("This will deduct 500$ in ARB from your system wallet.");
            $('#activate_pkgModal').modal('show');
        }
        $('#activate_pkgType').html(activate_pkgVal);
        $('#activate_pkgModal').modal('show');
    }
    
    function activate_pkgConfirm(){
        $('#activate_pkgModal').modal('hide');
        if(activate_pkgVal == 0)
        {
            alert("Please Try again.");
        }
        else
        {
            $.post( "<?php echo base_url(); ?>change_package", {packagee:activate_pkgVal})
            .done(function( data ) {
                
                var myDataSendVote = JSON.parse(data);
                if(myDataSendVote.success == "1")
                {
                    $('#successVoteHeading').html(myDataSendVote.msg);
                    $('#successVoteModal').modal('show');
                    setInterval(function(){ location.reload(); }, 2000);
                }
                else if(myDataSendVote.error)
                {
                    $('#ErrTextGeneric').html(myDataSendVote.msg);
                    $('#ErrModalGeneric').modal('show');
                }
            });  
        }    
    }
    
     ////////////////////////////////////   Voting   //////////////////////////////////////////////////
    if(<?php echo $voting_status;?> == 0)
    {
        $('#votingSubscriptionBtn').html("Activate Voting");
    }
    else
    {
        $('#votingSubscriptionBtn').html("Voting Activated");
        $('#votingSubscriptionBtn').prop('disabled', true);
    }
    
    function votingSubs(){
        $('#activateVotingModal').modal('hide');
        
        $.get("<?php echo base_url(); ?>register_for_vote", function( data ) {
            var myDataVoting = JSON.parse(data);
            if(myDataVoting.success == "1")
            {
                $('#votingSubscriptionBtn').html("Activated");
                $('#votingSubscriptionBtn').prop('disabled', true);
                
                $('#successVotingActivateModal').modal('show');
                setInterval(function(){ location.reload(); }, 3000);
            } 
            else if(myDataVoting.error == "1")
            {
                $('#errorModal').modal('show');
                $('#errorModalDiv').html(myDataVoting.msg);
                setInterval(function(){ $('#errorModal').modal('hide'); $('.modal-backdrop.fade').removeClass('show'); }, 3000);
            }
            
        });
    }
    
    function cast_vote(id){
        var test_vote = $("input[name='vote_"+id+"']:checked").val();
        if(test_vote == null){
            $('#errorModal').modal('show');
            $('#errorModalDiv').html('Please select an option');
            return;
        }
        var user_option_text = false;
        
        if(test_vote == 'other'){
            user_option_text = $("input[name='user_option_"+id+"']").val();
            if(user_option_text == ''){
                $('#errorModal').modal('show');
                $('#errorModalDiv').html('Please enter some text');
                return;
            }
        }
        $.post( "<?php echo base_url(); ?>cast_vote", {topic_id:id, option:test_vote, user_option:user_option_text})
            .done(function( data ) {
                
                var myDataSendVote = JSON.parse(data);
                if(myDataSendVote.success == "1")
                {
                    $('#successVoteHeading').html(myDataSendVote.msg);
                    $('#successVoteModal').modal('show');
                    setInterval(function(){ location.reload(); }, 2000);
                }
                else if(myDataSendVote.error)
                {
                    $('#errorModal').modal('show');
                    $('#errorModalDiv').html(myDataSendVote.msg);
                }
            });  
    }
    
    function suggestionInput(id) {
         var mySuggestionData = [];
        $.post( "<?php echo base_url(); ?>suggestion_list_voting", {ticket_id:id})
        .done(function( data ) {
            mySuggestionData = JSON.parse(data);

            function autocomplete(inp, arr) {
              /*the autocomplete function takes two arguments,
              the text field element and an array of possible autocompleted values:*/
              var currentFocus;
              /*execute a function when someone writes in the text field:*/
              inp.addEventListener("input", function(e) {
                  var a, b, i, val = this.value;
                  /*close any already open lists of autocompleted values*/
                  closeAllLists();
                  if (!val) { return false;}
                  currentFocus = -1;
                  /*create a DIV element that will contain the items (values):*/
                  a = document.createElement("DIV");
                  a.setAttribute("id", this.id + "autocomplete-list");
                  a.setAttribute("class", "autocomplete-items");
                  /*append the DIV element as a child of the autocomplete container:*/
                  this.parentNode.appendChild(a);
                  /*for each item in the array...*/
                  for (i = 0; i < arr.length; i++) {
                    /*check if the item starts with the same letters as the text field value:*/
                    if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                      /*create a DIV element for each matching element:*/
                      b = document.createElement("DIV");
                      /*make the matching letters bold:*/
                      b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                      b.innerHTML += arr[i].substr(val.length);
                      /*insert a input field that will hold the current array item's value:*/
                      b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                      /*execute a function when someone clicks on the item value (DIV element):*/
                      b.addEventListener("click", function(e) {
                          /*insert the value for the autocomplete text field:*/
                          inp.value = this.getElementsByTagName("input")[0].value;
                          /*close the list of autocompleted values,
                          (or any other open lists of autocompleted values:*/
                          closeAllLists();
                      });
                      a.appendChild(b);
                    }
                  }
              });
              /*execute a function presses a key on the keyboard:*/
              inp.addEventListener("keydown", function(e) {
                  var x = document.getElementById(this.id + "autocomplete-list");
                  if (x) x = x.getElementsByTagName("div");
                  if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                  } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                  } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                      /*and simulate a click on the "active" item:*/
                      if (x) x[currentFocus].click();
                    }
                  }
              });
              function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                x[currentFocus].classList.add("autocomplete-active");
              }
              function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                  x[i].classList.remove("autocomplete-active");
                }
              }
              function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                  if (elmnt != x[i] && elmnt != inp) {
                    x[i].parentNode.removeChild(x[i]);
                  }
                }
              }
              /*execute a function when someone clicks in the document:*/
              document.addEventListener("click", function (e) {
                  closeAllLists(e.target);
              });
            }
            autocomplete(document.getElementById("myInputSuggestion_"+id), mySuggestionData);
        });     
    }
    
    //////////////////////////   Access Account   ////////////////////////////////
    
    var accessGivenUsers = <?php echo $access_given_by_users; ?>;

    if('<?php echo $access_given_to; ?>' == 0) {
        $("#AC_First").css("display","block");
    } 
    else {
        $("#AC_MyAccountAssess").css("display","block");
    }
    
    if(accessGivenUsers != 0 || accessGivenUsers != "0") {
        var td = "";
        
        $("#AC_AssessOfOthersAccount").css("display","block");
        $.each(accessGivenUsers, function(i)
        {
            td += "<tr><td>" + accessGivenUsers[i].u_email + "</td><td>" + accessGivenUsers[i].u_username + "</td><td><button class='btn btn-success' onclick='acceptAccessRequ("+accessGivenUsers[i].u_id+")'>Accept</button></td></tr>";
        });
        $('#AssessOfOthersTable').html(td);
    }
    
    function sendAccessEmail() {
        var emailAccessAcc = $('#accessAccountEmail').val();
        
        if( emailAccessAcc.length > 0 && emailAccessAcc) {
            $("#loader_access").css("display","inline-block");
            $.post("<?php echo base_url(); ?>find_user", {email: emailAccessAcc })
            .done(function( data ) {
                data = JSON.parse(data);
                $("#loader_access").css("display","none");
                
                if(data.error == "1") {
                    $('#errorModal').modal('show');
                    $('#errorModalDiv').html(data.msg);
                }
                else {
                    userAccessId = data.u_id;
                    $("#AC_First").css("display","none");
                    $("#AC_Second").css("display","block");
                }   
            });
        }
        else {
            alert("Enter a Email address.");
        }
    }
    
    
    var acceptAccessReqId = 0;
    function acceptAccessRequ(id) {
        acceptAccessReqId = id;
        $('#acceptAccessReqModal').modal('show');
    }
    
    function acceptAccessReqPin() {
        var selectedAccessReqPin = $('#acceptAccessReqPin').val();
        $.post("<?php echo base_url(); ?>verify_access", {accessed_user_id: acceptAccessReqId, pin: selectedAccessReqPin })
        .done(function( data ) {
            data = JSON.parse(data);
            $("#loader_access").css("display","none");
            
            if(data.error == "1") {
                $('#acceptAccessReqModal').modal('hide');
                $('#errorModal').modal('show');
                $('#errorModalDiv').html(data.msg);
            }
            else {
                setInterval(function(){ $('#successKeysModal').modal('hide'); }, 3000);
                location.reload();
            }   
        });
    }
    
    function cancelAccessAcc() {
        $.get("<?php echo base_url(); ?>deny_request", function( data ) {
            data = JSON.parse(data);
            if(data.error == "1") {
                $('#errorModal').modal('show');
                $('#errorModalDiv').html(data.msg);
            }
            else if (data.success == "1"){
                $('#successVoteModal').modal('show');
                $('#successVoteHeading').html(data.msg);
                setInterval(function(){ location.reload(); }, 3000);
            }   
        });
    }
    
    function checkGoogleAuth(access) {
        
        $('#hiddenInput2faModal').val(access);
        
        if(<?php if(isset($allow_pin)){ echo $allow_pin; }else{ echo 0;}?> == 1)
        {
            $('#save2faModal').modal('show');
        }
        else
        {
            $('#activate_Modal').modal('show');
        }
    }
    
    //////////////////////////   Pro +   ////////////////////////////////

        $( document ).ready(function() {
        // if( $("#automate_abote_active_value").val() > 0 ){
        //     $("#noteSpan").css("display","inline-block");
        // }
        
        // alert( );
        // $("#investmentPercentage").val()
        if( ($("#automate_abote_active_value").val() > 0) && ($("#automate_abote_active_arb").val() > 0)){
            $("#arb_value_p").text($("#automate_abote_active_value").val());
            $("#dollar_p").text($("#automate_abote_active_arb").val());
            
        }else{$("#noteSpan").css("display","none");}
        
        if( ($("#investmentPercentage").find(":selected").text() > 0) && ($("#dollarValue").val() > 0)){
            $("#arb_value_p2").text($("#investmentPercentage").find(":selected").text());
            $("#dollar_p2").text($("#dollarValue").val());
            
        }else{
            $("#noteSpan2").css("display","none");
        }
        
        $("#automate_abote_active_value").keyup(function(){
             
            // if( $("#noteSpan").css("display","none") ){
            //     $(this).css("display","inline-block");
            // } 
                  $("#arb_value_p").text($("#automate_abote_active_value").val());
                  $("#dollar_p").text($("#automate_abote_active_arb").val());
        });
        $("#automate_abote_active_arb").keyup(function(){
             
            // if( $("#noteSpan").css("display","none") ){
            //     $(this).css("display","inline-block");
            // } 
                  $("#arb_value_p").text($("#automate_abote_active_value").val());
                  $("#dollar_p").text($("#automate_abote_active_arb").val());
        });
        
        $("#dollarValue").keyup(function(){
             
            // if( $("#noteSpan").css("display","none") ){
            //     $(this).css("display","inline-block");
            // } 
                  $("#arb_value_p2").text($("#investmentPercentage").find(":selected").text());
                  $("#dollar_p2").text($("#dollarValue").val());
        });
        
        $("#investmentPercentage").change(function(){
             
            // if( $("#noteSpan").css("display","none") ){
            //     $(this).css("display","inline-block");
            // } 
                  $("#arb_value_p2").text($("#investmentPercentage").find(":selected").text());
                  $("#dollar_p2").text($("#dollarValue").val());
        });
          
    });
    
    ///////////////   Add Ons   ////////////////////////
    var addOnActivateVal = 0;
    
    function addOnActivate(name){
        addOnActivateVal = name;
        $('#add_onModalType').html(addOnActivateVal);
        $('#add_onModal').modal('show');
    }
    
    function add_onModalConfirm(){
        $('#add_onModal').modal('hide');
        if(addOnActivateVal == 0)
        {
            alert("Please Try again.");
        }
        else
        {
            $.post( "<?php echo base_url(); ?>activate_add_on", {add_on_name:addOnActivateVal})
            .done(function( data ) {
                
                var myDataSendVote = JSON.parse(data);
                if(myDataSendVote.success == "1")
                {
                    $('#successVoteHeading').html(myDataSendVote.msg);
                    $('#successVoteModal').modal('show');
                    setInterval(function(){ location.reload(); }, 2000);
                }
                else if(myDataSendVote.error)
                {
                    alert(myDataSendVote.msg);
                }
            });  
        }    
    }
    
    function votingNotiCheck() {
        $.get("<?php echo base_url();?>voting_seen", function( data ) {});
        $('#votingNotiDiv').css('display', 'none');
    }
    
</script>