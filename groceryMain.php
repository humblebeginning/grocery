<?php

	require __DIR__ . '/vendor/autoload.php';

	//
	// High level Grocery class.  Very basic and could be further 
	// enhanced to separate functionality further
	//

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
	// Set up basic logging using Monolog php library
	//
	/* $log = new Monolog\Logger('name');
	$log->pushHandler(new Monolog\Handler\StreamHandler(__FILE__ . ".log", Monolog\Logger::WARNING));
	$log->addWarning('Foo'); */

	//
	// Create new instance of Grocery class
	//
	$groceryTest = new Grocery();


	$logger = $groceryTest->getLogger();
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

		$groceryTest->CreateJson($htmlContent);
	}

	$logger->info("Finished");
	// $jsonContent = json_encode($productList, 128);
	// echo $jsonContent;

	// echo "Finished";

?>
