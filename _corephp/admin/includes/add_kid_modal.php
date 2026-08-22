<div class="modal fade" id="addKidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-child"></i> Add New Kid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addKidForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kidName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="kidName" name="name" placeholder="Enter kid's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="kidAge" class="form-label">Age</label>
                        <input type="number" class="form-control" id="kidAge" name="age" min="1" max="20" placeholder="Enter age">
                    </div>
                    
                    <div class="mb-3">
                        <label for="kidParentName" class="form-label">Parent Name</label>
                        <input type="text" class="form-control" id="kidParentName" name="parent_name" placeholder="Enter parent's name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="kidContact" class="form-label">Contact</label>
                        <input type="text" class="form-control" id="kidContact" name="contact" placeholder="Enter contact number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="kidCase" class="form-label">Case</label>
                        <input type="text" class="form-control" id="kidCase" name="case_type" placeholder="Enter case type (e.g., Autism, ADHD)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Kid
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
