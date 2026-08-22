<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/reviews_schema.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - <?php echo SITE_NAME; ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Upright:wght@300;400;500;600;700&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-star me-2"></i>Manage Reviews</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal" id="createReviewBtn">
                    <i class="fas fa-plus me-1"></i>Create New Review
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <div id="reviewsAlert" class="alert d-none"></div>
                    <div id="reviewsLoading" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="text-muted mt-2">Loading reviews...</p>
                    </div>
                    <div id="reviewsList" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">Order</th>
                                        <th style="width: 80px;">Photo</th>
                                        <th>Review</th>
                                        <th>Author</th>
                                        <th>Location</th>
                                        <th style="width: 160px;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewsTableBody"></tbody>
                            </table>
                        </div>
                        <div id="reviewsEmpty" class="text-center text-muted py-5 d-none">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p>No reviews yet. Create one to show on the main page.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="fas fa-plus me-1"></i>Create First Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-star me-2"></i><span id="reviewModalTitle">Create Review</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm">
                        <input type="hidden" id="reviewId" name="id" value="">
                        <div class="mb-3">
                            <label for="reviewText" class="form-label">Review text <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reviewText" name="text" rows="4" required placeholder="What the family said..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reviewAuthor" class="form-label">Author <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="reviewAuthor" name="author" required placeholder="e.g. Priya Sharma">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reviewLocation" class="form-label">Location</label>
                                <input type="text" class="form-control" id="reviewLocation" name="location" placeholder="e.g. Dehradun">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reviewOrder" class="form-label">Display order</label>
                            <input type="number" class="form-control" id="reviewOrder" name="display_order" value="0" min="0">
                            <div class="form-text">Lower number appears first on the homepage.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveReviewBtn">
                        <i class="fas fa-save me-1"></i>Save Review
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalTitle"><i class="fas fa-image me-2"></i>Add / Change Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="photoReviewId" value="">
                    <p class="text-muted small">Image will be compressed and saved. Max size ~800px. JPG, PNG allowed.</p>
                    <div class="mb-3">
                        <label for="photoInput" class="form-label">Choose image</label>
                        <input type="file" class="form-control" id="photoInput" accept="image/jpeg,image/jpg,image/png">
                    </div>
                    <div id="photoPreview" class="mb-3 text-center d-none">
                        <img id="photoPreviewImg" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="uploadPhotoBtn">
                        <i class="fas fa-upload me-1"></i>Upload & Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const baseUrl = '<?php echo BASE_URL; ?>';

        function showAlert(msg, type) {
            var el = $('#reviewsAlert');
            el.removeClass('d-none alert-success alert-danger alert-info').addClass('alert-' + (type || 'info'));
            el.text(msg);
        }

        function loadReviews() {
            $('#reviewsLoading').removeClass('d-none');
            $('#reviewsList').addClass('d-none');
            $.get('api/get_reviews.php', function(res) {
                $('#reviewsLoading').addClass('d-none');
                $('#reviewsList').removeClass('d-none');
                if (!res.success) {
                    $('#reviewsTableBody').html('<tr><td colspan="6" class="text-center text-danger">' + (res.message || 'Failed to load') + '</td></tr>');
                    $('#reviewsEmpty').addClass('d-none');
                    return;
                }
                var rows = res.data || [];
                if (rows.length === 0) {
                    $('#reviewsTableBody').html('');
                    $('#reviewsEmpty').removeClass('d-none');
                    return;
                }
                $('#reviewsEmpty').addClass('d-none');
                var html = '';
                rows.forEach(function(r) {
                    var photoCell = r.photo_path
                        ? '<img src="../' + escapeHtml(r.photo_path) + '" alt="" class="rounded" style="width:50px;height:50px;object-fit:cover;">'
                        : '<span class="text-muted small">No photo</span>';
                    html += '<tr data-id="' + r.id + '">' +
                        '<td>' + (r.display_order != null ? r.display_order : 0) + '</td>' +
                        '<td>' + photoCell + '</td>' +
                        '<td><div class="text-truncate" style="max-width:320px" title="' + escapeHtml(r.text) + '">' + escapeHtml(r.text) + '</div></td>' +
                        '<td>' + escapeHtml(r.author) + '</td>' +
                        '<td>' + escapeHtml(r.location || '') + '</td>' +
                        '<td class="text-end">' +
                        '<button type="button" class="btn btn-sm btn-outline-primary me-1 edit-review" data-id="' + r.id + '" title="Edit"><i class="fas fa-edit"></i></button>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary me-1 upload-photo" data-id="' + r.id + '" title="Add/Change photo"><i class="fas fa-image"></i></button>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger delete-review" data-id="' + r.id + '" title="Delete"><i class="fas fa-trash"></i></button>' +
                        '</td></tr>';
                });
                $('#reviewsTableBody').html(html);
            }).fail(function() {
                $('#reviewsLoading').addClass('d-none');
                $('#reviewsList').removeClass('d-none');
                $('#reviewsTableBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading reviews.</td></tr>');
                $('#reviewsEmpty').addClass('d-none');
            });
        }

        function escapeHtml(s) {
            if (!s) return '';
            var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return String(s).replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        $('#createReviewBtn').on('click', function() {
            $('#reviewModalTitle').text('Create Review');
            $('#reviewId').val('');
            $('#reviewForm')[0].reset();
            $('#reviewOrder').val(0);
        });

        $(document).on('click', '.edit-review', function() {
            var id = $(this).data('id');
            var row = $(this).closest('tr');
            var text = row.find('td:eq(2)').attr('title') || row.find('td:eq(2)').text();
            var author = row.find('td:eq(3)').text();
            var location = row.find('td:eq(4)').text();
            var order = row.find('td:eq(0)').text();
            $('#reviewModalTitle').text('Edit Review');
            $('#reviewId').val(id);
            $('#reviewText').val(text);
            $('#reviewAuthor').val(author);
            $('#reviewLocation').val(location);
            $('#reviewOrder').val(order);
            $('#reviewModal').modal('show');
        });

        $('#saveReviewBtn').on('click', function() {
            var id = $('#reviewId').val();
            var data = {
                text: $('#reviewText').val(),
                author: $('#reviewAuthor').val(),
                location: $('#reviewLocation').val(),
                display_order: parseInt($('#reviewOrder').val(), 10) || 0
            };
            if (!data.text.trim() || !data.author.trim()) {
                showAlert('Review text and author are required.', 'danger');
                return;
            }
            var url = id ? 'api/update_review.php' : 'api/create_review.php';
            if (id) data.id = id;
            $('#saveReviewBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
            $.post(url, data).done(function(res) {
                if (res.success) {
                    $('#reviewModal').modal('hide');
                    showAlert('Review saved.', 'success');
                    loadReviews();
                } else {
                    showAlert(res.message || 'Save failed', 'danger');
                }
            }).fail(function() {
                showAlert('Request failed.', 'danger');
            }).always(function() {
                $('#saveReviewBtn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Save Review');
            });
        });

        var photoModalTrigger = null;
        $(document).on('click', '.upload-photo', function() {
            photoModalTrigger = this;
            var id = $(this).data('id');
            $('#photoReviewId').val(id);
            $('#photoInput').val('');
            $('#photoPreview').addClass('d-none');
            $('#photoModal').modal('show');
        });

        $('#photoInput').on('change', function() {
            var f = this.files[0];
            if (f) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#photoPreviewImg').attr('src', e.target.result);
                    $('#photoPreview').removeClass('d-none');
                };
                reader.readAsDataURL(f);
            } else {
                $('#photoPreview').addClass('d-none');
            }
        });

        $('#uploadPhotoBtn').on('click', function() {
            var reviewId = $('#photoReviewId').val();
            var fileInput = document.getElementById('photoInput');
            if (!fileInput.files.length) {
                showAlert('Please choose an image.', 'danger');
                return;
            }
            var fd = new FormData();
            fd.append('review_id', reviewId);
            fd.append('photo', fileInput.files[0]);
            $('#uploadPhotoBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Uploading...');
            $.ajax({
                url: 'api/upload_review_photo.php',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json'
            }).done(function(res) {
                if (res.success) {
                    $('#photoModal').modal('hide');
                    showAlert('Photo saved (compressed).', 'success');
                    loadReviews();
                } else {
                    showAlert(res.message || 'Upload failed', 'danger');
                }
            }).fail(function() {
                showAlert('Upload failed.', 'danger');
            }).always(function() {
                $('#uploadPhotoBtn').prop('disabled', false).html('<i class="fas fa-upload me-1"></i>Upload & Save');
            });
        });

        $(document).on('click', '.delete-review', function() {
            var id = $(this).data('id');
            if (!confirm('Delete this review? This cannot be undone.')) return;
            $.post('api/delete_review.php', { id: id }).done(function(res) {
                if (res.success) {
                    showAlert('Review deleted.', 'success');
                    loadReviews();
                } else {
                    showAlert(res.message || 'Delete failed', 'danger');
                }
            }).fail(function() {
                showAlert('Delete failed.', 'danger');
            });
        });

        // Return focus when modals close to avoid aria-hidden accessibility warning
        $('#photoModal').on('hidden.bs.modal', function() {
            if (photoModalTrigger && typeof photoModalTrigger.focus === 'function') {
                try { photoModalTrigger.focus(); } catch (e) {}
            }
            photoModalTrigger = null;
        });

        $(function() {
            loadReviews();
        });
    </script>
</body>
</html>
