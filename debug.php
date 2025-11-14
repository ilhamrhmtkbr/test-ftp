<?php

$logFile = __DIR__ . '/debug.log';

if (file_exists($logFile)) {
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($logFile));
    echo "</pre>";

    echo "<hr>";
    echo "<a href='?clear=1'>Clear Log</a>";

    if (isset($_GET['clear'])) {
        file_put_contents($logFile, '');
        echo " - Log cleared!";
    }
} else {
    echo "Debug log not found!";
}
?>