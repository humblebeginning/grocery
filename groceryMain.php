<?php

/**
 * Main file used to retrieve products from the Sainsburys product category page 
 *
 * PHP version 5.3
 *
 * @author     Manoj Solanki <manoj@ms4web.co.uk>
 *
 */

require __DIR__ . '/vendor/autoload.php';

$url = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";

//
// Process command line options
//
$options = getopt("u:h");
if (isset($options['h']))
{
	echo "Usage: " . __FILE__ . " (-u url)" . PHP_EOL;
	exit(0);
}

if (isset($options['u']))
{
	$url = $options['u'];
}

//
// Create new instance of Grocery class
//
$groceryTest = new Grocery();


$logger = $groceryTest->GetLogger();
$logger->info("Starting");

//
// Get html of target url
//
$htmlContent = $groceryTest->GetWebPage($url);


if (!$htmlContent)
{
	$log->addError("Failed to get data");
	exit(0);
}
else
{
	// Run class method that processes HTML and creates output JSON file
	// including any products found
	$groceryTest->CreateJson($htmlContent);
}

$logger->info("Finished");

?>
