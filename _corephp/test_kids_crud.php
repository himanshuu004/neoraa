<?php
require_once 'config/config.php';
requireAdmin();

// Test database connection
try {
    $pdo->query("SELECT 1");
    $dbStatus = "✓ Connected";
} catch (Exception $e) {
    $dbStatus = "✗ Failed: " . $e->getMessage();
}

// Check kids table structure
try {
    $stmt = $pdo->query("DESCRIBE kids");
    $columns = $stmt->fetchAll();
    $tableStatus = "✓ Table exists";
} catch (Exception $e) {
    $tableStatus = "✗ Error: " . $e->getMessage();
}

// Count kids
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM kids");
    $count = $stmt->fetch();
    $kidsCount = $count['total'];
} catch (Exception $e) {
    $kidsCount = "Error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kids CRUD Test - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .test-card { margin-bottom: 1rem; }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4"><i class="fas fa-vial"></i> Kids CRUD Test Page</h2>
        
        <!-- Database Status -->
        <div class="card test-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">1. Database Connection Status</h5>
            </div>
            <div class="card-body">
                <p><strong>Database:</strong> neora_db</p>
                <p><strong>Connection:</strong> <span class="<?php echo strpos($dbStatus, '✓') !== false ? 'status-ok' : 'status-error'; ?>"><?php echo $dbStatus; ?></span></p>
                <p><strong>Kids Table:</strong> <span class="<?php echo strpos($tableStatus, '✓') !== false ? 'status-ok' : 'status-error'; ?>"><?php echo $tableStatus; ?></span></p>
                <p><strong>Total Kids:</strong> <span class="badge bg-info"><?php echo $kidsCount; ?></span></p>
            </div>
        </div>
        
        <!-- Table Structure -->
        <div class="card test-card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">2. Kids Table Structure</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                                <th>Null</th>
                                <th>Key</th>
                                <th>Default</th>
                                <th>Extra</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($columns as $col): ?>
                            <tr>
                                <td><?php echo $col['Field']; ?></td>
                                <td><?php echo $col['Type']; ?></td>
                                <td><?php echo $col['Null']; ?></td>
                                <td><?php echo $col['Key']; ?></td>
                                <td><?php echo $col['Default'] ?? 'NULL'; ?></td>
                                <td><?php echo $col['Extra']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- API Endpoints Test -->
        <div class="card test-card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">3. API Endpoints Test</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-primary mb-2 w-100" onclick="testGetAllKids()">
                            <i class="fas fa-list"></i> Test: Get All Kids
                        </button>
                        <button class="btn btn-success mb-2 w-100" onclick="testCreateKid()">
                            <i class="fas fa-plus"></i> Test: Create Kid
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-info mb-2 w-100" onclick="testUpdateKid()">
                            <i class="fas fa-edit"></i> Test: Update Kid
                        </button>
                        <button class="btn btn-danger mb-2 w-100" onclick="testDeleteKid()">
                            <i class="fas fa-trash"></i> Test: Delete Kid
                        </button>
                    </div>
                </div>
                <div id="apiTestResult" class="mt-3" style="display: none;">
                    <h6>Result:</h6>
                    <pre id="apiResult" class="bg-light p-3" style="max-height: 300px; overflow-y: auto;"></pre>
                </div>
            </div>
        </div>
        
        <!-- Live Kids List -->
        <div class="card test-card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">4. Live Kids Data</h5>
                <button class="btn btn-sm btn-light" onclick="refreshKidsList()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Parent Name</th>
                                <th>Contact</th>
                                <th>Case Type</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody id="kidsListBody">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status"></div> Loading...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Back to Dashboard -->
        <div class="text-center mt-4">
            <a href="admin/dashboard.php#kids" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        refreshKidsList();
    });
    
    function refreshKidsList() {
        $('#kidsListBody').html('<tr><td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</td></tr>');
        
        $.ajax({
            url: 'admin/api/get_all_kids.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success && res.data) {
                    var html = '';
                    res.data.forEach(function(kid) {
                        html += '<tr>';
                        html += '<td>' + kid.kid_id + '</td>';
                        html += '<td>' + (kid.kid_name || 'N/A') + '</td>';
                        html += '<td>' + (kid.age || 'N/A') + '</td>';
                        html += '<td>' + (kid.parent_name || 'N/A') + '</td>';
                        html += '<td>' + (kid.contact || 'N/A') + '</td>';
                        html += '<td>' + (kid.case_type || 'N/A') + '</td>';
                        html += '<td>' + (kid.created_at || 'N/A') + '</td>';
                        html += '</tr>';
                    });
                    $('#kidsListBody').html(html || '<tr><td colspan="7" class="text-center text-muted">No kids found</td></tr>');
                } else {
                    $('#kidsListBody').html('<tr><td colspan="7" class="text-center text-danger">Error: ' + (res.message || 'Unknown error') + '</td></tr>');
                }
            },
            error: function() {
                $('#kidsListBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading kids data</td></tr>');
            }
        });
    }
    
    function testGetAllKids() {
        $('#apiTestResult').show();
        $('#apiResult').text('Testing GET /admin/api/get_all_kids.php ...');
        
        $.ajax({
            url: 'admin/api/get_all_kids.php',
            method: 'GET',
            success: function(response) {
                $('#apiResult').text(JSON.stringify(response, null, 2));
            },
            error: function(xhr) {
                $('#apiResult').text('Error: ' + xhr.responseText);
            }
        });
    }
    
    function testCreateKid() {
        $('#apiTestResult').show();
        $('#apiResult').text('Testing POST /admin/api/create_kid.php ...');
        
        var testData = {
            name: 'Test Kid ' + Date.now(),
            age: 8,
            parent_name: 'Test Parent',
            contact: '1234567890',
            case_type: 'Test Case'
        };
        
        $.ajax({
            url: 'admin/api/create_kid.php',
            method: 'POST',
            data: testData,
            success: function(response) {
                $('#apiResult').text(JSON.stringify(response, null, 2));
                refreshKidsList();
            },
            error: function(xhr) {
                $('#apiResult').text('Error: ' + xhr.responseText);
            }
        });
    }
    
    function testUpdateKid() {
        $('#apiTestResult').show();
        $('#apiResult').text('Testing POST /admin/api/update_kid.php ...');
        
        // First, get a kid to update
        $.ajax({
            url: 'admin/api/get_all_kids.php',
            method: 'GET',
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success && res.data && res.data.length > 0) {
                    var kid = res.data[0];
                    var updateData = {
                        kid_id: kid.kid_id,
                        name: kid.kid_name + ' (Updated)',
                        age: kid.age || 10,
                        parent_name: kid.parent_name || 'Updated Parent',
                        contact: kid.contact || '9999999999',
                        case_type: 'Updated Case'
                    };
                    
                    $.ajax({
                        url: 'admin/api/update_kid.php',
                        method: 'POST',
                        data: updateData,
                        success: function(updateResponse) {
                            $('#apiResult').text(JSON.stringify(updateResponse, null, 2));
                            refreshKidsList();
                        },
                        error: function(xhr) {
                            $('#apiResult').text('Error: ' + xhr.responseText);
                        }
                    });
                } else {
                    $('#apiResult').text('No kids available to test update');
                }
            }
        });
    }
    
    function testDeleteKid() {
        if (!confirm('This will delete a test kid. Continue?')) return;
        
        $('#apiTestResult').show();
        $('#apiResult').text('Testing POST /admin/api/delete_kid.php ...');
        
        // First, create a test kid to delete
        $.ajax({
            url: 'admin/api/create_kid.php',
            method: 'POST',
            data: {
                name: 'Temp Kid to Delete',
                age: 5,
                parent_name: 'Temp Parent',
                contact: '0000000000',
                case_type: 'Temp'
            },
            success: function(createResponse) {
                var createRes = typeof createResponse === 'string' ? JSON.parse(createResponse) : createResponse;
                if (createRes.success && createRes.kid_id) {
                    $.ajax({
                        url: 'admin/api/delete_kid.php',
                        method: 'POST',
                        data: { id: createRes.kid_id },
                        success: function(deleteResponse) {
                            $('#apiResult').text(JSON.stringify(deleteResponse, null, 2));
                            refreshKidsList();
                        },
                        error: function(xhr) {
                            $('#apiResult').text('Error: ' + xhr.responseText);
                        }
                    });
                }
            }
        });
    }
    </script>
</body>
</html>
