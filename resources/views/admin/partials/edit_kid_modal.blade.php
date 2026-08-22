@php
if (!isset($pdo)) { $pdo = neora_pdo(); }
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = auth()->id();
    $_SESSION['username'] = auth()->user()->username ?? '';
    $_SESSION['role'] = role_name();
}
@endphp
<div class="modal fade" id="editKidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Kid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editKidForm" enctype="multipart/form-data">
                <input type="hidden" id="editKidId" name="kid_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editKidName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editKidName" name="name" placeholder="Enter kid's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editKidAge" class="form-label">Age</label>
                        <input type="number" class="form-control" id="editKidAge" name="age" min="1" max="20" placeholder="Enter age">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editKidParentName" class="form-label">Parent Name</label>
                        <input type="text" class="form-control" id="editKidParentName" name="parent_name" placeholder="Enter parent's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editKidContact" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="editKidContact" name="contact" placeholder="Enter contact number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editKidCase" class="form-label">Case</label>
                        <input type="text" class="form-control" id="editKidCase" name="case_type" placeholder="Enter case type (e.g., Autism, ADHD)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Kid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
