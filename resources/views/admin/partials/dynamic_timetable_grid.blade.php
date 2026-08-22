@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<?php
// Dynamic Timetable Grid - Editable for Admin, Read-only for Therapist
// Requires: $therapist_id, $pdo, $is_editable (true for admin, false for therapist)

if (!isset($therapist_id) || !isset($pdo)) {
    echo '<div class="alert alert-danger">Error: Missing required variables</div>';
    return;
}

$is_editable = isset($is_editable) ? $is_editable : false;

// Get kids list
$kidsStmt = $pdo->query("SELECT kid_id, kid_name FROM kids ORDER BY kid_name");
$kids = $kidsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get time slots
$timeSlotsStmt = $pdo->query("SELECT id, display_name, time_start, time_end FROM time_slots ORDER BY sort_order, time_start");
$timeSlots = $timeSlotsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get existing sessions for this therapist
$sessionsStmt = $pdo->prepare("SELECT id, day_of_week, time_slot, kid_id, client_name 
                               FROM sessions 
                               WHERE therapist_id = ?
                               ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')");
$sessionsStmt->execute([$therapist_id]);
$sessions = $sessionsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get therapist name for PDF
$therapistStmt = $pdo->prepare("SELECT u.username, tp.name FROM users u LEFT JOIN therapist_profile tp ON u.id = tp.user_id WHERE u.id = ?");
$therapistStmt->execute([$therapist_id]);
$therapistInfo = $therapistStmt->fetch(PDO::FETCH_ASSOC);
$therapistName = $therapistInfo ? ($therapistInfo['name'] ?: $therapistInfo['username']) : 'Unknown';

// Debug: Show session count
if ($is_editable) {
    $sessionCount = count($sessions);
    echo "<div class='alert alert-secondary mb-2'><small><i class='fas fa-database'></i> Loaded $sessionCount session(s) from database</small></div>";
}

// PDF Export Buttons
echo '<div class="mb-3 d-flex gap-2 flex-wrap">';
echo '<button class="btn btn-info btn-sm" onclick="viewGridAsPDF_' . $therapist_id . '()"><i class="fas fa-eye"></i> View as PDF</button>';
echo '<button class="btn btn-success btn-sm" onclick="downloadGridPDF_' . $therapist_id . '()"><i class="fas fa-download"></i> Download PDF</button>';
echo '</div>';

// Store therapist name for JavaScript
echo "<input type='hidden' id='therapistName_$therapist_id' value='" . htmlspecialchars($therapistName) . "'>";
echo "<input type='hidden' id='currentTherapistId' value='$therapist_id'>";

// Organize sessions by day and time
$grid = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Initialize empty grid
foreach ($days as $day) {
    $grid[$day] = [];
    foreach ($timeSlots as $slot) {
        $grid[$day][$slot['display_name']] = null;
    }
}

// Fill grid with session data
foreach ($sessions as $session) {
    $day = $session['day_of_week'];
    $timeSlot = $session['time_slot'];
    
    // Store in grid
    if (isset($grid[$day])) {
        $grid[$day][$timeSlot] = [
            'kid_id' => $session['kid_id'],
            'kid_name' => $session['client_name']
        ];
    }
}
?>

<style>
.timetable-grid {
    font-size: 0.85rem;
}
.timetable-grid th {
    background: #f8f9fa;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 0.5rem;
    position: sticky;
    top: 0;
    z-index: 5;
}
.timetable-grid th:first-child {
    left: 0;
    z-index: 10;
}
.timetable-grid td {
    padding: 0.25rem;
    text-align: center;
    vertical-align: middle;
    min-width: 120px;
}
.timetable-grid td:first-child {
    background: #f8f9fa;
    font-weight: 600;
    position: sticky;
    left: 0;
    z-index: 5;
}
.grid-cell {
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s;
}
.grid-cell:hover {
    background: #e9ecef;
}
.grid-cell.filled {
    background: #e7f3ff;
}
.grid-cell .kid-badge {
    background: #0d6efd;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
}
.grid-cell .empty-text {
    color: #adb5bd;
    font-size: 0.75rem;
}
.grid-cell select {
    width: 100%;
    font-size: 0.75rem;
    padding: 0.25rem;
}
.success-tick {
    color: #28a745;
    font-size: 1rem;
    animation: fadeIn 0.3s;
}
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.5); }
    to { opacity: 1; transform: scale(1); }
}
@media (max-width: 768px) {
    .timetable-grid {
        font-size: 0.7rem;
    }
    .timetable-grid td {
        min-width: 90px;
        padding: 0.2rem;
    }
}
</style>

<div class="table-responsive">
    <table class="table table-bordered timetable-grid table-sm">
        <thead>
            <tr>
                <th style="min-width: 100px;">Day / Time</th>
                <?php foreach ($timeSlots as $slot): ?>
                <th><?php echo htmlspecialchars($slot['display_name']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($days as $day): ?>
            <tr>
                <td><?php echo $day; ?></td>
                <?php foreach ($timeSlots as $slot): 
                    $cellData = $grid[$day][$slot['display_name']];
                    $kidId = $cellData ? $cellData['kid_id'] : '';
                    $kidName = $cellData ? $cellData['kid_name'] : '';
                    $cellId = 'cell_' . $therapist_id . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $day) . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $slot['display_name']);
                ?>
                <td>
                    <div class="grid-cell <?php echo $kidName ? 'filled' : ''; ?>" 
                         id="<?php echo $cellId; ?>"
                         data-therapist-id="<?php echo $therapist_id; ?>"
                         data-day="<?php echo $day; ?>"
                         data-timeslot="<?php echo htmlspecialchars($slot['display_name']); ?>"
                         data-kid-id="<?php echo $kidId; ?>">
                        <?php if ($is_editable): ?>
                            <select class="form-select form-select-sm cell-dropdown" 
                                    data-cell-id="<?php echo $cellId; ?>"
                                    style="display: none;">
                                <option value="">-- Clear --</option>
                                <?php foreach ($kids as $kid): ?>
                                <option value="<?php echo $kid['kid_id']; ?>" 
                                        <?php echo ($kidId == $kid['kid_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($kid['kid_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="cell-display">
                                <?php if ($kidName): ?>
                                    <span class="kid-badge"><?php echo htmlspecialchars($kidName); ?></span>
                                <?php else: ?>
                                    <span class="empty-text">Click to assign</span>
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <?php if ($kidName): ?>
                                <span class="kid-badge"><?php echo htmlspecialchars($kidName); ?></span>
                            <?php else: ?>
                                <span class="empty-text">-</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($is_editable): ?>
<script>
$(document).ready(function() {
    // Click on cell to edit
    $('.grid-cell').on('click', function(e) {
        if ($(e.target).is('select') || $(e.target).closest('select').length) {
            return; // Don't trigger if clicking dropdown
        }
        
        var $cell = $(this);
        var $display = $cell.find('.cell-display');
        var $dropdown = $cell.find('.cell-dropdown');
        
        // Hide display, show dropdown
        $display.hide();
        $dropdown.show().focus();
    });
    
    // On dropdown change
    $('.cell-dropdown').on('change', function() {
        var $dropdown = $(this);
        var $cell = $dropdown.closest('.grid-cell');
        var cellId = $dropdown.data('cell-id');
        
        var therapistId = $cell.data('therapist-id');
        var day = $cell.data('day');
        var timeSlot = $cell.data('timeslot');
        var kidId = $dropdown.val();
        
        // Show loading
        $dropdown.prop('disabled', true);
        
        // Save via AJAX
        $.ajax({
            url: 'api/update-grid-cell',
            method: 'POST',
            data: {
                therapist_id: therapistId,
                day: day,
                time_slot: timeSlot,
                kid_id: kidId
            },
            success: function(response) {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                
                if (res.success) {
                    // Update display
                    var $display = $cell.find('.cell-display');
                    
                    if (res.kid_name) {
                        $display.html('<span class="kid-badge">' + res.kid_name + '</span>');
                        $cell.addClass('filled');
                        $cell.data('kid-id', kidId);
                    } else {
                        $display.html('<span class="empty-text">Click to assign</span>');
                        $cell.removeClass('filled');
                        $cell.data('kid-id', '');
                    }
                    
                    // Show success tick
                    $display.append(' <span class="success-tick"><i class="fas fa-check-circle"></i></span>');
                    setTimeout(function() {
                        $cell.find('.success-tick').fadeOut(300, function() {
                            $(this).remove();
                        });
                    }, 1500);
                    
                    // Hide dropdown, show display
                    $dropdown.hide();
                    $display.show();
                } else {
                    alert('Error: ' + res.message);
                }
                
                $dropdown.prop('disabled', false);
            },
            error: function() {
                alert('Error saving. Please try again.');
                $dropdown.prop('disabled', false);
            }
        });
    });
    
    // Hide dropdown on blur
    $('.cell-dropdown').on('blur', function() {
        var $dropdown = $(this);
        var $cell = $dropdown.closest('.grid-cell');
        
        setTimeout(function() {
            $dropdown.hide();
            $cell.find('.cell-display').show();
        }, 200);
    });
});
</script>
<?php endif; ?>

<!-- PDF Export Functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.31/dist/jspdf.plugin.autotable.min.js"></script>
<script>
// PDF Generation for Therapist ID: <?php echo $therapist_id; ?>

function viewGridAsPDF_<?php echo $therapist_id; ?>() {
    generateGridPDF_<?php echo $therapist_id; ?>(true);
}

function downloadGridPDF_<?php echo $therapist_id; ?>() {
    generateGridPDF_<?php echo $therapist_id; ?>(false);
}

function generateGridPDF_<?php echo $therapist_id; ?>(viewOnly) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4'); // Landscape A4
    
    // Get therapist name
    const therapistName = $('#therapistName_<?php echo $therapist_id; ?>').val() || 'Therapist';
    const currentDate = new Date().toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    // Title
    doc.setFontSize(18);
    doc.setTextColor(40, 40, 40);
    doc.text('Weekly Timetable', 148, 20, { align: 'center' });
    
    // Therapist info
    doc.setFontSize(12);
    doc.setTextColor(80, 80, 80);
    doc.text('Therapist: ' + therapistName, 20, 35);
    doc.text('Generated: ' + currentDate, 20, 42);
    
    // Line separator
    doc.setDrawColor(200, 200, 200);
    doc.line(20, 47, 277, 47);
    
    // Prepare table data
    const headers = ['Day / Time'];
    const timeSlots = <?php echo json_encode(array_column($timeSlots, 'display_name')); ?>;
    headers.push(...timeSlots);
    
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const gridData = <?php echo json_encode($grid); ?>;
    
    const rows = [];
    days.forEach(day => {
        const row = [day];
        timeSlots.forEach(slot => {
            const cellData = gridData[day] && gridData[day][slot] ? gridData[day][slot] : null;
            const kidName = cellData && cellData.kid_name ? cellData.kid_name : '-';
            row.push(kidName);
        });
        rows.push(row);
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
                if (data.cell.raw !== '-') {
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
        const filename = 'Timetable_' + therapistName.replace(/\s+/g, '_') + '_' + 
                        new Date().toISOString().split('T')[0] + '.pdf';
        doc.save(filename);
    }
}
</script>
