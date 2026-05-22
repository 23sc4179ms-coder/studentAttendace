@push('modals')
    <div class="modal fade app-modal" id="viewDegreeModal" tabindex="-1" aria-labelledby="viewDegreeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDegreeModalLabel">Degree Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div><strong>Degree:</strong> <span id="viewDegreeName">—</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ui btn-ui--ghost" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade app-modal confirm-modal" id="deleteDegreeModal" tabindex="-1" aria-labelledby="deleteDegreeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDegreeModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="degreeNameInModal">this degree</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ui btn-ui--ghost" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteDegreeForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ui btn-ui--danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var viewModal = document.getElementById('viewDegreeModal');
            if (viewModal) {
                viewModal.addEventListener('show.bs.modal', function (event) {
                    var triggerButton = event.relatedTarget;
                    if (!triggerButton) return;

                    var degreeName = triggerButton.getAttribute('data-degree-name');
                    var degreeNameTarget = document.getElementById('viewDegreeName');

                    if (degreeNameTarget) degreeNameTarget.textContent = degreeName || '—';
                });
            }

            var deleteModal = document.getElementById('deleteDegreeModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    var triggerButton = event.relatedTarget;
                    if (!triggerButton) return;

                    var action = triggerButton.getAttribute('data-degree-action');
                    var degreeName = triggerButton.getAttribute('data-degree-name');

                    var form = document.getElementById('deleteDegreeForm');
                    var nameTarget = document.getElementById('degreeNameInModal');

                    if (form && action) form.setAttribute('action', action);
                    if (nameTarget && degreeName) nameTarget.textContent = degreeName;
                });
            }
        });
    </script>
@endpush
