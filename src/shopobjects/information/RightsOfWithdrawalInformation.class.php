<?php
/**
 * This file represents the rights of withdrawal class.
 */
namespace ep6;
require_once("src/shopobjects/information/InformationTrait.class.php");
/**
 * This class is needed for use the information coming from rights of withdrawal.
 */
class RightsOfWithdrawalInformation {
	
	use InformationTrait;

	/**
	 * The REST path for rights of withdrawal.
	 */
	private static $RESTPATH = "legal/rights-of-withdrawal";
	
}
?>