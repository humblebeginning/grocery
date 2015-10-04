<?php

/**
 * Class file for Grocery class for use by phpunit
 *
 * PHP version 5.3
 *
 * @author     Manoj Solanki <manoj@ms4web.co.uk>
 *
 */

require __DIR__ . '/vendor/autoload.php';

/**
  * GroceryTest class definition
  *
  * Class used to test the Grocery class in a very simple manner.
  *
  */
class GroceryTest extends PHPUnit_Framework_TestCase
{
	/**
	 * testConnectionIsValid()
	 *
	 * Test to ensure that a URL can be retrieved ok
	 *
	 */
	public function testConnectionIsValid()
	{
		$grocery = new Grocery();
		$url = 'http://www.google.co.uk';
		$this->assertTrue($grocery->GetWebPage($url) !== false);
	}

	/**
	 * testConnectionIsValid()
	 *
	 * Test to check that a json file is produced from processing some HTML
	 *
	 */
	public function testHTMLProcessing()
	{
		$grocery = new Grocery();

		// This URL won't find any product data, which is fine for this test
		$url = 'http://www.google.co.uk';
		$this->assertTrue($grocery->GetWebPage($url) !== false);

		$grocery->CreateJson($htmlContent);
		$this->assertFileExists('./grocery.json');
	}
}

?>
