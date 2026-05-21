<?php
    include_once __DIR__. "/../config/database.php";
    include_once __DIR__."/../models/modelHome.php";

    $highlights = getHighlights($conn);
    $divPrint = "<div class='highlights'><h2>Highlights</h2><div class='highlight-items'>";
    foreach ($highlights as $highlight) {
        $divPrint .= "
            <div class='highlight-item'>
                <h3>" . htmlspecialchars($highlight['title']) . "</h3>
                <p>" . htmlspecialchars($highlight['description']) . "</p>
                <p>Downloads: " . htmlspecialchars($highlight['download_count']) . "</p>
            </div>";
    }
    $divPrint .= "</div></div>";
?>
