<?php
/**
 * [REDACTED-VENDOR] - WORDPRESS REST API CLONE
 * Exact replica of /wp-json/sage/v1/calculator/submit endpoint
 * 
 * This recreates the EXACT endpoint structure from REDACTED-COMPANY
 */

$submissions_file = '/tmp/redacted_wp_real_submissions.json';
$submissions = file_exists($submissions_file) ? json_decode(file_get_contents($submissions_file), true) : [];

// Handle REST API request: POST /wp-json/sage/v1/calculator/submit
if (strpos($_SERVER['REQUEST_URI'], '/wp-json/sage/v1/calculator/submit') !== false && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input (WordPress style)
    $input = json_decode(file_get_contents('php://input'), true);
    $calculator_type = $input['calculator_type'] ?? $_POST['calculator_type'] ?? '';
    $data = $input['data'] ?? $_POST['data'] ?? [];
    
    // VULNERABLE: No sanitization (same as [REDACTED-VENDOR])
    // But DO decode Base64 if it looks encoded (same as [REDACTED-VENDOR] likely does)
    if (base64_encode(base64_decode($calculator_type, true)) === $calculator_type && strlen($calculator_type) > 20) {
        // Looks like Base64, decode it (this is likely what [REDACTED-VENDOR] does)
        $calculator_type = base64_decode($calculator_type);
    }
    
    $record_key = sha1($calculator_type . time() . rand());
    
    // Store submission
    $submissions[] = array(
        'id' => count($submissions) + 1,
        'calculator_type' => $calculator_type,
        'data' => $data,
        'record_key' => $record_key,
        'date' => date('Y-m-d H:i:s'),
        'user' => 'anonymous'
    );
    file_put_contents($submissions_file, json_encode($submissions));
    
    // Return WordPress-style JSON response (same as [REDACTED-VENDOR])
    header('Content-Type: application/json; charset=UTF-8');
    header('X-WP-DoingItWrong: wp_send_json (since 5.5.0)');
    echo json_encode(array(
        'success' => true,
        'data' => array(
            'message' => 'Your Report is created successfully.',
            'mode' => 'Create',
            'record_key' => $record_key
        )
    ));
    exit;
}

// Handle admin panel: /wp-admin/admin.php?page=sage-calculator
if (isset($_GET['page']) && $_GET['page'] === 'sage-calculator') {
    ?>
<!DOCTYPE html>
Code
•
2.49 KiB




    <html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Calculator Submissions ‹ REDACTED-COMPANY — WordPress</title>
        <style>
            /* WordPress admin styles */
            :root{--wp-admin-theme-color:#2271b1}
            body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:13px;background:#f0f0f1}
            #wpadminbar{position:fixed;top:0;left:0;z-index:99990;height:32px;background:#1d2327}
            #adminmenuback{position:fixed;top:32px;left:0;bottom:0;width:160px;background:#1d2327}
            #wpcontent{margin-left:160px;margin-top:32px}
            .wp-list-table{width:100%;border-collapse:collapse;background:#fff}
            .wp-list-table thead th{background:#f6f7f7;padding:8px 12px;text-align:left}
            .wp-list-table tbody td{padding:8px 12px;border-bottom:1px solid #f0f0f1}
        </style>
    </head>
    <body>
        <div id="wpadminbar">
            <a href="#">Howdy, Admin</a>
            <a href="#">Calculator</a>
        </div>
        
        <div id="adminmenuback">
            <div id="adminmenu">
                <a href="#">📊 Dashboard</a>
                <a href="#">📝 Posts</a>
                <a href="#" class="current">🧮 Calculator</a>
            </div>
        </div>
        
        <div id="wpcontent">
            <div class="wrap">
                <h1>Calculator Submissions</h1>
                
                <table class="wp-list-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Calculator Data (RENDERED AS-IS - XSS EXECUTES HERE!)</th>
                            <th>Record Key</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($submissions) as $sub): ?>
                        <tr>
                            <td><?= $sub['id'] ?></td>
                            <!-- ⚠️ VULNERABLE: No esc_html() - XSS EXECUTES HERE! -->
                            <td><?= $sub['calculator_type'] ?></td>
                            <td><code><?= substr($sub['record_key'], 0, 16) ?>...</code></td>
                            <td><?= $sub['date'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
