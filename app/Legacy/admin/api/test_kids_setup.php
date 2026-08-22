<?php
/** Converted from core PHP — runs inside Laravel LegacyApiController with $pdo in scope. */
/** @var PDO $pdo */
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!defined('BASE_URL')) { define('BASE_URL', base_url()); }
if (!defined('SITE_NAME')) { define('SITE_NAME', site_name()); }

/**
 * Kids Table Setup Test Script
 * This script verifies that the kids table is properly configured
 * Run this from your browser: admin/api/test_kids_setup.php
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kids Table Setup Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem 0;
        }
        .test-card {
            margin-bottom: 1rem;
        }
        .result-success {
            color: #198754;
        }
        .result-error {
            color: #dc3545;
        }
        .result-warning {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-flask"></i> Kids Table Setup Test</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $allTestsPassed = true;
                        $results = [];
                        
                        // Test 1: Check if kids table exists
                        echo '<div class="test-card card">';
                        echo '<div class="card-body">';
                        echo '<h5><i class="fas fa-database"></i> Test 1: Kids Table Exists</h5>';
                        try {
                            $stmt = $pdo->query("SHOW TABLES LIKE 'kids'");
                            if ($stmt->rowCount() > 0) {
                                echo '<p class="result-success"><i class="fas fa-check-circle"></i> <strong>PASSED:</strong> Kids table exists</p>';
                                $results['table_exists'] = true;
                            } else {
                                echo '<p class="result-error"><i class="fas fa-times-circle"></i> <strong>FAILED:</strong> Kids table does not exist</p>';
                                echo '<p class="text-muted">Solution: Run database.sql to create the kids table</p>';
                                $results['table_exists'] = false;
                                $allTestsPassed = false;
                            }
                        } catch (Exception $e) {
                            echo '<p class="result-error"><i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                            $results['table_exists'] = false;
                            $allTestsPassed = false;
                        }
                        echo '</div></div>';
                        
                        // Test 2: Check required columns
                        if ($results['table_exists']) {
                            echo '<div class="test-card card">';
                            echo '<div class="card-body">';
                            echo '<h5><i class="fas fa-columns"></i> Test 2: Required Columns</h5>';
                            try {
                                $stmt = $pdo->query("SHOW COLUMNS FROM kids");
                                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                
                                $requiredColumns = [
                                    'kid_id' => 'Primary key',
                                    'kid_name' => 'Kid name',
                                    'age' => 'Kid age',
                                    'parent_name' => 'Parent name',
                                    'contact' => 'Contact number',
                                    'image_path' => 'Profile image path',
                                    'case_type' => 'Case/therapy type',
                                    'created_at' => 'Created timestamp',
                                    'updated_at' => 'Updated timestamp'
                                ];
                                
                                $missingColumns = [];
                                foreach ($requiredColumns as $col => $desc) {
                                    if (in_array($col, $columns)) {
                                        echo '<p class="result-success mb-1"><i class="fas fa-check"></i> ' . $col . ' <small class="text-muted">(' . $desc . ')</small></p>';
                                    } else {
                                        echo '<p class="result-error mb-1"><i class="fas fa-times"></i> ' . $col . ' <small class="text-muted">(' . $desc . ')</small></p>';
                                        $missingColumns[] = $col;
                                        $allTestsPassed = false;
                                    }
                                }
                                
                                if (empty($missingColumns)) {
                                    echo '<p class="result-success mt-2"><strong>PASSED:</strong> All required columns exist</p>';
                                    $results['columns_exist'] = true;
                                } else {
                                    echo '<p class="result-error mt-2"><strong>FAILED:</strong> Missing columns: ' . implode(', ', $missingColumns) . '</p>';
                                    echo '<p class="text-muted">Solution: Run docs/add_image_path_column.sql in phpMyAdmin</p>';
                                    $results['columns_exist'] = false;
                                }
                            } catch (Exception $e) {
                                echo '<p class="result-error"><i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                                $results['columns_exist'] = false;
                                $allTestsPassed = false;
                            }
                            echo '</div></div>';
                        }
                        
                        // Test 3: Check uploads directory
                        echo '<div class="test-card card">';
                        echo '<div class="card-body">';
                        echo '<h5><i class="fas fa-folder"></i> Test 3: Uploads Directory</h5>';
                        $uploadDir = public_path('uploads/kids').'/';
                        if (is_dir($uploadDir)) {
                            if (is_writable($uploadDir)) {
                                echo '<p class="result-success"><i class="fas fa-check-circle"></i> <strong>PASSED:</strong> Directory exists and is writable</p>';
                                echo '<p class="text-muted">Path: ' . realpath($uploadDir) . '</p>';
                                $results['upload_dir'] = true;
                            } else {
                                echo '<p class="result-warning"><i class="fas fa-exclamation-triangle"></i> <strong>WARNING:</strong> Directory exists but is not writable</p>';
                                echo '<p class="text-muted">Solution: chmod 0755 ' . realpath($uploadDir) . '</p>';
                                $results['upload_dir'] = false;
                            }
                        } else {
                            echo '<p class="result-warning"><i class="fas fa-exclamation-triangle"></i> <strong>WARNING:</strong> Directory does not exist (will be created on first upload)</p>';
                            echo '<p class="text-muted">Expected path: ' . realpath('../../uploads/') . '/kids/</p>';
                            $results['upload_dir'] = false;
                        }
                        echo '</div></div>';
                        
                        // Test 4: Test API endpoint
                        echo '<div class="test-card card">';
                        echo '<div class="card-body">';
                        echo '<h5><i class="fas fa-plug"></i> Test 4: API Endpoint</h5>';
                        try {
                            // Make internal API call
                            $apiUrl = 'get_all_kids.php';
                            if (file_exists($apiUrl)) {
                                echo '<p class="result-success"><i class="fas fa-check-circle"></i> <strong>PASSED:</strong> API file exists</p>';
                                echo '<p class="text-muted">File: ' . realpath($apiUrl) . '</p>';
                                $results['api_exists'] = true;
                                
                                // Try to fetch data
                                if ($results['table_exists'] && $results['columns_exist']) {
                                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM kids");
                                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    echo '<p class="result-success"><i class="fas fa-database"></i> Database query successful</p>';
                                    echo '<p class="text-muted">Found ' . $count . ' kid(s) in database</p>';
                                }
                            } else {
                                echo '<p class="result-error"><i class="fas fa-times-circle"></i> <strong>FAILED:</strong> API file not found</p>';
                                $results['api_exists'] = false;
                                $allTestsPassed = false;
                            }
                        } catch (Exception $e) {
                            echo '<p class="result-error"><i class="fas fa-times-circle"></i> <strong>ERROR:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                            $results['api_exists'] = false;
                            $allTestsPassed = false;
                        }
                        echo '</div></div>';
                        
                        // Test 5: PHP Configuration
                        echo '<div class="test-card card">';
                        echo '<div class="card-body">';
                        echo '<h5><i class="fas fa-cog"></i> Test 5: PHP Configuration</h5>';
                        
                        $uploadMaxSize = ini_get('upload_max_filesize');
                        $postMaxSize = ini_get('post_max_size');
                        $maxFileUploads = ini_get('max_file_uploads');
                        
                        echo '<p class="mb-1"><i class="fas fa-info-circle"></i> upload_max_filesize: <strong>' . $uploadMaxSize . '</strong></p>';
                        echo '<p class="mb-1"><i class="fas fa-info-circle"></i> post_max_size: <strong>' . $postMaxSize . '</strong></p>';
                        echo '<p class="mb-1"><i class="fas fa-info-circle"></i> max_file_uploads: <strong>' . $maxFileUploads . '</strong></p>';
                        
                        if ((int)$uploadMaxSize >= 10) {
                            echo '<p class="result-success mt-2"><strong>PASSED:</strong> PHP upload settings are adequate</p>';
                        } else {
                            echo '<p class="result-warning mt-2"><strong>WARNING:</strong> Upload size limit is small. Consider increasing in php.ini</p>';
                        }
                        echo '</div></div>';
                        
                        // Final Summary
                        echo '<div class="card bg-' . ($allTestsPassed ? 'success' : 'warning') . ' text-white">';
                        echo '<div class="card-body">';
                        echo '<h5><i class="fas fa-flag-checkered"></i> Test Summary</h5>';
                        if ($allTestsPassed) {
                            echo '<p class="mb-0"><strong>All critical tests passed!</strong> Your kids table is properly configured.</p>';
                            echo '<p class="mb-0 mt-2"><a href="../dashboard.php#kids" class="btn btn-light btn-sm"><i class="fas fa-arrow-right"></i> Go to Kids Dashboard</a></p>';
                        } else {
                            echo '<p class="mb-0"><strong>Some tests failed.</strong> Please review the failed tests above and follow the solutions provided.</p>';
                            echo '<p class="mb-0 mt-2"><a href="#" onclick="location.reload()" class="btn btn-light btn-sm"><i class="fas fa-sync"></i> Re-run Tests</a></p>';
                        }
                        echo '</div></div>';
                        ?>
                    </div>
                    <div class="card-footer text-muted">
                        <small><i class="fas fa-clock"></i> Test completed at <?php echo date('Y-m-d H:i:s'); ?></small>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="../dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
