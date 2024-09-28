<?php
require_once 'db_connection.php';

try {
    // Create master_kata table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `master_kata` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `kata` varchar(255) NOT NULL,
        `clue` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "Table 'master_kata' created successfully.<br>";

    // Create point_game table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `point_game` (
        `id_point` int(11) NOT NULL AUTO_INCREMENT,
        `nama_user` varchar(255) NOT NULL,
        `total_point` int(20) NOT NULL,
        PRIMARY KEY (`id_point`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    echo "Table 'point_game' created successfully.<br>";

} catch(PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>