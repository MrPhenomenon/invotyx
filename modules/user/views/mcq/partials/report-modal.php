<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-flag me-2"></i> Report MCQ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      
      <div class="modal-body">
        <form id="reportForm">
          <input type="hidden" name="mcq_id" id="report-mcq-id">
          
          <div class="mb-3">
            <label for="report-message" class="form-label">Message</label>
            <textarea class="form-control" id="report-message" name="message" rows="3" maxlength="250" required></textarea>
          </div>
        </form>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" form="reportForm" class="btn btn-danger">Submit Report</button>
      </div>
      
    </div>
  </div>
</div>