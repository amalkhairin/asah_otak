<?php
require_once 'db_connection.php';

// Create tables
require_once 'create_tables.php';

$words = [
    ['kata' => 'LEMARI', 'clue' => 'Aku tempat menyimpan pakaian?'],
    ['kata' => 'KOMPUTER', 'clue' => 'Alat elektronik untuk mengolah data'],
    ['kata' => 'JENDELA', 'clue' => 'Tempat masuknya cahaya ke dalam ruangan'],
    ['kata' => 'KAMERA', 'clue' => 'Alat untuk mengambil gambar'],
    ['kata' => 'PAYUNG', 'clue' => 'Melindungi dari hujan dan panas']
];

try {
    $stmt = $pdo->prepare("INSERT INTO master_kata (kata, clue) VALUES (:kata, :clue)");

    foreach ($words as $word) {
        $stmt->execute($word);
    }

    echo "Seeder executed successfully!";
} catch(PDOException $e) {
    echo "Error executing seeder: " . $e->getMessage();
}
?>