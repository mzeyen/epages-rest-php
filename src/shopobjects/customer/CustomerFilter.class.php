<?php
/**
 * This file represents the customer filter class.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.0
 */
namespace ep6;
/**
 * This is a product filter class to search customers via the REST call "product".
 *
 * @author David Pauli <contact@david-pauli.de>
 * @package ep6
 * @since 0.0.0
 * @since 0.1.0 Use a default Locale and Currency.
 * @since 0.1.1 The object can be echoed now.
 * @since 0.1.2 Add error reporting.
 * @subpackage Shopobjects\Product
 */
class CustomerFilter {

	use ErrorReporting;

	/** @var String The REST path to the filter ressource. */
	const RESTPATH = "customers";

	/** @var int The page of the product search result. */
	private $page = 1;

	/** @var int The number of results per page of the product search result. */
	private $resultsPerPage = 10;

	/**
	 * This is the constructor to prefill the customer filter.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param String[] $productFilterParameter The values of a product filter.
	 * @since 0.0.1
	 */
	public function __construct($customerFilterParameter = array()) {

		if (InputValidator::isArray($customerFilterParameter) &&
			!InputValidator::isEmptyArray($customerFilterParameter)) {

			$this->setProductFilter($customerFilterParameter);
		}
	}

	/**
	 * Prints the Customer Filter object as a string.
	 *
	 * This function returns the setted values of the Customer attribute object.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @return String The Product attribute as a string.
	 * @since 0.1.1
	 */
	public function __toString() {

		return "<strong>Page:</strong> " . $this->page . "<br/>" .
				"<strong>Results per page:</strong> " . $this->resultsPerPage . "<br/>";
	}

	/**
	 * This function returns the hash code of the object to equals the object.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @return String Returns the hash code of the object.
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale and Currency.
	 * @since 0.1.2 Add error reporting.
	 */
	public function hashCode() {

		$this->errorReset();

		$message = $this->page
			. $this->resultsPerPage;

		foreach ($this->IDs as $id) {

			$message .= $id;
		}

		return hash("sha512", $message);
	}

	/**
	 * This function gets the page.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @return int The page number of this product filter.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function getPage() {

		$this->errorReset();

		return $this->page;
	}

	/**
	 * This function returns the products by using the product filter.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale.
	 * @since 0.1.1 Unstatic every attributes.
	 * @since 0.1.2 Add error reporting.
	 * @return Product[] Returns an array of products.
	 */
	public function getCustomers() {

		$this->errorReset();

		$parameter = $this->getParameter();

		// if request method is blocked
		if (!RESTClient::setRequestMethod(HTTPRequestMethod::GET)) {

			$this->errorSet("RESTC-9");
			return;
		}

		$content = RESTClient::send(self::RESTPATH . "?" . $parameter);
		// if respond is empty
		if (InputValidator::isEmpty($content)) {

			$this->errorSet("PF-8");
		    Logger::error("ep6\CustomerFilter\nREST respomd for getting customers is empty.");
			return;
		}

		// if there is no results, page AND resultsPerPage element
		if (InputValidator::isEmptyArrayKey($content, "results") ||
			InputValidator::isEmptyArrayKey($content, "page") ||
			InputValidator::isEmptyArrayKey($content, "resultsPerPage")) {

			$this->errorSet("PF-9");
		    Logger::error("ep6\CustomerFilter\nRespond for " . self::RESTPATH . " can not be interpreted.");
			return;
		}

		$customers = array();

		// is there any product found: load the products.
	 	if (!InputValidator::isEmptyArrayKey($content, "items") && (sizeof($content['items']) != 0)) {

			foreach ($content['items'] as $item) {

				$customer = new Customer($item);
				
				array_push($customers, $customer);
			}
	 	}

		return $customers;
	}

	/**
	 * This function gets the results per page.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @return int The results per page number of this product filter.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function getResultsPerPage() {

		$this->errorReset();

		return $this->resultsPerPage;
	}

	/**
	 * This function reset all product IDs from filter.
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale and Currency.
	 * @since 0.1.2 Add error reporting.
	 */
	public function resetFilter() {

		$this->errorReset();

		$this->page = 1;
		$this->resultsPerPage = 10;
		
	}

	/**
	 * This function reset all product IDs from filter.
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function resetIDs() {

		$this->errorReset();
		$this->IDs = array();
	}

	/**
	 * This function sets the page to show.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param int $page The page number to filter.
	 * @return boolean True if setting the page works, false if not.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function setPage($page) {

		$this->errorReset();

		if (!InputValidator::isRangedInt($page, 1)) {

			$this->errorSet("PF-3");
			Logger::warning("ep6\CustomerFilter\nThe number " . $page . " as a product filter page needs to be bigger than 0.");
			return false;
		}

		$this->page = $page;
		return true;
	}

	/**
	 * Fill the product filter with an array.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param mixed[] $productFilterParameter The Product Filter Parameter as an array.
	 * @since 0.0.1
	 * @since 0.1.0 Use a default Locale and Currency.
	 * @since 0.1.2 Add error reporting.
	 */
	public function setProductFilter($customerFilterParameter) {

		$this->errorReset();

		if (!InputValidator::isArray($customerFilterParameter) ||
			InputValidator::isEmptyArray($customerFilterParameter)) {

			$this->errorSet("PF-1");
			Logger::warning("ep6\CustomerFilter\nProduct filter parameter " . $customerFilterParameter . " to create product filter is invalid.");
			return;
		}

		foreach ($customerFilterParameter as $key => $parameter) {

			if($key == "page") {

				$this->setPage($parameter);
			}
			else if($key == "resultsPerPage") {

				$this->setResultsPerPage($parameter);
			}
			else {

				$this->errorSet("PF-2");
				Logger::warning("ep6\CustomerFilter\nUnknown attribute <i>" . $key . "</i> in product filter attribute.");
			}
		}
	}

	/**
	 * This function sets the query search string to show.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param String $q The query search string to filter.
	 * @return boolean True if setting the query search string works, false if not.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function setQ($q) {

		$this->errorReset();

		if (InputValidator::isEmpty($q)) {

			return false;
		}

		$this->q = $q;

		return true;
	}

	/**
	 * This function sets the results per page to show.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param int $resultsPerPage The results per page to filter.
	 * @return boolean True if setting the results per page works, false if not.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function setResultsPerPage($resultsPerPage) {

		$this->errorReset();

		if (!InputValidator::isRangedInt($resultsPerPage, null, 100)) {

			$this->errorSet("PF-4");
			Logger::warning("ep6\CustomerFilter\The number " . $resultsPerPage . " as a product filter results per page needs to be lower than 100.");
			return false;
		}

		$this->resultsPerPage = $resultsPerPage;

		return true;
	}

	/**
	 * This function sets the order parameter to show.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param String $sort The sort parameter to filter.
	 * @return boolean True if setting the sort parameter works, false if not.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function setSort($sort) {

		$this->errorReset();

		if (!InputValidator::isProductSort($sort)) {

			$this->errorSet("PF-6");
			Logger::warning("ep6\CustomerFilter\nThe parameter " . $sort . " as a product filter sort has not a valid value.");
			return false;
		}

		$this->sort = $sort;

		return true;
	}

	/**
	 * This function delete a product ID from filter.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @param String $productID	The product ID to unset from filter.
	 * @return boolean True if unsetting the product ID string works, false if not.
	 * @since 0.0.0
	 * @since 0.1.0 Use attribute unstatic.
	 * @since 0.1.2 Add error reporting.
	 */
	public function unsetID($productID) {

		$this->errorReset();

		if (InputValidator::isEmpty($productID)
			|| !in_array($productID, $this->IDs)) {

			return false;
		}

		unset($this->IDs[array_search($productID, $this->IDs)]);

		return true;
	}

	/**
	 * This function returns the parameter as string.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @return String The parameter build with this product filter.
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale and Currency.
	 */
	private function getParameter() {

		$parameter = array();
		#array_push($parameter, "locale=" . Locales::getLocale());
		#array_push($parameter, "currency=" . Currencies::getCurrency());

		if (!InputValidator::isEmpty($this->page)) array_push($parameter, "page=" . $this->page);
		if (!InputValidator::isEmpty($this->resultsPerPage)) array_push($parameter, "resultsPerPage=" . $this->resultsPerPage);

		return implode("&", $parameter);
	}
}
?>