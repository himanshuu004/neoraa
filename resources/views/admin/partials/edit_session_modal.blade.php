@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<div class="modal fade" id="editSessionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSessionForm">
                <input type="hidden" name="id" id="editSessionId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Therapist *</label>
                        <select name="therapist_id" id="editSessionTherapist" class="form-select" required>
                            <option value="">Select Therapist</option>
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
                    <div class="mb-3">
                        <label class="form-label">Day of Week *</label>
                        <select name="day_of_week" id="editSessionDay" class="form-select" required>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Time Slot *</label>
                        <input type="time" name="time_slot" id="editSessionTime" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client Name *</label>
                        <input type="text" name="client_name" id="editSessionClient" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Session Type</label>
                        <input type="text" name="session_type" id="editSessionType" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Special Notes</label>
                        <textarea name="special_notes" id="editSessionNotes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
