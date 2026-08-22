<?php
/**
 * Timetable Grid View Component
 * 
 * Used by both Admin and Therapist dashboards
 * 
 * Parameters:
 * - $therapist_id: ID of therapist to show timetable for
 * - $is_admin: true if admin (can edit), false if therapist (read-only)
 * - $pdo: Database connection object
 */

// Validate required variables
if (!isset($pdo)) {
    echo '<div class="alert alert-danger">Error: Database connection not available.</div>';
    return;
}

if (!isset($therapist_id) || empty($therapist_id)) {
    // Try to get from session if not set
    if (isset($_SESSION['user_id'])) {
        $therapist_id = (int)$_SESSION['user_id'];
    } else {
        echo '<div class="alert alert-warning">Error: Therapist ID not specified.</div>';
        return;
    }
}

if (!is_numeric($therapist_id) || $therapist_id <= 0) {
    echo '<div class="alert alert-warning">Error: Invalid Therapist ID.</div>';
    return;
}

$therapist_id = (int)$therapist_id;
$is_admin = isset($is_admin) ? (bool)$is_admin : false;

// ============================================================================
// DATA FETCHING
// ============================================================================

// Check if required tables exist (non-blocking - just show warning)
$timeSlotsTableExists = true;
try {
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'time_slots'");
    if ($tableCheck->rowCount() === 0) {
        $timeSlotsTableExists = false;
        echo '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Time slots table not found. Please run the database migration.</div>';
    }
} catch (Exception $e) {
    $timeSlotsTableExists = false;
    echo '<div class="alert alert-danger">Error checking database tables: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Get time slots - handle both old and new column names (optimized with single check)
$timeSlots = [];
$hasNewColumns = false;
if ($timeSlotsTableExists) {
    try {
        // Check which column names exist (single check, cached)
        $checkStmt = $pdo->query("SHOW COLUMNS FROM time_slots LIKE 'start_time'");
        $hasNewColumns = ($checkStmt->rowCount() > 0);
    
    if ($hasNewColumns) {
        // New structure: start_time, end_time, display_time
        $stmt = $pdo->query("SELECT id, start_time, end_time, display_time, sort_order FROM time_slots ORDER BY sort_order, start_time");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Normalize to consistent format with fallbacks
        foreach ($rows as $row) {
            $timeSlots[] = [
                'id' => isset($row['id']) ? (int)$row['id'] : 0,
                'start_time' => $row['start_time'] ?? '',
                'end_time' => $row['end_time'] ?? '',
                'display_time' => !empty($row['display_time']) ? $row['display_time'] : ($row['start_time'] . '-' . $row['end_time']),
                'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : 0
            ];
        }
    } else {
        // Old structure: time_start, time_end, display_name
        $stmt = $pdo->query("SELECT id, time_start, time_end, display_name, sort_order FROM time_slots ORDER BY sort_order, time_start");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Normalize to consistent format with fallbacks
        foreach ($rows as $row) {
            $timeSlots[] = [
                'id' => isset($row['id']) ? (int)$row['id'] : 0,
                'start_time' => $row['time_start'] ?? '',
                'end_time' => $row['time_end'] ?? '',
                'display_time' => !empty($row['display_name']) ? $row['display_name'] : ($row['time_start'] . '-' . $row['time_end']),
                'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : 0
            ];
        }
    }
    } catch (Exception $e) {
        error_log("Timetable Grid Error: " . $e->getMessage());
        $timeSlots = [];
    }
}

// Days of week
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Get timetable data organized by day and time_slot_id (optimized - reuse $hasNewColumns)
$timetableData = [];
if ($therapist_id > 0) {
    try {
        // Check if timetable_grid table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'timetable_grid'");
        if ($tableCheck->rowCount() > 0) {
            // Use cached $hasNewColumns from above
            if ($hasNewColumns) {
                // New structure: start_time, end_time, display_time
                $stmt = $pdo->prepare("SELECT tg.*, k.kid_name, ts.start_time, ts.display_time
                                       FROM timetable_grid tg
                                       LEFT JOIN kids k ON tg.kid_id = k.kid_id
                                       JOIN time_slots ts ON tg.time_slot_id = ts.id
                                       WHERE tg.therapist_id = ?
                                       ORDER BY tg.day_of_week, ts.sort_order");
            } else {
                // Old structure: time_start, time_end, display_name
                $stmt = $pdo->prepare("SELECT tg.*, k.kid_name, ts.time_start as start_time, ts.display_name as display_time
                                       FROM timetable_grid tg
                                       LEFT JOIN kids k ON tg.kid_id = k.kid_id
                                       JOIN time_slots ts ON tg.time_slot_id = ts.id
                                       WHERE tg.therapist_id = ?
                                       ORDER BY tg.day_of_week, ts.sort_order");
            }
            $stmt->execute([$therapist_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Organize data by day and time_slot_id
            foreach ($rows as $row) {
                $day = $row['day_of_week'] ?? '';
                $timeSlotId = isset($row['time_slot_id']) ? (int)$row['time_slot_id'] : 0;
                $kidId = isset($row['kid_id']) && $row['kid_id'] !== null && $row['kid_id'] !== '' && $row['kid_id'] !== '0' ? (int)$row['kid_id'] : null;
                
                if ($day && $timeSlotId > 0) {
                    $timetableData[$day][$timeSlotId] = [
                        'id' => isset($row['id']) ? (int)$row['id'] : 0,
                        'kid_id' => $kidId,
                        'kid_name' => !empty($row['kid_name']) ? $row['kid_name'] : null,
                        'time_slot_id' => $timeSlotId
                    ];
                }
            }
        }
        // If table doesn't exist, $timetableData remains empty (grid will show empty cells)
    } catch (Exception $e) {
        error_log("Timetable Grid Data Error: " . $e->getMessage());
        $timetableData = [];
    }
}

// Get kids list for dropdown (admin only)
$kidsList = [];
if ($is_admin) {
    try {
        $stmt = $pdo->query("SELECT kid_id, kid_name FROM kids ORDER BY kid_name");
        $kidsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $kidsList = [];
    }
}
?>

<!-- ============================================================================
     TIMETABLE GRID HTML
     ============================================================================ -->

<div class="timetable-grid-container mb-4">
    <?php if (empty($timeSlots)): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> No time slots found. Please configure time slots first.
            <br><small>If you just set up the database, make sure time slots are inserted in the <code>time_slots</code> table.</small>
        </div>
    <?php else: ?>
        <?php 
        // Count total cells with data
        $totalCells = count($days) * count($timeSlots);
        $filledCells = 0;
        foreach ($days as $day) {
            if (isset($timetableData[$day])) {
                $filledCells += count($timetableData[$day]);
            }
        }
        ?>
        <?php if ($filledCells === 0 && !$is_admin): ?>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> Your timetable is currently empty. The admin will assign sessions to your schedule.
            </div>
        <?php endif; ?>
        
        <!-- Grid Controls -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <label class="mb-0 small fw-medium">Zoom:</label>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomOut" title="Zoom Out">
                    <i class="fas fa-search-minus"></i>
                </button>
                <span class="small" id="zoomLevel">100%</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="zoomIn" title="Zoom In">
                    <i class="fas fa-search-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="resetZoom" title="Reset Zoom">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
            <div class="small text-muted">
                <i class="fas fa-info-circle"></i> Scroll horizontally to see all time slots
            </div>
        </div>
        
        <div class="table-responsive timetable-grid-wrapper" id="timetableGridWrapper">
            <table class="table table-bordered timetable-grid-view" id="timetableGrid">
                <thead class="table-light">
                    <tr>
                        <th class="time-column">Day / Time</th>
                        <?php foreach ($timeSlots as $slot): 
                            $displayTime = isset($slot['display_time']) && !empty($slot['display_time']) 
                                ? $slot['display_time'] 
                                : (isset($slot['start_time']) && isset($slot['end_time']) 
                                    ? $slot['start_time'] . '-' . $slot['end_time'] 
                                    : 'N/A');
                        ?>
                            <th class="day-column"><?php echo htmlspecialchars($displayTime); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($days as $day): ?>
                        <tr>
                            <td class="time-column fw-bold"><?php echo htmlspecialchars($day); ?></td>
                            <?php foreach ($timeSlots as $slot): 
                                $timeSlotId = (int)$slot['id'];
                                $cellData = $timetableData[$day][$timeSlotId] ?? null;
                                $cellId = 'cell_' . $therapist_id . '_' . $day . '_' . $timeSlotId;
                                
                                // Get selected kid ID
                                $selectedKidId = null;
                                if ($cellData && isset($cellData['kid_id']) && $cellData['kid_id'] !== null && $cellData['kid_id'] !== '' && $cellData['kid_id'] !== '0') {
                                    $selectedKidId = (int)$cellData['kid_id'];
                                    if ($selectedKidId <= 0) {
                                        $selectedKidId = null;
                                    }
                                }
                                
                                $kidIdValue = $selectedKidId ?? '';
                                $sessionIdValue = $cellData['id'] ?? '';
                            ?>
                                <td class="day-column cell-editable" 
                                    data-therapist-id="<?php echo $therapist_id; ?>"
                                    data-day="<?php echo htmlspecialchars($day); ?>"
                                    data-time-slot-id="<?php echo $timeSlotId; ?>"
                                    data-session-id="<?php echo $sessionIdValue; ?>"
                                    data-kid-id="<?php echo $kidIdValue; ?>"
                                    id="<?php echo htmlspecialchars($cellId); ?>">
                                    
                                    <?php if ($is_admin): ?>
                                        <!-- Admin: Editable cell with popup -->
                                        <div class="cell-content">
                                            <!-- Normal View -->
                                            <div class="cell-normal-view">
                                                <p class="cell-kid-name mb-2">
                                                    <?php 
                                                    $kidName = ($cellData && isset($cellData['kid_name']) && !empty($cellData['kid_name'])) 
                                                        ? htmlspecialchars($cellData['kid_name']) 
                                                        : 'Empty';
                                                    echo $kidName;
                                                    ?>
                                                </p>
                                                <button class="btn btn-sm btn-primary cell-edit-btn" 
                                                        data-cell-id="<?php echo htmlspecialchars($cellId); ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                            
                                            <!-- Popup Edit View (hidden by default) -->
                                            <div class="cell-popup-edit" style="display: none;">
                                                <select class="form-select form-select-sm cell-kid-select mb-2" 
                                                        data-cell-id="<?php echo htmlspecialchars($cellId); ?>">
                                                    <option value="">-- Select Kid --</option>
                                                    <?php foreach ($kidsList as $kid): ?>
                                                        <?php
                                                        $optionKidId = (int)$kid['kid_id'];
                                                        $isSelected = ($selectedKidId !== null && $selectedKidId !== 0 && $selectedKidId === $optionKidId);
                                                        ?>
                                                        <option value="<?php echo $optionKidId; ?>" <?php echo $isSelected ? 'selected="selected"' : ''; ?>>
                                                            <?php echo htmlspecialchars($kid['kid_name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-success cell-save-btn flex-fill" 
                                                            data-cell-id="<?php echo htmlspecialchars($cellId); ?>">
                                                        <i class="fas fa-check"></i> Save
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary cell-cancel-btn flex-fill" 
                                                            data-cell-id="<?php echo htmlspecialchars($cellId); ?>">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Therapist: Read-only cell (same structure as admin, but no edit button) -->
                                        <div class="cell-content">
                                            <div class="cell-normal-view">
                                                <p class="cell-kid-name mb-2">
                                                    <?php 
                                                    $kidName = ($cellData && isset($cellData['kid_name']) && !empty($cellData['kid_name'])) 
                                                        ? htmlspecialchars($cellData['kid_name']) 
                                                        : 'Empty';
                                                    echo $kidName;
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
/* Timetable Grid Wrapper */
.timetable-grid-wrapper {
    border: 2px solid #df5589;
    border-radius: 0.5rem;
    background: white;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

/* Grid Table Styling */
#timetableGrid {
    width: 100%;
    font-size: 0.875rem;
    background: white;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
}

#timetableGrid thead th {
    background: linear-gradient(135deg, #df5589 0%, #c44478 100%);
    color: white;
    font-weight: 600;
    text-align: center;
    padding: 12px 8px;
    border: 1px solid rgba(255,255,255,0.2);
    position: sticky;
    top: 0;
    z-index: 10;
    font-size: 0.85rem;
    white-space: nowrap;
}

#timetableGrid thead th.time-column {
    background: linear-gradient(135deg, #5568d3 0%, #6a3d8f 100%);
    z-index: 11;
    min-width: 120px;
    width: 120px;
}

#timetableGrid tbody td.time-column {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    font-weight: 600;
    position: sticky;
    left: 0;
    z-index: 5;
    border-right: 2px solid #df5589;
    padding: 12px;
    min-width: 120px;
    width: 120px;
    text-align: center;
    font-size: 0.9rem;
}

#timetableGrid tbody td.day-column {
    min-width: 160px;
    width: 160px;
    padding: 0;
    vertical-align: top;
    background: white;
    border: 1px solid #dee2e6;
}

#timetableGrid tbody tr:hover td.day-column {
    background: #f8f9fa;
}

#timetableGrid tbody tr:hover td.time-column {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.cell-content {
    padding: 10px;
    position: relative;
    min-height: 90px;
    background: white;
}

.cell-normal-view {
    text-align: center;
}

.cell-kid-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #212529;
    margin: 0;
    min-height: 28px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    padding: 4px 0;
}

.cell-edit-btn {
    width: 100%;
    font-size: 0.85rem;
    padding: 0.4rem 0.6rem;
    font-weight: 500;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.cell-edit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cell-popup-edit {
    background: white;
    border: 2px solid #df5589;
    border-radius: 4px;
    padding: 8px;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.cell-kid-select {
    width: 100%;
    font-size: 0.85rem;
    margin-bottom: 8px;
}

.cell-save-btn, .cell-cancel-btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

.d-flex.gap-2 {
    gap: 0.5rem;
}

.timetable-cell-therapist {
    padding: 4px;
}

/* Zoom Controls */
#timetableGridWrapper {
    transition: transform 0.3s ease;
    transform-origin: top left;
}

/* Mobile responsive - Mobile First */
@media (max-width: 767px) {
    .cell-content {
        padding: 8px;
        min-height: 70px;
    }
    
    .cell-kid-name {
        font-size: 0.85rem;
    }
    
    .cell-edit-btn {
        font-size: 0.75rem;
        padding: 0.35rem 0.5rem;
        min-height: 38px;
    }
    
    .cell-popup-edit {
        padding: 8px;
    }
    
    .cell-kid-select {
        font-size: 0.8rem;
        min-height: 40px;
    }
    
    .cell-save-btn, .cell-cancel-btn {
        font-size: 0.75rem;
        padding: 0.35rem 0.5rem;
        min-height: 38px;
    }
    
    #timetableGrid thead th {
        padding: 10px 6px;
        font-size: 0.75rem;
    }
    
    #timetableGrid tbody td.time-column {
        padding: 10px 8px;
        font-size: 0.8rem;
        min-width: 100px;
        width: 100px;
    }
    
    #timetableGrid tbody td.day-column {
        min-width: 140px;
        width: 140px;
    }
}

@media (min-width: 768px) {
    .cell-content {
        padding: 12px;
        min-height: 100px;
    }
    
    .cell-kid-name {
        font-size: 1rem;
    }
    
    .cell-edit-btn {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    
    .cell-popup-edit {
        padding: 10px;
    }
    
    .cell-kid-select {
        font-size: 0.9rem;
    }
    
    .cell-save-btn, .cell-cancel-btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }
    
    #timetableGrid thead th {
        padding: 14px 10px;
        font-size: 0.9rem;
    }
    
    #timetableGrid tbody td.time-column {
        padding: 14px;
        font-size: 0.95rem;
        min-width: 140px;
        width: 140px;
    }
    
    #timetableGrid tbody td.day-column {
        min-width: 180px;
        width: 180px;
    }
}

/* Empty cell styling - applied via JavaScript */
.cell-content.empty-cell {
    background: #f8f9fa;
    border: 1px dashed #dee2e6;
}

.cell-kid-name.empty-text {
    color: #6c757d;
    font-style: italic;
}
</style>

<script>
// Zoom functionality
document.addEventListener('DOMContentLoaded', function() {
    const gridWrapper = document.getElementById('timetableGridWrapper');
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const resetZoomBtn = document.getElementById('resetZoom');
    const zoomLevel = document.getElementById('zoomLevel');
    
    let currentZoom = 100;
    const minZoom = 50;
    const maxZoom = 150;
    const zoomStep = 10;
    
    function updateZoom(level) {
        currentZoom = Math.max(minZoom, Math.min(maxZoom, level));
        if (gridWrapper) {
            gridWrapper.style.transform = `scale(${currentZoom / 100})`;
            gridWrapper.style.transformOrigin = 'top left';
        }
        if (zoomLevel) {
            zoomLevel.textContent = currentZoom + '%';
        }
    }
    
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function() {
            updateZoom(currentZoom + zoomStep);
        });
    }
    
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function() {
            updateZoom(currentZoom - zoomStep);
        });
    }
    
    if (resetZoomBtn) {
        resetZoomBtn.addEventListener('click', function() {
            updateZoom(100);
        });
    }
    
    // Initialize zoom level display
    updateZoom(100);
    
    // Mark empty cells for styling
    document.querySelectorAll('.cell-kid-name').forEach(function(el) {
        if (el.textContent.trim() === 'Empty') {
            el.classList.add('empty-text');
            el.closest('.cell-content')?.classList.add('empty-cell');
        }
    });
});
</script>
