<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//$route['showmajaz'] = 'register/majaz';
$route['testing_1'] = 'test/testing_1';
// $route['test_code'] = 'amount/test_code';
//$route['default_controller'] = 'admin/uncon';
$route['enc_all'] = 'test/enc_all';
$route['shift_data'] = 'test/shift_data';

$route['send_abot_price'] = 'amount/send_abot_price';

$route['default_controller'] = 'register/login';
$route['login'] = 'register/login';
$route['update2fa'] = 'register/update2fa';
$route['pin'] = 'register/loginpin';
$route['update_pwd'] = 'register/update_pwd';
$route['logout'] = 'register/logout';
$route['user'] = 'admin/account';
$route['auth'] = 'register/auth';
$route['authpin'] = 'register/authpin';
$route['forgot'] = 'register/forgot';
$route['resend_email_verification'] = 'register/resend_email_verification';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// auto buy routes

$route['activate_auto_buy'] = 'register/activate_auto_buy';
$route['deactivate_auto_buy'] = 'register/deactivate_auto_buy';
$route['auto_order_cron'] = 'exchange/auto_order_cron';

$route['activate_auto_sell'] = 'register/activate_auto_sell';
$route['deactivate_auto_sell'] = 'register/deactivate_auto_sell';
$route['auto_order_cron_sell'] = 'exchange/auto_order_cron_sell';


$route['generate_ticket'] = 'support/generate_ticket';
$route['support_response'] = 'support/support_response';
$route['query_data'] = 'support/query_data';
$route['complete_chat'] = 'support/complete_chat';
$route['all_tickets'] = 'support/all_tickets';
$route['support_cat'] = 'support/support_cat';

$route['msg_count'] = 'support/msg_count';

$route['call_support'] = 'admin/call_support';
$route['update_wallet'] = 'admin/update_user_wallet';
$route['userCount'] = 'admin/userCount';


$route['verification'] = 'register/verification';


$route['sendWithdraw'] = 'amount/sendWithdraw';
// $route['sendWithdraw_external'] = 'amount/sendWithdraw_external';

$route['deposit'] = 'amount/deposit';
$route['getbalance'] = 'amount/getbalance';

$route['change_package'] = 'amount/change_package';
$route['activate_add_on'] = 'amount/activate_add_on';
$route['add_on_cron'] = 'amount/add_on_cron';

// ---- Cron Function Routes ----//
$route['profit_abot_cc'] = 'amount/profit_abot_cc';
$route['ethtransferCron'] = 'amount/ethtransferCron';
$route['transferTokens'] = 'amount/transferTokens';

$route['delsession_c'] = 'amount/delsession_c';

// ---- Cron Function Routes End----//


$route['getKraken'] = 'amount/getKraken';

// ---- Exchange Function Routes ----//
$route['auto_order'] = 'exchange/auto_order';
$route['saveOrder'] = 'exchange_v2/saveOrder';
$route['cancel_less_then_onedollor_orders'] = 'exchange_v2/cancel_less_then_onedollor_orders';
$route['orderBook'] = 'exchange/orderBook';
$route['close_order'] = 'exchange/close_order_by_id';
$route['exchange_wallet_balance'] = 'exchange/exchange_wallet_balance';
$route['arb_price_stats'] = 'exchange/arb_price_stats';
$route['closeOrderAuto'] = 'exchange/closeOrderAuto';
$route['all_user_orders'] = 'exchange/all_user_orders';

$route['eth_dollor_value'] = 'exchange/eth_dollor_value';
$route['update_coinexchange_value'] = 'wallet/update_coinexchange_value';

// $route['proplusreq'] = 'exchange/proplusreq';


//---- Wallet Function routs ----//

$route['eth_payout_status'] = 'wallet/eth_payout_status';

$route['add_wallet'] = 'wallet/add_system_wallet';
$route['system_to_abot'] = 'wallet/system_to_abot';
$route['system_to_exchange'] = 'wallet/system_to_exchange'; 
$route['system_to_mbot'] = 'wallet/system_to_mbot';
$route['abot_active_to_Wallet'] = 'wallet/abot_active_to_systemWallet';
$route['abot_earned_to_Wallet'] = 'wallet/abot_earned_to_systemWallet';
$route['admin_to_wallet'] = 'wallet/admin_to_systemwallet';
$route['exchange_to_wallet'] = 'wallet/exchange_to_wallet';

$route['system_arb_to_gas'] = 'wallet/system_arb_to_gas';
// $route['send_eth_to_gas'] = 'wallet/system_eth_to_gas'; (live)
$route['exEarned_to_system_wallet'] = 'wallet/exEarned_to_system_wallet';
$route['transfer_to_vault'] = 'wallet/abot_earned_to_vault';
$route['abot_active_to_stop_abot_wallet'] = 'wallet/abot_active_to_stop_abot_wallet';
$route['stop_abot_wallet_to_abot'] = 'wallet/stop_abot_wallet_to_abot';

$route['new_system_eth_to_gas'] = 'wallet/new_system_eth_to_gas';
$route['system_eth_to_gas_choise'] = 'wallet/system_eth_to_gas_choise';

$route['abot_reinvest'] = 'wallet/abot_reinvest';

$route['auto_reinvest'] = 'wallet/auto_reinvest';
$route['activate_in_wallet'] = 'amount/activate_in_wallet';

// $route['external_to_abot'] = 'wallet/external_to_abot';

// $route['stop_abot_wallet_to_external_wallet'] = 'wallet/stop_abot_wallet_to_external_wallet';
$route['eth_earned_to_systemWallet'] = 'wallet/eth_earned_to_systemWallet';


//--------Deposit ----//
$route['get_deposit_address'] = 'amount/get_deposit_address';
$route['free_depositAddresses'] = 'amount/cron_free_depositAddresses';

//----------withdraw------//
$route['revertwithdraw'] = 'amount/revertWithdraw';
$route['manual_withdraw']       = 'amount/manual_withdraw';
$route['bulk_manual_withdraw']  = 'amount/bulk_manual_withdraw';
$route['get_support_pin'] = 'amount/get_support_pin';


//------Test---------//
//$route['test'] = 'test/index';
$route['get_address_trans_c'] = 'amount/get_address_trans_c';
//$route['test2'] = 'test/test2';
//$route['test3'] = 'test/test3';
$route['arb_value'] = 'wallet/arb_value';
$route['arb_valueLive'] = 'wallet/arb_valueLive';
$route['abot_arb'] = 'wallet/abot_arb';

//-------------authentication -----------------//
$route['getQrImage'] = 'register/getQrImage';
$route['activate2fa'] = 'register/activate2fa';
$route['deactive2fa'] = 'register/deactive2fa';
$route['verify2fa'] = 'register/verify2fa';

$route['block_login'] = 'register/block_login';

$route['add_support_pin'] = 'register/add_support_pin';
//------------- test --------------------//
$route['activateaccount'] = 'register/activateaccount';
$route['index'] = 'test/index';
$route['get_abot_profitdetail'] = 'test/get_abot_profitdetail';

$route['testing_call'] = 'test/testing_call';

//--------account accsess----------//
$route['find_user'] = 'account/find_user';
$route['generate_request'] = 'account/generate_request';
$route['deny_request'] = 'account/deny_request';
$route['verify_access'] = 'account/verify_access';
$route['switch_session'] = 'account/switch_session';

$route['transfer_to_exearned'] = 'wallet/abot_earned_to_exEarned';
// $route['earnedexchange_to_wallet'] = 'wallet/exEarned_to_wallet';
$route['earnedexchange_to_abot'] = 'wallet/exEarned_to_abot';
$route['hold_arbs'] = 'amount/hold_arbs';
$route['update_order_amount_limit'] = 'exchange/update_order_amount_limit';

//------------------pro+------------------------//
$route['auto_abot_active'] = 'automation/auto_abot_active';
$route['auto_stop_abot'] = 'automation/auto_stop_abot';
$route['auto_selling_or_buying'] = 'automation/auto_selling_or_buying';
$route['proplus_eth_setting'] = 'automation/proplus_eth_setting';
$route['deactive_auto_selling'] = 'automation/deactive_auto_selling';
//transfer free arb
$route['freearb_to_wallet'] = 'automation/freearb_to_wallet';


//------------- testing ------------------------//
$route['test_address'] = 'test/test_address';

$route['remove_u_wallets'] = 'test/remove_u_wallets';

//---------------- Mbot user functions -------------//
$route['user_current_trade'] = 'mbot/user_current_trade';
$route['user_mbot_history'] = 'mbot/user_mbot_history';
$route['cancel_trade'] = 'mbot/cancel_trade';
$route['get_apikeys'] = 'mbot/get_apikeys';

//--------------- Mbot -------------------------//
//$route['mbot_test'] = 'mbot/get_deposit_address_bittrex';

//$route['mbot_register'] = 'amount/mbot_register';
$route['mbot_register_form_system'] = 'amount/mbot_register_form_system';

//$route['hit_trade'] = 'mbot/hit_trade';
//$route['after_withdraw_key'] = 'mbot/after_withdraw_key';
$route['mbot_cron'] = 'mbot/mbot_cron';

$route['auto_hit_trade'] = 'mbot/auto_hit_trade';

//---------------- Mbot Cred -----------------------//
$route['mbot_cred_trading'] = 'mbotcred/getCred_trading';
$route['mbot_cred'] = 'mbotcred/getCred';
$route['mbot_credentials'] = 'admin/mbotCrendials';
$route['exchanges_blnc'] = 'mbotcred/getBlnc';
$route['getUsersMbotCred'] = 'mbotcred/getUsersMbotCred';
$route['get_exchanges'] = 'mbotcred/get_exchanges';

//$route['testing'] = 'mbotcred/get_balance_bittrex';

//--------------- Transfer exchanges ----------------------//
$route['transfercoin_initial'] = 'transferex/transfercoin_initial';
$route['transfercoin'] = 'transferex/transfercoin';
$route['cancel_transfer'] = 'transferex/cancel_transfer';
$route['user_transfer_history'] = 'transferex/user_transfer_history';
$route['user_current_transfer'] = 'transferex/user_current_transfer';

//------------------backup rourtes---------------------//
$route['get_logs_backup'] = 'backup/get_logs_backup';
$route['get_orders_backup'] = 'backup/get_orders_backup';
$route['get_orders_block_exchange'] = 'backup/get_orders_block_exchange';
$route['get_current_logs'] = 'backup/get_current_logs';

$route['get_vault_log'] = 'vault/get_vault_log';



//--------exchange beta ---------//
// //orderplacing
// $route['place_buy_order'] = 'exchange_beta/place_buy_order';
// $route['sellOrder_exchange'] = 'exchange_beta/sellOrder_exchange';
// $route['sellOrder_exchange_earned'] = 'exchange_beta/sellOrder_exchange_earned';
// $route['sellOrder_stop_abot'] = 'exchange_beta/sellOrder_stop_abot';
// $route['sellOrder_pro_plus'] = 'exchange_beta/sellOrder_pro_plus';
// //user related
// $route['close_order_by_id_beta'] = 'exchange_beta/close_order_by_id';
// $route['exchange_wallet_balance_beta'] = 'exchange_beta/exchange_wallet_balance';
// $route['user_orders_beta'] = 'exchange_beta/user_orders';
// //crons
// $route['one_hour_blocks'] = 'exchange_beta/one_hour_blocks';
// $route['distribute_cron'] = 'exchange_beta/distribute_cron';
// //blocks related
// $route['get_blocks_data'] = 'exchange_beta/get_blocks_data';

// $route['buy_for_abot'] = 'exchange_beta/buy_for_abot';
// $route['init_block'] = 'exchange_beta/init_block';
// $route['get_block_info'] = 'exchange_beta/get_block_info';
//----------------- voting ------------------------------//
$route['register_for_vote'] = "voting/register_for_vote";
$route['cast_vote'] = "voting/cast_vote";
$route['suggestion_list_voting'] = "voting/suggestion_list";
$route['voting_seen'] = "voting/voting_seen";


//----------------- announcement ------------------------------//
$route['announcement_seen'] = "voting/announcement_seen";

//------------------vault-----------------------//
$route['wallet_to_vault'] = 'vault/wallet_to_vault';
$route['vault_to_wallet'] = 'vault/vault_to_wallet';
$route['cron_for_vault'] = 'vault/cron_for_vault';
$route['in_progress'] = 'vault/in_progress';
$route['vault_data'] = 'vault/vault_data';
// $route['external_wallet_to_vault'] = 'vault/external_wallet_to_vault';
// $route['vault_to_external_wallet'] = 'vault/vault_to_external_wallet';

//------abot 24 hour flag ----------//
$route['abot_flag_zero'] = 'amount/abot_flag_zero';


//--------- abot eth bonus links -----------//
$route['distribute_bonus_eth'] = 'abot/distribute_bonus_eth';
$route['open_eth_bonus_cron'] = 'abot/open_eth_bonus_cron';

//audit route
//$route['user_select_audit'] = 'amount/user_select_audit';


//--------Auction ----//
$route['request_for_auction'] = 'auction/request_for_auction';
$route['place_bid'] = 'auction/place_bid';
$route['get_auctions'] = 'auction/get_auctions';
$route['auction_win_cron'] = 'auction/auction_win_cron';
$route['verify_auction'] = 'auction/verify_auction';
$route['reject_auction'] = 'auction/reject_auction';
$route['sendemail_link'] = 'register/sendemail_link';
$route['get_user_auctions'] = 'auction/get_user_auctions';


//trading Pro 
$route['place_trade_in_exchanges'] = 'user_exchanges_data/place_trade';
$route['get_exchange_keys'] = 'user_exchanges_data/get_exchange_keys';
$route['cancel_order_exchange'] = 'user_exchanges_data/cancel_order_exchange';
$route['add_fav_pairs'] = 'user_exchanges_data/add_fav_pairs';
$route['get_fav_pairs'] = 'user_exchanges_data/get_fav_pairs';

