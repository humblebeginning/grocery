<?php
	require_once "support/http.php";
	require_once "support/web_browser.php";
	require_once "support/simple_html_dom.php";


	function getWebPage($url)
	{
		$web = new WebBrowser();
		$result = $web->Process($url);

		if (!$result["success"])
		{
			echo "Error retrieving URL.  " . $result["error"] . "\n";
			return false;
		}
		else if ($result["response"]["code"] != 200)  
		{
			echo "Error retrieving URL.  Server returned:  " . $result["response"]["code"] . " " . $result["response"]["meaning"] . "\n";
			return false;
		}
		else
		{
			return $result["body"];
		}

	}

	function extractProductData($productHTML = NULL)
	{
		$productList = array();

		if ($productHTML == NULL)
			return false;
		
	
		// $data = $productHTML->find("div[class=productInfo]");

		$link = $productHTML->find('div[class=productInfo]', 0)->find('h3');

		return $link->href;
			/* 

			$size = 

			$unitPrice =

			$description =
			*/
				

	}

	// Simple HTML DOM tends to leak RAM like
	// a sieve.  Declare what you will need here.
	// Objects are reusable.
	$html = new simple_html_dom();
	$html2 = new simple_html_dom();

	$url = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";

	$htmlContent = getWebPage($url);

	if (!$htmlContent)
	{
		echo "Failed to get data" . PHP_EOL;
		exit(0);
	}
	else
	{
		echo "Processing:\n";
		$html->load($htmlContent);

		// $products = $html->find("ul");

		$productList = array();

		$priceTotal = 0.00;
		$price = 0.00;

		foreach ($html->find("ul[class=productLister]") as $ul)
		
			foreach ($ul->find("li") as $product)
			{
				$price = $product->find('p[class=pricePerUnit] [!abbr]', 0);
				$link = $product->find('div[class=productInfo]', 0)->find('h3', 0)->find('a', 0);
				// $echo $link->href . PHP_EOL;
				$title = $link->plaintext;

				// $priceTotal += $price;

				// echo "\t" . $product->href . "\n";
				$htmlDest = getWebPage($link->href);
				// echo $htmlDest;
				$size = strlen($htmlDest);
				$size = number_format($size/ 1024, 2);
				$html2->load($htmlDest);
				$description = $html2->find("meta[name='description']", 0)->content;
				$description = html_entity_decode($description, ENT_QUOTES);
				$price = $html2->find('p[class=pricePerUnit] [!abbr]', 0)->plaintext;
				// echo $price . PHP_EOL;

				$productList[] = array("title" => html_entity_decode($title, ENT_QUOTES), "size" => $size . "kb", 
						"unit_price" => $price, "description" => $description);
				echo PHP_EOL;

			}

			$productList = array("total" => $priceTotal);


	}

	$jsonContent = json_encode($productList, 128);
	echo $jsonContent;
?>
