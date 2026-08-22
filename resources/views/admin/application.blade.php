@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<?php
// Ensure status and note columns exist
try {
    $pdo->exec("ALTER TABLE hiring_applications ADD COLUMN status VARCHAR(50) DEFAULT 'New'");
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) throw $e;
}
try {
    $pdo->exec("ALTER TABLE hiring_applications ADD COLUMN note TEXT DEFAULT NULL");
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) throw $e;
}
try {
    $pdo->exec("ALTER TABLE hiring_applications ADD COLUMN generated_by INT UNSIGNED DEFAULT NULL");
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false) throw $e;
}

$stmt = $pdo->query("SELECT ha.*, u.username as generated_by_name FROM hiring_applications ha LEFT JOIN users u ON ha.generated_by = u.id ORDER BY ha.created_at DESC");
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isCoordinator = isCoordinator();
$statusOptions = ['New', 'Shortlisted', 'Interview Scheduled', 'Rejected', 'Hired'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - {{ $siteName }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ $baseUrl }}assets/css/style.css">
    <style>
        .app-card { border-left: 4px solid #df5589; transition: box-shadow 0.2s; }
        .app-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .status-badge { font-size: 0.7rem; padding: 0.25rem 0.5rem; }
        .status-new { background: #e3f2fd; color: #1565c0; }
        .status-shortlisted { background: #fff3e0; color: #e65100; }
        .status-interview-scheduled { background: #f3e5f5; color: #6a1b9a; }
        .status-rejected { background: #ffebee; color: #c62828; }
        .status-hired { background: #e8f5e9; color: #2e7d32; }
        .detail-section { background: #f8f9fa; border-radius: 0.5rem; padding: 1rem 1.25rem; margin-bottom: 1rem; }
        .detail-section h6 { color: #495057; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; padding-bottom: 0.35rem; border-bottom: 1px solid #dee2e6; }
        .detail-row { display: flex; padding: 0.35rem 0; border-bottom: 1px solid #eee; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { width: 140px; flex-shrink: 0; color: #6c757d; font-size: 0.875rem; }
        .detail-value { flex: 1; font-size: 0.9rem; }
        .admin-actions { background: #fff; border: 1px solid #dee2e6; border-radius: 0.5rem; padding: 1rem; margin-top: 1rem; }
        .note-textarea { font-size: 0.875rem; min-height: 80px; resize: vertical; }
    </style>
</head>
<body>
    @include($isCoordinator ? 'coordinator.partials.navbar' : 'admin.partials.navbar')
    <div class="main-content">
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Hiring Applications</h5>
                    <small class="text-muted"><?php echo count($applications); ?> total submissions</small>
                </div>
            </div>

            <?php if (empty($applications)): ?>
                <div class="nd-sheet-card">
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p class="mb-0">No applications submitted yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="nd-sheet-card">
                    <div class="nd-sheet-toolbar">
                        <input type="search" id="appSearch" class="form-control" style="max-width:260px;" placeholder="Search name, phone, email…">
                        <select id="appStatusFilter" class="form-select" style="max-width:200px;">
                            <option value="">All statuses</option>
                            <?php foreach ($statusOptions as $opt): ?>
                                <option value="<?php echo htmlspecialchars(strtolower($opt)); ?>"><?php echo htmlspecialchars($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="ms-auto small text-muted" id="appVisibleCount"><?php echo count($applications); ?> shown</span>
                    </div>
                    <div class="nd-sheet-wrap">
                        <table class="table nd-sheet mb-0" id="applicationsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Applying for</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($applications as $row):
                                $rowStatus = $row['status'] ?? 'New';
                                $statusClass = 'status-' . strtolower(preg_replace('/\s+/', '-', $rowStatus));
                                $search = strtolower(($row['name'] ?? '') . ' ' . ($row['mobile'] ?? '') . ' ' . ($row['email'] ?? '') . ' ' . ($row['applying_for'] ?? ''));
                            ?>
                                <tr class="app-row" data-application-id="<?php echo (int) $row['id']; ?>"
                                    data-status="<?php echo htmlspecialchars(strtolower($rowStatus)); ?>"
                                    data-search="<?php echo htmlspecialchars($search); ?>">
                                    <td class="cell-muted">#<?php echo (int) $row['id']; ?></td>
                                    <td>
                                        <div class="cell-strong"><?php echo htmlspecialchars($row['name']); ?></div>
                                        <?php if (!empty($row['city'])): ?>
                                            <div class="cell-muted"><?php echo htmlspecialchars($row['city']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cell-nowrap">
                                        <div><a href="tel:<?php echo htmlspecialchars($row['mobile']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($row['mobile']); ?></a></div>
                                        <div class="cell-muted text-truncate" style="max-width:220px;" title="<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['applying_for'] ?: '—'); ?></td>
                                    <td class="cell-nowrap cell-muted"><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                    <td><span class="badge status-badge <?php echo htmlspecialchars($statusClass); ?>"><?php echo htmlspecialchars($rowStatus); ?></span></td>
                                    <td class="text-end cell-nowrap">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal" data-app="<?php echo htmlspecialchars(json_encode($row)); ?>">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="fas fa-user me-2"></i>Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="detailContent">
                    <p class="text-muted">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var baseUrl = '{{ $baseUrl }}';
        var statusOptions = <?php echo json_encode($statusOptions); ?>;

        function escapeHtml(s) {
            if (!s) return '';
            var div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        }

        function buildDetailHtml(app) {
            var resume = app.resume_path ? '<a href="' + baseUrl + app.resume_path + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Download Resume</a>' : '<span class="text-muted">—</span>';
            var cert = app.certificate_path ? '<a href="' + baseUrl + app.certificate_path + '" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-download me-1"></i>Certificate</a>' : '<span class="text-muted">—</span>';
            var status = app.status || 'New';
            var statusOpts = statusOptions.map(function(s) {
                var sel = s === status ? ' selected' : '';
                return '<option value="' + escapeHtml(s) + '"' + sel + '>' + escapeHtml(s) + '</option>';
            }).join('');

            var html = '<input type="hidden" id="detailAppId" value="' + app.id + '">' +

                '<div class="detail-section">' +
                '<h6><i class="fas fa-user me-1"></i>Basic Details</h6>' +
                '<div class="detail-row"><span class="detail-label">Name</span><span class="detail-value">' + escapeHtml(app.name) + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Mobile</span><span class="detail-value">' + escapeHtml(app.mobile) + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Email</span><span class="detail-value">' + escapeHtml(app.email) + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">City</span><span class="detail-value">' + escapeHtml(app.city || '—') + '</span></div>' +
                '</div>' +

                '<div class="detail-section">' +
                '<h6><i class="fas fa-briefcase me-1"></i>Professional</h6>' +
                '<div class="detail-row"><span class="detail-label">Applying For</span><span class="detail-value">' + escapeHtml(app.applying_for || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Qualification</span><span class="detail-value">' + escapeHtml(app.qualification || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">College</span><span class="detail-value">' + escapeHtml(app.college || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Year</span><span class="detail-value">' + escapeHtml(app.year || '—') + '</span></div>' +
                '</div>' +

                '<div class="detail-section">' +
                '<h6><i class="fas fa-chart-line me-1"></i>Experience</h6>' +
                '<div class="detail-row"><span class="detail-label">Type</span><span class="detail-value">' + escapeHtml(app.experience_type || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Years</span><span class="detail-value">' + escapeHtml(app.experience_years || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Current Place</span><span class="detail-value">' + escapeHtml(app.current_place || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Specialization</span><span class="detail-value">' + escapeHtml(app.areas_specialization || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Languages</span><span class="detail-value">' + escapeHtml(app.languages || '—') + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Joining Time</span><span class="detail-value">' + escapeHtml(app.joining_time || '—') + '</span></div>' +
                '</div>' +

                '<div class="detail-section">' +
                '<h6><i class="fas fa-paperclip me-1"></i>Documents</h6>' +
                '<div class="detail-row"><span class="detail-label">Resume</span><span class="detail-value">' + resume + '</span></div>' +
                '<div class="detail-row"><span class="detail-label">Certificate</span><span class="detail-value">' + cert + '</span></div>' +
                '</div>' +

                (app.why_join_neora ? '<div class="detail-section"><h6><i class="fas fa-quote-left me-1"></i>Why Neora</h6><p class="mb-0 small">' + escapeHtml(app.why_join_neora) + '</p></div>' : '') +

                (app.generated_by_name ? '<div class="detail-section"><h6 class="text-muted small"><i class="fas fa-user-tag me-1"></i>Generated By</h6><span class="detail-value"><span class="badge bg-info">' + escapeHtml(app.generated_by_name) + '</span></span></div>' : '') +

                '<div class="detail-section"><h6 class="text-muted small">Submitted</h6><span class="detail-value">' + escapeHtml(app.created_at) + '</span></div>' +

                '<div class="admin-actions">' +
                '<h6 class="mb-3"><i class="fas fa-cog me-1"></i>Actions</h6>' +
                '<div class="row g-3">' +
                '<div class="col-md-6">' +
                '<label class="form-label small fw-semibold">Status</label>' +
                '<select class="form-select form-select-sm" id="detailStatus">' + statusOpts + '</select>' +
                '</div>' +
                '<div class="col-12">' +
                '<label class="form-label small fw-semibold">Note</label>' +
                '<textarea class="form-control note-textarea" id="detailNote" placeholder="Add a note...">' + escapeHtml(app.note || '') + '</textarea>' +
                '</div>' +
                '<div class="col-12">' +
                '<button type="button" class="btn btn-primary btn-sm" id="saveDetailBtn"><i class="fas fa-save me-1"></i>Save Status & Note</button>' +
                '</div>' +
                '</div>' +
                '</div>';
            return html;
        }

        document.getElementById('detailModal').addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            var data = btn.getAttribute('data-app');
            if (!data) return;
            var app = JSON.parse(data);
            document.getElementById('detailContent').innerHTML = buildDetailHtml(app);
        });

        function filterApplications() {
            var q = ($('#appSearch').val() || '').toLowerCase().trim();
            var status = ($('#appStatusFilter').val() || '').toLowerCase();
            var shown = 0;
            $('.app-row').each(function() {
                var row = $(this);
                var matchSearch = !q || String(row.attr('data-search') || '').indexOf(q) !== -1;
                var matchStatus = !status || String(row.attr('data-status') || '') === status;
                var visible = matchSearch && matchStatus;
                row.toggle(visible);
                if (visible) shown++;
            });
            $('#appVisibleCount').text(shown + ' shown');
        }
        $(document).on('input', '#appSearch', filterApplications);
        $(document).on('change', '#appStatusFilter', filterApplications);

        $(document).on('click', '#saveDetailBtn', function() {
            var id = $('#detailAppId').val();
            var status = $('#detailStatus').val();
            var note = $('#detailNote').val();
            var btnEl = $(this);
            btnEl.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
            $.post('api/update-application', { id: id, status: status, note: note })
                .done(function(r) {
                    if (r.success) {
                        btnEl.html('<i class="fas fa-check me-1"></i>Saved');
                        setTimeout(function() { btnEl.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Status & Note'); }, 1500);
                        $('[data-app]').each(function() {
                            try {
                                var d = JSON.parse($(this).attr('data-app'));
                                if (d.id == id) { d.status = status; d.note = note; $(this).attr('data-app', JSON.stringify(d)); return false; }
                            } catch (x) {}
                        });
                        var card = $('[data-application-id="' + id + '"]');
                        var badge = card.find('.status-badge');
                        badge.text(status).removeClass().addClass('badge status-badge status-' + status.replace(/\s+/g, '-').toLowerCase());
                        card.attr('data-status', status.toLowerCase());
                    } else {
                        alert(r.message || 'Failed to save');
                        btnEl.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Status & Note');
                    }
                })
                .fail(function() {
                    alert('Request failed');
                    btnEl.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Status & Note');
                });
        });
    </script>
</body>
</html>
