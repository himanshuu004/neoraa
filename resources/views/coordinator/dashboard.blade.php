@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<?php


$stmt = $pdo->prepare("SELECT id, username, created_at, profile_image FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard - {{ $siteName }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ $baseUrl }}assets/css/style.css">
    <style>
        .profile-picture-default {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #df5589 0%, #c44478 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 2rem; border: 2px solid #df5589;
        }
    </style>
</head>
<body>
    @include('coordinator.partials.navbar')
    <div class="main-content">
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="background: linear-gradient(135deg, #df5589 0%, #c44478 100%); padding: 1rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 4px 15px rgba(223,85,137,0.25);">
                <div class="d-block mb-1 fw-semibold" style="color: rgba(255,255,255,0.95); font-size: 1rem;">
                    <i class="fas fa-user-tie me-2"></i>
                    <?php
                    $hour = (int)date('H');
                    echo $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                    ?>, <span style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($user['username'] ?? 'User'); ?></span>
                </div>
                <div class="d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 1rem;">
                    <i class="fas fa-clock" style="color: rgba(255,255,255,0.9);"></i>
                    <span id="dashboardTime" style="font-size: 0.95rem; color: #fff; font-weight: 600;"></span>
                </div>
            </div>

            <div id="content-sections">
                <div class="content-section" id="personal">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-4 mb-4">
                                <?php
                                $profileImage = $user['profile_image'] ?? null;
                                if ($profileImage && file_exists(public_path($profileImage))) {
                                    echo '<img src="' . htmlspecialchars($baseUrl . $profileImage) . '" alt="Profile" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:2px solid #df5589;">';
                                } else {
                                    echo '<div class="profile-picture-default"><i class="fas fa-user"></i></div>';
                                }
                                ?>
                                <div>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($user['username']); ?></div>
                                    <span class="badge bg-info">Coordinator</span>
                                    <div class="small text-muted mt-1">Joined <?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Username</label>
                                    <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Role</label>
                                    <div class="info-value"><span class="badge bg-info">Coordinator</span></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-muted small">Account Created</label>
                                    <div class="info-value"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                            <h6 class="mb-2">Hiring Applications</h6>
                            <p class="text-muted small mb-3">View and manage submitted hiring forms.</p>
                            <a href="{{ route('admin.applications') }}" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Open Applications
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateDashboardTime() {
            var now = new Date();
            var s = now.toLocaleString('en-IN', { timeZone: 'Asia/Kolkata', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            var el = document.getElementById('dashboardTime');
            if (el) el.textContent = s;
        }
        updateDashboardTime();
        setInterval(updateDashboardTime, 1000);
        
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
    </script>
</body>
</html>
