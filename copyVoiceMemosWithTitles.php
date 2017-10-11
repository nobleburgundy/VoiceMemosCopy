<?php

class CopyVoiceMemos {
    private $params = [];
    private $destination;
    private $xmlPath;

    function __construct() {
        $this->params      = $this->getInputParams();
        $this->xmlPath     = $this->params['xml'];
        $this->destination = $this->params['destination'];
    }

    function run() {
        $xml    = simplexml_load_file($this->xmlPath);
        $json   = json_encode($xml);
        $titles = $this->getJsonValueArrayByKey($json, 'string');

        if (!file_exists($this->destination)) {
            echo("\nWARNING - '$this->destination' did not exist, so creating it.\n");
            mkdir($this->destination);
        }

        foreach ($titles as $k => $v) {
            $formattedFilePath = str_replace('file://', '', $v);
            $formattedFilePath = str_replace('%20', ' ', $formattedFilePath);
            $filePath          = end($formattedFilePath);
            $name              = reset($v);
            $name              = str_replace('/', '_', $name);
            $newFullPath       = $this->destination . "$name.m4a";

            if (file_exists($newFullPath)) {
                echo("$newFullPath already exists - skipping copy action\n");
            } else {
                copy($filePath, $newFullPath);
            }
        }

        $this->verifyCopy();
    }

    function getOriginalFileNameArray() {
        $xml          = simplexml_load_file($this->xmlPath);
        $json         = json_encode($xml);
        $titles       = $this->getJsonValueArrayByKey($json, 'string');
        $oldFileArray = [];

        foreach ($titles as $k => $v) {
            $formattedFilePath = str_replace('file://', '', $v);
            $formattedFilePath = str_replace('%20', ' ', $formattedFilePath);
            $filePath          = end($formattedFilePath);
            $name              = reset($v);
            $name              = str_replace('/', '_', $name);
            if (strpos($filePath, 'm4a') > -1) {
                array_push($oldFileArray, $name);
            }
        }

        return $oldFileArray;
    }

    function getNewFileArray() {
        $newDestination = $this->destination;
        $files          = glob("$newDestination*");
        $newFileArray   = [];
        foreach ($files as $file) {
            array_push($newFileArray, basename(str_replace('.m4a', '', $file)));
        }

        return $newFileArray;
    }

    function countFiles($dir, $extension = '*') {
        $files = glob($dir . $extension);

        return $files ? count($files) : 0;
    }

    function verifyCopy() {
        $oldFileArray   = $this->getOriginalFileNameArray();
        $oldFileCount   = count($oldFileArray);
        $newDestination = $this->destination;
        $newFileCount   = $this->countFiles($newDestination, '*m4a');

        $newFileArray = $this->getNewFileArray();
        sort($oldFileArray);
        sort($newFileArray);

        $pass = true;
        foreach ($oldFileArray as $file) {
            if (!in_array($file, $newFileArray)) {
                $pass = false;
            }
        }

        $result = $pass ? 'PASS - all files found' : 'FAIL - there are some missing files. Check the origin.';
        echo("\nOld File Count\t: $oldFileCount\nNew File Count\t: $newFileCount\nDestination\t: $newDestination\nCopy Result\t: $result\n");

        return $pass;
    }

    function getJsonValueArrayByKey($json, $arrayKey = '') {
        $returnValueArray = [];
        $jsonArray        = json_decode($json, true);
        $jsonIterator     = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($jsonArray),
            RecursiveIteratorIterator::SELF_FIRST);
        foreach ($jsonIterator as $key => $val) {
            if (empty($arrayKey)) {
                array_push($returnValueArray, $val);
            } else {
                if ($key === $arrayKey) {
                    array_push($returnValueArray, $val);
                }
            }
        }
        if (count($returnValueArray) === 0) {
            $returnValueArray[0] = '';
        }

        return $returnValueArray;
    }

    function getInputParams() {
        global $argv, $argc;
        $xmlPath         = '';
        $destinationPath = '';
        if ($argc < 4) {
            echo("\nERROR - Missing Input Parameters for Library XML Path and Destination.");
            echo("\nRe-run using the -x and -d flags. (Example: php copyVoiceMemos.php -x ~/Desktop/Library.xml -d ~/Documents/VoiceMemoCopy)\n");
            die();
        } else {
            for ($i = 0; $i < count($argv); $i++) {
                if (strpos($argv[$i], '-x') > -1) {
                    $xmlPath = $argv[$i + 1];
                } else if (strpos($argv[$i], '-d') > -1) {
                    $destinationPath = $argv[$i + 1];
                }
            }
        }

        $returnArray = [
            'xml'         => $xmlPath,
            'destination' => $destinationPath,
        ];

        return $returnArray;
    }
}

$class = new CopyVoiceMemos();
$class->run();

        }
    }

    return $oldFileArray;
}

function getNewFileArray() {
    $parameters     = getInputParams();
    $newDestination = $parameters['destination'];
    $files          = glob("$newDestination*");
    $newFileArray   = [];
    foreach ($files as $file) {
        array_push($newFileArray, basename(str_replace('.m4a', '', $file)));
    }

    return $newFileArray;
}

function countFiles($dir, $extension = '*') {
    $files = glob($dir . $extension);

    return $files ? count($files) : 0;
}

function verifyCopy() {
    $oldFileArray   = getOriginalFileNameArray();
    $oldFileCount   = count($oldFileArray);
    $parameters     = getInputParams();
    $newDestination = $parameters['destination'];
    $newFileCount   = countFiles($newDestination, '*m4a');

    $newFileArray = getNewFileArray();
    sort($oldFileArray);
    sort($newFileArray);

    $pass = true;
    foreach ($oldFileArray as $file) {
        if (!in_array($file, $newFileArray)) {
            $pass = false;
        }
    }

    $result = $pass ? 'PASS - all files found' : 'FAIL - there are some missing files. Check the origin.';
    echo("\nOld File Count\t: $oldFileCount\nNew File Count\t: $newFileCount\nDestination\t: $newDestination\nCopy Result\t: $result\n");

    return $pass;
}

function getJsonValueArrayByKey($json, $arrayKey = '') {
    $returnValueArray = [];
    $jsonArray        = json_decode($json, true);
    $jsonIterator     = new RecursiveIteratorIterator(
        new RecursiveArrayIterator($jsonArray),
        RecursiveIteratorIterator::SELF_FIRST);
    foreach ($jsonIterator as $key => $val) {
        if (empty($arrayKey)) {
            array_push($returnValueArray, $val);
        } else {
            if ($key === $arrayKey) {
                array_push($returnValueArray, $val);
            }
        }
    }
    if (count($returnValueArray) === 0) {
        $returnValueArray[0] = '';
    }

    return $returnValueArray;
}

function getInputParams() {
    global $argv, $argc;
    $xmlPath         = '';
    $destinationPath = '';
    if ($argc < 4) {
        echo("\nERROR - Missing Input Parameters for Library XML Path and Destination.");
        echo("\nRe-run using the -x and -d flags. (Example: php copyVoiceMemos.php -x ~/Desktop/Library.xml -d ~/Documents/VoiceMemoCopy)\n");
        die();
    } else {
        for ($i = 0; $i < count($argv); $i++) {
            if (strpos($argv[$i], '-x') > -1) {
                $xmlPath = $argv[$i + 1];
            } else if (strpos($argv[$i], '-d') > -1) {
                $destinationPath = $argv[$i + 1];
            }
        }
    }

    $returnArray = [
        'xml'         => $xmlPath,
        'destination' => $destinationPath,
    ];

    return $returnArray;
}

run();
