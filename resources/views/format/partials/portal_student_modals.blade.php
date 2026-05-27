@push('modals')
    @php
        $coursesList = $courses ?? collect();
        $teachersList = $allTeachers ?? collect();
        $roleForModal = $logged_role ?? session('logged_role');
    @endphp
    <div class="modal fade app-modal" id="viewStudentModal" tabindex="-1" aria-labelledby="viewStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewStudentModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="viewStudentImage" class="img-fluid rounded mb-3 d-none" alt="Student image">
                    <div class="mb-2"><strong>First Name:</strong> <span id="viewStudentFirstName">—</span></div>
                    <div class="mb-2"><strong>Middle Name:</strong> <span id="viewStudentMiddleName">—</span></div>
                    <div class="mb-2"><strong>Last Name:</strong> <span id="viewStudentLastName">—</span></div>
                    <div class="mb-2"><strong>Email:</strong> <span id="viewStudentEmail">—</span></div>
                    <div class="mb-2"><strong>Contact No:</strong> <span id="viewStudentContactNo">—</span></div>
                    <div><strong>Degree:</strong> <span id="viewStudentDegree">—</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ui btn-ui--ghost" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade app-modal" id="viewTeacherModal" tabindex="-1" aria-labelledby="viewTeacherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewTeacherModalLabel">Teacher Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="viewTeacherImage" class="img-fluid rounded mb-3 d-none" alt="Teacher image">
                    <div class="mb-2"><strong>First Name:</strong> <span id="viewTeacherFirstName">—</span></div>
                    <div class="mb-2"><strong>Middle Name:</strong> <span id="viewTeacherMiddleName">—</span></div>
                    <div class="mb-2"><strong>Last Name:</strong> <span id="viewTeacherLastName">—</span></div>
                    <div class="mb-2"><strong>Email:</strong> <span id="viewTeacherEmail">—</span></div>
                    <div><strong>Contact No:</strong> <span id="viewTeacherContactNo">—</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ui btn-ui--ghost" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade app-modal confirm-modal" id="deleteStudentModal" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteStudentModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong id="studentNameInModal">this student</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ui btn-ui--ghost" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteStudentForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-ui btn-ui--danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade app-modal" id="attendanceStudentModal" tabindex="-1" aria-labelledby="attendanceStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceStudentModalLabel">Mark Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="attendanceAlert" class="alert alert-danger d-none"></div>

                    <input type="hidden" id="attendanceStudentId" value="">

                    <div class="mb-2"><strong>Student:</strong> <span id="attendanceStudentName">—</span></div>

                    <div class="mb-3">
                        <label for="attendanceCourseId" class="form-label">Course</label>
                        <select id="attendanceCourseId" class="form-select" required>
                            <option value="">Select course...</option>
                            @foreach($coursesList as $course)
                                @php /** @var \App\Models\Course $course */ @endphp
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="attendanceSection" class="form-label">Section</label>
                        <input id="attendanceSection" type="text" class="form-control" placeholder="e.g. BSIT-2A" />
                    </div>

                    @if($roleForModal === 'admin')
                        <div class="mb-3">
                            <label for="attendanceTeacherId" class="form-label">Teacher</label>
                            <select id="attendanceTeacherId" class="form-select" required>
                                <option value="">Select teacher...</option>
                                @foreach($teachersList as $t)
                                    @php /** @var \App\Models\teacher $t */ @endphp
                                    @php
                                        $tName = trim($t->first_name . ' ' . ($t->middle_name ?? '') . ' ' . $t->last_name);
                                    @endphp
                                    <option value="{{ $t->id }}">{{ $tName }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" id="attendanceTeacherId" value="">
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-ui btn-ui--ghost" data-bs-dismiss="modal">Cancel</button>
                    <button
                        type="button"
                        class="btn-ui btn-ui--primary"
                        id="confirmAttendanceBtn"
                        data-url="{{ route('course.attendance') }}"
                    >Save Attendance</button>
                </div>
            </div>
        </div>
    </div>
@endpush
