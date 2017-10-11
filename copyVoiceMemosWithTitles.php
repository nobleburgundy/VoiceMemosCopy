<?php

function run() {
    $parameters     = getInputParams();
    $xmlFilePath    = $parameters['xml'];
    $xml            = simplexml_load_file($xmlFilePath);
    $json           = json_encode($xml);
    $titles         = getJsonValueArrayByKey($json, 'string');
    $newDestination = $parameters['destination'];
    if (!file_exists($newDestination)) {
        echo("\nWARNING - '$newDestination' did not exist, so creating it.\n");
        mkdir($newDestination);
    }

    foreach ($titles as $k => $v) {
        $formattedFilePath = str_replace('file://', '', $v);
        $formattedFilePath = str_replace('%20', ' ', $formattedFilePath);
        $filePath          = end($formattedFilePath);
        $name              = reset($v);
        $name              = str_replace('/', '_', $name);
        $newFullPath       = $newDestination . "$name.m4a";

        if (file_exists($newFullPath)) {
            echo("$newFullPath already exists - skipping copy action\n");
        } else {
            if (!copy($filePath, $newFullPath)) {
                echo("\nERROR coping $filePath to $newFullPath");
            }
        }
    }

    verifyCopy();
}

function getOriginalFileNameArray() {
    $xml          = simplexml_load_file('/Users/z001ll6/Desktop/Library.xml');
    $json         = json_encode($xml);
    $titles       = getJsonValueArrayByKey($json, 'string');
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
    $newDestination = "/Users/z001ll6/Documents/m-projects/audio/voice_memo_copy/";
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
    $newDestination = "/Users/z001ll6/Documents/m-projects/audio/voice_memo_copy/";
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
