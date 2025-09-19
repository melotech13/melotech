<?php
// Test favicon serving
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favicon Test - MeloTech</title>
    
    <!-- Test favicon with absolute URL -->
    <link rel="icon" href="/favicon.ico?v=<?php echo time(); ?>" type="image/x-icon">
    <link rel="shortcut icon" href="/favicon.ico?v=<?php echo time(); ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png?v=<?php echo time(); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png?v=<?php echo time(); ?>">
</head>
<body>
    <h1>Favicon Test Page</h1>
    <p>This page tests if the MeloTech favicon is being served correctly.</p>
    
    <h2>Test Results:</h2>
    <ul>
        <li>Favicon.ico exists: <?php echo file_exists('favicon.ico') ? '✅ Yes' : '❌ No'; ?></li>
        <li>Favicon-32x32.png exists: <?php echo file_exists('favicon-32x32.png') ? '✅ Yes' : '❌ No'; ?></li>
        <li>Favicon-16x16.png exists: <?php echo file_exists('favicon-16x16.png') ? '✅ Yes' : '❌ No'; ?></li>
        <li>Apple touch icon exists: <?php echo file_exists('apple-touch-icon.png') ? '✅ Yes' : '❌ No'; ?></li>
    </ul>
    
    <h2>Direct Links (click to test):</h2>
    <ul>
        <li><a href="/favicon.ico" target="_blank">favicon.ico</a></li>
        <li><a href="/favicon-32x32.png" target="_blank">favicon-32x32.png</a></li>
        <li><a href="/favicon-16x16.png" target="_blank">favicon-16x16.png</a></li>
        <li><a href="/apple-touch-icon.png" target="_blank">apple-touch-icon.png</a></li>
    </ul>
    
    <h2>Instructions:</h2>
    <ol>
        <li>Check if the MeloTech icon appears in the browser tab</li>
        <li>If not, try clicking the direct links above to see if the files are accessible</li>
        <li>Check browser developer tools (F12) → Network tab to see if favicon requests are successful</li>
        <li>Try hard refresh: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)</li>
    </ol>
    
    <h2>Debug Info:</h2>
    <p>Current time: <?php echo date('Y-m-d H:i:s'); ?></p>
    <p>Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
    <p>Document root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
</body>
</html>
