<?php

require __DIR__ . '/vendor/autoload.php';

class GroceryTest extends PHPUnit_Framework_TestCase
{
  public function setUp(){ }
  public function tearDown(){ }

  public function testConnectionIsValid()
  {
    // test to ensure that the object from an fsockopen is valid
    $grocery = new Grocery();
    $url = 'http://www.google.co.uk';
    $this->assertTrue($grocery->GetWebPage($url) !== false);
  }

  public function testHTMLProcessing()
  {
    // test to ensure that the object from an fsockopen is valid
    $grocery = new Grocery();
    $url = 'http://www.google.co.uk';
    $this->assertTrue($grocery->GetWebPage($url) !== false);
  }
}

}
?>
