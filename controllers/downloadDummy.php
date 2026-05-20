<?php
include __DIR__ . "/../config/database.php";

$id = intval($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT file_path FROM contents WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$content = $result->fetch_assoc();
$stmt->close();

if (!$content) {
    die("Content not found.");
}

$stmt = $conn->prepare("UPDATE contents SET download_count = download_count + 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: " . $content["file_path"]);
exit();
?>