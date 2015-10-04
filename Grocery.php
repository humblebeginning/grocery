<?php

/**
 * Class file for Grocery class 
 *
 * PHP version 5.3
 *
 * @author     Manoj Solanki <manoj@ms4web.co.uk>
 *
 */

require __DIR__ . '/vendor/autoload.php';


/**
  * Grocery class definition
  *
  * Class used to encapsulate functionality for fetching a product type pages from Sainsburys website.  Very basic
  * and could be enhanced further. Uses Monolog/logger, simple_dom_html.php for DOM parsing and also 
  * ultimate web scraper library to retrieving the web page.
  *
  */

Class Grocery
{
	/**
	 * Stores the target web URL for fetching
	 *
	 * @var string
	 * @access private
	 */
	var $url;


	/**
	 * Variable to point to instance of logger defined by Monolog
	 *
	 * @var Monolog\Logger
	 * @access private
	 */
	var $log = NULL;

	/**
	 * Grocery constructor
	 *
	 * Sets up logging variable for Monolog.
	 *
	 * @return none
	 *
	 */
	function Grocery()
	{
		$this->log = new Monolog\Logger('name');
		$this->log->pushHandler(new Monolog\Handler\StreamHandler(__FILE__ . ".log", Monolog\Logger::INFO));
	}


	/**
	 * GetLogger()
	 *
	 * Sets up logging variable for Monolog if not already defined (unlikely).
	 * Used in a Singleton-like fashion to ensure one instance of it used.
	 *
	 * @param string $arg1 the string to quote
	 * @param int    $arg2 an integer of how many problems happened.
	 *                     Indent to the description's starting point
	 *                     for long ones.
	 *
	 * @return Monolog\Logger  Instance of logger
	 *
	 * @access public
	 *
	 */
	function GetLogger()
	{
		if ($this->log == NULL)
		{
			$this->log = new Monolog\Logger('name');
			$this->log->pushHandler(new Monolog\Handler\StreamHandler(__FILE__ . ".log", Monolog\Logger::INFO));
		}
		return $this->log;
	}


	/**
	 * GetWebPage()
	 *
	 * Retrieve web page html for given url in parameter $url.  Uses third-party class under ultimate-web-scraper
	 *
	 * @param string $url  The URL to fetch.
	 *
	 * @return false if failed to get web page for URL or the HTML content on success
	 *
	 *
	 */
	function GetWebPage($url)
	{
		$web = new WebBrowser();
		$result = $web->Process($url);

		//
		// If fetch failed, or HTTP response code is anything other than 200, report an error
		// and return false
		//
		if (!$result["success"])
		{
			$this->log->error("Error retrieving URL.  " . $result["error"]);
			return false;
		}
		else if ($result["response"]["code"] != 200)  
		{
			$this->log->error("Error retrieving URL.  Server returned:  " . 
				$result["response"]["code"] . " " . $result["response"]["meaning"]);
			return false;
		}
		else
		{
			return $result["body"];
		}

	}



	/**
	 * CreateJson()
	 *
	 * Takes HTML content as input and extracts product contents as per Sainsburys product
	 * HTML structure.  Outputs content to file name in JSON format as per requirements.
	 *
	 * @param string $htmlContent  HTML content to process
	 *
	 * @return none
	 *
	 *
	 */
	function CreateJson($htmlContent)
	{
		$html = new simple_html_dom();
		$htmlDomForDest = new simple_html_dom();

		// Open file for writing output json
		$outFile = fopen("./grocery.json", "w");
		if (!$outFile)
		{
			$log->addError("Failed to open output file");
			exit(0);
		}

		$this->log->info("Processing HTML");
		$html->load($htmlContent);

		$productList = array();

		$priceTotal = 0.00;
		$price = 0.00;

		// Write start of output JSON to file
		fwrite ($outFile, "{" . PHP_EOL . "\t\"results\":[" . PHP_EOL);

		// Loop through each li that contains a product
		//
		// Note: As this has been written on a system using PHP 5.3,
		// the JSON PRETTY_PRINT isn't available for json_encode
		// option in't available so JSON is created manually without using json_encode
		//
		foreach ($html->find("ul[class=productLister]") as $ul)
		{
			$ulElements = $ul->find("li");
			$last_key = end(array_keys($ulElements));
			
			// foreach ($ul->find("li") as $product)
			foreach ($ulElements as $product)
			{
				//
				// Assign required variables by finding the relevant data
				//

				// Get price and format so we have just a float number that can be used for
				// cumalative total price of all products
				$price = strip_tags(trim($product->find('p[class=pricePerUnit] [!abbr]', 0)->plaintext));
				$price = preg_replace("/[^0-9\.]/", "", $price);
				$priceTotal += $price;

				// Get link and title for product
				$link = $product->find('div[class=productInfo]', 0)->find('h3', 0)->find('a', 0);
				$title = trim($link->plaintext);

				// Get the HTML of the destination product link to get size and description
				$htmlDest = $this->GetWebPage($link->href);
				$size = strlen($htmlDest);
				$size = number_format($size/ 1024, 2);
				$htmlDomForDest->load($htmlDest);
				$description = $htmlDomForDest->find("meta[name='description']", 0)->content;
				$description = html_entity_decode($description, ENT_QUOTES);

				// Write the product data to the file retrieved
				$productOutput = "\t\t{" . PHP_EOL . "\t\t\t\"title:\": \"$title\"," . PHP_EOL .
				       "\t\t\t\"size\": \"" . $size ."kb\"," . PHP_EOL .
				       "\t\t\t\"unit_price\": " . number_format((float) $price, 2, '.', '') . "," . PHP_EOL .
				       "\t\t\t\"description\": \"$description\"" . PHP_EOL . "\t\t}";
				if ($product !== end($ulElements))
					$productOutput .=  ",";
				fwrite($outFile, $productOutput . PHP_EOL);
			}
		}

		// Write remaining output, including price total to file
		fwrite($outFile, "\t], " . PHP_EOL . "\t\"total\": " . $priceTotal . PHP_EOL);
		fwrite($outFile, "}" . PHP_EOL);

		fclose($outFile);

	}

}

?>
