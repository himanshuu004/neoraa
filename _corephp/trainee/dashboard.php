<?php
require_once '../config/config.php';
require_once '../config/trainee_schema.php'; // Ensure tables exist
requireTrainee();

$trainee_id = $_SESSION['user_id'];

// Get trainee profile
$stmt = $pdo->prepare("SELECT u.*, tp.name, tp.contact, tp.email, tp.profile_image 
                       FROM users u
                       LEFT JOIN trainee_profile tp ON u.id = tp.user_id
                       WHERE u.id = ?");
$stmt->execute([$trainee_id]);
$trainee = $stmt->fetch();

// If profile doesn't exist, create it
if (!$trainee || !$trainee['name']) {
    $name = $trainee['username'] ?? 'Trainee';
    $stmt = $pdo->prepare("INSERT INTO trainee_profile (user_id, name) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE name = ?");
    $stmt->execute([$trainee_id, $name, $name]);
    
    // Fetch again
    $stmt = $pdo->prepare("SELECT u.*, tp.name, tp.contact, tp.email, tp.profile_image 
                           FROM users u
                           LEFT JOIN trainee_profile tp ON u.id = tp.user_id
                           WHERE u.id = ?");
    $stmt->execute([$trainee_id]);
    $trainee = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainee Dashboard - <?php echo SITE_NAME; ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        #profilePictureUploadModal {
            display: flex;
        }
        .info-value {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
        }
        .attendance-form-card {
            border: 2px dashed #df5589;
            background: #f8f9ff;
        }
        .session-image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }
        /* Calendar Styles */
        .attendance-calendar {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .calendar-nav-btn {
            background: #df5589;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.35rem 0.6rem;
            cursor: pointer;
            font-size: 0.75rem;
            transition: all 0.3s;
            min-width: 36px;
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .calendar-nav-btn:hover {
            background: #5568d3;
            transform: scale(1.05);
        }
        .calendar-nav-btn:active {
            transform: scale(0.95);
        }
        .calendar-month-year {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            flex: 1;
        }
        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .calendar-weekday {
            text-align: center;
            font-weight: 600;
            color: #df5589;
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
        }
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.9rem;
            min-height: 44px;
            border: 2px solid transparent;
        }
        .calendar-day:hover {
            background: #f0f0f0;
            transform: scale(1.05);
        }
        .calendar-day.other-month {
            color: #ccc;
            cursor: default;
        }
        .calendar-day.other-month:hover {
            background: transparent;
            transform: none;
        }
        .calendar-day.today {
            background: #df5589;
            color: white;
            font-weight: 700;
        }
        .calendar-day.attended {
            background: #28a745;
            color: white;
            font-weight: 600;
        }
        .calendar-day.selected {
            border-color: #df5589;
            background: #e7e9ff;
            font-weight: 700;
        }
        .attendance-form-panel {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 1.5rem;
        }
        .image-upload-container {
            position: relative;
            width: 100%;
        }
        .image-upload-area {
            width: 100%;
            padding: 1.5rem;
            border-radius: 12px;
            border: 2px dashed #df5589;
            background: #f8f9ff;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .image-upload-area:hover {
            background: #e7e9ff;
            border-color: #5568d3;
        }
        .image-upload-area.dragover {
            background: #d6d9ff;
            border-color: #df5589;
        }
        .image-upload-icon {
            font-size: 2.5rem;
            color: #df5589;
        }
        .image-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .image-preview-item {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e0e0e0;
        }
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .image-preview-item .remove-image {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .image-preview-item .remove-image:hover {
            background: #dc3545;
            transform: scale(1.1);
        }
        .file-input-hidden {
            display: none;
        }
        .quick-form-input {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s;
        }
        .quick-form-input:focus {
            border-color: #df5589;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
        .submit-attendance-btn {
            background: linear-gradient(135deg, #df5589 0%, #c44478 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
            min-height: 56px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .submit-attendance-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        .submit-attendance-btn:active {
            transform: translateY(0);
        }
        .submit-attendance-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .attendance-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: linear-gradient(135deg, #df5589 0%, #c44478 100%);
            color: white;
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }
        /* Attendance Success Popup */
        .attendance-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            z-index: 1060;
            display: none;
            align-items: center;
            gap: 1rem;
            min-width: 280px;
            max-width: 90%;
            animation: popupSlideIn 0.3s ease-out;
        }
        .attendance-popup.show {
            display: flex;
        }
        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
        .attendance-popup-icon {
            font-size: 2rem;
            color: #28a745;
            flex-shrink: 0;
        }
        .attendance-popup-message {
            flex: 1;
            font-size: 1rem;
            font-weight: 500;
            color: #333;
        }
        .attendance-popup-close {
            background: transparent;
            border: none;
            font-size: 1.25rem;
            color: #666;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            line-height: 1;
            transition: color 0.2s;
            flex-shrink: 0;
        }
        .attendance-popup-close:hover {
            color: #333;
        }
        .attendance-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 1059;
            display: none;
        }
        .attendance-popup-overlay.show {
            display: block;
        }
        @media (max-width: 768px) {
            .attendance-calendar {
                padding: 1rem;
                border-radius: 8px;
            }
            .calendar-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
                gap: 0.5rem;
            }
            .calendar-nav-btn {
                width: auto;
                min-width: 32px;
                min-height: 32px;
                padding: 0.25rem 0.4rem;
                font-size: 0.7rem;
            }
            .calendar-month-year {
                font-size: 1rem;
                padding: 0 0.5rem;
            }
            .calendar-weekdays {
                gap: 0.25rem;
                margin-bottom: 0.5rem;
            }
            .calendar-weekday {
                font-size: 0.75rem;
                padding: 0.4rem 0.25rem;
            }
            .calendar-days {
                gap: 0.25rem;
            }
            .calendar-day {
                font-size: 0.75rem;
                min-height: 36px;
                border-radius: 6px;
            }
            .calendar-day:hover {
                transform: scale(1.02);
            }
            .attendance-form-panel {
                padding: 1rem;
                margin-top: 1rem;
            }
            .stat-value {
                font-size: 1.5rem;
            }
            .attendance-popup {
                padding: 1.25rem 1.5rem;
                min-width: 260px;
            }
            .attendance-popup-icon {
                font-size: 1.75rem;
            }
            .attendance-popup-message {
                font-size: 0.9rem;
            }
        }
        @media (max-width: 480px) {
            .attendance-calendar {
                padding: 0.75rem;
            }
            .calendar-header {
                margin-bottom: 0.75rem;
            }
            .calendar-nav-btn {
                min-width: 28px;
                min-height: 28px;
                padding: 0.2rem 0.3rem;
                font-size: 0.65rem;
            }
            .calendar-month-year {
                font-size: 0.9rem;
            }
            .calendar-weekday {
                font-size: 0.7rem;
                padding: 0.3rem 0.2rem;
            }
            .calendar-day {
                font-size: 0.7rem;
                min-height: 32px;
            }
            .attendance-popup {
                padding: 1rem 1.25rem;
                min-width: 240px;
            }
            .attendance-popup-icon {
                font-size: 1.5rem;
            }
            .attendance-popup-message {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Small Greeting and Time -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="background: linear-gradient(135deg, #df5589 0%, #c44478 100%); padding: 1rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(223,85,137,0.25);">
                <div>
                    <div class="d-block mb-1 fw-semibold" style="color: rgba(255,255,255,0.95); font-size: 1rem;">
                        <i class="fas fa-graduation-cap me-2"></i>
                        <?php 
                        $hour = (int)date('H');
                        if ($hour < 12) {
                            echo 'Good Morning';
                        } elseif ($hour < 17) {
                            echo 'Good Afternoon';
                        } else {
                            echo 'Good Evening';
                        }
                        ?>, <span style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($trainee['name'] ?? $trainee['username'] ?? 'User'); ?></span>
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
                                                $profileImage = $trainee['profile_image'] ?? null;
                                                if ($profileImage && file_exists('../' . $profileImage)) {
                                                    echo '<img src="../' . htmlspecialchars($profileImage) . '" alt="Profile Picture" class="profile-picture" id="profilePictureDisplay">';
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
                                        <div class="info-value"><?php echo htmlspecialchars($trainee['username']); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Name</label>
                                        <div class="info-value"><?php echo htmlspecialchars($trainee['name'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Contact</label>
                                        <div class="info-value"><?php echo htmlspecialchars($trainee['contact'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <div class="info-value"><?php echo htmlspecialchars($trainee['email'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Role</label>
                                        <div class="info-value">
                                            <span class="badge bg-info">Trainee</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Account Created</label>
                                        <div class="info-value"><?php echo date('F d, Y', strtotime($trainee['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Profile Picture Upload Modal -->
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
                                            <label for="traineeUsername" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="traineeUsername" name="username" 
                                                   value="<?php echo htmlspecialchars($trainee['username']); ?>" 
                                                   required readonly>
                                            <div class="form-text">Your login username (cannot be changed)</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traineeName" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="traineeName" name="name" 
                                                   value="<?php echo htmlspecialchars($trainee['name'] ?? ''); ?>" 
                                                   required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traineeContact" class="form-label">Contact</label>
                                            <input type="text" class="form-control" id="traineeContact" name="contact" 
                                                   value="<?php echo htmlspecialchars($trainee['contact'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traineeEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="traineeEmail" name="email" 
                                                   value="<?php echo htmlspecialchars($trainee['email'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traineePassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="traineePassword" name="password" 
                                                   placeholder="Leave blank to keep current password">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="traineeConfirmPassword" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="traineeConfirmPassword" 
                                                   placeholder="Confirm new password">
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
                
                <!-- Notices Section -->
                <div class="content-section" id="notices" style="display: none;">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-bullhorn"></i> Notices & Updates</h5>
                            <button type="button" class="btn btn-sm btn-light" id="refreshNoticesBtn">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="traineeNoticesContainer">
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="text-muted mt-2">Loading notices...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance & Sessions Section -->
                <div class="content-section" id="attendance" style="display: none;">
                    <!-- Calendar View -->
                    <div class="attendance-calendar mb-4">
                        <div class="calendar-header">
                            <button class="calendar-nav-btn" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="calendar-month-year" id="currentMonthYear"></div>
                            <button class="calendar-nav-btn" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="calendar-weekdays">
                            <div class="calendar-weekday">Sun</div>
                            <div class="calendar-weekday">Mon</div>
                            <div class="calendar-weekday">Tue</div>
                            <div class="calendar-weekday">Wed</div>
                            <div class="calendar-weekday">Thu</div>
                            <div class="calendar-weekday">Fri</div>
                            <div class="calendar-weekday">Sat</div>
                        </div>
                        <div class="calendar-days" id="calendarDays"></div>
                    </div>
                    
                    <!-- Quick Attendance Form -->
                    <div class="attendance-form-panel">
                        <h5 class="mb-3"><i class="fas fa-pencil-alt"></i> Mark Attendance for <span id="selectedDateDisplay">Today</span></h5>
                        <form id="attendanceForm" enctype="multipart/form-data">
                            <input type="hidden" id="sessionDate" name="session_date" value="<?php echo date('Y-m-d'); ?>">
                            
                            <div class="mb-3">
                                <label for="childName" class="form-label fw-semibold">Child Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control quick-form-input" id="childName" name="child_name" 
                                       placeholder="Enter child's name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="activityDescription" class="form-label fw-semibold">Today's Activity <span class="text-danger">*</span></label>
                                <textarea class="form-control quick-form-input" id="activityDescription" name="activity_description" 
                                          rows="4" placeholder="Describe the activities conducted during this session..." required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Upload Today Session Pictures</label>
                                <div class="image-upload-container">
                                    <div class="image-upload-area" id="imageUploadArea">
                                        <i class="fas fa-cloud-upload-alt image-upload-icon"></i>
                                        <div>
                                            <strong>Click to upload</strong> or drag and drop
                                        </div>
                                        <small class="text-muted">Multiple images allowed (JPG, PNG - Max 5MB each)</small>
                                    </div>
                                    <input type="file" id="sessionImages" name="session_images[]" 
                                           class="file-input-hidden" multiple accept="image/jpeg,image/jpg,image/png">
                                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                                </div>
                            </div>
                            
                            <button type="submit" class="submit-attendance-btn" id="submitAttendanceBtn">
                                <i class="fas fa-check-circle"></i> Submit Attendance
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Success Popup -->
    <div class="attendance-popup-overlay" id="attendancePopupOverlay"></div>
    <div class="attendance-popup" id="attendancePopup">
        <i class="fas fa-check-circle attendance-popup-icon"></i>
        <div class="attendance-popup-message" id="attendancePopupMessage">Attendance submitted successfully!</div>
        <button type="button" class="attendance-popup-close" id="attendancePopupClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            const hash = window.location.hash.substring(1) || 'personal';
            $('.content-section').hide();
            const targetSection = $('#' + hash);
            if (targetSection.length) {
                targetSection.show();
            } else {
                $('#personal').show();
            }
        }
        
        $(document).ready(function() {
            showContentSection();
            loadTraineeNotices();
            if (window.location.hash === '#attendance') {
                const today = '<?php echo date('Y-m-d'); ?>';
                selectDate(today);
                loadAttendanceRecords();
            }
        });
        
        $(window).on('hashchange', function() {
            showContentSection();
            if (window.location.hash === '#attendance') {
                selectDate('<?php echo date('Y-m-d'); ?>');
                loadAttendanceRecords();
            }
            if (window.location.hash === '#notices') {
                loadTraineeNotices();
            }
        });
        
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
        });
        
        $('#personalInfoForm').on('submit', function(e) {
            e.preventDefault();
            
            const password = $('#traineePassword').val();
            const confirmPassword = $('#traineeConfirmPassword').val();
            
            if (password && password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            const formData = {
                name: $('#traineeName').val(),
                contact: $('#traineeContact').val(),
                email: $('#traineeEmail').val(),
                password: password || ''
            };
            
            $.ajax({
                url: 'api/update_trainee_profile.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Personal information updated successfully!');
                        $('#personalInfoEdit').hide();
                        $('#personalInfoView').show();
                        $('#editPersonalInfoBtn').show();
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
        
        // Profile Picture Upload
        $('#uploadProfilePictureBtn').on('click', function() {
            $('#profilePictureUploadModal').show();
            $('#profilePictureInput').val('');
            $('#profilePicturePreview').hide();
        });
        
        $('#cancelProfilePictureBtn').on('click', function() {
            $('#profilePictureUploadModal').hide();
        });
        
        $('#profilePictureInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, JPEG, and PNG images are allowed.');
                    $(this).val('');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit.');
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                    $('#profilePicturePreview').show();
                };
                reader.readAsDataURL(file);
            }
        });
        
        $('#profilePictureForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $('#saveProfilePictureBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
            
            $.ajax({
                url: 'api/upload_profile_picture.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.image_path) {
                            $('#profilePictureDisplay').replaceWith(
                                '<img src="../' + response.image_path + '" alt="Profile Picture" class="profile-picture" id="profilePictureDisplay">'
                            );
                        }
                        $('#profilePictureUploadModal').hide();
                        alert('Profile picture uploaded successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error uploading profile picture. Please try again.');
                },
                complete: function() {
                    $('#saveProfilePictureBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Upload');
                }
            });
        });
        
        // Load Notices
        function loadTraineeNotices() {
            $.ajax({
                url: 'api/get_notices.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data && response.data.length > 0) {
                        displayNotices(response.data);
                    } else {
                        $('#traineeNoticesContainer').html('<div class="alert alert-info text-center"><i class="fas fa-info-circle"></i> No notices at this time.</div>');
                    }
                },
                error: function() {
                    $('#traineeNoticesContainer').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Unable to load notices.</div>');
                }
            });
        }
        
        function displayNotices(notices) {
            let html = '<div class="row g-3">';
            notices.forEach(function(notice) {
                const priorityClass = {
                    'high': 'danger',
                    'medium': 'warning',
                    'low': 'info'
                }[notice.priority] || 'secondary';
                
                html += `
                    <div class="col-12">
                        <div class="card border-start border-4 border-${priorityClass} shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">${escapeHtml(notice.title)}</h6>
                                <small class="text-muted">${new Date(notice.created_at).toLocaleDateString('en-IN')}</small>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-0" style="white-space: pre-wrap;">${escapeHtml(notice.content)}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#traineeNoticesContainer').html(html);
        }
        
        $('#refreshNoticesBtn').on('click', function() {
            $(this).find('i').addClass('fa-spin');
            loadTraineeNotices();
            setTimeout(() => {
                $('#refreshNoticesBtn').find('i').removeClass('fa-spin');
            }, 1000);
        });
        
        // Calendar and Attendance Management
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();
        let attendedDates = new Set();
        let allRecords = [];
        // Load attendance records and render calendar
        function loadAttendanceRecords() {
            $.ajax({
                url: 'api/get_attendance_records.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        allRecords = response.data || [];
                        attendedDates = new Set(allRecords.map(r => r.session_date));
                    }
                    // Always render calendar, even if no records
                    renderCalendar();
                },
                error: function() {
                    console.error('Error loading attendance records');
                    // Render calendar even on error
                    renderCalendar();
                }
            });
        }
        
        // Render calendar
        function renderCalendar() {
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            
            $('#currentMonthYear').text(`${monthNames[currentMonth]} ${currentYear}`);
            
            let calendarHTML = '';
            
            // Empty cells for days before month starts
            for (let i = 0; i < startingDayOfWeek; i++) {
                calendarHTML += `<div class="calendar-day other-month"></div>`;
            }
            
            // Days of the month
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            
            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const dateObj = new Date(currentYear, currentMonth, day);
                const isToday = dateStr === todayStr;
                const isAttended = attendedDates.has(dateStr);
                const isPast = dateObj < today && !isToday;
                
                let classes = 'calendar-day';
                if (isAttended) classes += ' attended';
                // Don't show today's purple highlight when attendance is filled - show green only
                if (isToday && !isAttended) classes += ' today';
                if (dateStr === $('#sessionDate').val()) classes += ' selected';
                
                calendarHTML += `
                    <div class="${classes}" data-date="${dateStr}" ${isPast || isAttended ? '' : 'style="cursor: pointer;"'}>
                        ${day}
                    </div>
                `;
            }
            
            $('#calendarDays').html(calendarHTML);
            
            // Add click handlers
            $('.calendar-day:not(.other-month)').on('click', function() {
                const date = $(this).data('date');
                if (date && !attendedDates.has(date)) {
                    selectDate(date);
                }
            });
        }
        
        // Select date
        function selectDate(date) {
            $('#sessionDate').val(date);
            const dateObj = new Date(date + 'T00:00:00');
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            $('#selectedDateDisplay').text(dateObj.toLocaleDateString('en-IN', options));
            $('.calendar-day').removeClass('selected');
            $(`.calendar-day[data-date="${date}"]`).addClass('selected');
        }
        
        // Calendar navigation
        $('#prevMonth').on('click', function() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });
        
        $('#nextMonth').on('click', function() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });
        
        // Multiple Image Upload Functionality
        let selectedImages = [];
        
        // Click to upload
        $('#imageUploadArea').on('click', function() {
            $('#sessionImages').click();
        });
        
        // Drag and drop
        $('#imageUploadArea').on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $('#imageUploadArea').on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $('#imageUploadArea').on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            handleFiles(files);
        });
        
        // File input change
        $('#sessionImages').on('change', function(e) {
            const files = e.target.files;
            handleFiles(files);
        });
        
        function handleFiles(files) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            
            Array.from(files).forEach(function(file) {
                // Validate file type
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type: ' + file.name + '. Only JPG and PNG images are allowed.');
                    return;
                }
                
                // Validate file size
                if (file.size > maxSize) {
                    alert('File too large: ' + file.name + '. Maximum size is 5MB.');
                    return;
                }
                
                // Add to selected images
                selectedImages.push(file);
                
                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewHtml = `
                        <div class="image-preview-item" data-filename="${file.name}">
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="remove-image" onclick="removeImage('${file.name}')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    $('#imagePreviewContainer').append(previewHtml);
                };
                reader.readAsDataURL(file);
            });
        }
        
        // Remove image function (global scope for onclick)
        window.removeImage = function(filename) {
            selectedImages = selectedImages.filter(file => file.name !== filename);
            $(`.image-preview-item[data-filename="${filename}"]`).remove();
            
            // Update file input
            const dt = new DataTransfer();
            selectedImages.forEach(file => dt.items.add(file));
            $('#sessionImages')[0].files = dt.files;
        };
        
        // Attendance Form Submission
        $('#attendanceForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('session_date', $('#sessionDate').val());
            formData.append('child_name', $('#childName').val());
            formData.append('activity_description', $('#activityDescription').val());
            
            // Handle multiple image uploads
            const imageFiles = $('#sessionImages')[0].files;
            if (imageFiles && imageFiles.length > 0) {
                // Append each file with proper array notation for PHP
                for (let i = 0; i < imageFiles.length; i++) {
                    formData.append('session_images[]', imageFiles[i], imageFiles[i].name);
                }
                console.log('Sending ' + imageFiles.length + ' image(s)');
            } else {
                console.log('No images selected');
            }
            
            $('#submitAttendanceBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
            
            $.ajax({
                url: 'api/submit_attendance.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        // Show success popup
                        let message = response.message || 'Attendance submitted successfully!';
                        if (response.images_saved !== undefined && response.images_saved > 0) {
                            message += ' (' + response.images_saved + ' image(s) uploaded)';
                        }
                        showAttendancePopup(message);
                        
                        // Immediately mark today as attended in calendar (green) - no API wait
                        const submittedDate = $('#sessionDate').val();
                        attendedDates.add(submittedDate);
                        renderCalendar();
                        selectDate(submittedDate);
                        
                        // Reset form
                        $('#attendanceForm')[0].reset();
                        selectedImages = [];
                        $('#imagePreviewContainer').empty();
                        selectDate('<?php echo date('Y-m-d'); ?>');
                        
                        // Reload records in background (updates stats, etc.)
                        loadAttendanceRecords();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Error submitting attendance. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = 'Error: ' + xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    $('#submitAttendanceBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> Submit Attendance');
                }
            });
        });
        
        // Attendance Popup Functions
        function showAttendancePopup(message) {
            $('#attendancePopupMessage').text(message);
            $('#attendancePopupOverlay').addClass('show');
            $('#attendancePopup').addClass('show');
        }
        
        function hideAttendancePopup() {
            $('#attendancePopup').removeClass('show');
            setTimeout(function() {
                $('#attendancePopupOverlay').removeClass('show');
            }, 300);
        }
        
        // Close popup handlers
        $('#attendancePopupClose').on('click', function() {
            hideAttendancePopup();
        });
        
        $('#attendancePopupOverlay').on('click', function() {
            hideAttendancePopup();
        });
        
        // Close popup on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $('#attendancePopup').hasClass('show')) {
                hideAttendancePopup();
            }
        });
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
