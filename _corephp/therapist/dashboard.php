<?php
require_once '../config/config.php';
requireTherapist();

$therapist_id = $_SESSION['user_id'];

// Get therapist profile
$stmt = $pdo->prepare("SELECT u.*, tp.name, tp.contact, tp.email, tp.profile_image 
                       FROM users u
                       LEFT JOIN therapist_profile tp ON u.id = tp.user_id
                       WHERE u.id = ?");
$stmt->execute([$therapist_id]);
$therapist = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapist Dashboard - <?php echo SITE_NAME; ?></title>
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
                        ?>, <span style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($therapist['name'] ?? $therapist['username'] ?? 'User'); ?></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 1rem;">
                    <i class="fas fa-clock" style="font-size: 0.9rem; color: rgba(255,255,255,0.9);"></i>
                    <span id="dashboardTime" style="font-size: 0.95rem; color: #fff; font-weight: 600;"></span>
                </div>
            </div>
            
            <!-- Content Sections - Shown based on URL hash -->
            <div id="content-sections">
                <!-- Notices Section (Always visible at top) -->
                <div class="content-section" id="notices" style="display: none;">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-bullhorn"></i> Notices & Updates</h5>
                            <button type="button" class="btn btn-sm btn-light" id="refreshNoticesBtn">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="therapistNoticesContainer">
                                <div class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                    <p class="text-muted mt-2">Loading notices...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
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
                                                $profileImage = $therapist['profile_image'] ?? null;
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
                                        <div class="info-value"><?php echo htmlspecialchars($therapist['username']); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Name</label>
                                        <div class="info-value"><?php echo htmlspecialchars($therapist['name'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Contact</label>
                                        <div class="info-value"><?php echo htmlspecialchars($therapist['contact'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <div class="info-value"><?php echo htmlspecialchars($therapist['email'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Role</label>
                                        <div class="info-value">
                                            <span class="badge bg-success">Therapist</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Account Created</label>
                                        <div class="info-value"><?php echo date('F d, Y', strtotime($therapist['created_at'])); ?></div>
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
                                            <label for="therapistUsername" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="therapistUsername" name="username" 
                                                   value="<?php echo htmlspecialchars($therapist['username']); ?>" 
                                                   required readonly>
                                            <div class="form-text">Your login username (cannot be changed)</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistName" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="therapistName" name="name" 
                                                   value="<?php echo htmlspecialchars($therapist['name'] ?? ''); ?>" 
                                                   required>
                                            <div class="form-text">Your full name</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistContact" class="form-label">Contact</label>
                                            <input type="text" class="form-control" id="therapistContact" name="contact" 
                                                   value="<?php echo htmlspecialchars($therapist['contact'] ?? ''); ?>">
                                            <div class="form-text">Your contact number</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="therapistEmail" name="email" 
                                                   value="<?php echo htmlspecialchars($therapist['email'] ?? ''); ?>">
                                            <div class="form-text">Your email address</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistPassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="therapistPassword" name="password" 
                                                   placeholder="Leave blank to keep current password">
                                            <div class="form-text">Enter new password only if you want to change it</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistConfirmPassword" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="therapistConfirmPassword" 
                                                   placeholder="Confirm new password">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistRole" class="form-label">Role</label>
                                            <input type="text" class="form-control" id="therapistRole" 
                                                   value="Therapist" readonly>
                                            <div class="form-text">Your account role</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="therapistCreated" class="form-label">Account Created</label>
                                            <input type="text" class="form-control" id="therapistCreated" 
                                                   value="<?php echo date('F d, Y', strtotime($therapist['created_at'])); ?>" readonly>
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
                
                <!-- My Timetable Section (View Only) -->
                <div class="content-section" id="timetable" style="display: none;">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> My Weekly Timetable (View Only)</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Show dynamic timetable grid (read-only for therapist)
                            if (!isset($therapist_id) || empty($therapist_id)) {
                                $therapist_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
                            }
                            
                            if (!isset($pdo)) {
                                echo '<div class="alert alert-danger">Database connection error. Please contact administrator.</div>';
                            } else {
                                echo '<div class="alert alert-info"><i class="fas fa-info-circle"></i> This is your assigned timetable. Contact admin to make changes.</div>';
                                
                                // Set to read-only for therapist
                                $is_editable = false;
                                include_once '../admin/includes/dynamic_timetable_grid.php';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.31/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="../assets/js/therapist.js"></script>
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
            
            // Always show notices section at the top
            $('#notices').show();
            
            const targetSection = $('#' + hash);
            if (targetSection.length && hash !== 'notices') {
                targetSection.show();
            } else if (hash === 'notices') {
                // If hash is notices, only show notices
                $('#notices').show();
            } else {
                // Default to personal
                $('#personal').show();
            }
        }
        
        // Show content on page load
        $(document).ready(function() {
            showContentSection();
            // Always load notices
            loadTherapistNotices();
        });
        
        // Show content when hash changes
        $(window).on('hashchange', function() {
            showContentSection();
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
            // Reset form to original values
            $('#therapistUsername').val('<?php echo htmlspecialchars($therapist['username']); ?>');
            $('#therapistName').val('<?php echo htmlspecialchars($therapist['name'] ?? ''); ?>');
            $('#therapistContact').val('<?php echo htmlspecialchars($therapist['contact'] ?? ''); ?>');
            $('#therapistEmail').val('<?php echo htmlspecialchars($therapist['email'] ?? ''); ?>');
            $('#therapistPassword').val('');
            $('#therapistConfirmPassword').val('');
        });
        
        $('#personalInfoForm').on('submit', function(e) {
            e.preventDefault();
            
            const password = $('#therapistPassword').val();
            const confirmPassword = $('#therapistConfirmPassword').val();
            
            if (password && password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            const formData = {
                name: $('#therapistName').val(),
                contact: $('#therapistContact').val(),
                email: $('#therapistEmail').val(),
                password: password || ''
            };
            
            $.ajax({
                url: 'api/update_therapist_profile.php',
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
        
        // Therapist data for PDF
        const therapistName = <?php echo json_encode($therapist_name_for_pdf); ?>;
        const therapistId = <?php echo $therapist_id; ?>;
        
        // Function to generate PDF
        function generatePDF(download = false) {
            try {
                // Check if jsPDF is available
                if (typeof window.jspdf === 'undefined') {
                    alert('PDF library not loaded. Please refresh the page and try again.');
                    return;
                }
                
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape', 'mm', 'a4');
                
                // Get grid data from table
                const table = document.querySelector('#timetableGrid');
                if (!table) {
                    alert('Timetable grid not found. Please refresh the page.');
                    return;
                }
                
                // Extract headers (time slots)
                const headers = [];
                const headerRow = table.querySelector('thead tr');
                if (!headerRow) {
                    alert('Table headers not found.');
                    return;
                }
                
                const headerCells = headerRow.querySelectorAll('th');
                headerCells.forEach((cell, index) => {
                    if (index > 0) { // Skip "Day / Time" column
                        headers.push(cell.textContent.trim());
                    }
                });
                
                // Extract rows (days and data)
                const rows = [];
                const bodyRows = table.querySelectorAll('tbody tr');
                if (bodyRows.length === 0) {
                    alert('No timetable data found.');
                    return;
                }
                
                bodyRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length === 0) return;
                    
                    const dayCell = cells[0];
                    const dayName = dayCell.textContent.trim();
                    
                    const rowData = [dayName];
                    for (let i = 1; i < cells.length; i++) {
                        const cell = cells[i];
                        const kidName = cell.querySelector('.cell-kid-name');
                        const cellText = kidName ? kidName.textContent.trim() : 'Empty';
                        rowData.push(cellText);
                    }
                    rows.push(rowData);
                });
                
                // Add title
                doc.setFontSize(18);
                doc.setFont(undefined, 'bold');
                doc.text('Weekly Timetable', 14, 15);
                
                // Add therapist name
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                doc.text('Therapist: ' + therapistName, 14, 22);
                
                // Add date
                const today = new Date();
                const dateStr = today.toLocaleDateString('en-IN', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                doc.text('Generated on: ' + dateStr, 14, 28);
                
                // Check if autoTable is available
                if (typeof doc.autoTable === 'function') {
                    // Create table using autoTable plugin
                    doc.autoTable({
                        startY: 35,
                        head: [['Day / Time', ...headers]],
                        body: rows,
                        theme: 'grid',
                        headStyles: {
                            fillColor: [102, 126, 234],
                            textColor: 255,
                            fontStyle: 'bold',
                            fontSize: 10
                        },
                        bodyStyles: {
                            fontSize: 9,
                            cellPadding: 3
                        },
                        styles: {
                            cellPadding: 3,
                            fontSize: 9,
                            overflow: 'linebreak',
                            cellWidth: 'wrap'
                        },
                        columnStyles: {
                            0: { cellWidth: 30, fontStyle: 'bold' }
                        },
                        margin: { top: 35, left: 14, right: 14 }
                    });
                } else {
                    // Fallback: Simple table without autoTable
                    alert('PDF table plugin not loaded. Using simple format.');
                    let yPos = 35;
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'bold');
                    
                    // Headers
                    let xPos = 14;
                    doc.text('Day / Time', xPos, yPos);
                    xPos += 30;
                    headers.forEach(header => {
                        doc.text(header, xPos, yPos);
                        xPos += 25;
                    });
                    
                    // Rows
                    yPos += 7;
                    doc.setFont(undefined, 'normal');
                    rows.forEach(row => {
                        if (yPos > 190) {
                            doc.addPage();
                            yPos = 20;
                        }
                        xPos = 14;
                        row.forEach((cell, index) => {
                            if (index === 0) {
                                doc.setFont(undefined, 'bold');
                            } else {
                                doc.setFont(undefined, 'normal');
                            }
                            doc.text(cell, xPos, yPos);
                            xPos += (index === 0 ? 30 : 25);
                        });
                        yPos += 7;
                    });
                }
                
                // Generate filename
                const filename = 'Timetable_' + therapistName.replace(/\s+/g, '_') + '_' + 
                               today.toISOString().split('T')[0] + '.pdf';
                
                if (download) {
                    // Download PDF
                    doc.save(filename);
                } else {
                    // View PDF in new tab
                    const pdfBlob = doc.output('blob');
                    const pdfUrl = URL.createObjectURL(pdfBlob);
                    window.open(pdfUrl, '_blank');
                    // Clean up after a delay
                    setTimeout(() => URL.revokeObjectURL(pdfUrl), 100);
                }
            } catch (error) {
                console.error('PDF Generation Error:', error);
                alert('Error generating PDF: ' + error.message);
            }
        }
        
        // View as PDF (opens in new tab)
        function viewAsPDF() {
            generatePDF(false);
        }
        
        // Download PDF
        function downloadPDF() {
            generatePDF(true);
        }
        
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
                url: 'api/upload_profile_picture.php',
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
        
        // Load notices for therapist
        function loadTherapistNotices() {
            $.ajax({
                url: 'api/get_notices.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            displayTherapistNotices(response.data);
                        } else {
                            $('#therapistNoticesContainer').html('<div class="alert alert-info text-center"><i class="fas fa-info-circle"></i> No notices at this time. Check back later!</div>');
                        }
                    } else {
                        $('#therapistNoticesContainer').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ' + (response.message || 'No notices available at this time.') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Unable to load notices. Please try again later.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMsg = 'Notice API not found. Please contact administrator.';
                    } else if (xhr.status === 401 || xhr.status === 403) {
                        errorMsg = 'Session expired. Please refresh the page.';
                    }
                    $('#therapistNoticesContainer').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</div>');
                }
            });
        }
        
        // Display notices for therapist
        function displayTherapistNotices(notices) {
            if (notices.length === 0) {
                $('#therapistNoticesContainer').html('<div class="alert alert-info text-center"><i class="fas fa-info-circle"></i> No notices at this time. Check back later!</div>');
                return;
            }
            
            let html = '<div class="row g-3">';
            notices.forEach(function(notice) {
                const priorityClass = {
                    'high': 'danger',
                    'medium': 'warning',
                    'low': 'info'
                }[notice.priority] || 'secondary';
                
                const priorityIcon = {
                    'high': 'exclamation-triangle',
                    'medium': 'info-circle',
                    'low': 'bell'
                }[notice.priority] || 'bell';
                
                const createdDate = new Date(notice.created_at).toLocaleDateString('en-IN', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                html += `
                    <div class="col-12">
                        <div class="card border-start border-4 border-${priorityClass} shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-${priorityIcon} text-${priorityClass} me-2"></i>
                                        ${escapeHtml(notice.title)}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> ${escapeHtml(notice.created_by_name || 'Admin')} 
                                        <span class="ms-2"><i class="fas fa-clock"></i> ${createdDate}</span>
                                    </small>
                                </div>
                                <span class="badge bg-${priorityClass}">${notice.priority.toUpperCase()}</span>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-0" style="white-space: pre-wrap;">${escapeHtml(notice.content)}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#therapistNoticesContainer').html(html);
        }
        
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
        
        // Refresh notices button
        $('#refreshNoticesBtn').on('click', function() {
            $(this).find('i').addClass('fa-spin');
            loadTherapistNotices();
            setTimeout(() => {
                $('#refreshNoticesBtn').find('i').removeClass('fa-spin');
            }, 1000);
        });
        
        // Show notices section by default or when hash is #notices
        function showContentSection() {
            const hash = window.location.hash.substring(1) || 'personal';
            $('.content-section').hide();
            
            // Always show notices section at top if there are any
            if (hash === 'notices' || hash === 'personal') {
                $('#notices').show();
            }
            
            const targetSection = $('#' + hash);
            if (targetSection.length) {
                targetSection.show();
            } else {
                $('#personal').show();
            }
        }
    </script>
</body>
</html>
