// CONSTANTS
// // ONE ADDRESS IS FOR THE - THE LEFT SIDE - ARB - THAT IS ADDRESS B // //
// // THE OTHER ADDRESS - THAT ENDS IN F3B IS FOR THE ETH CONTRACT // //
var contractAddressB = '0x57f1525a868e5B9856b5Ea58B65B66C49E97ecdA' // // THIS CONTRACT IS FOR THE ARB ONLY - THE LEFT SIDE -
var donationAddress = '0x57f1525a868e5B9856b5Ea58B65B66C49E97ecdA'
var contractAddressC = '0x3c52e0a89b554c2ce443bf467050f66Abdd88F3B' // // THERE IS  THE ETH SIDE CONTRACT // //0xDF03D76ca6333F7Dc412081DF0C921F2614D2042
var contractAddressPROPER = '0x3c52e0a89b554c2ce443bf467050f66Abdd88F3B' // // THis IS  THE ETH SIDE CONTRACT // //
var contractAddress = '0x77AA09393227549D831b0C0Ea1Bd4cF4ef687251' // // THIS IS THE KOVAN 

// GLOBALS
var web3Mode = null
var walletMode = 'metamask'
var currentAddress = null
var keystore = null
var dividendValue = 0
var tokenBalance = 0
var holdingsBalance = 0
var contract = null

var buyPrice = 0
var globalBuyPrice = 0
var sellPrice = 0
var ethPrice = 0
var currency = (typeof default_currency === 'undefined') ? 'USD' : default_currency
var ethPriceTimer = null
var dataTimer = null
var muteSound = false;
var abi = [
    {
        "constant": true,
        "inputs": [
            {
                "name": "_customerAddress",
                "type": "address"
            }
        ],
        "name": "dividendsOf",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "name",
        "outputs": [
            {
                "name": "",
                "type": "string"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_sellEth",
                "type": "uint256"
            }
        ],
        "name": "withdraw",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [],
        "name": "withdrawAll",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [
            {
                "name": "_ethereumToSpend",
                "type": "uint256"
            }
        ],
        "name": "calculateTokensReceived",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "totalSupply",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [
            {
                "name": "_tokensToSell",
                "type": "uint256"
            }
        ],
        "name": "calculateEthereumReceived",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "onlyAmbassadors",
        "outputs": [
            {
                "name": "",
                "type": "bool"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "decimals",
        "outputs": [
            {
                "name": "",
                "type": "uint8"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "etherToSendCharity",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [],
        "name": "withdrawOld",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [],
        "name": "payCharity",
        "outputs": [],
        "payable": true,
        "stateMutability": "payable",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "sellPrice",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "stakingRequirement",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [
            {
                "name": "_includeReferralBonus",
                "type": "bool"
            }
        ],
        "name": "myDividends",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "totalEthereumBalance",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [
            {
                "name": "_customerAddress",
                "type": "address"
            }
        ],
        "name": "balanceOf",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [
            {
                "name": "",
                "type": "address"
            }
        ],
        "name": "administrators",
        "outputs": [
            {
                "name": "",
                "type": "bool"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_amountOfTokens",
                "type": "uint256"
            }
        ],
        "name": "setStakingRequirement",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "buyPrice",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_identifier",
                "type": "address"
            },
            {
                "name": "_status",
                "type": "bool"
            }
        ],
        "name": "setAdministrator",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "totalEthCharityRecieved",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "myTokens",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "symbol",
        "outputs": [
            {
                "name": "",
                "type": "string"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [],
        "name": "disableInitialStage",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_toAddress",
                "type": "address"
            },
            {
                "name": "_amountOfTokens",
                "type": "uint256"
            }
        ],
        "name": "transfer",
        "outputs": [
            {
                "name": "",
                "type": "bool"
            }
        ],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "giveEthCharityAddress",
        "outputs": [
            {
                "name": "",
                "type": "address"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_symbol",
                "type": "string"
            }
        ],
        "name": "setSymbol",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_name",
                "type": "string"
            }
        ],
        "name": "setName",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": true,
        "inputs": [],
        "name": "totalEthCharityCollected",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_amountOfTokens",
                "type": "uint256"
            }
        ],
        "name": "sell",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [],
        "name": "exit",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [
            {
                "name": "_referredBy",
                "type": "address"
            }
        ],
        "name": "buy",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": true,
        "stateMutability": "payable",
        "type": "function"
    },
    {
        "constant": false,
        "inputs": [],
        "name": "reinvest",
        "outputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "function"
    },
    {
        "inputs": [],
        "payable": false,
        "stateMutability": "nonpayable",
        "type": "constructor"
    },
    {
        "payable": true,
        "stateMutability": "payable",
        "type": "fallback"
    },
    {
        "anonymous": false,
        "inputs": [
            {
                "indexed": true,
                "name": "customerAddress",
                "type": "address"
            },
            {
                "indexed": false,
                "name": "incomingEthereum",
                "type": "uint256"
            },
            {
                "indexed": false,
                "name": "tokensMinted",
                "type": "uint256"
            },
            {
                "indexed": true,
                "name": "referredBy",
                "type": "address"
            }
        ],
        "name": "onTokenPurchase",
        "type": "event"
    },
    {
        "anonymous": false,
        "inputs": [
            {
                "indexed": true,
                "name": "customerAddress",
                "type": "address"
            },
            {
                "indexed": false,
                "name": "tokensBurned",
                "type": "uint256"
            },
            {
                "indexed": false,
                "name": "ethereumEarned",
                "type": "uint256"
            }
        ],
        "name": "onTokenSell",
        "type": "event"
    },
    {
        "anonymous": false,
        "inputs": [
            {
                "indexed": true,
                "name": "customerAddress",
                "type": "address"
            },
            {
                "indexed": false,
                "name": "ethereumReinvested",
                "type": "uint256"
            },
            {
                "indexed": false,
                "name": "tokensMinted",
                "type": "uint256"
            }
        ],
        "name": "onReinvestment",
        "type": "event"
    },
    {
        "anonymous": false,
        "inputs": [
            {
                "indexed": true,
                "name": "customerAddress",
                "type": "address"
            },
            {
                "indexed": false,
                "name": "ethereumWithdrawn",
                "type": "uint256"
            }
        ],
        "name": "onWithdraw",
        "type": "event"
    },
    {
        "anonymous": false,
        "inputs": [
            {
                "indexed": true,
                "name": "from",
                "type": "address"
            },
            {
                "indexed": true,
                "name": "to",
                "type": "address"
            },
            {
                "indexed": false,
                "name": "tokens",
                "type": "uint256"
            }
        ],
        "name": "Transfer",
        "type": "event"
    },
    {
        "constant": true,
        "inputs": [
            {
                "name": "_customerAddress",
                "type": "address"
            }
        ],
        "name": "ethBalanceOf",
        "outputs": [
            {
                "name": "",
                "type": "uint256"
            }
        ],
        "payable": false,
        "stateMutability": "view",
        "type": "function"
    }
];

/// UTILITY FUNCTIONS
if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] !== 'undefined' ?
                args[number] :
                match

        })
    }
}

function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return clipboardData.setData('Text', text)

    } else if (document.queryCommandSupported && document.queryCommandSupported('copy')) {
        var textarea = document.createElement('textarea')
        textarea.textContent = text
        textarea.style.position = 'fixed' // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea)
        textarea.select()
        try {
            return document.execCommand('copy') // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn('Copy to clipboard failed.', ex)
            return false
        } finally {
            document.body.removeChild(textarea)
        }
    }
}

function updateEthPrice() {
    clearTimeout(ethPriceTimer)
    if (currency === 'EPY') {
        ethPrice = 1 / (sellPrice + ((buyPrice - sellPrice) / 2))
        ethPriceTimer = setTimeout(updateEthPrice, 10000)
    } else {
        $.getJSON('https://api.coinmarketcap.com/v1/ticker/ethereum/?convert=' + currency, function(result) {
            var eth = result[0]
            ethPrice = parseFloat(eth['price_' + currency.toLowerCase()])
            ethPriceTimer = setTimeout(updateEthPrice, 10000)
        })
    }
}

function convertEthToWei(e) {
    return 1e18 * e
}

function convertWeiToEth(e) {
    return e / 1e18
}

function getSeed() {
    useWallet(function(pwDerivedKey) {
        console.log(keystore.getSeed(pwDerivedKey))
    })
}



function getPassword(cb) {
    $('#password-prompt').modal('show')

    $('#confirm-tx').off('click')
    $('#confirm-tx').on('click', function() {
        var password = $('#password').val()
        $('#password').val('')

        $('#password-prompt').modal('hide')

        cb(password)
    })
}

function useWallet(cb) {
    getPassword(function(password) {
        keystore.keyFromPassword(password, function(err, pwDerivedKey) {
            if (err) throw err
            cb(pwDerivedKey)
        })
    })
}


function detectWeb3() {
    if ($('#metamask-detecting').hasClass('visible')) {
        $('#metamask-detecting').dimmer('hide')
    }

    if (typeof web3 !== 'undefined') {
        web3js = new Web3(web3.currentProvider)
        web3Mode = 'metamask'
        currentAddress = web3js.eth.accounts[0]
    } else {
        web3js = new Web3(new Web3.providers.HttpProvider('https://mainnet.infura.io/iAuiwox78xdSQSkLkeXB'))
        web3Mode = 'direct'
    }

    var ks = localStorage.getItem('keystore')
    if (ks !== null) {
        keystore = lightwallet.keystore.deserialize(ks)
        $('#unlock-wallet-container').show()
    }

    var contractClass = web3js.eth.contract(abi)
    contract = contractClass.at(contractAddress)


    updateData()
		attachEvents()
}

window.addEventListener('load', function() {

    setTimeout(detectWeb3, 500)

    function call(address, method, params, amount) {
        web3js.eth.getTransactionCount(currentAddress, function(err, nonce) {
            if (err) throw err

            web3js.eth.getGasPrice(function(err, gasPrice) {
                if (err) throw err

                // Median network gas price is too high most the time, divide by 10 or minimum 1 gwei
                gasPrice = Math.max(gasPrice / 10, 1000000000)

                var tx = {
                    'from': currentAddress,
                    'to': address,
                    'value': '0x' + amount.toString(16),
                    'gasPrice': '0x' + (gasPrice).toString(16),
                    'gasLimit': '0x' + (100000).toString(16),
                    'nonce': nonce,
                }

                var rawTx = lightwallet.txutils.functionTx(abi, method, params, tx)

                useWallet(function(pwDerivedKey) {
                    try {
                        var signedTx = '0x' + lightwallet.signing.signTx(keystore, pwDerivedKey, rawTx, currentAddress)
                    } catch (err) {
                        console.log(err)
                        alert(lang.incorrectPassword)
                        return
                    }
                    web3js.eth.sendRawTransaction(signedTx, function(err, hash) {
                        if (err) {
                            alert(err.message.substring(0, err.message.indexOf('\n')))
                            throw err
                        }

                        $('#tx-hash').empty().append($('<a target="_blank" href="https://etherscan.io/tx/' + hash + '">' + hash + '</a>'))
                        $('#tx-confirmation').modal('show')
                    })
                })
            })
        })
    }

    function getCookie(name) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);

        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        } else {
            begin += 2;
            var end = document.cookie.indexOf(";", begin);
            if (end == -1) {
                end = dc.length;
            }
        }

        return decodeURI(dc.substring(begin + prefix.length, end));
    }

    function fundarb(address, amount) {
        if (walletMode === 'metamask') {
            contract.buy(getCookie('masternode').split(';')[0], {
                value: convertEthToWei(amount)
            }, function(e, r) {
                console.log(e, r)
            })
        } else if (walletMode === 'web') {
            call(address, 'buy', [], convertEthToWei(amount))
        }
    }

    function withdrawsomearb(amount) {
        if (walletMode === 'metamask') {
            contract.withdraw(convertEthToWei(amount), function(e, r) {
                console.log(e, r)
            })
        } else if (walletMode === 'web') {
            call(contractAddress, 'withdraw', [convertEthToWei(amount)], 0)
        }
    }

    function withdrawallarb() {
        if (walletMode === 'metamask') {
            contract.withdrawAll(function(e, r) {
                console.log(e, r)
            })
        } else if (walletMode === 'web') {
            call(contractAddress, 'withdrawAll', [], 0)
        }
    }








		function transferTokens(amount, address) {
			if (walletMode === 'metamask') {
					contract.myTokens(function(err, myTokens) {
						if (parseFloat(amount) <= parseFloat(myTokens)) {
							contract.transfer(address, amount, function(err, result) {
								if (err) {
									alertify.error('An error occured. Please check the logs.');
									console.log('An error occured', err);
								} else {
									alertify.success('You have successfully transferred '+ amount.div(1000000000000000000).toFixed(4) +
										' tokens to address ' + address);
								}
							})
						} else {
							$('#transfer-amount').addClass('error').popup({
									title: lang.invalidInput,
									content: "You input more tokens then can be transferred!"
							}).popup('show')
						}
					});
			} else {
				alert.log('Transfer functionality supported only with Metamask or Trust Wallet.');
			}

		}



    // Buy token click handler
    $('#depositarb-tokens').click(function() {
        let amount = $('#depositarb-amount').val().trim()
        if (amount <= 0 || !isFinite(amount) || amount === '') {
            $('#depositarb-amount').addClass('error').popup({
                title: lang.invalidInput,
                content: lang.invalidInputResponse
            }).popup('show')
        } else {
            $('#depositarb-amount').removeClass('error').popup('destroy')
            fundarb(contractAddress, amount)
        }
    })


    $('#send-action').click(function() {
        var amount = $('#send-amount').val().trim()
        if (amount <= 0 || !isFinite(amount) || amount === '') {
            $('#send-amount').addClass('error').popup({
                title: lang.invalidInput,
                content: lang.invalidInputResponse
            }).popup('show')
        } else {
            var address = $('#send-address').val()
            if (!address.match(/^0x[0-9a-fA-F]{40}$/)) {
                $('#send-address').addClass('error').popup({
                    title: lang.invalidInput,
                    content: lang.invalidInputResponse
                }).popup('show')
            } else {
                $('#send-amount').removeClass('error').popup('destroy')
                $('#send-address').removeClass('error').popup('destroy')
                fund(address, amount)
            }
        }
    })



    $('#wallet-open').click(function(e) {
        e.preventDefault()
        $('#wallet-dimmer').dimmer('show')
    })

    $('#wallet-close').click(function(e) {
        e.preventDefault()
        $('#wallet-dimmer').dimmer('hide')

        $('#exported-seed').html('').slideUp()
        $('#exported-private-key').val('').slideUp()
    })



    // Withdraw Some ARB click handler
    $('#withdraw-somearb-btn').click(function() {
        withdrawsomearb($("#withdraw-arb-amount").val())
    })

    // Withdraw ALL ARB - click handler
    $('#withdraw-allarb-btn').click(function() {
        withdrawallarb()
    })






    $('#currency').val(currency)

    $('#currency').change(function() {
        currency = $(this).val()
        updateEthPrice()
    })

    updateEthPrice()

    $('#password-prompt').modal({
        closable: false
    })

    $('#cancel-tx').click(function() {
        $('#password-prompt').modal('hide')
    })

    $('#password').keyup(function(e) {
        var code = e.keyCode || e.which
        if (code === 13) {
            $('#confirm-tx').click()
        }
    })

    $('#purchase-amount').bind("keypress keyup click", function(e) {
        var number = $('#purchase-amount').val() * 100000;

        contract.calculateTokensReceived(number, function(e, r) {

            var numTokens = r / 100000;

            $('.number-of-tokens').text("With " + (number == 0 ? 0 : number / 100000) + " ETH you can buy " + numTokens.toFixed(3) + " Tokens");
        })
    })

    $('#delete-wallet').click(function(e) {
        e.preventDefault()

        if (!confirm(lang.deleteWalletConfirmation))
            return

        useWallet(function(pwDerivedKey) {
            if (!keystore.isDerivedKeyCorrect(pwDerivedKey)) {
                alert(lang.incorrectPassword)
            } else {
                $('#wallet-close').click()
                keystore = null
                localStorage.removeItem('keystore')
                currentAddress = null
                updateData()
            }
        })
    })

    $('#export-private-key').click(function(e) {
        e.preventDefault()

        useWallet(function(pwDerivedKey) {
            var key = keystore.exportPrivateKey(currentAddress, pwDerivedKey)
            $('#exported-seed').html('').slideUp()
            $('#exported-private-key').val('0x' + key).slideDown()
        })
    })

    $('#export-seed').click(function(e) {
        e.preventDefault()


        useWallet(function(pwDerivedKey) {
            var seed = keystore.getSeed(pwDerivedKey)
            $('#exported-private-key').val('').slideUp()
            $('#exported-seed').html(seed).slideDown()
        })
    })

		$('.mute-sound').click(function(e) {
				e.preventDefault()
				console.log('Clicked the mute sound')

				muteSound = !muteSound;

        if($(this).find('svg').hasClass('fa-volume-up')){
            $('.mute-sound').find("svg").removeClass('fa-volume-up').addClass('fa-volume-off');
        } else if($(this).find('svg').hasClass('fa-volume-off')) {
						$('.mute-sound').find('svg').removeClass('fa-volume-off').addClass('fa-volume-up');
      	}
		})

    $('#copy-eth-address').click(function(e) {
        e.preventDefault()
        copyToClipboard(currentAddress)

        $('#copy-eth-address').popup({
            content: lang.copiedToClip,
            hoverable: true
        }).popup('show')

    }).on('mouseout', function() {
        $('#copy-eth-address').popup('destroy')
    })
})

function updateData() {
    clearTimeout(dataTimer)

    var loggedIn = false

    if (walletMode === 'metamask') {
        loggedIn = typeof web3js.eth.defaultAccount !== 'undefined' && web3js.eth.defaultAccount !== null
        currentAddress = web3js.eth.defaultAccount
        $('#meta-mask-ui').removeClass('wallet-web').addClass('wallet-mm')
    } else if (walletMode === 'web') {
        loggedIn = currentAddress !== null
        $('#meta-mask-ui').addClass('wallet-web').removeClass('wallet-mm')
    }

    if (currentAddress !== null) {
        $('#arb-address').html(currentAddress)
        $('#arb-public-address a.etherscan-link').attr('href', 'https://etherscan.io/address/' + currentAddress).html(currentAddress)
    } else {
        $('#arb-address').html('Not Set')
    }

    if (loggedIn) {

        $('#meta-mask-ui').removeClass('logged-out').addClass('logged-in')

        contract.balanceOf(currentAddress, function(e, r) {
            const tokenAmount = (r / 1e18 * 0.9999)
            $('.arb-balance').text(Number(tokenAmount.toFixed(2)).toLocaleString() + '')
            contract.calculateEthereumReceived(r, function(e, r) {
                let bal = convertWeiToEth(r)
                $('.arb-value').text(bal.toFixed(4) + ' arb')
                $('.arb-value-usd').text('(' + Number((convertWeiToEth(r * 1) * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
                if (tokenBalance !== 0) {
                    if (bal > tokenBalance) {
                        $('.arb-value').addClass('up').removeClass('down')
                        setTimeout(function() {
                            $('.arb-value').removeClass('up')
                        }, 3000)
                    } else if (bal < tokenBalance) {
                        $('.arb-value').addClass('down').removeClass('up')
                        setTimeout(function() {
                            $('.arb-value').removeClass('down')
                        }, 3000)
                    }
                }
                tokenBalance = bal
            })
        })

        contract.arbBalanceOf(currentAddress, function(e, r) {
            const holdingsAmount = (r / 1e18 * 0.9999)
            $('.user-holdarb-balance').text(Number(holdingsAmount.toFixed(2)).toLocaleString() + '')
            contract.calculateEthereumReceived(r, function(e, r) {
                let bal = convertWeiToEth(r)
                $('.user-holdarb-value').text(bal.toFixed(4) + ' arb')
                $('.user-holdarb-value-usd').text('(' + Number(((r * 1) * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
                if (holdingsBalance !== 0) {
                    if (bal > holdingsBalance) {
                        $('.user-holdarb-value').addClass('up').removeClass('down')
                        $('.user-holdarb-balance').addClass('up').removeClass('down')
                        setTimeout(function() {
                            $('.user-holdarb-value').removeClass('up')
                        }, 3000)
                    } else if (bal < holdingsBalance) {
                        $('.user-holdarb-value').addClass('down').removeClass('up')
                        $('.user-holdarb-balance').addClass('down').removeClass('up')
                        setTimeout(function() {
                            $('.user-holdarb-value').removeClass('down')
                        }, 3000)
                    }
                }
                holdingsBalance = bal
            })
        })

        contract.myDividends(false, function(e, r) {
            let div = convertWeiToEth(r).toFixed(6)
            let refdiv = (dividendValue - div).toFixed(6);

            $('.arb-refdiv').text(refdiv + ' ETH')
            $('.arb-refdiv-usd').text('(' + Number((refdiv * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')

            $('.arb-nonrefdiv').text(div + ' ETH')
            $('.arb-nonrefdiv-usd').text('(' + Number((convertWeiToEth(r) * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
        })


        contract.arbBalanceOf(false,function(e,r) {
            let userbal = convertWeiToEth(r).toFixed(6);
            $('.user-holdold').text(userbal + 'eth')
            $('.user-holdold-usd').text('(' + Number((userbal * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')

        })

        contract.totalEthCharityCollected(function(e,r) {
          let totalEthCharityCollected = convertWeiToEth(r).toFixed(6);

          $('.arb-totalcharity').text(totalEthCharityCollected + ' ETH')
          $('.arb-totalcharity-usd').text('(' + Number((totalEthCharityCollected * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
        })

        contract.etherToSendCharity(function(e,r) {
          let ethForCharity = convertWeiToEth(r).toFixed(6);

          $('.arb-charity').text(ethForCharity + ' ETH')
        })


        contract.myDividends(true, function(e, r) {
            let div = convertWeiToEth(r).toFixed(6)

            $('.arb-div').text(div + '')
            $('.arb-div-usd').text('(' + Number((convertWeiToEth(r) * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')

            if (dividendValue != div) {
                $('.arb-div').fadeTo(100, 0.3, function() {
                    $(this).fadeTo(250, 1.0)
                })

                dividendValue = div
            }
        })

        web3js.eth.getBalance(currentAddress, function(e, r) {
            // We only want to show six DP in a wallet, consistent with MetaMask
            $('.addressarb-balance').text(convertWeiToEth(r).toFixed(6) + ' ETH')
        })
    } else {
        $('#meta-mask-ui').addClass('logged-out').removeClass('logged-in')
    }

    contract.buyPrice(function(e, r) {
        let buyPrice = convertWeiToEth(r)
        globalBuyPrice = convertWeiToEth(r)
        $('.arb-buy').text(buyPrice.toFixed(6) + ' ETH')
        $('.arb-buy-usd').text('(' + Number((buyPrice * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
    })

    contract.totalSupply(function(e, r) {
        let actualSupply = r / 1e18;
        $('.contractarb-tokens').text(Number(actualSupply.toFixed(0)).toLocaleString());
    })

    contract.sellPrice(function(e, r) {
        let sellPrice = convertWeiToEth(r)
        $('.arb-sell').text(sellPrice.toFixed(6) + ' ETH')
        $('.arb-sell-usd').text('(' + Number((sellPrice * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
    })

    web3js.eth.getBalance(contract.address, function(e, r) {
        $('.contractarb-balance').text(convertWeiToEth(r).toFixed(4))
        $('.contractarb-balance-usd').text('(' + Number((convertWeiToEth(r) * ethPrice).toFixed(2)).toLocaleString() + ' ' + currency + ')')
    })



    dataTimer = setTimeout(function() {
        updateData()
    }, web3Mode === 'metamask' ? 1000 : 5000)
}

function attachEvents() {


	// Always start from 20 blocks behind
	web3js.eth.getBlockNumber(function(error, result) {
		console.log("Current Block Number is", result);
	  contract.allEvents({
			fromBlock: result - 100,
		},function(e, result) {
			console.log('Current user - ', web3.eth.accounts[0])
			let currentUserEvent = web3.eth.accounts[0] == result.args.customerAddress;
			console.log('Found new transaction');
			console.log(alertify);

			switch(result.event) {
				case 'onTokenPurchase':
					if (currentUserEvent) {
							alertify.success('Your buy order is confirmed! You spent ' + result.args.incomingEthereum.div(1000000000000000000).toFixed(4) + ' ETH and received ' + result.args.tokensMinted.div(1000000000000000000).toFixed(4) + ' tokens.');
					} else {
							alertify.log('Someone else bought tokens.They spent ' + result.args.incomingEthereum.div(1000000000000000000).toFixed(4) + ' ETH and received ' + result.args.tokensMinted.div(1000000000000000000).toFixed(4) + ' tokens.');
					}
					// if (!muteSound) {
					// 	heyheySound.play();
					// }
					break;
				case 'onTokenSell':
					if (currentUserEvent) {
							alertify.success('Your sell order is confirmed! You received' + result.args['ethereumEarned'].div(1000000000000000000).toFixed(4) + ' for ' + result.args.tokensBurned.div(1000000000000000000).toFixed(4) + ' tokens.');
					} else {
							alertify.log('Someone else sold tokens. They received ' + result.args['ethereumEarned'].div(1000000000000000000).toFixed(4) + ' for ' + result.args.tokensBurned.div(1000000000000000000).toFixed(4) + ' tokens.');
					}
					// if (!muteSound) {
					// 	hmNoNoSound.play()
					// }
					break;
				case 'onWithdraw':
					if (currentUserEvent) {
						alertify.success('Your withdrawal request is confirmed! You received ' + result.args['ethereumWithdrawn'].div(1000000000000000000).toFixed(4) + '.');
					}
					break;
				case 'onReinvestment':
					if (currentUserEvent) {
						alertify.success('Your reinvestment order is confirmed! You received ' + result.args.tokensMinted.div(1000000000000000000).toFixed(4) + '. tokens.');
					}
				break;
				case 'Transfer':
					if (currentUserEvent) {
						alertify.success('Your transfer order is confirmed!' + result.args['to'] + ' received ' + result.args['tokens'].div(1000000000000000000).toFixed(4) + '. tokens.');
					}
					break;
			}
		})
	})
}