# Grocery product consumption
Sainsburys Test, scraping a product page.  A URL can be specified during running of program, or it will use the following URL as default.

http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true

## Install
```
$ php composer.phar install
```

## Update
```
$ php composer.phar update
```

## Run and consume a Sainsburys product category page
```
$ php groceryMain.php <-u optional URL>  <-h for help>
```

## To test using phpunit
```
$ ./vendor/phpunit/phpunit/composer/bin/phpunit GroceryTest.php
```
