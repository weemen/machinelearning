<?php

class Normalize
{
    protected  $inputs = array();

    /**
     * @param array $inputs
     */
    public function setInputs(array $inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * @return array
     */
    public function getInputs()
    {
        if (0 === count($this->inputs)) {
            throw new RuntimeException("Inputs are empty");
        }
        return $this->inputs;
    }

    public function  addInput($rooms, $area, $type) {
        $this->inputs[] = array($rooms, $area, $type);
    }

    /**
     * @param array $unknownData
     */
    public function normalizeData(array $unknownData)
    {
        $minMaxValues            = $this->locateMinMaxValues();
        $unknownData['distance'] = $this->distanceBetweenValues($unknownData, $minMaxValues);
        return $unknownData;
    }

    protected function locateMinMaxValues()
    {
        $minRooms   = null;
        $minArea    = null;
        $maxRooms   = null;
        $maxArea    = null;

        foreach($this->getInputs() as $input) {
            if (null === $minRooms || $input[0] < $minRooms) $minRooms = $input[0];
            if (null === $minArea  || $input[1] < $minArea)  $minArea  = $input[1];
            if (null === $maxRooms || $input[0] > $maxRooms) $maxRooms = $input[0];
            if (null === $maxArea  || $input[1] > $maxArea)  $maxArea  = $input[1];
        }

        return array(
            "minRooms" => $minRooms,
            "minArea"  => $minArea,
            "maxRooms" => $maxRooms,
            "maxArea"  => $maxArea
        );
    }

    protected function distanceBetweenValues($unknownData, $minMaxValues)
    {
        $roomSpectrum = $minMaxValues['maxRooms'] - $minMaxValues['minRooms'];
        $areaSpectrum = $minMaxValues['maxArea']  - $minMaxValues['minArea'];

        $distance     = array();
        foreach($this->inputs as $knownData) {
            //what is the distance between known data and unknown data
            //bring it back to a number between 0 & 1;

            $deltaRooms = $knownData[0] - $unknownData[0];
            $deltaRooms = $deltaRooms / $roomSpectrum;

            $deltaArea  = $knownData[1] - $unknownData[1];
            $deltaArea  = $deltaArea / $areaSpectrum;

            //pythagoras
            $distance[] = sqrt( (pow($deltaRooms, 2) + pow($deltaArea, 2)) );
        }

        return $distance;
    }
}