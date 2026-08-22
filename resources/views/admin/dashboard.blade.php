@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<?php
// Ensure trainee tables exist
// Get admin info
$stmt = $pdo->prepare("SELECT *, profile_image FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ $siteName }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ $baseUrl }}assets/css/style.css">
    <style>
        .profile-picture-container {
            position: relative;
        }
        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #df5589;
            box-shadow: 0 2px 8px rgba(223,85,137,0.2);
        }
        .profile-picture-default {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #df5589 0%, #c44478 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            border: 3px solid #df5589;
            box-shadow: 0 2px 8px rgba(223,85,137,0.2);
        }
        #profilePictureUploadModal {
            display: flex;
        }
        /* Trainee Attendance Images Styles */
        .attendance-image-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 2px;
        }
        .attendance-image-thumbnail:hover {
            transform: scale(1.1);
            border-color: #df5589;
            box-shadow: 0 2px 8px rgba(223,85,137,0.3);
            z-index: 10;
            position: relative;
        }
        .attendance-images-container {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
            justify-content: flex-start;
        }
        .attendance-images-container .image-wrapper {
            position: relative;
            flex-shrink: 0;
        }
        .attendance-images-container .image-count-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #df5589;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
        }
        .attendance-images-container .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        .attendance-images-container .badge:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
        /* Responsive table styles */
        @media (max-width: 768px) {
            .attendance-image-thumbnail {
                width: 50px;
                height: 50px;
                max-width: 100%;
            }
            #traineeAttendanceTable {
                font-size: 0.875rem;
            }
            #traineeAttendanceTable th,
            #traineeAttendanceTable td {
                padding: 0.5rem 0.25rem;
            }
            /* Ensure Images column stays visible and doesn't overflow */
            #traineeAttendanceTable td:last-child {
                min-width: 80px;
            }
        }
        @media (max-width: 576px) {
            .attendance-image-thumbnail {
                width: 40px;
                height: 40px;
                max-width: 100%;
            }
            .attendance-images-container {
                gap: 2px;
            }
            #traineeAttendanceTable {
                font-size: 0.75rem;
            }
            #traineeAttendanceTable td:last-child {
                min-width: 70px;
            }
        }
        /* Trainees section responsive */
        @media (max-width: 768px) {
            #trainees .card-header {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.75rem;
            }
            #trainees .card-header h5 {
                margin-bottom: 0 !important;
            }
            #trainees .card-header .btn {
                align-self: flex-start;
            }
            #trainees .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -1rem;
                padding: 0 1rem;
            }
            #traineesTable,
            #traineeAttendanceTable {
                min-width: 500px;
            }
            .main-content .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }
        @media (max-width: 576px) {
            #trainees .table-responsive {
                margin: 0 -0.5rem;
                padding: 0 0.5rem;
            }
            #traineesTable {
                min-width: 450px;
            }
            #traineeAttendanceTable {
                min-width: 400px;
            }
        }
        /* View images modal - ensure images visible on mobile */
        @media (max-width: 576px) {
            #viewImageModal .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            #viewImagesContainer .card img {
                width: 100%;
                height: auto !important;
                max-height: 200px;
                object-fit: contain;
            }
        }
    </style>
</head>
<body>
    @include('admin.partials.navbar')
    
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Small Greeting and Time -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="background: linear-gradient(135deg, #df5589 0%, #c44478 100%); padding: 1rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(223,85,137,0.25);">
                <div>
                    <div class="d-block mb-1 fw-semibold" style="color: rgba(255,255,255,0.95); font-size: 1rem;">
                        <i class="fas fa-hand-sparkles me-2"></i>
                        <?php 
                        $hour = (int)date('H');
                        if ($hour < 12) {
                            echo 'Good Morning';
                        } elseif ($hour < 17) {
                            echo 'Good Afternoon';
                        } else {
                            echo 'Good Evening';
                        }
                        ?>, <span style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 1rem;">
                    <i class="fas fa-clock" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);"></i>
                    <span id="dashboardTime" style="font-size: 0.95rem; color: #fff; font-weight: 600;"></span>
                </div>
            </div>
            
            <!-- Content Sections - Shown based on URL hash -->
            <div id="content-sections">
                <!-- Personal Info Section -->
                <div class="content-section" id="personal">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="editPersonalInfoBtn">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- View Mode (Default) -->
                            <div id="personalInfoView">
                                <!-- Profile Picture Section -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold mb-3">Profile Picture</label>
                                        <div class="d-flex align-items-center gap-4">
                                            <div class="profile-picture-container">
                                                <?php 
                                                $profileImage = $admin['profile_image'] ?? null;
                                                if ($profileImage && file_exists(public_path($profileImage))) {
                                                    echo '<img src="' . htmlspecialchars($baseUrl . $profileImage) . '" alt="Profile Picture" class="profile-picture" id="profilePictureDisplay">';
                                                } else {
                                                    echo '<div class="profile-picture-default" id="profilePictureDisplay">';
                                                    echo '<i class="fas fa-user"></i>';
                                                    echo '</div>';
                                                }
                                                ?>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-primary" id="uploadProfilePictureBtn">
                                                    <i class="fas fa-upload"></i> Upload Photo
                                                </button>
                                                <div class="form-text mt-2">Allowed: JPG, PNG, JPEG (Max 5MB)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Username</label>
                                        <div class="info-value"><?php echo htmlspecialchars($admin['username']); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Role</label>
                                        <div class="info-value">
                                            <span class="badge bg-primary">Admin</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Account Created</label>
                                        <div class="info-value"><?php echo date('F d, Y', strtotime($admin['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Generate Apply Link Card -->
                            <div class="card mt-3 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="mb-2"><i class="fas fa-link me-2"></i>Generate Apply Form Link</h6>
                                    <p class="text-muted small mb-3">Create a unique application link that tracks submissions to your account.</p>
                                    <button type="button" class="btn btn-sm btn-primary" id="generateLinkBtn">
                                        <i class="fas fa-plus-circle me-1"></i>Generate Link
                                    </button>
                                    <div id="generatedLinkDisplay" style="display: none;" class="mt-3">
                                        <label class="form-label small fw-semibold">Your Apply Link</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control" id="generatedLinkInput" readonly>
                                            <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn">
                                                <i class="fas fa-copy"></i> Copy
                                            </button>
                                        </div>
                                        <small class="text-muted">Share this link to track applications generated by you.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Picture Upload Modal (Hidden) -->
                            <div id="profilePictureUploadModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center;">
                                <div style="background: white; padding: 2rem; border-radius: 0.5rem; max-width: 500px; width: 90%;">
                                    <h5 class="mb-3"><i class="fas fa-upload"></i> Upload Profile Picture</h5>
                                    <form id="profilePictureForm" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="profilePictureInput" class="form-label">Select Image</label>
                                            <input type="file" class="form-control" id="profilePictureInput" name="profile_picture" accept="image/jpeg,image/jpg,image/png" required>
                                            <div class="form-text">Only JPG, JPEG, and PNG files are allowed (Max 5MB)</div>
                                        </div>
                                        <div id="profilePicturePreview" class="mb-3" style="display: none;">
                                            <label class="form-label">Preview</label>
                                            <div class="text-center">
                                                <img id="previewImage" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;">
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary" id="saveProfilePictureBtn">
                                                <i class="fas fa-save"></i> Upload
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="cancelProfilePictureBtn">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Edit Form (Hidden by Default) -->
                            <div id="personalInfoEdit" style="display: none;">
                                <form id="personalInfoForm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="adminUsername" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="adminUsername" name="username" 
                                                   value="<?php echo htmlspecialchars($admin['username']); ?>" 
                                                   required>
                                            <div class="form-text">Your login username</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adminRole" class="form-label">Role</label>
                                            <input type="text" class="form-control" id="adminRole" 
                                                   value="Admin" readonly>
                                            <div class="form-text">Your account role</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adminPassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="adminPassword" name="password" 
                                                   placeholder="Leave blank to keep current password">
                                            <div class="form-text">Enter new password only if you want to change it</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adminConfirmPassword" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="adminConfirmPassword" 
                                                   placeholder="Confirm new password">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="adminCreated" class="form-label">Account Created</label>
                                            <input type="text" class="form-control" id="adminCreated" 
                                                   value="<?php echo date('F d, Y', strtotime($admin['created_at'])); ?>" readonly>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex gap-2 mt-3">
                                                <button type="submit" class="btn btn-primary" id="savePersonalInfoBtn">
                                                    <i class="fas fa-save"></i> Save Changes
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Manage Therapists Section -->
                <div class="content-section" id="therapists" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Therapist Management</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTherapistModal">
                                <i class="fas fa-plus"></i> Create Therapist
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="therapistsTable" class="table table-striped table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th style="min-width: 100px;">Username</th>
                                            <th style="min-width: 120px;">Name</th>
                                            <th style="min-width: 100px;">Contact</th>
                                            <th style="min-width: 150px;">Email</th>
                                            <th style="width: 100px;">Created</th>
                                            <th style="width: 100px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    try {
                                        // Fetch from therapist_profile table - show all therapists with profile data
                                        $stmt = $pdo->query("SELECT 
                                                                u.id as user_id,
                                                                u.username,
                                                                u.created_at,
                                                                tp.id as profile_id,
                                                                tp.name,
                                                                tp.contact,
                                                                tp.email,
                                                                tp.updated_at
                                                            FROM users u
                                                            INNER JOIN roles r ON u.role_id = r.id
                                                            LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                                                            WHERE r.role_name = 'therapist'
                                                            ORDER BY COALESCE(tp.id, u.id) DESC");
                                        $hasRows = false;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                                            $hasRows = true;
                                    ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['user_id']); ?></td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 120px;" title="<?php echo htmlspecialchars($row['username']); ?>">
                                                <?php echo htmlspecialchars($row['username']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?>">
                                                <?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 120px;" title="<?php echo htmlspecialchars($row['contact'] ?? 'N/A'); ?>">
                                                <?php echo htmlspecialchars($row['contact'] ?? 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?>">
                                                <?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td class="small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-info edit-therapist" data-id="<?php echo $row['user_id']; ?>" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger delete-therapist" data-id="<?php echo $row['user_id']; ?>" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                        if (!$hasRows) {
                                            echo '<tr><td colspan="7" class="text-center text-muted py-4">No therapists found. Click "Create Therapist" to add one.</td></tr>';
                                        }
                                    } catch (Exception $e) {
                                        echo '<tr><td colspan="7" class="text-center text-danger py-4">Error loading therapists: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Session Timing Section -->
                <div class="content-section" id="timetable" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Weekly Timetable Grid</h5>
                            <div>
                                <select id="therapistFilterGrid" class="form-select form-select-sm d-inline-block" style="width: auto;">
                                    <option value="">-- Select Therapist --</option>
                                    <?php
                                    $stmt = $pdo->query("SELECT u.id, tp.name, u.username 
                                                         FROM users u
                                                         LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                                                         JOIN roles r ON u.role_id = r.id
                                                         WHERE r.role_name = 'therapist'
                                                         ORDER BY tp.name, u.username");
                                    $therapistCount = 0;
                                    $firstTherapistId = null;
                                    while ($therapist = $stmt->fetch()):
                                        $therapistCount++;
                                        if ($therapistCount === 1) {
                                            $firstTherapistId = $therapist['id'];
                                        }
                                    ?>
                                    <option value="<?php echo $therapist['id']; ?>" <?php echo ($therapistCount === 1 && $therapistCount === 1) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($therapist['name'] ?: $therapist['username']); ?>
                                    </option>
                                    <?php 
                                    endwhile; 
                                    // Store first therapist ID for JavaScript
                                    if ($therapistCount === 1 && $firstTherapistId) {
                                        echo '<script>var autoLoadTherapistId = ' . $firstTherapistId . ';</script>';
                                    }
                                    ?>
                                </select>
                                <button class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                                    <i class="fas fa-calendar-plus"></i> Create Timetable
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="gridContainer">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                                    <p>Please select a therapist to view their timetable grid</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Kid Info Section -->
                <div class="content-section" id="kids" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="mb-0"><i class="fas fa-child"></i> Kids Information</h5>
                                <small class="text-muted">Total: <span id="totalKidsCount">0</span> kids</small>
                            </div>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKidModal">
                                <i class="fas fa-plus"></i> Add Kid
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="filterKidName" placeholder="Filter by name...">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control form-control-sm" id="filterKidAge" placeholder="Age...">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="filterKidCase" placeholder="Filter by case...">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-secondary w-100" id="clearKidFilters">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Kids Table -->
                            <div class="table-responsive">
                                <table id="kidsTable" class="table table-striped table-hover table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 70px;">S.No.</th>
                                            <th style="min-width: 120px;">Name</th>
                                            <th style="width: 60px;">Age</th>
                                            <th style="min-width: 120px;">Parent Name</th>
                                            <th style="min-width: 100px;">Contact</th>
                                            <th style="min-width: 100px;">Case Type</th>
                                            <th style="width: 120px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="kidsTableBody">
                                        <?php
                                        try {
                                            // Fetch all kids from database
                                            $stmt = $pdo->query("SELECT kid_id, kid_name, age, parent_name, contact, case_type FROM kids ORDER BY kid_id DESC");
                                            $kids = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            if (count($kids) > 0) {
                                                $serial = 1;
                                                foreach ($kids as $kid) {
                                                    echo '<tr>';
                                                    echo '<td class="fw-semibold">' . $serial++ . '</td>';
                                                    echo '<td>' . htmlspecialchars($kid['kid_name'] ?? 'N/A') . '</td>';
                                                    echo '<td>' . htmlspecialchars($kid['age'] ?? 'N/A') . '</td>';
                                                    echo '<td>' . htmlspecialchars($kid['parent_name'] ?? 'N/A') . '</td>';
                                                    echo '<td>' . htmlspecialchars($kid['contact'] ?? 'N/A') . '</td>';
                                                    echo '<td><span class="badge bg-info">' . htmlspecialchars($kid['case_type'] ?? 'General') . '</span></td>';
                                                    echo '<td class="text-center">';
                                                    echo '<div class="btn-group btn-group-sm" role="group">';
                                                    echo '<button class="btn btn-info edit-kid" data-id="' . $kid['kid_id'] . '" title="Edit">';
                                                    echo '<i class="fas fa-edit"></i>';
                                                    echo '</button>';
                                                    echo '<button class="btn btn-danger delete-kid" data-id="' . $kid['kid_id'] . '" title="Delete">';
                                                    echo '<i class="fas fa-trash"></i>';
                                                    echo '</button>';
                                                    echo '</div>';
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                echo '<tr><td colspan="7" class="text-center text-muted py-4">';
                                                echo '<i class="fas fa-users fa-2x mb-2 d-block"></i>';
                                                echo 'No kids found. Click "Add Kid" to add one.';
                                                echo '</td></tr>';
                                            }
                                        } catch (Exception $e) {
                                            echo '<tr><td colspan="7" class="text-center text-danger py-4">';
                                            echo '<i class="fas fa-exclamation-triangle"></i> ';
                                            echo 'Error loading kids: ' . htmlspecialchars($e->getMessage());
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Info Alert -->
                            <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Tip:</strong> You can upload profile images for kids. Supported formats: JPG, PNG, GIF (max 10MB).
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notice Board Section -->
                <div class="content-section" id="noticeboard" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Notice Board</h5>
                            <small class="text-muted">Total: <span id="totalNoticesCount">0</span> notices</small>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="createNoticeBtn">
                            <i class="fas fa-plus"></i> Create Notice
                        </button>
                    </div>
                    <div class="nd-sheet-card">
                        <div class="nd-sheet-toolbar">
                            <input type="text" id="noticeSearch" class="form-control form-control-sm" placeholder="Search title or content…">
                            <select id="noticeStatusFilter" class="form-select form-select-sm" style="max-width:160px;">
                                <option value="all">All statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div id="noticesContainer">
                            <div class="text-center py-4">
                                <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Loading notices...</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Admin Time Table Section -->
                <div class="content-section" id="admin-timetable" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Admin Time Table Management</h5>
                                <small class="text-muted">Total Sessions: <span id="totalSessionsCount">0</span></small>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-success btn-sm" id="exportTimetableBtn">
                                    <i class="fas fa-file-excel"></i> Export
                                </button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSessionModal">
                                    <i class="fas fa-plus"></i> Add Timetable
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" id="filterTherapistTimetable">
                                        <option value="">All Therapists</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT u.id, tp.name, u.username 
                                                             FROM users u
                                                             LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                                                             JOIN roles r ON u.role_id = r.id
                                                             WHERE r.role_name = 'therapist'
                                                             ORDER BY tp.name, u.username");
                                        while ($therapist = $stmt->fetch()):
                                        ?>
                                        <option value="<?php echo $therapist['id']; ?>">
                                            <?php echo htmlspecialchars($therapist['name'] ?: $therapist['username']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select form-select-sm" id="filterDayTimetable">
                                        <option value="">All Days</option>
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select form-select-sm" id="filterKidTimetable">
                                        <option value="">All Kids</option>
                                        <!-- Will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-secondary w-100" id="clearTimetableFilters">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Timetable List -->
                            <div class="table-responsive">
                                <table id="adminTimetableTable" class="table table-striped table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th style="min-width: 150px;">Therapist</th>
                                            <th style="width: 100px;">Day</th>
                                            <th style="width: 100px;">Time</th>
                                            <th style="min-width: 120px;">Kid</th>
                                            <th style="min-width: 100px;">Session Type</th>
                                            <th style="min-width: 150px;">Notes</th>
                                            <th style="width: 100px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminTimetableBody">
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-spinner fa-spin"></i> Loading timetable...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Manage Trainees Section -->
                <div class="content-section" id="trainees" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Trainee Management</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTraineeModal">
                                <i class="fas fa-plus"></i> Create Trainee
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="traineesTable" class="table table-striped table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody id="traineesTableBody">
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trainee Attendance Records -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> All Trainee Attendance Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="traineeAttendanceTable" class="table table-striped table-hover table-sm" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 10%;">Date</th>
                                            <th style="width: 15%;">Trainee</th>
                                            <th style="width: 15%;">Child Name</th>
                                            <th style="width: 35%;">Activity</th>
                                            <th style="width: 25%;" class="text-center">Images</th>
                                        </tr>
                                    </thead>
                                    <tbody id="traineeAttendanceTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-spinner fa-spin"></i> Loading records...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Manage Coordinators Section -->
                <div class="content-section" id="coordinators" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-user-tie"></i> Coordinator Management</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCoordinatorModal">
                                <i class="fas fa-plus"></i> Create Coordinator
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="coordinatorsTable" class="table table-striped table-hover table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Created</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coordinatorsTableBody">
                                        <!-- Loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    
    
    <!-- Create Therapist Modal -->
    @include('admin.partials.create_therapist_modal')
    
    <!-- Edit Therapist Modal -->
    @include('admin.partials.edit_therapist_modal')
    
    <!-- Create Session Modal -->
    @include('admin.partials.create_session_modal')
    
    <!-- Edit Session Modal -->
    @include('admin.partials.edit_session_modal')
    
    <!-- Add Kid Modal -->
    @include('admin.partials.add_kid_modal')
    
    <!-- Create / Edit Notice Modal -->
    <div class="modal fade" id="createNoticeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-bullhorn"></i> <span id="noticeModalTitle">Create Notice</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="noticeForm">
                        <input type="hidden" id="noticeId" name="id" value="">
                        <div class="mb-3">
                            <label for="noticeTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="noticeTitle" name="title" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="noticeContent" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="noticeContent" name="content" rows="6" required></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="noticePriority" class="form-label">Priority</label>
                                <select class="form-select" id="noticePriority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="noticeStatus" class="form-label">Status</label>
                                <select class="form-select" id="noticeStatus" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNoticeBtn">
                        <i class="fas fa-save"></i> Save Notice
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Kid Modal -->
    @include('admin.partials.edit_kid_modal')
    
    <!-- Create Trainee Modal -->
    <div class="modal fade" id="createTraineeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-graduation-cap"></i> Create Trainee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createTraineeForm">
                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact</label>
                            <input type="text" class="form-control" name="contact">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTraineeBtn">
                        <i class="fas fa-save"></i> Create Trainee
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- View Image Modal -->
    <div class="modal fade" id="viewImageModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-images"></i> Session Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <strong>Child:</strong> <span id="viewImageChild"></span><br>
                        <strong>Date:</strong> <span id="viewImageDate"></span>
                    </div>
                    <div id="viewImagesContainer" class="row g-3">
                        <!-- Images will be inserted here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Coordinator Modal -->
    <div class="modal fade" id="createCoordinatorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-tie"></i> Create Coordinator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCoordinatorForm">
                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCoordinatorBtn">
                        <i class="fas fa-save"></i> Create Coordinator
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="{{ $baseUrl }}assets/js/admin.js"></script>
    <script src="{{ $baseUrl }}assets/js/timetable.js"></script>
    <script>
        // Set BASE_URL for JavaScript
        const BASE_URL = '{{ $baseUrl }}';
        // Update IST time in real-time
        function updateISTTime() {
            const now = new Date();
            const options = { 
                timeZone: 'Asia/Kolkata',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = now.toLocaleString('en-IN', options);
            const istTimeEl = document.getElementById('istTime');
            if (istTimeEl) {
                istTimeEl.textContent = timeString;
            }
        }
        updateISTTime();
        setInterval(updateISTTime, 1000);
        
        // Update dashboard time
        function updateDashboardTime() {
            const now = new Date();
            const options = { 
                timeZone: 'Asia/Kolkata',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = now.toLocaleString('en-IN', options);
            const dashboardTimeEl = document.getElementById('dashboardTime');
            if (dashboardTimeEl) {
                dashboardTimeEl.textContent = timeString;
            }
        }
        updateDashboardTime();
        setInterval(updateDashboardTime, 1000);
        
        // Show content based on URL hash
        function showContentSection() {
            const hash = (window.location.hash || '#personal').replace(/^#/, '') || 'personal';
            $('.content-section').hide();
            const targetEl = document.getElementById(hash);
            if (targetEl) {
                $(targetEl).show();
            } else {
                $('#personal').show();
            }
            
            if (hash === 'noticeboard') {
                loadNotices();
            }
            
            // Initialize DataTable for therapists if needed
            if (hash === 'therapists') {
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable('#therapistsTable')) {
                        $('#therapistsTable').DataTable().destroy();
                    }
                    $('#therapistsTable').DataTable({
                        responsive: {
                            details: {
                                type: 'column',
                                target: 'tr'
                            }
                        },
                        pageLength: 10,
                        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        order: [[0, 'desc']],
                        scrollX: true,
                        autoWidth: false,
                        columnDefs: [
                            { 
                                targets: [6], 
                                orderable: false, 
                                searchable: false,
                                responsivePriority: 1
                            },
                            { 
                                targets: [0], 
                                responsivePriority: 2,
                                width: '60px'
                            },
                            { 
                                targets: [1, 2, 3, 4], 
                                responsivePriority: 3
                            },
                            { 
                                targets: [5], 
                                responsivePriority: 4
                            }
                        ],
                        language: {
                            search: "Search:",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ therapists",
                            infoEmpty: "No therapists found",
                            infoFiltered: "(filtered from _MAX_ total therapists)"
                        },
                        destroy: true
                    });
                }, 200);
            }
        }
        
        // Show content on page load
        $(document).ready(function() {
            showContentSection();
            // Load timetable if it's the active section
            if (window.location.hash === '#timetable' || (!window.location.hash && $('#timetable').is(':visible'))) {
                loadTimetableGrid();
            }
        });
        
        // Show content when hash changes
        $(window).on('hashchange', function() {
            showContentSection();
            // Load timetable if it's the active section
            if (window.location.hash === '#timetable') {
                loadTimetableGrid();
            }
        });
        
        // Auto-load grid when timetable section is shown
        function loadTimetableGrid() {
            if ($('#timetable').is(':visible')) {
                setTimeout(function() {
                    if ($('#therapistFilterGrid').val()) {
                        $('#therapistFilterGrid').trigger('change');
                    }
                }, 200);
            }
        }
        
        // Load timetable when section becomes visible
        const timetableObserver = new MutationObserver(function(mutations) {
            if ($('#timetable').is(':visible')) {
                loadTimetableGrid();
            }
        });
        
        if (document.getElementById('timetable')) {
            timetableObserver.observe(document.getElementById('timetable'), {
                attributes: true,
                attributeFilter: ['style']
            });
        }
        
        // Personal Info Edit Functionality
        $('#editPersonalInfoBtn').on('click', function() {
            $('#personalInfoView').hide();
            $('#personalInfoEdit').show();
            $('#editPersonalInfoBtn').hide();
        });
        
        $('#cancelEditBtn').on('click', function() {
            $('#personalInfoEdit').hide();
            $('#personalInfoView').show();
            $('#editPersonalInfoBtn').show();
            // Reset form to original values
            $('#adminUsername').val('<?php echo htmlspecialchars($admin['username']); ?>');
            $('#adminPassword').val('');
            $('#adminConfirmPassword').val('');
        });
        
        $('#personalInfoForm').on('submit', function(e) {
            e.preventDefault();
            
            const password = $('#adminPassword').val();
            const confirmPassword = $('#adminConfirmPassword').val();
            
            if (password && password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            const formData = {
                username: $('#adminUsername').val(),
                password: password || ''
            };
            
            $.ajax({
                url: 'api/update-admin',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Personal information updated successfully!');
                        // Hide edit form and show view
                        $('#personalInfoEdit').hide();
                        $('#personalInfoView').show();
                        $('#editPersonalInfoBtn').show();
                        // Reload page to show updated info
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error updating personal information. Please try again.');
                }
            });
        });
        
        // ============================================================================
        // KIDS CRUD OPERATIONS
        // ============================================================================
        
        // Edit Kid Button Click
        $(document).on('click', '.edit-kid', function() {
            var kidId = $(this).data('id');
            console.log('Editing kid:', kidId);
            
            // Fetch kid data
            $.ajax({
                url: 'api/get-kid?id=' + kidId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Kid data:', response);
                    if (response.success && response.data) {
                        var kid = response.data;
                        // Fill form with kid data
                        $('#editKidId').val(kid.kid_id);
                        $('#editKidName').val(kid.kid_name || '');
                        $('#editKidAge').val(kid.age || '');
                        $('#editKidParentName').val(kid.parent_name || '');
                        $('#editKidContact').val(kid.contact || '');
                        $('#editKidCase').val(kid.case_type || '');
                        // Show modal
                        $('#editKidModal').modal('show');
                    } else {
                        alert('Error loading kid data: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error loading kid data. Please try again.');
                }
            });
        });
        
        // Add Kid Form Submit
        $('#addKidForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Adding new kid...');
            
            var formData = {
                name: $('#kidName').val(),
                age: $('#kidAge').val() || null,
                parent_name: $('#kidParentName').val() || null,
                contact: $('#kidContact').val() || null,
                case_type: $('#kidCase').val() || null
            };
            
            $.ajax({
                url: 'api/create-kid',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Add response:', response);
                    if (response.success) {
                        alert('✓ Kid added successfully!');
                        $('#addKidModal').modal('hide');
                        $('#addKidForm')[0].reset();
                        // Reload page to show new kid
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to add kid'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error adding kid. Please try again.');
                }
            });
        });
        
        // Edit Kid Form Submit
        $('#editKidForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Updating kid...');
            
            var formData = {
                kid_id: $('#editKidId').val(),
                name: $('#editKidName').val(),
                age: $('#editKidAge').val() || null,
                parent_name: $('#editKidParentName').val() || null,
                contact: $('#editKidContact').val() || null,
                case_type: $('#editKidCase').val() || null
            };
            
            $.ajax({
                url: 'api/update-kid',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('Update response:', response);
                    if (response.success) {
                        alert('✓ Kid updated successfully!');
                        $('#editKidModal').modal('hide');
                        // Reload page to show updated data
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to update kid'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error updating kid. Please try again.');
                }
            });
        });
        
        // Delete Kid Button Click
        $(document).on('click', '.delete-kid', function() {
            var kidId = $(this).data('id');
            var kidName = $(this).closest('tr').find('td:eq(1)').text();
            
            if (!confirm('Are you sure you want to delete "' + kidName + '"?\n\nThis action cannot be undone.')) {
                return;
            }
            
            console.log('Deleting kid:', kidId);
            
            $.ajax({
                url: 'api/delete-kid',
                method: 'POST',
                data: { id: kidId },
                dataType: 'json',
                success: function(response) {
                    console.log('Delete response:', response);
                    if (response.success) {
                        alert('✓ Kid deleted successfully!');
                        // Reload page to reflect deletion
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to delete kid'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error deleting kid. Please try again.');
                }
            });
        });
        
        // Update kids count when section is shown
        $(window).on('hashchange load', function() {
            if (window.location.hash === '#kids') {
                // Update kids count
                var kidCount = $('#kidsTableBody tr').length;
                if ($('#kidsTableBody tr td[colspan]').length === 0) {
                    $('#totalKidsCount').text(kidCount);
                } else {
                    $('#totalKidsCount').text('0');
                }
                
                // Initialize DataTable - destroy existing instance first
                try {
                    if ($.fn.DataTable.isDataTable('#kidsTable')) {
                        $('#kidsTable').DataTable().destroy();
                    }
                    $('#kidsTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        order: [[1, 'asc']],
                        columnDefs: [
                            { targets: 0, orderable: false, searchable: false },
                            { targets: [6], orderable: false, searchable: false }
                        ],
                        drawCallback: function () {
                            var api = this.api();
                            var start = api.page.info().start;
                            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                                cell.innerHTML = start + i + 1;
                            });
                        },
                        language: {
                            search: "Search kids:",
                            lengthMenu: "Show _MENU_ kids per page",
                            info: "Showing _START_ to _END_ of _TOTAL_ kids",
                            infoEmpty: "No kids available",
                            emptyTable: "No kids found. Click 'Add Kid' to add one."
                        },
                        destroy: true
                    });
                    console.log('DataTable initialized for kids table');
                } catch (e) {
                    console.error('Error initializing DataTable:', e);
                }
            }
        });
        
        // Profile Picture Upload Functionality
        $('#uploadProfilePictureBtn').on('click', function() {
            $('#profilePictureUploadModal').show();
            $('#profilePictureInput').val('');
            $('#profilePicturePreview').hide();
        });
        
        $('#cancelProfilePictureBtn').on('click', function() {
            $('#profilePictureUploadModal').hide();
            $('#profilePictureInput').val('');
            $('#profilePicturePreview').hide();
        });
        
        // Close modal when clicking outside
        $('#profilePictureUploadModal').on('click', function(e) {
            if ($(e.target).is('#profilePictureUploadModal')) {
                $(this).hide();
                $('#profilePictureInput').val('');
                $('#profilePicturePreview').hide();
            }
        });
        
        // Preview image before upload
        $('#profilePictureInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, JPEG, and PNG images are allowed.');
                    $(this).val('');
                    $('#profilePicturePreview').hide();
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit.');
                    $(this).val('');
                    $('#profilePicturePreview').hide();
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                    $('#profilePicturePreview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#profilePicturePreview').hide();
            }
        });
        
        // Handle profile picture form submission
        $('#profilePictureForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Disable submit button
            $('#saveProfilePictureBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
            
            $.ajax({
                url: 'api/upload-profile-picture',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update profile picture display
                        if (response.image_path) {
                            $('#profilePictureDisplay').replaceWith(
                                '<img src="../' + response.image_path + '" alt="Profile Picture" class="profile-picture" id="profilePictureDisplay">'
                            );
                        }
                        
                        // Close modal
                        $('#profilePictureUploadModal').hide();
                        $('#profilePictureInput').val('');
                        $('#profilePicturePreview').hide();
                        
                        alert('Profile picture uploaded successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error uploading profile picture. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = 'Error: ' + xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    // Re-enable submit button
                    $('#saveProfilePictureBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Upload');
                }
            });
        });
        
        // Notice Board Functionality
        let noticeModal = null;
        let noticesCache = [];
        const noticeApi = function (action) {
            const base = (typeof BASE_URL === 'string' ? BASE_URL : '/').replace(/\/?$/, '/');
            return base + 'admin/api/' + action;
        };

        function getNoticeModal() {
            const el = document.getElementById('createNoticeModal');
            if (!el) return null;
            if (el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
            noticeModal = bootstrap.Modal.getOrCreateInstance(el);
            return noticeModal;
        }

        function loadNotices() {
            $('#noticesContainer').html(
                '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="text-muted mt-2 mb-0">Loading notices...</p></div>'
            );
            $.ajax({
                url: noticeApi('get-notices'),
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        noticesCache = Array.isArray(response.data) ? response.data : [];
                        displayNotices(noticesCache);
                    } else {
                        $('#totalNoticesCount').text('0');
                        $('#noticesContainer').html('<div class="alert alert-danger m-3 mb-3">Error loading notices: ' + escapeHtml(response.message || 'Unknown error') + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#totalNoticesCount').text('0');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error loading notices. Please try again.';
                    $('#noticesContainer').html('<div class="alert alert-danger m-3 mb-3">' + escapeHtml(msg) + '</div>');
                }
            });
        }

        function noticeMatchesFilters(notice, query, status) {
            if (status && status !== 'all' && String(notice.status || '') !== status) {
                return false;
            }
            if (!query) return true;
            var hay = ((notice.title || '') + ' ' + (notice.content || '')).toLowerCase();
            return hay.indexOf(query) !== -1;
        }

        function displayNotices(notices) {
            notices = Array.isArray(notices) ? notices : [];
            var query = ($('#noticeSearch').val() || '').toLowerCase().trim();
            var status = $('#noticeStatusFilter').val() || 'all';
            var filtered = notices.filter(function (notice) {
                return noticeMatchesFilters(notice, query, status);
            });

            $('#totalNoticesCount').text(notices.length);

            if (filtered.length === 0) {
                var emptyMsg = notices.length === 0
                    ? 'No notices yet. Click “Create Notice” to post one.'
                    : 'No notices match this search.';
                $('#noticesContainer').html('<div class="text-center text-muted py-5"><i class="fas fa-bullhorn fa-2x mb-2 d-block"></i>' + emptyMsg + '</div>');
                return;
            }

            var html = '<div class="nd-sheet-wrap"><table class="table nd-sheet table-hover align-middle mb-0"><thead><tr>' +
                '<th style="width:70px;">S.No.</th>' +
                '<th>Title</th>' +
                '<th>Content</th>' +
                '<th style="width:110px;">Priority</th>' +
                '<th style="width:100px;">Status</th>' +
                '<th style="width:170px;">Posted</th>' +
                '<th style="width:120px;" class="text-center">Actions</th>' +
                '</tr></thead><tbody>';

            filtered.forEach(function(notice, index) {
                var priority = String(notice.priority || 'medium').toLowerCase();
                var priorityClass = { high: 'danger', medium: 'warning', low: 'info' }[priority] || 'secondary';
                var statusBadge = notice.status === 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
                var createdDate = notice.created_at
                    ? new Date(notice.created_at).toLocaleDateString('en-IN', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
                    : '—';
                var content = String(notice.content || '');
                var preview = content.length > 90 ? content.substring(0, 90) + '…' : content;

                html += '<tr>' +
                    '<td class="fw-semibold">' + (index + 1) + '</td>' +
                    '<td><div class="cell-strong">' + escapeHtml(notice.title || '') + '</div>' +
                    '<div class="cell-muted">By ' + escapeHtml(notice.created_by_name || 'Admin') + '</div></td>' +
                    '<td class="cell-muted">' + escapeHtml(preview).replace(/\n/g, ' ') + '</td>' +
                    '<td><span class="badge bg-' + priorityClass + '">' + escapeHtml(priority.toUpperCase()) + '</span></td>' +
                    '<td>' + statusBadge + '</td>' +
                    '<td class="cell-nowrap cell-muted">' + createdDate + '</td>' +
                    '<td class="text-center"><div class="btn-group btn-group-sm">' +
                    '<button type="button" class="btn btn-info edit-notice" data-id="' + notice.id + '" title="Edit"><i class="fas fa-edit"></i></button>' +
                    '<button type="button" class="btn btn-danger delete-notice" data-id="' + notice.id + '" title="Delete"><i class="fas fa-trash"></i></button>' +
                    '</div></td></tr>';
            });

            html += '</tbody></table></div>';
            $('#noticesContainer').html(html);
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text == null ? '' : text).replace(/[&<>"']/g, m => map[m]);
        }

        $('#noticeSearch, #noticeStatusFilter').on('input change', function () {
            displayNotices(noticesCache);
        });

        $('#createNoticeBtn').on('click', function() {
            $('#noticeForm')[0].reset();
            $('#noticeId').val('');
            $('#noticeModalTitle').text('Create Notice');
            getNoticeModal().show();
        });

        $('#saveNoticeBtn').on('click', function() {
            const formData = {
                id: $('#noticeId').val(),
                title: $.trim($('#noticeTitle').val()),
                content: $.trim($('#noticeContent').val()),
                priority: $('#noticePriority').val(),
                status: $('#noticeStatus').val()
            };

            if (!formData.title || !formData.content) {
                alert('Title and content are required!');
                return;
            }

            const isUpdate = !!formData.id;
            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: noticeApi(isUpdate ? 'update-notice' : 'create-notice'),
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        getNoticeModal().hide();
                        loadNotices();
                    } else {
                        alert('Error: ' + (response.message || 'Could not save notice'));
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error saving notice. Please try again.';
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Notice');
                }
            });
        });

        $(document).on('click', '.edit-notice', function() {
            const noticeId = $(this).data('id');
            const notice = noticesCache.find(function (n) { return String(n.id) === String(noticeId); });
            if (!notice) {
                alert('Notice not found. Refresh and try again.');
                return;
            }
            $('#noticeId').val(notice.id);
            $('#noticeTitle').val(notice.title);
            $('#noticeContent').val(notice.content);
            $('#noticePriority').val(notice.priority || 'medium');
            $('#noticeStatus').val(notice.status || 'active');
            $('#noticeModalTitle').text('Edit Notice');
            getNoticeModal().show();
        });

        $(document).on('click', '.delete-notice', function() {
            if (!confirm('Are you sure you want to delete this notice?')) {
                return;
            }
            const noticeId = $(this).data('id');
            $.ajax({
                url: noticeApi('delete-notice'),
                method: 'POST',
                data: { id: noticeId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadNotices();
                    } else {
                        alert('Error: ' + (response.message || 'Could not delete notice'));
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error deleting notice. Please try again.';
                    alert(msg);
                }
            });
        });
        
        // Generate Apply Link
        $('#generateLinkBtn').on('click', function() {
            var userId = <?php echo (int)$_SESSION['user_id']; ?>;
            var baseUrl = '{{ $baseUrl }}';
            var applyLink = @json(route('apply')) + '?ref=' + userId;
            $('#generatedLinkInput').val(applyLink);
            $('#generatedLinkDisplay').slideDown();
        });
        
        // Copy link to clipboard
        $('#copyLinkBtn').on('click', function() {
            var input = document.getElementById('generatedLinkInput');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            $(this).html('<i class="fas fa-check"></i> Copied!');
            setTimeout(function() {
                $('#copyLinkBtn').html('<i class="fas fa-copy"></i> Copy');
            }, 2000);
        });
        
        // Load trainees when section is shown
        function loadTrainees() {
            $.ajax({
                url: 'api/get-trainees',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.data.length === 0) {
                            html = '<tr><td colspan="6" class="text-center text-muted py-4">No trainees found.</td></tr>';
                        } else {
                            response.data.forEach(function(trainee) {
                                html += `
                                    <tr>
                                        <td>${trainee.user_id}</td>
                                        <td>${escapeHtml(trainee.username)}</td>
                                        <td>${escapeHtml(trainee.name || 'N/A')}</td>
                                        <td>${escapeHtml(trainee.contact || 'N/A')}</td>
                                        <td>${escapeHtml(trainee.email || 'N/A')}</td>
                                        <td>${new Date(trainee.created_at).toLocaleDateString('en-IN')}</td>
                                    </tr>
                                `;
                            });
                        }
                        $('#traineesTableBody').html(html);
                        
                        // Initialize DataTable
                        if ($.fn.DataTable.isDataTable('#traineesTable')) {
                            $('#traineesTable').DataTable().destroy();
                        }
                        $('#traineesTable').DataTable({
                            pageLength: 10,
                            order: [[0, 'desc']],
                            responsive: true,
                            columnDefs: [
                                { responsivePriority: 1, targets: 0 },
                                { responsivePriority: 2, targets: 1 },
                                { responsivePriority: 3, targets: 2 },
                                { responsivePriority: 4, targets: [3, 4] },
                                { responsivePriority: 5, targets: 5 }
                            ]
                        });
                    }
                }
            });
        }
        
        // Load trainee attendance
        function loadTraineeAttendance() {
            $.ajax({
                url: 'api/get-all-trainee-attendance',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Destroy existing DataTable first
                        if ($.fn.DataTable.isDataTable('#traineeAttendanceTable')) {
                            $('#traineeAttendanceTable').DataTable().destroy();
                            $('#traineeAttendanceTableBody').empty();
                        }
                        
                        let html = '';
                        if (!response.data || response.data.length === 0) {
                            html = '<tr><td colspan="5" class="text-center text-muted py-4">No attendance records found.</td></tr>';
                        } else {
                            response.data.forEach(function(record) {
                                // Debug: Log record data
                                console.log('Record:', {
                                    id: record.id,
                                    session_image: record.session_image,
                                    child_name: record.child_name
                                });
                                
                                // Process multiple image paths
                                let imagePaths = [];
                                if (record.image_paths && Array.isArray(record.image_paths) && record.image_paths.length > 0) {
                                    record.image_paths.forEach(function(path) {
                                        if (path && path.trim() !== '') {
                                            if (path.startsWith('http')) {
                                                imagePaths.push(path);
                                            } else {
                                                const baseUrl = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
                                                const imagePathClean = path.startsWith('/') ? path.substring(1) : path;
                                                imagePaths.push(baseUrl + imagePathClean);
                                            }
                                        }
                                    });
                                }
                                
                                // Create image thumbnails HTML
                                let imagesHtml = '';
                                if (imagePaths.length > 0) {
                                    imagesHtml = '<div class="attendance-images-container">';
                                    // Show first 3 images as thumbnails, rest as count badge
                                    const maxVisible = 3;
                                    const visibleImages = imagePaths.slice(0, maxVisible);
                                    const remainingCount = imagePaths.length - maxVisible;
                                    const imagePathsJson = JSON.stringify(imagePaths).replace(/'/g, "\\'");
                                    
                                    visibleImages.forEach(function(imagePath, index) {
                                        const isFirstWithMore = (index === 0 && imagePaths.length > maxVisible);
                                        imagesHtml += `
                                            <div class="image-wrapper" style="position: relative;">
                                                <img src="${imagePath}" 
                                                     alt="Session Image ${index + 1}" 
                                                     class="attendance-image-thumbnail"
                                                     onclick="viewAttendanceImages(${imagePathsJson}, '${escapeHtml(record.child_name || 'N/A')}', '${new Date(record.session_date).toLocaleDateString('en-IN')}')"
                                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\\'http://www.w3.org/2000/svg\\' width=\\'60\\' height=\\'60\\'%3E%3Crect fill=\\'%23ddd\\' width=\\'60\\' height=\\'60\\'/%3E%3Ctext fill=\\'%23999\\' font-family=\\'sans-serif\\' font-size=\\'10\\' x=\\'50%25\\' y=\\'50%25\\' text-anchor=\\'middle\\' dominant-baseline=\\'middle\\'%3EBroken%3C/text%3E%3C/svg%3E';">
                                                ${isFirstWithMore ? `<span class="image-count-badge">+${remainingCount}</span>` : ''}
                                            </div>
                                        `;
                                    });
                                    
                                    // Add total count indicator if more than maxVisible
                                    if (imagePaths.length > maxVisible) {
                                        imagesHtml += `<div class="ms-2 d-inline-flex align-items-center">
                                            <span class="badge bg-info" style="cursor: pointer;" onclick="viewAttendanceImages(${imagePathsJson}, '${escapeHtml(record.child_name || 'N/A')}', '${new Date(record.session_date).toLocaleDateString('en-IN')}')">
                                                <i class="fas fa-images"></i> ${imagePaths.length} images
                                            </span>
                                        </div>`;
                                    } else if (imagePaths.length > 1) {
                                        imagesHtml += `<div class="ms-2 d-inline-flex align-items-center">
                                            <span class="badge bg-secondary" style="cursor: pointer;" onclick="viewAttendanceImages(${imagePathsJson}, '${escapeHtml(record.child_name || 'N/A')}', '${new Date(record.session_date).toLocaleDateString('en-IN')}')">
                                                <i class="fas fa-images"></i> ${imagePaths.length}
                                            </span>
                                        </div>`;
                                    }
                                    
                                    imagesHtml += '</div>';
                                } else {
                                    imagesHtml = '<span class="text-muted small">No images</span>';
                                }
                                
                                html += `
                                    <tr>
                                        <td>${new Date(record.session_date).toLocaleDateString('en-IN')}</td>
                                        <td>${escapeHtml(record.trainee_name || record.trainee_username || 'N/A')}</td>
                                        <td>${escapeHtml(record.child_name || 'N/A')}</td>
                                        <td>${escapeHtml((record.activity_description || '').substring(0, 50))}${(record.activity_description || '').length > 50 ? '...' : ''}</td>
                                        <td class="text-center">${imagesHtml}</td>
                                    </tr>
                                `;
                            });
                        }
                        
                        // Populate table body
                        $('#traineeAttendanceTableBody').html(html);
                        
                        // Initialize DataTable after a small delay to ensure DOM is updated
                        setTimeout(function() {
                            if ($('#traineeAttendanceTableBody tr').length > 0) {
                                $('#traineeAttendanceTable').DataTable({
                                    pageLength: 10,
                                    order: [[0, 'desc']],
                                    columnDefs: [
                                        { orderable: false, targets: [4] }, // Images column not sortable
                                        { responsivePriority: 1, targets: 0 }, // Date - highest priority
                                        { responsivePriority: 2, targets: 1 }, // Trainee - high priority
                                        { responsivePriority: 3, targets: 4 }, // Images - keep visible on mobile
                                        { responsivePriority: 4, targets: 2 }, // Child Name - medium priority
                                        { responsivePriority: 5, targets: 3 }, // Activity - hide first on small screens
                                        { targets: 4, createdCell: function(td) { td.style.minWidth = '90px'; } } // Ensure images column has space
                                    ],
                                    responsive: {
                                        details: {
                                            type: 'column',
                                            target: 'tr'
                                        }
                                    },
                                    language: {
                                        emptyTable: "No attendance records found"
                                    }
                                });
                            }
                        }, 100);
                    } else {
                        $('#traineeAttendanceTableBody').html('<tr><td colspan="5" class="text-center text-danger py-4">Error loading records: ' + (response.message || 'Unknown error') + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#traineeAttendanceTableBody').html('<tr><td colspan="5" class="text-center text-danger py-4">Error loading attendance records. Please try again.</td></tr>');
                }
            });
        }
        
        // Load coordinators
        function loadCoordinators() {
            $.ajax({
                url: 'api/get-coordinators',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        let html = '';
                        if (response.data.length === 0) {
                            html = '<tr><td colspan="4" class="text-center text-muted py-4">No coordinators found.</td></tr>';
                        } else {
                            response.data.forEach(function(coord) {
                                html += `
                                    <tr>
                                        <td>${coord.id}</td>
                                        <td>${escapeHtml(coord.username)}</td>
                                        <td>${new Date(coord.created_at).toLocaleDateString('en-IN')}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger delete-coordinator" data-id="${coord.id}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#coordinatorsTableBody').html(html);
                        
                        if ($.fn.DataTable.isDataTable('#coordinatorsTable')) {
                            $('#coordinatorsTable').DataTable().destroy();
                        }
                        $('#coordinatorsTable').DataTable({
                            pageLength: 10,
                            order: [[0, 'desc']]
                        });
                    }
                }
            });
        }
        
        // Create trainee
        $('#saveTraineeBtn').on('click', function() {
            const formData = {
                username: $('#createTraineeForm [name="username"]').val(),
                password: $('#createTraineeForm [name="password"]').val(),
                name: $('#createTraineeForm [name="name"]').val(),
                contact: $('#createTraineeForm [name="contact"]').val(),
                email: $('#createTraineeForm [name="email"]').val()
            };
            
            $.ajax({
                url: 'api/create-trainee',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Trainee created successfully!');
                        $('#createTraineeModal').modal('hide');
                        $('#createTraineeForm')[0].reset();
                        loadTrainees();
                        loadTraineeAttendance();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
        
        // Create coordinator
        $('#saveCoordinatorBtn').on('click', function() {
            const formData = {
                username: $('#createCoordinatorForm [name="username"]').val(),
                password: $('#createCoordinatorForm [name="password"]').val()
            };
            
            $.ajax({
                url: 'api/create-coordinator',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Coordinator created successfully!');
                        $('#createCoordinatorModal').modal('hide');
                        $('#createCoordinatorForm')[0].reset();
                        loadCoordinators();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
        
        // Delete trainee handler removed - no action buttons in trainees table
        
        // View attendance images function (called from thumbnail click)
        window.viewAttendanceImages = function(imagePaths, childName, sessionDate) {
            $('#viewImageChild').text(childName);
            $('#viewImageDate').text(sessionDate);
            
            // Clear previous images
            $('#viewImagesContainer').empty();
            
            if (imagePaths && imagePaths.length > 0) {
                // Add image count header
                const countHtml = `
                    <div class="col-12 mb-3">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-images"></i> <strong>Total Images:</strong> ${imagePaths.length}
                        </div>
                    </div>
                `;
                $('#viewImagesContainer').append(countHtml);
                
                imagePaths.forEach(function(imagePath, index) {
                    const imageHtml = `
                        <div class="col-12 col-md-6 col-lg-4 mb-3">
                            <div class="card shadow-sm h-100">
                                <img src="${imagePath}" 
                                     alt="Session Image ${index + 1}" 
                                     class="card-img-top img-fluid" 
                                     style="height: 250px; object-fit: cover; cursor: pointer; max-width: 100%;"
                                     onclick="window.open('${imagePath}', '_blank')"
                                     onerror="this.onerror=null; this.src=''; this.alt='Image not found'; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'alert alert-warning m-2\\'><i class=\\'fas fa-exclamation-triangle\\'></i> Image not found</div>';">
                                <div class="card-body p-2 text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-image"></i> Image ${index + 1} of ${imagePaths.length}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#viewImagesContainer').append(imageHtml);
                });
            } else {
                $('#viewImagesContainer').html('<div class="col-12 text-center text-muted py-4"><i class="fas fa-image fa-2x mb-2"></i><br>No images available</div>');
            }
            
            $('#viewImageModal').modal('show');
        };
        
        // Delete coordinator
        $(document).on('click', '.delete-coordinator', function() {
            if (!confirm('Are you sure you want to delete this coordinator?')) return;
            const id = $(this).data('id');
            $.ajax({
                url: 'api/delete-coordinator',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Coordinator deleted successfully!');
                        loadCoordinators();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
        
        // Load data when sections are shown
        $(window).on('hashchange', function() {
            if (window.location.hash === '#trainees') {
                loadTrainees();
                loadTraineeAttendance();
            }
            if (window.location.hash === '#coordinators') {
                loadCoordinators();
            }
        });
        
        // Also load on page load if section is active
        if (window.location.hash === '#trainees') {
            loadTrainees();
            loadTraineeAttendance();
        }
        if (window.location.hash === '#coordinators') {
            loadCoordinators();
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text || '').replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
