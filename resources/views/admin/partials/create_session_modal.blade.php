@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<div class="modal fade" id="createSessionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-alt"></i> Create Weekly Timetable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Select Therapist -->
                <div id="step1" class="mb-4">
                    <label class="form-label fw-bold">Select Therapist</label>
                    <select id="selectTherapist" class="form-select form-select-lg">
                        <option value="">-- Select Therapist --</option>
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
                
                <!-- Step 2: Timetable Grid -->
                <div id="step2" style="display: none;">
                    <label class="form-label fw-bold mb-3">Weekly Timetable</label>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Select a kid for each time slot. Leave blank for empty slots.
                    </div>
                    
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-sm table-hover" id="timetableGrid">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="min-width: 100px; position: sticky; left: 0; background: #f8f9fa; z-index: 10;">Day / Time</th>
                                    <!-- Time slot headers will be inserted here by JS -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Grid rows will be inserted here by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveTimetableBtn" style="display: none;">
                    <i class="fas fa-save"></i> Save Timetable
                </button>
            </div>
        </div>
    </div>
</div>
