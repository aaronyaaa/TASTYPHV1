<?php
header('Content-Type: application/json');

require_once '../datasbdb_connection.php'; // Your PDO connection

function getOrCreate($pdo, $table, $name, $codeField, $code, $extra = []) {
    // Check if exists
    $sql = "SELECT id FROM $table WHERE $codeField = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$code]);
    $row = $stmt->fetch();
    if ($row) return $row['id'];

    // Insert if not exists
    $fields = array_merge([$codeField, 'name'], array_keys($extra));
    $placeholders = array_fill(0, count($fields), '?');
    $values = array_merge([$code, $name], array_values($extra));
    $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    return $pdo->lastInsertId();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'No data received']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Region
    $region_id = getOrCreate(
        $pdo, 'regions', $data['region_name'], 'code', $data['region_code'], ['country_id' => 1]
    );

    // 2. Province
    $province_id = getOrCreate(
        $pdo, 'provinces', $data['province_name'], 'code', $data['province_code'], ['region_id' => $region_id]
    );

    // 3. City
    $city_id = getOrCreate(
        $pdo, 'cities', $data['city_name'], 'code', $data['city_code'], ['country_id' => 1]
    );

    // 4. Barangay
    $barangay_id = getOrCreate(
        $pdo, 'barangays', $data['barangay_name'], 'code', $data['barangay_code'], ['city_id' => $city_id]
    );

    // 5. Update user address
    $sql = "UPDATE users SET 
        region_id = ?, province_id = ?, city_id = ?, barangay_id = ?,
        postal_code = ?, streetname = ?
        WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $region_id, $province_id, $city_id, $barangay_id,
        $data['postal_code'], $data['address_line'], $data['user_id']
    ]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}