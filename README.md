# Sainsbury’s Software Engineering Test

This task is intended to test your ability to consume a webpage, process some data 
and present it.

Using best practice coding methods, build a console application that scrapes the 
Sainsbury’s grocery site - Ripe Fruits page and returns a JSON array of all the 
products on the page.

You need to follow each link and get the size (in kb) of the linked HTML (no assets) 
and the description to display in the JSON

Each element in the JSON results array should contain `title`, `unit_price`, `size` and 
`description` keys corresponding to items in the table.
Additionally, there should be a total field which is a sum of all unit prices on the page.
The link to use is:


Example JSON:
```json
{
   "results":[
      {
         "title":"Sainsbury's Avocado, Ripe & Ready x2",
         "size":"90.6kb",
         "unit_price":1.80,
         "description":"Great to eat now - refrigerate at home 1 of 5 a day 1 avocado counts as 1 of your 5..."
      },
      {
         "title":"Sainsbury's Avocado, Ripe & Ready x4",
         "size":"87kb",
         "unit_price":2.00,
         "description":"Great to eat now - refrigerate at home 1 of 5 a day 1 "
      }
   ],
   "total":3.80
}
```

## Installation

Clone the repository and install dependencies:

```bash
    ./composer.phar install
```

## Run

Just launch the command below in you shell:
```bash
 
 bin/scraper
```

Opionally you can pass a specific url 

```bash
 
 bin/scraper products-scraper http://example.com
```

You can get a formatted json output using the --pretty option

```bash
 
 bin/scraper products-scraper --pretty
```

## Tests

#### PhpUnit

```bash
 bin/phpunit
```

#### Behat

```bash
 bin/behat
```


## Design

I designed the app as per requirement: the code should concise as possibile and get straight to the point without forgetting decoupled code.
I could have used a dependency injection container, even a small one like Pimple, but I prefereed to keep it simple and 
initialize the objects with their releations directly in the [app](https://github.com/cirpo/sainsburys-scraper/blob/76be66b0dd4f4171058d896a1c0e1ef6a05d3bcd/bin/scraper#L13L20)

## Structure

* **Command** ProductsScraperCommand is responsible to start the app and to handle the possible command options
* **Service** ProductsInfoScraper is the service responsible to call the scraper and collet the product info
* **Scraper** ProductDetailScraper and ProductListScraper
* **Model** Product, Products and Url are the main object in the domain

