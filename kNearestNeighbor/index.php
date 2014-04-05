<?php
require_once __DIR__.'/CSVReader.php';
require_once __DIR__.'/Normalize.php';


//this is a test for machine learning purposes using prediction based on kNearestNeighbor algorithm

echo "\033[01;31m loading training data! \n\033[0m";
//loading training data
$row = 1;
$reader = new CSVReader();
$rawData = $reader->read(__DIR__."/Data/kNNTrainingData.csv", "--- loading training data ---", csvReader::RED);

echo "\033[01;31m normalizing input \n\033[0m";
$normalize = new Normalize();
$normalize->setInputs($rawData);

$liveData = $reader->read(__DIR__."/Data/liveData.csv", "--- loading training data ---", csvReader::GREEN);

foreach($liveData as $unknownData) {
    $unknownDataNormalized = $normalize->normalizeData($unknownData);
    asort($unknownDataNormalized['distance']);


    $counter = 0;
    $machinePrediction = array("apartment" => 0, "house" => 0, "flat" => 0);
    foreach($unknownDataNormalized['distance'] as $knownDataKey => $knownDataValue) {
        if ($counter > 4) {
            continue;
        }

        $usedData = $normalize->getInputs();
        switch ($usedData[$knownDataKey][2]) {
            case "apartment":
                $machinePrediction['apartment']++;
                break;
            case "house":
                $machinePrediction['house']++;
                break;
            case "flat":
                $machinePrediction['flat']++;
                break;
            default:
                throw new RuntimeException('Help uknown building detected!');
                break;
        }

        $counter++;
    }

    arsort($machinePrediction);
    echo "\033[01;33m AI prediction = ".key($machinePrediction)." because I found ".$machinePrediction['apartment']." apartments, ".$machinePrediction['house']." houses ".$machinePrediction['flat']." flats\n\033[0m";
    echo "\033[01;33m Is this correct y/n \n\033[0m";
    $handle  = fopen ("php://stdin","r");
    $correct = fgets($handle);

    switch (trim($correct)) {
        case "y":
            $normalize->addInput($unknownData[0], $unknownData[1], key($machinePrediction));
            break;
        case "n":
            echo "\033[01;33m What the correct type\n 1: apartment\n 2: house\n 3: flat \n (1,2,3)\033[0m";
            $handle      = fopen ("php://stdin","r");
            $correctType = fgets($handle);

            switch (trim($correctType)) {
                case "1":
                    $normalize->addInput($unknownData[0], $unknownData[1], "apartment");
                    break;
                case "2":
                    $normalize->addInput($unknownData[0], $unknownData[1], "house");
                    break;
                case "3":
                    $normalize->addInput($unknownData[0], $unknownData[1], "flat");
                    break;
                default:
                    echo "unknown input I'm quitting";
                    break;
            }
            break;
        default:
            echo "unknown input I'm quitting";
            break;
    }

    $machinePrediction = array("apartment" => 0, "house" => 0, "flat" => 0);
}
