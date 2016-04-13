<?php

/**
 * This file represents the Customer class.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.0
 */
namespace ep6;


class Customer {
    
    use ErrorReporting;
    
    /** @var String The REST path to the customers ressource. */
	const RESTPATH = "customers";
    
    /** @var ProductAttribute[] This array saves all the attributes. */
	private $attributes = array();
    
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
    
    /** @var emailAddress|null Here the email address is saved. */
    private $emailAddress = null;
    
    /** @var firstName| Here the first name is saved. */
    private $firstName = null;
    
    /** @var internalNote|null Here the internal Note is saved. */
    private $internalNote = null;
    
    /** @var lastName|null Here the last name is saved. */
    private $lastName = null;
    
    /** @var salutation@null Here the salutation is saved. */
    private $salutation = null;
    
    /** @var street|null Here the street is saved. */
    private $street = null;
    
    /** @var streetDetails|null Here the street details are saved. */
    private $streetDetails = null;
    
    /** @var title|null Here the title is saved. */
    private $title = null;
    
    /** @var vatId|null Here the vatId is saved. */
    private $vatId = null;
    
    /** @var zipCode|null Here the zipCode is saved. */
    private $zipCode = null;
    
    /** @var int Timestamp in ms when the next request needs to be done. */
	private $NEXT_REQUEST_TIMESTAMP = 0;
    
    	/**
	 * This is the constructor of the Product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param mixed[]|String $customerParameter The customer to create as array or customer ID.
	 */
	public function __construct($customerParameter) {

		if (!InputValidator::isString($customerParameter) &&
			!InputValidator::isArray($customerParameter)) {

			self::errorSet("P-1");
			Logger::warning("ep6\Product\nProduct parameter " . $customerParameter . " to create product is invalid.");
			return;
		}

		if (InputValidator::isArray($customerParameter)) {
			$this->parseData($customerParameter);
		}
		else {
			$this->customerID = $customerParameter;
			$this->reload();
		}
	}
	
	/**
	 * Prints the Customer object as a string.
	 *
	 * This function returns the setted values of the Customer object.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @return String The Customer as a string.
	 * @since 0.1.1
	 */
	public function __toString() {

		return "<strong>Customer ID:</strong> " . $this->customerID . "<br/>" .
				"<strong>First Name:</strong> " . $this->firstName . "<br/>" .
				"<strong>Surname:</strong> " . $this->Surname . "<br/>" .
				"<strong>eMail</strong> " . $this->emailAddress . "<br/>" .
				"<strong>Address:</strong> " . $this->street . "<br/>" .
				"<strong>Street details:</strong> " . $this->streetDetails . "<br/>" .
				"<strong>Postcode:</strong> " . $this->zipCode . "<br/>" .
				"<strong>Town:</strong> " . $this->city . "<br/>" .
				"<strong>Country:</strong> " . $this->country . "<br/>" .
				"<strong>Price:</strong> " . $this->price . "<br/>";
	}
	
		/**
	 * Returns the product attributes.
	 *
	 * @author David Pauli <contact@david-pauli.de>
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
	 * @author David Pauli <contact@david-pauli.de>
	 * @param Array $customerParameter The product in an array.
	 * @since 0.1.3
	 */
	private function parseData($customerParameter) {

		// if the product comes from the shop API
		if (InputValidator::isArray($customerParameter) &&
			!InputValidator::isEmptyArrayKey($customerParameter, "customerId")) {

			$this->productID = $customerParameter['customerId'];

			if (!InputValidator::isEmptyArrayKey($customerParameter, "birthday")) {

				$this->birthday = $customerParameter['birthday'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "city")) {

				$this->city = $customerParameter['city'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "company")) {

				$this->company = $customerParameter['company'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "country")) {

				$this->country = $customerParameter['country'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "creationDate")) {

				$this->creationDate = $customerParameter['creationDate'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "customerNumber")) {

				$this->customerNumber = $customerParameter['customerNumber'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "emailAddress")) {

				$this->emailAddress = $customerParameter['emailAddress'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "firstName")) {

				$this->firstName = $customerParameter['firstName'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "internalNote")) {

				$this->internalNote = $customerParameter['internalNote'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "lastName")) {

				$this->lastName = $customerParameter['lastName'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "salutation")) {

				$this->salutation = $customerParameter['salutation'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "street")) {

				$this->street = $customerParameter['street'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "streetDetails")) {

				$this->streetDetails = $customerParameter['streetDetails'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "title")) {

				$this->title = $customerParameter['title'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "vatId")) {

				$this->vatId = $customerParameter['vatId'];
			}

			if (!InputValidator::isEmptyArrayKey($customerParameter, "zipCode")) {

				$this->zipCode = $customerParameter['zipCode'];
			}
		}
	}

 	/**
 	 * This function checks whether a reload is needed.
 	 *
 	 * @author David Pauli <contact@david-pauli.de>
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
    
}

?>