<?php

namespace App;
use Exception;

/**
 * dilplay Fulfillable Orders on console
 */
class getFulfillableOrders {

    private $orders;
    private $ordersH;
    private $arguments;
    private $argumentsCounts;
    private $stock;

    /**
     * construct functions
     *
     * @param integer $argumentCount
     * @param array $arguments
     */
    public function __construct(int $argumentCount, array $arguments)
    {
        $this->orders = [];
        $this->ordersH = [];
        $this->arguments = $arguments;
        $this->argumentsCounts = $argumentCount;

    }

    /**
     * Main function
     *
     * @return void
     */
    public function displayOrdersOnConsole() :void
    {
        try{
            //set up stock after validate given json
            $this->validateParams();
            print_r("aa");
            //read orders from attached CSV file
            $this->getOrdes();
            //short orders
            $this->sortOrders();
            //after all data we have, display it as a table
            $this->displayOrders();

        } catch (Exception $ex){
            echo "<<<<<< ERROR >>>>>> ". $ex->getMessage();
        }
    }

    /**
     * Validate the given parameters before stet up the stock
     *
     * @return boolean|null
     */
    public function validateParams() :?bool
    {
        //we will receive only one parameter
        if($this->argumentsCounts != 2){
            throw new Exception('Ambiguous number of parameters!');
        }

        //trying to convert the given json string to object
        $stockInput = json_decode( $this->addQuotesToJsonKeys($this->arguments[1]));
        if( $stockInput !== null) {
            //if conversation was successfully we set up the stock to use in the class
            $this->stock = $stockInput;
        } else {
            throw new Exception('Invalid json! errorCode: '.json_last_error_msg());
        }

        return true;
    }

    /**
     * valid json have to qute the parameter name
     *
     * @param string $string
     *
     * @return string
     */
    public function addQuotesToJsonKeys(string $string) :string
    {
        return preg_replace('/(\w+):/i', '"\1":', $string);
    }

    /**
     * short the orders by priority and created date
     *
     * @return boolean
     */
    public function sortOrders() :bool
    {
        usort($this->orders, function ($a, $b) {
            $pc = -1 * ($a['priority'] <=> $b['priority']);
            return $pc == 0 ? $a['created_at'] <=> $b['created_at'] : $pc;
        });
        return true;
    }

    /**
     * read orders data from CSV file
     *
     * @return boolean|null
     */
    public function getOrdes() :?bool
    {
        $row = 1;
        if (($handle = fopen('orders.csv', 'r')) !== false) {
            try{
                while (($data = fgetcsv($handle)) !== false) {
                    //first row in the CSV file is the hed
                    if ($row == 1) {
                        //set up th headsers
                        $this->ordersH = $data;
                    } else {
                        //set up orders key by the head name
                        $o = [];
                        for ($i = 0, $iMax = count($this->ordersH); $i < $iMax; $i++) {
                            $o[$this->ordersH[$i]] = $data[$i];
                        }
                        $this->orders[] = $o;
                    }
                    $row++;
                }
                fclose($handle);
            } catch (Exception $ex) {
                throw new Exception('Error while trying to import orders'. $ex->getMessage());
            }

            return true;
        }

        throw new Exception('Orders were not found: ');
    }

    /**
     * display orders on the console  like a table
     *
     * @return boolean|null
     */
    private function displayOrders() :?bool
    {
        //displa heads and the separators
        $headTitles = '';
        $headSeparator = '';
        foreach ($this->ordersH as $h) {
            $headTitles .= str_pad($h, 20);
            $headSeparator .= str_repeat('=', 20);
        }
        echo $headTitles;
        echo "\n";
        echo $headSeparator;
        echo "\n";
        //dilslay order date
        foreach ($this->orders as $item) {
            if ( $this->stock->{$item['product_id']} >= $item['quantity']) {
                foreach ($this->ordersH as $h) {
                    $text = $item[$h];
                    if ($h === 'priority') {
                        switch($item['priority']){
                            case 1 : $text = 'low'; break;
                            case 2 : $text = 'medium'; break;
                            default:  $text = 'high';
                        }
                    }
                    echo str_pad($text, 20);
                }
                echo "\n";
            }
        }
        return true;
    }
}
