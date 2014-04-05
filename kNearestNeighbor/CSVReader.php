<?php

class CSVReader
{
    const GREEN = "[01;32m";
    const RED   = "[01;31m";

    /**
     * @param $file
     * @param $message
     * @param $color
     */
    public function __construct()
    {

    }

    public function read($file, $message, $color)
    {
        $rawData = array();
        echo $message."\n";

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $num = count($data);
                for ($c=0; $c < $num; $c++) {
                    $data[$c] = trim($data[$c]);
                    echo "\033".$color." ".$data[$c] ."\t\033[0m";
                }

                $rawData[] = $data;
                echo "\n";
            }
            fclose($handle);
            echo "\n";
        }

        return $rawData;
    }
} 