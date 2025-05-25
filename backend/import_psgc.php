<?php
// PSGC Import Script
// Usage: Run this script once to import all PSGC data into your MySQL tables

ini_set('max_execution_time', 0);
ini_set('memory_limit', '512M');

require_once '../database/db_connect.php';

function importJson($pdo, $jsonPath, $table, $fields, $uniqueField) {
    if (!file_exists($jsonPath)) {
        echo "File not found: $jsonPath<br>";
        return [0,0];
    }
    $json = file_get_contents($jsonPath);
    $data = json_decode($json, true);
    if (!$data) {
        echo "Failed to decode $jsonPath<br>";
        return [0,0];
    }
    $inserted = 0;
    $updated = 0;
    foreach ($data as $row) {
        $placeholders = [];
        $values = [];
        foreach ($fields as $dbField => $jsonField) {
            $placeholders[] = ":$dbField";
            $values[":$dbField"] = $row[$jsonField] ?? null;
        }
        // Check if exists
        $sql = "SELECT id FROM $table WHERE $uniqueField = :unique LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':unique' => $row[$fields[$uniqueField]]]);
        if ($stmt->fetch()) {
            // Update
            $set = [];
            foreach ($fields as $dbField => $jsonField) {
                $set[] = "$dbField = :$dbField";
            }
            $sql2 = "UPDATE $table SET ".implode(',', $set)." WHERE $uniqueField = :unique";
            $values[':unique'] = $row[$fields[$uniqueField]];
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute($values);
            $updated++;
        } else {
            // Insert
            $sql2 = "INSERT INTO $table (".implode(',', array_keys($fields)).") VALUES (".implode(',', $placeholders).")";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute($values);
            $inserted++;
        }
    }
    return [$inserted, $updated];
}

$summary = [];

// Import regions
list($ins, $upd) = importJson(
    $pdo,
    '../assets/json/regions.json',
    'regions',
    [
        'region_code' => 'code',
        'name' => 'name',
        'country_id' => 'countryCode' // You may need to map this if not present
    ],
    'region_code'
);
$summary['regions'] = [$ins, $upd];

// Import provinces
list($ins, $upd) = importJson(
    $pdo,
    '../assets/json/provinces.json',
    'provinces',
    [
        'province_code' => 'code',
        'name' => 'name',
        'region_id' => 'regionCode' // You may need to map regionCode to region_id
    ],
    'province_code'
);
$summary['provinces'] = [$ins, $upd];

// Import cities
list($ins, $upd) = importJson(
    $pdo,
    '../assets/json/cities.json',
    'cities',
    [
        'city_code' => 'code',
        'name' => 'name',
        'province_id' => 'provinceCode' // You may need to map provinceCode to province_id
    ],
    'city_code'
);
$summary['cities'] = [$ins, $upd];

// Import barangays
list($ins, $upd) = importJson(
    $pdo,
    '../assets/json/barangays.json',
    'barangays',
    [
        'barangay_code' => 'code',
        'name' => 'name',
        'city_id' => 'cityCode' // You may need to map cityCode to city_id
    ],
    'barangay_code'
);
$summary['barangays'] = [$ins, $upd];

// Output summary
foreach ($summary as $table => $counts) {
    echo ucfirst($table).": Inserted $counts[0], Updated $counts[1]<br>";
}

echo "<br>Done!"; 