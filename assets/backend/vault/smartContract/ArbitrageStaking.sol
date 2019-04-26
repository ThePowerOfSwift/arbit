pragma solidity ^0.4.24;

// File: contracts/Ownable.sol

/**
 * @title Ownable
 * @dev The Ownable contract has an owner address, and provides basic authorization control
 * functions, this simplifies the implementation of "user permissions".
 */
contract Ownable {
  address public owner;


  event OwnershipRenounced(address indexed previousOwner);
  event OwnershipTransferred(
    address indexed previousOwner,
    address indexed newOwner
  );


  /**
   * @dev The Ownable constructor sets the original `owner` of the contract to the sender
   * account.
   */
  constructor() public {
    owner = msg.sender;
  }

  /**
   * @dev Throws if called by any account other than the owner.
   */
  modifier onlyOwner() {
    require(msg.sender == owner);
    _;
  }

  /**
   * @dev Allows the current owner to relinquish control of the contract.
   * @notice Renouncing to ownership will leave the contract without an owner.
   * It will not be possible to call the functions with the `onlyOwner`
   * modifier anymore.
   */
  function renounceOwnership() public onlyOwner {
    emit OwnershipRenounced(owner);
    owner = address(0);
  }

  /**
   * @dev Allows the current owner to transfer control of the contract to a newOwner.
   * @param _newOwner The address to transfer ownership to.
   */
  function transferOwnership(address _newOwner) public onlyOwner {
    _transferOwnership(_newOwner);
  }

  /**
   * @dev Transfers control of the contract to a newOwner.
   * @param _newOwner The address to transfer ownership to.
   */
  function _transferOwnership(address _newOwner) internal {
    require(_newOwner != address(0));
    emit OwnershipTransferred(owner, _newOwner);
    owner = _newOwner;
  }
}

// File: contracts/Pausable.sol

/**
 * @title Pausable
 * @dev Base contract which allows children to implement an emergency stop mechanism.
 */
contract Pausable is Ownable {
  event Pause();
  event Unpause();

  bool public paused = false;


  /**
   * @dev Modifier to make a function callable only when the contract is not paused.
   */
  modifier whenNotPaused() {
    require(!paused);
    _;
  }

  /**
   * @dev Modifier to make a function callable only when the contract is paused.
   */
  modifier whenPaused() {
    require(paused);
    _;
  }

  /**
   * @dev called by the owner to pause, triggers stopped state
   */
  function pause() onlyOwner whenNotPaused public {
    paused = true;
    emit Pause();
  }

  /**
   * @dev called by the owner to unpause, returns to normal state
   */
  function unpause() onlyOwner whenPaused public {
    paused = false;
    emit Unpause();
  }
}

// File: contracts/SafeMath.sol

/**
 * @title SafeMath
 * @dev Math operations with safety checks that throw on error
 */
library SafeMath {

  /**
  * @dev Multiplies two numbers, throws on overflow.
  */
  function mul(uint256 a, uint256 b) internal pure returns (uint256 c) {
    if (a == 0) {
      return 0;
    }
    c = a * b;
    assert(c / a == b);
    return c;
  }

  /**
  * @dev Integer division of two numbers, truncating the quotient.
  */
  function div(uint256 a, uint256 b) internal pure returns (uint256) {
    // assert(b > 0); // Solidity automatically throws when dividing by 0
    // uint256 c = a / b;
    // assert(a == b * c + a % b); // There is no case in which this doesn't hold
    return a / b;
  }

  /**
  * @dev Subtracts two numbers, throws on overflow (i.e. if subtrahend is greater than minuend).
  */
  function sub(uint256 a, uint256 b) internal pure returns (uint256) {
    assert(b <= a);
    return a - b;
  }

  /**
  * @dev Adds two numbers, throws on overflow.
  */
  function add(uint256 a, uint256 b) internal pure returns (uint256 c) {
    c = a + b;
    assert(c >= a);
    return c;
  }
}

// File: contracts/ArbitrageStaking.sol

/**
*  @dev  ArbitrageToken contract interface to check balance and transfer tokens
*/
interface ArbitrageInterface {
    function balanceOf(address tokenOwner) external view returns (uint balance);
    function transfer(address to, uint tokens) external returns (bool success);
    function transferFrom(address from, address to, uint tokens) external returns (bool success);
}

/**
* @title ArbitrageStaking
* @dev The ArbitrageStaking contract staking ARBITRAGE(ARB) tokens.
*      Here is stored all function and data of user stakes in contract.
*      Staking is configured for 2%.
*/
contract ArbitrageStaking is Pausable {
    using SafeMath for uint256;

    /*==============================
    =            EVENTS            =
    ==============================*/

    event onPurchase(
        address indexed customerAddress,
        uint256 tokensIn,
        uint256 contractBal,
        uint256 poolFee,
        uint timestamp
    );

    event onWithdraw(
        address indexed customerAddress,
        uint256 tokensOut,
        uint256 contractBal,
        uint timestamp
    );

            /*** STORAGE ***/

            // Arbitrage Contract Interface initialized on construction
    ArbitrageInterface public arbitrageToken_;

    mapping(address => uint256) internal personalFactorLedger_; // personal factor ledger
    mapping(address => uint256) internal balanceLedger_; // users balance ledger

            // Configurations
    uint256 minBuyIn = 0.001 ether; // can't buy less then 0.0001 ARB
    uint256 stakingPrecent = 2;
    uint256 internal globalFactor = 10e21; // global factor
    uint256 constant internal constantFactor = 10e21 * 10e21; // constant factor

    /**
    * Constructor
    * _arbitrageToken -> arbitrage Token Contract
    */
    constructor(address _arbitrageToken) public {
        arbitrageToken_ = ArbitrageInterface(_arbitrageToken);
    }

    /// @notice No tipping!
    /// @dev Reject all Ether from being sent here. Hopefully, we can prevent user accidents.
    function() external payable {
        require(msg.sender == address(this), "dont allow transfer ethereum to contract");
    }

    /**
    * @dev  Buy in staking pool, transfer tokens in the contract, pay 2% fee
    * @param _amount - Amount of tokens to send in this contract
    */
    function buy(uint256 _amount)
        public
        whenNotPaused()
    {

        require(_amount >= minBuyIn, "should be more the 0.0001 token sent");

        require(
            arbitrageToken_.transferFrom(msg.sender, address(this), _amount),
            "transfer failed, either amount is too big, either allowance is too small");

        address _customerAddress = msg.sender;

        uint256 _tokensBeforeBuyIn = arbitrageToken_.balanceOf(address(this)).sub(_amount);

        uint256 poolFee;
        // Check is not a first buy in
        if (_tokensBeforeBuyIn != 0) {

            // Add 2% fee of the buy to the staking pool
            poolFee = _amount.mul(stakingPrecent).div(100);

            // Increase amount of eth everyone else owns
            uint256 globalIncrease = globalFactor.mul(poolFee) / _tokensBeforeBuyIn;
            globalFactor = globalFactor.add(globalIncrease);
        }


        balanceLedger_[_customerAddress] = arbBalanceOf(_customerAddress).add(_amount).sub(poolFee);
        personalFactorLedger_[_customerAddress] = constantFactor / globalFactor;

        emit onPurchase(_customerAddress, _amount, getBalance(), poolFee, now);
    }

    /**
    * @dev Withdraw selected amount of tokens from the contract back to user,
    *      update the balance.
    * @param  _withdrawAmount - Amount of tokens to withdraw from contract
    */
    function withdraw(uint256 _withdrawAmount)
        public
        whenNotPaused()
    {
        address _customerAddress = msg.sender;

        // User must have enough ARB and cannot withdraw 0
        require(_withdrawAmount > 0, "user cant spam transactions with 0 value");
        require(_withdrawAmount <= arbBalanceOf(_customerAddress), "user cant withdraw more then he has");

        // Transfer balance and update user ledgers
        arbitrageToken_.transfer(msg.sender, _withdrawAmount);

        balanceLedger_[_customerAddress] = arbBalanceOf(_customerAddress).sub(_withdrawAmount);
        personalFactorLedger_[_customerAddress] = constantFactor / globalFactor;

        emit onWithdraw(_customerAddress, _withdrawAmount, getBalance(), now);
    }

    /**
    * @dev Withdraw all the tokens user holds in the contract, set balance to 0
    */
    function withdrawAll()
        public
        whenNotPaused()
    {
        address _customerAddress = msg.sender;
        // Set the withdraw amount to the user's full balance, don't withdraw if empty
        uint256 _withdrawAmount = arbBalanceOf(_customerAddress);
        require(_withdrawAmount > 0, "user cant call withdraw, when holds nothing");

        // Transfer balance and update user ledgers
        require(arbitrageToken_.transfer(msg.sender, _withdrawAmount), "transfer failed, not enough balance");

        balanceLedger_[_customerAddress] = 0;
        personalFactorLedger_[_customerAddress] = constantFactor / globalFactor;

        emit onWithdraw(_customerAddress, _withdrawAmount, getBalance(), now);
    }

    /**
    * UI Logic - View Functions
    */

    // @dev Returns contract ARB tokens balance
    function getBalance()
        public
        view
        returns (uint256)
    {
        return arbitrageToken_.balanceOf(address(this));
    }

    // @dev Returns user ARB tokens balance in contract
    function arbBalanceOf(address _customerAddress)
        public
        view
        returns (uint256)
    {
        // Balance ledger * personal factor * globalFactor / constantFactor
        return balanceLedger_[_customerAddress].mul(personalFactorLedger_[_customerAddress]).mul(globalFactor) / constantFactor;
    }

}
