<?php

/**
 * This file represents the Customer class.
 *
 * @author Maik Zeyen <maik_zeyen@web.de>
 * @since 0.0.0
 */
namespace ep6;


class Customer {
    
    use ErrorReporting;
    
    /** @var String The REST path to the customers ressource. */
	const RESTPATH = "customers";
    
    /** @var ProductAttribute[] This array saves all the attributes. */
	private $attributes = array();
	
	/** @var Addressattributes[] This array saves all address attributes. */
	private $billingAddress = array();
    
    /** @var birthday|null Here the birthday is saved.  */
    private $birthday = null;
    
    /** @var city|null Here the city is saved. */
    private $city = null;
    
    /** @var company|null Here the company name is saved. */
    private $company = null;
    
    /** @var country|null Here the country is saved. */
    private $country = null;
    
    /** @var creationDate|null Here the creationDate is saved. */
    private $creationDate = null;
    
    /** @var customerNumber|null Here the customer number is saved. */
    private $customerNumber = null;
    
    /** @var customerId|null*/
    private $customerId = null;
    
    /** @var int Timestamp in ms when the next request needs to be done. */
	private $NEXT_REQUEST_TIMESTAMP = 0;
    
    	/**
	 * This is the constructor of the Product.
	 *
	 * @author Maik Zeyen <maik_zeyen@web.de>
	 * @param mixed[]|String $customerParameter The customer to create as array or customer ID.
	 */
	public function __construct($customerParameter) {

		if (!InputValidator::isString($customerParameter) &&
			!InputValidator::isArray($customerParameter)) {

			self::errorSet("P-1");
			Logger::warning("ep6\Customer\Customer parameter " . $customerParameter . " to create customer is invalid.");
			return;
		}

		if (InputValidator::isArray($customerParameter)) {
			
			#echo print_r($customerParameter);
			$this->parseData($customerParameter);
		}
		else {
			$this->customerID = $customerParameter;
			$this->firstName  = $customerParameter;
			$this->reload();
		}
	}
	
	/**
	 * Prints the Customer object as a string.
	 *
	 * This function returns the setted values of the Customer object.
	 *
	 * @author Maik Zeyen <maik_zeyen@web.de>
	 * @return String The Customer as a string.
	 * @since 0.1.1
	 */
	public function __toString() {

		return "<strong>Customer ID:</strong> " . $this->customerID . "<br/>" .
				"<strong>First Name:</strong> " . $this->billingAddress->getLastName() . "<br/>" .
				"<strong>Last Name:</strong> " . $this->lastName . "<br/>" .
				"<strong>eMail</strong> " . $this->emailAddress . "<br/>" .
				"<strong>Address:</strong> " . $this->street . "<br/>" .
				"<strong>Street details:</strong> " . $this->streetDetails . "<br/>" .
				"<strong>Postcode:</strong> " . $this->zipCode . "<br/>" .
				"<strong>Town:</strong> " . $this->city . "<br/>" .
				"<strong>Country:</strong> " . $this->country . "<br/>";
	}
	
		/**
	 * Returns the customer attributes.
	 *
	 * @author Maik Zeyen <maik_zeyen@web.de>
	 * @return ProductAttributes[] Gets the product attributes in an array.
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 * @since 0.1.2 Add error reporting.
	 */
	public function getAttributes() {

		self::errorReset();
		$timestamp = (int) (microtime(true) * 1000);

		// if the attribute is not loaded until now
		if (InputValidator::isEmptyArray($this->attributes) ||
			$this->NEXT_REQUEST_TIMESTAMP < $timestamp) {

			$this->loadAttributes();
		}

		return $this->attributes;
	}
	
	/**
	 * Parses the REST response data and save it.
	 *
	 * @author Maik Zeyen <maik_zeyen@web.de>
	 * @param Array $customerParameter The product in an array.
	 * @since 0.1.3
	 */
	private function parseData($customerParameter) {
		
		#echo "parseData: <pre>" . print_r($customerParameter, true) . "</pre>";

		// if the product comes from the shop API
		if (InputValidator::isArray($customerParameter) &&
			!InputValidator::isEmptyArrayKey($customerParameter, "customerId")) {
				
			$this->customerID = $customerParameter['customerId'];

			
			if (!InputValidator::isEmptyArrayKey($customerParameter, "billingAddress")) {

				$this->billingAddress = new Address($customerParameter['billingAddress']);
			}
			
			if (!InputValidator::isEmptyArrayKey($customerParameter, "customerNumber")) {

				$this->customerNumber = $customerParameter['customerNumber'];
			}
			
			if (!InputValidator::isEmptyArrayKey($customerParameter, "creationDate")) {

				$this->creationDate = $customerParameter['creationDate'];
				
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "internalNote")) {

				$this->internalNote = $customerParameter['internalNote'];
				
			}
		}
	}
	
	/**
	 * Loads the customer.
	 *
	 * @author Maik Zeyen <maik_zeyen@web.de>
	 * @since 0.1.2
	 * @since 0.1.3 Remove data parsing into correct function.
	 */
	private function load() {

		// if parameter is wrong or GET is blocked
		if (!RESTClient::setRequestMethod(HTTPRequestMethod::GET)) {

			self::errorSet("RESTC-9");
			return;
		}

		$content = RESTClient::sendWithLocalization(self::RESTPATH . "/" . $this->customerID, Locales::getLocale());

		// if respond is empty
		if (InputValidator::isEmpty($content)) {

			self::errorSet("PF-8");
			return;
		}

		$this->parseData($content);

		// update timestamp when make the next request
		$timestamp = (int) (microtime(true) * 1000);
		$this->NEXT_REQUEST_TIMESTAMP = $timestamp + RESTClient::$NEXT_RESPONSE_WAIT_TIME;
	}

 	/**
 	 * This function checks whether a reload is needed.
 	 *
 	 * @author Maik Zeyen <maik_zeyen@web.de>
 	 * @since 0.1.3
 	 */
 	private function reload() {

 		$timestamp = (int) (microtime(true) * 1000);

 		// if the value is empty
 		if ($this->NEXT_REQUEST_TIMESTAMP > $timestamp) {
 			return;
 		}

 		$this->load();
 	}
 	
 	/**
	 * Returns the customer number.
	 *
	 * @author
	 * @return String The customer number.
	 * @since 0.1.3
	 */
	public function getNumber() {

		self::errorReset();
		$this->reload();
		return $this->customerNumber;
	}
	
	/**
	 * Returns the creationDate fot the Customer.
	 *
	 * @author
	 * @return String The customer number.
	 * @since 0.1.3
	 */
	public function getCreationDate() {

		self::errorReset();
		$this->reload();
		return $this->creationDate;
	}
	/**
	 * Returns the BillingAddress object.
	 *
	 * @author
	 * @return Object The BillingAddress.
	 * @since 0.1.3
	 */
	public function getAddress() {

		self::errorReset();
		$this->reload();
		return $this->billingAddress;
	}
    
}

?>