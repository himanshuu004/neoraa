@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<?php
// Ensure the table exists (safe to run on every load)
$pdo->exec("CREATE TABLE IF NOT EXISTS session_bookings (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(255) NOT NULL,
    phone        VARCHAR(50)  NOT NULL,
    service      VARCHAR(255) DEFAULT NULL,
    message      TEXT         DEFAULT NULL,
    status       ENUM('new','reached_out','talked','closed') NOT NULL DEFAULT 'new',
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Fetch all bookings newest first
$bookings = $pdo->query(
    "SELECT * FROM session_bookings ORDER BY created_at DESC"
)->fetchAll();

$statusLabels = [
    'new'          => ['label' => 'New',         'class' => 'bg-primary'],
    'reached_out'  => ['label' => 'Reached Out', 'class' => 'bg-warning text-dark'],
    'talked'       => ['label' => 'Talked',      'class' => 'bg-success'],
    'closed'       => ['label' => 'Closed',      'class' => 'bg-secondary'],
];

$counts = ['new' => 0, 'reached_out' => 0, 'talked' => 0, 'closed' => 0];
foreach ($bookings as $b) {
    if (isset($counts[$b['status']])) $counts[$b['status']]++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Bookings — {{ $siteName }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ $baseUrl }}assets/css/style.css">
    <style>
        .stat-card {
            border-radius: 14px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }
        .stat-num  { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .stat-lbl  { font-size: .78rem; color: #888; font-weight: 500; margin-top: 3px; }
        .status-select {
            font-size: .78rem;
            padding: 4px 8px;
            border-radius: 8px;
            border: 1.5px solid #dee2e6;
            background: #fff;
            cursor: pointer;
            min-width: 130px;
        }
        .status-select:focus { outline: none; border-color: #86b7fe; box-shadow: 0 0 0 3px rgba(13,110,253,.15); }
        .saving-indicator { font-size: .72rem; color: #6c757d; margin-left: 6px; display: none; }
        tr.row-new          { border-left: 3px solid #0d6efd; }
        tr.row-reached_out  { border-left: 3px solid #ffc107; }
        tr.row-talked       { border-left: 3px solid #198754; }
        tr.row-closed       { border-left: 3px solid #6c757d; opacity: .7; }
        .filter-btn.active  { font-weight: 700; }
        #searchInput { max-width: 280px; }
    </style>
</head>
<body>
    @include('admin.partials.navbar')

    <div class="main-content">
        <div class="container-fluid p-4">

            <!-- Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h4 class="mb-0"><i class="fas fa-calendar-check me-2 text-primary"></i>Session Booking Requests</h4>
                    <small class="text-muted">All requests submitted via the website booking form</small>
                </div>
                <span class="badge bg-primary rounded-pill fs-6"><?php echo count($bookings); ?> total</span>
            </div>

            <!-- Summary cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="stat-card bg-white">
                        <div class="stat-icon" style="background:#e8f0fe;">
                            <i class="fas fa-inbox" style="color:#0d6efd;"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?php echo $counts['new']; ?></div>
                            <div class="stat-lbl">New</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card bg-white">
                        <div class="stat-icon" style="background:#fff8e1;">
                            <i class="fas fa-phone-alt" style="color:#ffc107;"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?php echo $counts['reached_out']; ?></div>
                            <div class="stat-lbl">Reached Out</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card bg-white">
                        <div class="stat-icon" style="background:#e8f5e9;">
                            <i class="fas fa-comments" style="color:#198754;"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?php echo $counts['talked']; ?></div>
                            <div class="stat-lbl">Talked</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-card bg-white">
                        <div class="stat-icon" style="background:#f0f0f0;">
                            <i class="fas fa-check-double" style="color:#6c757d;"></i>
                        </div>
                        <div>
                            <div class="stat-num"><?php echo $counts['closed']; ?></div>
                            <div class="stat-lbl">Closed</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table card -->
            <div class="nd-sheet-card">
                <div class="card-body p-0">

                    <!-- Toolbar -->
                    <div class="d-flex flex-wrap align-items-center gap-2 nd-sheet-toolbar border-bottom-0" style="border-radius:0;">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search name or phone…">
                        <div class="d-flex flex-wrap gap-1 ms-auto">
                            <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">All</button>
                            <button class="btn btn-sm btn-outline-primary filter-btn"           data-filter="new">New</button>
                            <button class="btn btn-sm btn-outline-warning filter-btn"           data-filter="reached_out">Reached Out</button>
                            <button class="btn btn-sm btn-outline-success filter-btn"           data-filter="talked">Talked</button>
                            <button class="btn btn-sm btn-outline-secondary filter-btn"         data-filter="closed">Closed</button>
                        </div>
                    </div>

                    <div id="toast-bar" style="display:none;padding:10px 16px;font-size:.82rem;font-weight:600;"></div>

                    <div class="nd-sheet-wrap">
                        <table class="table nd-sheet table-hover align-middle mb-0" id="bookingsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Service</th>
                                    <th>Message</th>
                                    <th>Submitted</th>
                                    <th style="width:170px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                        No booking requests yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $i => $b): ?>
                                <?php $sl = $statusLabels[$b['status']] ?? $statusLabels['new']; ?>
                                <tr class="booking-row row-<?php echo htmlspecialchars($b['status']); ?>"
                                    data-status="<?php echo htmlspecialchars($b['status']); ?>"
                                    data-search="<?php echo strtolower(htmlspecialchars($b['name'] . ' ' . $b['phone'])); ?>">
                                    <td class="text-muted small"><?php echo $b['id']; ?></td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($b['name']); ?></div>
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo htmlspecialchars($b['phone']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($b['phone']); ?>
                                        </a>
                                        <a href="https://wa.me/<?php echo preg_replace('/\D/','',$b['phone']); ?>"
                                           target="_blank" rel="noopener" class="ms-1" title="Open WhatsApp">
                                            <i class="fab fa-whatsapp text-success" style="font-size:.9rem;"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($b['service']): ?>
                                            <span class="badge rounded-pill" style="background:#f3edf7;color:#7b2d8b;font-size:.75rem;font-weight:600;">
                                                <?php echo htmlspecialchars($b['service']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="max-width:200px;">
                                        <?php if ($b['message']): ?>
                                            <span class="text-truncate d-block small" title="<?php echo htmlspecialchars($b['message']); ?>" style="max-width:180px;">
                                                <?php echo htmlspecialchars($b['message']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted" style="white-space:nowrap;">
                                        <?php
                                            $dt = new DateTime($b['created_at'], new DateTimeZone('UTC'));
                                            $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                            echo $dt->format('d M Y') . '<br>' . $dt->format('h:i A');
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <select class="status-select" data-id="<?php echo $b['id']; ?>">
                                                <option value="new"         <?php echo $b['status']==='new'         ? 'selected':'' ?>>&#128229; New</option>
                                                <option value="reached_out" <?php echo $b['status']==='reached_out' ? 'selected':'' ?>>&#128222; Reached Out</option>
                                                <option value="talked"      <?php echo $b['status']==='talked'      ? 'selected':'' ?>>&#9989; Talked</option>
                                                <option value="closed"      <?php echo $b['status']==='closed'      ? 'selected':'' ?>>&#10005; Closed</option>
                                            </select>
                                            <span class="saving-indicator" id="saving-<?php echo $b['id']; ?>">
                                                <i class="fas fa-circle-notch fa-spin"></i>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div><!-- /card -->

        </div>
    </div><!-- /main-content -->

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const baseUrl = '{{ $baseUrl }}';

    // --- Toast bar ---
    function showToast(msg, ok) {
        var bar = document.getElementById('toast-bar');
        bar.innerHTML = (ok ? '&#10003; ' : '&#10007; ') + msg;
        bar.style.cssText = ok
            ? 'display:block;padding:10px 16px;font-size:.82rem;font-weight:600;background:#d1e7dd;color:#0a3622;'
            : 'display:block;padding:10px 16px;font-size:.82rem;font-weight:600;background:#f8d7da;color:#58151c;';
        clearTimeout(bar._t);
        bar._t = setTimeout(function(){ bar.style.display = 'none'; }, 3500);
    }

    // --- Status change ---
    document.querySelectorAll('.status-select').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var id     = this.dataset.id;
            var status = this.value;
            var row    = this.closest('tr');
            var spin   = document.getElementById('saving-' + id);

            if (spin) spin.style.display = 'inline';

            var fd = new FormData();
            fd.append('id',     id);
            fd.append('status', status);

            fetch('{{ url('admin/api/update-booking-status') }}', { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(res){
                    if (res.success) {
                        // Update row class
                        row.className = row.className.replace(/row-\S+/, 'row-' + status);
                        row.dataset.status = status;
                        showToast('Status updated.', true);
                    } else {
                        showToast('Failed to update status.', false);
                    }
                })
                .catch(function(){ showToast('Request failed.', false); })
                .finally(function(){ if (spin) spin.style.display = 'none'; });
        });
    });

    // --- Filter buttons ---
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');
            applyFilter();
        });
    });

    // --- Search ---
    document.getElementById('searchInput').addEventListener('input', applyFilter);

    function applyFilter() {
        var filter = document.querySelector('.filter-btn.active').dataset.filter;
        var search = document.getElementById('searchInput').value.toLowerCase().trim();
        document.querySelectorAll('.booking-row').forEach(function(row) {
            var matchFilter = (filter === 'all') || (row.dataset.status === filter);
            var matchSearch = !search || row.dataset.search.includes(search);
            row.style.display = (matchFilter && matchSearch) ? '' : 'none';
        });
    }
    </script>
</body>
</html>
