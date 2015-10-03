<?php

	require __DIR__ . '/vendor/autoload.php';

	//
	// High level Grocery class.  Very basic and could be further 
	// enhanced to separate functionality further
	//
	Class Grocery
	{
		var $url;
		var $log = NULL;

		function Grocery()
		{
			$this->log = new Monolog\Logger('name');
			$this->log->pushHandler(new Monolog\Handler\StreamHandler(__FILE__ . ".log", Monolog\Logger::INFO));
		}


		function getLogger()
		{
			if ($this->log == NULL)
			{
				$this->log = new Monolog\Logger('name');
				$this->log->pushHandler(new Monolog\Handler\StreamHandler(__FILE__ . ".log", Monolog\Logger::INFO));
			}
			return $this->log;
		}


		function GetWebPage($url)
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

		function CreateJson($htmlContent)
		{
			$html = new simple_html_dom();
			$html2 = new simple_html_dom();

			// Open file for writing output json
			$outFile = fopen("./grocery.json", "w");
			if (!$outFile)
			{
				$log->addError("Failed to open output file");
				exit(0);
			}

			echo "Processing:\n";
			$html->load($htmlContent);

			// $products = $html->find("ul");

			$productList = array();

			$priceTotal = 0.00;
			$price = 0.00;

			fwrite ($outFile, "{" . PHP_EOL . "\t\"results\":[");

			foreach ($html->find("ul[class=productLister]") as $ul)
				foreach ($ul->find("li") as $product)
				{
					$price = $product->find('p[class=pricePerUnit] [!abbr]', 0);
					$link = $product->find('div[class=productInfo]', 0)->find('h3', 0)->find('a', 0);
					// $echo $link->href . PHP_EOL;
					$title = $link->plaintext;

					// $priceTotal += $price;

					// echo "\t" . $product->href . "\n";
					$htmlDest = $this->GetWebPage($link->href);
					// echo $htmlDest;
					$size = strlen($htmlDest);
					$size = number_format($size/ 1024, 2);
					$html2->load($htmlDest);
					$description = $html2->find("meta[name='description']", 0)->content;
					$description = html_entity_decode($description, ENT_QUOTES);
					$price = $html2->find('p[class=pricePerUnit] [!abbr]', 0)->plaintext;
					// echo $price . PHP_EOL;

					fwrite($outFile, "\t\t" . json_encode(array("title" => html_entity_decode($title, ENT_QUOTES), "size" => $size . "kb", "unit_price" => $price, "description" => $description)) . PHP_EOL);
					// echo PHP_EOL;

				}

			fwrite($outFile, "\t], " . PHP_EOL . json_encode(array("total" => $priceTotal)) . PHP_EOL);
			fwrite($outFile, "}" . PHP_EOL);

			fclose($outFile);

		}

	}

?>
