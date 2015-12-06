<?php
/**
 * This file represents the privacy policy information class.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.0
 */
namespace ep6;
require_once("src/shopobjects/information/InformationTrait.class.php");
/**
 * The privacy policy information.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.0
 * @package ep6
 * @subpackage Shopobjects\Information
 * @see InformationTrait This trait has all information needed objects.
 */
class PrivacyPolicyInformation {
	
	use InformationTrait;

	/** @var String The REST path for privacy policy. */
	private static $RESTPATH = "legal/privacy-policy";
	
}
?>