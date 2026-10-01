<?php

header("Content-Type: application/json");

$file = "data.json";

if (!file_exists($file)) {
    echo json_encode([]);
    exit;
}

$data = file_get_contents($file);

echo $data;
?>