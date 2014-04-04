<?php
//this is a test for machine learning purposes using prediction bases on kNearestNeighbor algorithm

echo "\033[01;31m loading training data! \n\033[0m";
//loading training data
$row = 1;
if (($handle = fopen(__DIR__."/trainingData/kNNTrainingData.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
            echo "\033[01;32m ".$data[$c] ."\t\033[0m";
        }
        echo "\n";
    }
    fclose($handle);
}