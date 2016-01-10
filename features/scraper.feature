Feature: Retrieve product information on a Sainsbury's product listing page.
  For each product will be displayed the name, the page size in kb and the unit_price.
  Display also a field named total as the sum of all the product's unit_price in the page.
  The format to display should be JSON

  Scenario: for a valid product list page, the scraper command will display the product information
    Given the product list page "products.html" exists
    And the page has the following product links
      | http://www.sainsburys.com/products/apricot.html |
      | http://www.sainsburys.com/products/avocado.html |
      | http://www.sainsburys.com/products/golden-kiwi.html |
    When I run the scraper command
    Then the output should be a Json containing 3 product's info with 7.1 as the total price

  Scenario: for an invalid product list page, the scraper command will display "No products found"
    Given that the  product page "productsX.html" doesn't exist
    When I run the scraper command with "productsX.html" as invalid url
    Then the output should be "No products found"
