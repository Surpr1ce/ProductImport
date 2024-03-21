<?php
require_once 'Database.php';
class ProductImport {
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function prepare($stock_file, $common_file): array
    {
        $formatted_data = [];

        $first_row = true;
        $stock_handle = fopen($stock_file, "r");
        if ($stock_handle !== false) {
            while (($data = fgetcsv($stock_handle, 0, ";")) !== false) {
                if ($first_row) {
                    $first_row = false;
                    continue;
                }
                $sku = trim($data[0],'"');
                $stock = intval($data[1]);
                $formatted_data[$sku]['stock'] = $stock;
            }
            fclose($stock_handle);
        }

        $first_row = true;
        $common_handle = fopen($common_file, "r");
        if ($common_handle !== false) {
            while (($data = fgetcsv($common_handle, 0, ";")) !== false) {
                if ($first_row) {
                    $first_row = false;
                    continue;
                }

                $sku = trim($data[0],'"');
                $ean = $data[1];
                $name = trim($data[2],'"');
                $shortDesc = trim($data[3],'"');
                $manufacturer = trim($data[4],'"');
                $price = floatval($data[5]);

                if (isset($formatted_data[$sku])) {
                    $formatted_data[$sku]['ean'] = $ean;
                    $formatted_data[$sku]['name'] = $name;
                    $formatted_data[$sku]['shortDesc'] = $shortDesc;
                    $formatted_data[$sku]['manufacturer'] = $manufacturer;
                    $formatted_data[$sku]['price'] = $price;
                } else {
                    $formatted_data[$sku] = [
                        'ean' => $ean,
                        'name' => $name,
                        'shortDesc' => $shortDesc,
                        'manufacturer' => $manufacturer,
                        'price' => $price,
                        'stock' => 0
                    ];
                }
            }
            fclose($common_handle);
        }
        return $formatted_data;
    }

    public function import($stock_file, $common_file): void
    {
        $data = $this->prepare($stock_file, $common_file);

        foreach ($data as $sku => $product) {
            $stock = $product['stock'];
            $ean = $product['ean'];
            $name = $product['name'];
            $shortDesc = $product['shortDesc'];
            $manufacturer = $product['manufacturer'];
            $price = $product['price'];

            $existing_product = $this->getDatabase()->selectProduct($sku);

            if ($existing_product) {
                $this->getDatabase()->updateStock($sku, $stock);
                echo "Updated product with SKU: $sku, Stock: $stock<br>";
            } else {
                $this->getDatabase()->insertProduct($sku, $ean, $name, $shortDesc, $manufacturer, $price, $stock);
                echo "Imported new product with SKU: $sku, Stock: $stock <br>";
            }
        }
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }
}


