<?php
require_once '../config/config.php';
requireAdmin();

// Get admin info
$admin_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();

// Get time slots
$timeSlots = $pdo->query("SELECT * FROM time_slots ORDER BY sort_order, time_start")->fetchAll();

// Get kids
$kids = $pdo->query("SELECT * FROM kids ORDER BY kid_name")->fetchAll();

// Get admin's existing sessions
$stmt = $pdo->prepare("SELECT * FROM sessions WHERE therapist_id = ? ORDER BY 
                      FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), 
                      time_slot");
$stmt->execute([$admin_id]);
$existingSessions = $stmt->fetchAll();

// DEBUG: Log what we got from database
error_log("=== MY SESSION LOAD DEBUG ===");
error_log("Admin ID: " . $admin_id);
error_log("Total sessions found: " . count($existingSessions));
foreach ($existingSessions as $idx => $session) {
    error_log("Session $idx: day={$session['day_of_week']}, time={$session['time_slot']}, kid_id={$session['kid_id']}, kid_name={$session['client_name']}");
}

// Create array for easy lookup
$sessionsMap = [];
foreach ($existingSessions as $session) {
    // Use kid_id column directly (not from special_notes)
    $kidId = $session['kid_id'] ?? '';
    if ($session['day_of_week'] && $session['time_slot']) {
        $sessionsMap[$session['day_of_week']][$session['time_slot']] = $kidId;
        error_log("Mapped: {$session['day_of_week']} / {$session['time_slot']} => kid_id: $kidId");
    }
}

error_log("Sessions map created with " . count($sessionsMap) . " days");
error_log("=== END LOAD DEBUG ===");

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sessions - <?php echo SITE_NAME; ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        .kid-select {
            transition: all 0.2s ease;
        }
        .kid-select.saving {
            opacity: 0.6;
            pointer-events: none;
        }
        .kid-select.saved {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid p-4">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar-check"></i> My Weekly Sessions</h4>
                            <p class="text-muted mb-0">Manage your personal weekly timetable - Changes save automatically</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-info" id="viewPdfBtn">
                                <i class="fas fa-eye"></i> View as PDF
                            </button>
                            <button type="button" class="btn btn-success" id="exportPdfBtn">
                                <i class="fas fa-download"></i> Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Auto-Save Mode:</strong> 
                Select a kid for each time slot below. Changes are saved automatically. Leave blank for empty slots.
            </div>
            
            <!-- Toast Notification -->
            <div id="saveToast" style="position: fixed; top: 80px; right: 20px; z-index: 9999; display: none;">
                <div class="alert alert-success shadow-lg" style="min-width: 250px; animation: slideIn 0.3s ease-out;">
                    <i class="fas fa-check-circle"></i> <span id="toastMessage">Saved!</span>
                </div>
            </div>
            
            <!-- Timetable Grid -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="timetableContainer">
                        <table class="table table-bordered table-hover" id="myTimetableGrid">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 100px; position: sticky; left: 0; background: #f8f9fa; z-index: 10;">
                                        Day / Time
                                    </th>
                                    <?php foreach ($timeSlots as $slot): ?>
                                    <th style="min-width: 150px; text-align: center;">
                                        <?php echo htmlspecialchars($slot['display_name']); ?>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($days as $day): ?>
                                <tr>
                                    <td class="fw-bold bg-light" style="position: sticky; left: 0; z-index: 5;">
                                        <?php echo $day; ?>
                                    </td>
                                    <?php foreach ($timeSlots as $slot): ?>
                                    <td style="padding: 5px; text-align: center;">
                                        <?php
                                        $selectedKidId = isset($sessionsMap[$day][$slot['display_name']]) ? 
                                                        $sessionsMap[$day][$slot['display_name']] : '';
                                        ?>
                                        <select class="form-select form-select-sm kid-select" 
                                                data-day="<?php echo $day; ?>" 
                                                data-timeslot-id="<?php echo $slot['id']; ?>"
                                                data-timeslot-name="<?php echo htmlspecialchars($slot['display_name']); ?>">
                                            <option value="">-- Empty --</option>
                                            <?php foreach ($kids as $kid): ?>
                                            <option value="<?php echo $kid['kid_id']; ?>" 
                                                    <?php echo ($selectedKidId == $kid['kid_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($kid['kid_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Summary Stats -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-week fa-2x text-primary mb-2"></i>
                            <h6 class="text-muted">Total Slots</h6>
                            <h3 id="totalSlots"><?php echo count($days) * count($timeSlots); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h6 class="text-muted">Filled Slots</h6>
                            <h3 id="filledSlots">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-circle-notch fa-2x text-warning mb-2"></i>
                            <h6 class="text-muted">Empty Slots</h6>
                            <h3 id="emptySlots">0</h3>
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
    
    <script>
    $(document).ready(function() {
        const adminId = <?php echo $admin_id; ?>;
        
        // DEBUG: Show what was loaded
        console.log('=== MY SESSION PAGE LOAD ===');
        console.log('Admin ID:', adminId);
        console.log('Sessions from PHP:', <?php echo json_encode($existingSessions); ?>);
        console.log('Sessions map:', <?php echo json_encode($sessionsMap); ?>);
        console.log('Total dropdowns:', $('.kid-select').length);
        
        // Count pre-selected kids
        var preSelected = $('.kid-select').filter(function() {
            return $(this).val() !== '';
        }).length;
        console.log('Pre-selected kids:', preSelected);
        
        // Log each pre-selected dropdown
        $('.kid-select').each(function() {
            var val = $(this).val();
            if (val) {
                console.log('Dropdown pre-selected:', {
                    day: $(this).data('day'),
                    timeSlotId: $(this).data('timeslot-id'),
                    timeSlotName: $(this).data('timeslot-name'),
                    selectedKidId: val,
                    selectedKidName: $(this).find('option:selected').text()
                });
            }
        });
        console.log('=== END PAGE LOAD ===');
        
        // Update stats on page load
        updateStats();
        
        // AUTO-SAVE: Save immediately when dropdown changes
        $('.kid-select').on('change', function() {
            var $select = $(this);
            var day = $select.data('day');
            var timeSlotId = $select.data('timeslot-id');
            var timeSlotName = $select.data('timeslot-name');
            var kidId = $select.val();
            
            console.log('Auto-save triggered:', {day, timeSlotId, timeSlotName, kidId});
            
            // Mark as saving
            $select.addClass('saving');
            
            // Save this single cell
            $.ajax({
                url: 'api/save_single_session.php',
                method: 'POST',
                data: {
                    admin_id: adminId,
                    day: day,
                    time_slot_id: timeSlotId,
                    time_slot_name: timeSlotName,
                    kid_id: kidId
                },
                success: function(response) {
                    console.log('Auto-save response:', response);
                    var res = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    $select.removeClass('saving');
                    
                    if (res.success) {
                        // Show success feedback
                        $select.addClass('saved');
                        setTimeout(function() {
                            $select.removeClass('saved');
                        }, 1000);
                        
                        // Show toast
                        showToast(res.message || 'Saved!');
                        
                        // Update stats
                        updateStats();
                    } else {
                        alert('Error: ' + res.message);
                    }
                },
                error: function(xhr) {
                    console.error('Auto-save error:', xhr.responseText);
                    $select.removeClass('saving');
                    showToast('Save failed!', 'danger');
                }
            });
        });
        
        // Update statistics
        function updateStats() {
            var total = $('.kid-select').length;
            var filled = $('.kid-select').filter(function() {
                return $(this).val() !== '';
            }).length;
            var empty = total - filled;
            
            $('#totalSlots').text(total);
            $('#filledSlots').text(filled);
            $('#emptySlots').text(empty);
            
            console.log('Stats updated - Total:', total, 'Filled:', filled, 'Empty:', empty);
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            var $toast = $('#saveToast');
            var $alert = $toast.find('.alert');
            var $message = $('#toastMessage');
            
            // Set message
            $message.text(message);
            
            // Set color
            $alert.removeClass('alert-success alert-danger alert-info');
            $alert.addClass('alert-' + type);
            
            // Show toast
            $toast.stop(true, true).fadeIn(200);
            
            // Auto-hide after 2 seconds
            setTimeout(function() {
                $toast.fadeOut(300);
            }, 2000);
        }
        
        // REMOVED: Manual save button - now using auto-save
        // Each dropdown change saves automatically
        
        // View as PDF (opens in new window) - Professional Layout
        $('#viewPdfBtn').on('click', function() {
            generateMySessionPDF(true);
        });
        
        // Download PDF - Professional Layout
        $('#exportPdfBtn').on('click', function() {
            generateMySessionPDF(false);
        });
        
        // Professional PDF Generation Function
        function generateMySessionPDF(viewOnly) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape A4
            
            const currentDate = new Date().toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            // Title
            doc.setFontSize(18);
            doc.setTextColor(40, 40, 40);
            doc.text('My Weekly Sessions', 148, 20, { align: 'center' });
            
            // Admin info
            doc.setFontSize(12);
            doc.setTextColor(80, 80, 80);
            doc.text('Admin Schedule', 20, 35);
            doc.text('Generated: ' + currentDate, 20, 42);
            
            // Line separator
            doc.setDrawColor(200, 200, 200);
            doc.line(20, 47, 277, 47);
            
            // Prepare table data from grid
            const headers = ['Day / Time'];
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            
            // Get time slots from table headers
            const timeSlots = [];
            $('#myTimetableGrid thead th').each(function(index) {
                if (index > 0) { // Skip first column (Day/Time)
                    timeSlots.push($(this).text().trim());
                }
            });
            headers.push(...timeSlots);
            
            // Extract data from grid
            const rows = [];
            $('#myTimetableGrid tbody tr').each(function() {
                const $row = $(this);
                const day = $row.find('td:first').text().trim();
                const rowData = [day];
                
                $row.find('select.kid-select').each(function() {
                    const selectedText = $(this).find('option:selected').text().trim();
                    const kidName = selectedText === '-- Empty --' ? '-' : selectedText;
                    rowData.push(kidName);
                });
                
                rows.push(rowData);
            });
            
            // Generate table
            doc.autoTable({
                head: [headers],
                body: rows,
                startY: 52,
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    cellPadding: 3,
                    halign: 'center',
                    valign: 'middle'
                },
                headStyles: {
                    fillColor: [66, 139, 202],
                    textColor: 255,
                    fontStyle: 'bold',
                    halign: 'center'
                },
                columnStyles: {
                    0: { 
                        fillColor: [240, 240, 240], 
                        fontStyle: 'bold',
                        halign: 'left' 
                    }
                },
                alternateRowStyles: {
                    fillColor: [250, 250, 250]
                },
                didParseCell: function(data) {
                    // Highlight cells with kids
                    if (data.section === 'body' && data.column.index > 0) {
                        if (data.cell.raw !== '-' && data.cell.raw !== '') {
                            data.cell.styles.fillColor = [225, 245, 254];
                            data.cell.styles.textColor = [13, 110, 253];
                            data.cell.styles.fontStyle = 'bold';
                        }
                    }
                }
            });
            
            // Footer
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(150, 150, 150);
                doc.text('Neora - Timetable Management System', 148, 200, { align: 'center' });
                doc.text('Page ' + i + ' of ' + pageCount, 277, 200, { align: 'right' });
            }
            
            // View or Download
            if (viewOnly) {
                window.open(doc.output('bloburl'), '_blank');
            } else {
                const filename = 'My_Weekly_Sessions_' + new Date().toISOString().split('T')[0] + '.pdf';
                doc.save(filename);
            }
        }
    });
    </script>
</body>
</html>
