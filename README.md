Messy Data
=========

Use this library as a convient way to read data files.

Supports
-------

.csv

Overview
-------

Given the data file, data.csv with the following content:

```
name,address,city,state,zip
"Paul","123 Easy Street","Somewhere","Texas","12345"
```

We have the the name, address, city, state, and zip column names. We can then read
the file with the CsvFile class like this:

```php
require_once "CsvFile.php";

$reader = new CsvFile("data.csv");

while($reader->getNext()) {
  $name = $reader->getName();
  $address = $reader->getAddress();
  $city = $reader->getCity();
  $state = $reader->getState();
  $zip = $reader->getZip();
}
```

The goal was simplicity, and I've refined the library over the past few months 
and it's been tested on a variety of data files. It's also currently being used
in production at the company, iClassPro, where I work.

There's a few more features that I need to cover, but feel free to read the code
to get started. 

More to come! :)
