$(document).ready(function() {

    // Build absolute URLs based on <meta name="app-url" content="...">.
    // Used by all AJAX calls so the app works in subfolders.
    function appUrl(path) {
        var base = $('meta[name="app-url"]').attr('content') || '';
        base = base.replace(/\/+$/, '');
        if (!path) return base;
        if (path.charAt(0) !== '/') path = '/' + path;
        return base + path;
    }

    // Read CSRF token from the layout meta tag or fallback hidden input (#csrf_token).
    // Used by create/update/delete AJAX requests.
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content') || $('#csrf_token').val() || '';
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        }
    });

    // Extract Laravel validation error messages (HTTP 422) or a JSON "message".
    // Used by create/update/enroll/login error handlers.
    function validationMessage(xhr) {
        if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            var errors = xhr.responseJSON.errors;
            var msgs = [];
            for (var k in errors) {
                if (errors.hasOwnProperty(k)) msgs.push(errors[k].join('\n'));
            }
            return msgs.join('\n');
        }
        if (xhr && xhr.responseJSON && xhr.responseJSON.message) return xhr.responseJSON.message;
        return '';
    }

    // Friendly hint per HTTP status.
    // Used when server doesn’t return a detailed message.
    function httpHint(status) {
        if (!status) return 'Network error.';
        if (status === 404) return 'Route not found (404).';
        if (status === 419) return 'CSRF token mismatch (419).';
        if (status === 500) return 'Server error (500).';
        if (status === 401) return 'Unauthorized (401).';
        if (status === 403) return 'Access denied (403).';
        return 'Request failed (' + status + ').';
    }

    // Standard warning HTML block for list containers.
    function setHtmlOrWarn($container, message) {
        $container.html('<div class="alert alert-warning mb-0">' + message + '</div>');
    }

    var filterTablesFn = null;

    // Client-side search filter for Students/Teachers table.
    // Used on the Students/Teachers page (resources/views/student.blade.php): #tableSearch, #clearSearch.
    function initFilter() {
        var searchInput = document.getElementById('tableSearch');
        var clearBtn = document.getElementById('clearSearch');
        if (!searchInput) return;
        if (searchInput.dataset && searchInput.dataset.bound === '1') {
            if (filterTablesFn) filterTablesFn();
            return;
        }

        function filterTables() {
            var query = (searchInput.value || '').toLowerCase().trim();
            var studentsDiv = document.getElementById('studentsList');
            var teachersDiv = document.getElementById('teachersList');
            var activeDiv = studentsDiv && !studentsDiv.classList.contains('d-none') ? studentsDiv : teachersDiv;
            if (!activeDiv) return;
            var rows = activeDiv.querySelectorAll('tbody tr');
            rows.forEach(function(row) {
                var text = (row.textContent || '').toLowerCase();
                row.style.display = query === '' || text.indexOf(query) !== -1 ? '' : 'none';
            });
        }

        filterTablesFn = filterTables;
        if (searchInput.dataset) searchInput.dataset.bound = '1';
        searchInput.addEventListener('input', filterTables);
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterTables();
                searchInput.focus();
            });
        }
        filterTables();
    }

    // Toggle between Students list and Teachers list containers.
    // Used on the Students/Teachers page (resources/views/student.blade.php): #showStudentsBtn, #showTeachersBtn.
    function initToggleTables() {
        var showStudentsBtn = document.getElementById('showStudentsBtn');
        var showTeachersBtn = document.getElementById('showTeachersBtn');
        var studentsList = document.getElementById('studentsList');
        var teachersList = document.getElementById('teachersList');
        if (!showStudentsBtn || !showTeachersBtn || !studentsList || !teachersList) return;

        if (showStudentsBtn.dataset && showStudentsBtn.dataset.bound === '1') return;
        if (showStudentsBtn.dataset) showStudentsBtn.dataset.bound = '1';

        showStudentsBtn.addEventListener('click', function() {
            studentsList.classList.remove('d-none');
            teachersList.classList.add('d-none');
            showStudentsBtn.classList.add('active');
            showTeachersBtn.classList.remove('active');
            if (filterTablesFn) filterTablesFn();
        });

        showTeachersBtn.addEventListener('click', function() {
            studentsList.classList.add('d-none');
            teachersList.classList.remove('d-none');
            showTeachersBtn.classList.add('active');
            showStudentsBtn.classList.remove('active');
            if (filterTablesFn) filterTablesFn();
        });
    }

    // Generic HTML loader for list/dashboards/pagination.
    // Used by: autoReloadStudents/Teachers/Courses, dashboard panels, and pagination clicks.
    function loadHtml($container, url, opts) {
        if (!$container || !$container.length) return;
        opts = opts || {};
        if (opts.loadingText) $container.html('<div class="text-muted">' + opts.loadingText + '</div>');
        $.get(url)
            .done(function(html) {
                $container.html(html);
                if (opts.persistUrl) $container.data('url', url);
                if (filterTablesFn) filterTablesFn();
                if (typeof opts.onDone === 'function') opts.onDone(html);
            })
            .fail(function(jqXHR) {
                var msg = (opts.failPrefix || 'Failed to load') + ' (' + (jqXHR.status || 'no status') + ').';
                if (opts.includeUrl) msg += ' URL: ' + url;
                setHtmlOrWarn($container, msg);
            });
    }

    // ---------- Lists ----------
    // Students list (resources/views/student.blade.php and demo.blade.php): #studentsList
    // Teachers list (resources/views/student.blade.php): #teachersList
    // Courses list (course pages): #coursesList
    var $studentsList = $('#studentsList');
    var $teachersList = $('#teachersList');
    var $coursesList = $('#coursesList');

    function autoReloadStudents() {
        if (!$studentsList.length) return;
        var url = $studentsList.data('url') || appUrl('/student/list');
        loadHtml($studentsList, url, { failPrefix: 'Failed to load students', includeUrl: true });
    }

    function autoReloadTeachers() {
        if (!$teachersList.length) return;
        var url = $teachersList.data('url') || appUrl('/teacher/list');
        loadHtml($teachersList, url, { failPrefix: 'Failed to load teachers', includeUrl: true });
    }

    function autoReloadCourses() {
        if (!$coursesList.length) return;
        var url = $coursesList.data('url') || appUrl('/course/list');
        loadHtml($coursesList, url, { failPrefix: 'Failed to load courses', includeUrl: true });
    }

    if ($studentsList.length) {
        autoReloadStudents();
        setInterval(autoReloadStudents, 5000);
    }
    if ($teachersList.length) {
        autoReloadTeachers();
        setInterval(autoReloadTeachers, 5000);
    }
    if ($coursesList.length) {
        autoReloadCourses();
    }

    // ---------- Teacher dashboard: load enrolled students ----------
    // Used on teacher dashboard (resources/views/teacherDashboard.blade.php):
    // - Left list items: .btn-show-enrolled (data-url)
    // - Right panel: #enrolledStudentsList
    function loadEnrolledStudents(url) {
        loadHtml($('#enrolledStudentsList'), url, {
            loadingText: 'Loading...',
            failPrefix: 'Failed to load enrolled students',
            persistUrl: true
        });
    }

    $(document).on('click', '.btn-show-enrolled', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        if (!url) return;

        // visual active state
        $('.btn-show-enrolled').removeClass('active');
        $(this).addClass('active');

        loadEnrolledStudents(url);
    });

    // auto-load first course on teacher dashboard
    if ($('#teacherCourses').length && $('.btn-show-enrolled').length) {
        $('.btn-show-enrolled').first().trigger('click');
    }

    // ---------- Student dashboard: load course details (teacher + classmates) ----------
    // Used on student dashboard pages:
    // - Buttons/links: .btn-student-course (data-url)
    // - Details container: #studentCourseDetails
    function loadStudentCourseDetails(url) {
        loadHtml($('#studentCourseDetails'), url, {
            loadingText: 'Loading...',
            failPrefix: 'Failed to load course details',
            persistUrl: true
        });
    }

    $(document).on('click', '.btn-student-course', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        if (!url) return;

        $('.btn-student-course').removeClass('active');
        $(this).addClass('active');

        loadStudentCourseDetails(url);
    });

    if ($('#studentCourses').length && $('.btn-student-course').length) {
        $('.btn-student-course').first().trigger('click');
    }

    // ---------- Pagination (AJAX) ----------
    // Used inside: #studentsList / #teachersList / #coursesList / #enrolledStudentsList / #studentCourseDetails
    $(document).on('click', '#studentsList .pagination a, #teachersList .pagination a, #coursesList .pagination a, #enrolledStudentsList .pagination a, #studentCourseDetails .pagination a', function(e) {
        e.preventDefault();

        var url = $(this).attr('href');
        if (!url) return;

        var container = $(this).closest('#studentsList, #teachersList, #coursesList, #enrolledStudentsList, #studentCourseDetails');
        if (!container.length) return;

        // Persist current page so auto-refresh doesn't jump back to page 1
        container.data('url', url);

        loadHtml(container, url, {
            failPrefix: 'Failed to load page',
            includeUrl: true
        });
    });

    // ---------- Create Course (AJAX) ----------
    // Used on course create page: #saveCourseBtn, #courseName, #addCourseAlert
    $(document).on('click', '#saveCourseBtn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $alert = $('#addCourseAlert');
        var url = $btn.data('url') || appUrl('/course');

        var data = {
            _token: $('#csrf_token').val(),
            course_name: $('#courseName').val()
        };

        $btn.prop('disabled', true).text('Saving...');
        if ($alert.length) $alert.addClass('d-none').text('');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function() {
                window.location.href = appUrl('/course');
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Save');
                var message = validationMessage(xhr) || 'Failed to create course. ' + httpHint(xhr && xhr.status);

                if ($alert.length) {
                    $alert.removeClass('d-none').text(message);
                } else {
                    alert(message);
                }
            }
        });
    });

    // ---------- Update Course (AJAX) ----------
    // Used on course edit page: #updateCourseBtn, #courseName, #editCourseAlert
    $(document).on('click', '#updateCourseBtn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $alert = $('#editCourseAlert');
        var url = $btn.data('url');
        if (!url) return;

        var data = {
            _token: $('#csrf_token').val(),
            _method: 'PUT',
            course_name: $('#courseName').val()
        };

        $btn.prop('disabled', true).text('Updating...');
        if ($alert.length) $alert.addClass('d-none').text('');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function() {
                window.location.href = appUrl('/course');
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Update');
                var message = validationMessage(xhr) || 'Failed to update course. ' + httpHint(xhr && xhr.status);

                if ($alert.length) {
                    $alert.removeClass('d-none').text(message);
                } else {
                    alert(message);
                }
            }
        });
    });

    // ---------- Delete Course (AJAX) ----------
    // Used on course list table: .btn-delete-course (data-action)
    $(document).on('click', '.btn-delete-course', function(e) {
        e.preventDefault();
        if (!confirm('Delete this course?')) return;
        var btn = $(this);
        var url = btn.data('action');
        if (!url) return;

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function() {
                autoReloadCourses();
                alert('Course deleted successfully!');
            },
            error: function(xhr) {
                alert('Failed to delete course. ' + httpHint(xhr && xhr.status));
            }
        });
    });

    // ---------- Enroll Student to Course (AJAX) ----------
    // Used in student list modal workflow:
    // - Open modal: #enrollStudentModal (button data-student-id/name)
    // - Submit enroll: #confirmEnrollBtn (POST /course/enroll)
    $(document).on('show.bs.modal', '#enrollStudentModal', function(e) {
        var button = $(e.relatedTarget);
        var studentId = button.data('student-id');
        var studentName = button.data('student-name');

        $('#enrollStudentId').val(studentId || '');
        $('#enrollStudentName').text(studentName || '—');
        $('#enrollCourseId').val('');
        $('#enrollSection').val('');
        $('#enrollTeacherId').val('');
        $('#enrollAlert').addClass('d-none').text('');
        $('#confirmEnrollBtn').prop('disabled', false).text('Enroll');
    });

    $(document).on('click', '#confirmEnrollBtn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var url = $btn.data('url') || appUrl('/course/enroll');
        var studentId = $('#enrollStudentId').val();
        var courseId = $('#enrollCourseId').val();
        var section = $('#enrollSection').val();
        var teacherId = $('#enrollTeacherId').val();
        var $alert = $('#enrollAlert');

        if (!studentId || !courseId) {
            $alert.removeClass('d-none').text('Please select a course.');
            return;
        }

        $btn.prop('disabled', true).text('Enrolling...');
        $alert.addClass('d-none').text('');

        var payload = {
            student_id: studentId,
            course_id: courseId,
            section: section
        };
        if (teacherId) payload.teacher_id = teacherId;

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            success: function(resp) {
                var modalEl = document.getElementById('enrollStudentModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                alert(resp.message || 'Student enrolled successfully!');
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Enroll');
                var message = validationMessage(xhr) || ('Failed to enroll student. ' + httpHint(xhr && xhr.status));
                $alert.removeClass('d-none').text(message);
            }
        });
    });

    // ---------- Bulk Enroll Page (Admin) ----------
    // Used on bulk enroll page:
    // - List container: #enrollStudentsList
    // - Search: #enrollStudentsSearchBtn / #enrollStudentsClearSearch
    // - Select all: #selectAllEnrollStudents
    // - Submit: #bulkEnrollBtn
    var selectedEnrollStudentIds = {};

    function updateSelectedCountText() {
        var count = Object.keys(selectedEnrollStudentIds).length;
        $('#selectedCountText').text(count + ' selected');
    }

    function loadEnrollStudentsList(url) {
        var container = $('#enrollStudentsList');
        if (!container.length) return;
        url = url || container.data('url') || appUrl('/enrollstudent/students');

        container.html('<div class="text-muted">Loading students...</div>');
        $.get(url)
            .done(function(html) {
                container.html(html);
                container.data('url', url);

                // restore checked state
                container.find('.enroll-student-checkbox').each(function() {
                    var sid = String($(this).val());
                    if (selectedEnrollStudentIds[sid]) {
                        $(this).prop('checked', true);
                    }
                });

                updateSelectedCountText();
            })
            .fail(function(jqXHR) {
                var msg = 'Failed to load students (' + (jqXHR.status || 'no status') + ').';
                container.html('<div class="alert alert-warning mb-0">' + msg + '</div>');
            });
    }

    if ($('#enrollStudentsList').length) {
        selectedEnrollStudentIds = {};
        updateSelectedCountText();
        loadEnrollStudentsList();
    }

    $(document).on('click', '#enrollStudentsSearchBtn', function() {
        var q = $('#enrollStudentsSearch').val() || '';
        var baseUrl = $('#enrollStudentsList').data('url') || appUrl('/enrollstudent/students');
        var url = baseUrl.split('?')[0] + '?q=' + encodeURIComponent(q);
        loadEnrollStudentsList(url);
    });

    $(document).on('click', '#enrollStudentsClearSearch', function() {
        $('#enrollStudentsSearch').val('');
        loadEnrollStudentsList(appUrl('/enrollstudent/students'));
    });

    $(document).on('change', '.enroll-student-checkbox', function() {
        var sid = String($(this).val());
        if ($(this).is(':checked')) selectedEnrollStudentIds[sid] = true;
        else delete selectedEnrollStudentIds[sid];
        updateSelectedCountText();
    });

    $(document).on('change', '#selectAllEnrollStudents', function() {
        var checked = $(this).is(':checked');
        $('#enrollStudentsList .enroll-student-checkbox').each(function() {
            $(this).prop('checked', checked).trigger('change');
        });
    });

    // paginate inside bulk enroll list
    $(document).on('click', '#enrollStudentsList .pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (url) loadEnrollStudentsList(url);
    });

    $(document).on('click', '#bulkEnrollBtn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var $alert = $('#bulkEnrollAlert');
        var url = $btn.data('url') || appUrl('/course/bulk-enroll');

        var courseId = $('#bulkCourseId').val();
        var teacherId = $('#bulkTeacherId').val();
        var section = $('#bulkSection').val();
        var studentIds = Object.keys(selectedEnrollStudentIds).map(function(x) { return parseInt(x, 10); });

        $alert.addClass('d-none').text('');

        if (!courseId || !teacherId) {
            $alert.removeClass('d-none').text('Please select a course and a teacher.');
            return;
        }
        if (!studentIds.length) {
            $alert.removeClass('d-none').text('Please select at least one student.');
            return;
        }

        $btn.prop('disabled', true).text('Enrolling...');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                course_id: courseId,
                teacher_id: teacherId,
                section: section,
                student_ids: studentIds
            },
            success: function(resp) {
                alert(resp.message || 'Students enrolled successfully!');
                // reset selection
                selectedEnrollStudentIds = {};
                updateSelectedCountText();
                loadEnrollStudentsList();
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Enroll Selected');
                var message = validationMessage(xhr) || ('Failed to enroll students. ' + httpHint(xhr && xhr.status));
                $alert.removeClass('d-none').text(message);
            }
        });
    });

    // ---------- View Student (AJAX -> modal) ----------
    // Used in Student list (View button opens #viewStudentModal and fetches JSON).
    $(document).on('show.bs.modal', '#viewStudentModal', function(e) {
        var trigger = e.relatedTarget;
        if (!trigger) return;

        var $trigger = $(trigger);
        var url = $trigger.data('url');
        var studentId = $trigger.data('student-id');
        if (!url && studentId) url = appUrl('/student/' + studentId + '/json');
        if (!url) return;

        $('#viewStudentFirstName, #viewStudentMiddleName, #viewStudentLastName, #viewStudentEmail, #viewStudentContactNo, #viewStudentDegree').text('Loading...');
        var $img = $('#viewStudentImage');
        if ($img.length) $img.addClass('d-none').attr('src', '');

        $.get(url)
            .done(function(data) {
                $('#viewStudentFirstName').text(data.first_name || '—');
                $('#viewStudentMiddleName').text(data.middle_name || '—');
                $('#viewStudentLastName').text(data.last_name || '—');
                $('#viewStudentEmail').text(data.email || '—');
                $('#viewStudentContactNo').text(data.contact_no || '—');
                $('#viewStudentDegree').text(data.degree_name || '—');

                if ($img.length) {
                    if (data.image_url) {
                        $img.attr('src', data.image_url).removeClass('d-none');
                    } else {
                        $img.addClass('d-none').attr('src', '');
                    }
                }
            })
            .fail(function(jqXHR) {
                var msg = 'Failed to load student details.';
                if (jqXHR && jqXHR.status) msg += ' (' + jqXHR.status + ')';
                $('#viewStudentFirstName').text('—');
                $('#viewStudentMiddleName').text('—');
                $('#viewStudentLastName').text('—');
                $('#viewStudentEmail').text('—');
                $('#viewStudentContactNo').text('—');
                $('#viewStudentDegree').text('—');
                if ($img.length) $img.addClass('d-none').attr('src', '');
                alert(msg);
            });
    });

    // ---------- View Teacher (AJAX -> modal) ----------
    // Used in Teacher list (View button opens #viewTeacherModal and fetches JSON).
    $(document).on('show.bs.modal', '#viewTeacherModal', function(e) {
        var trigger = e.relatedTarget;
        if (!trigger) return;

        var $trigger = $(trigger);
        var url = $trigger.data('url');
        var teacherId = $trigger.data('teacher-id');
        if (!url && teacherId) url = appUrl('/teacher/' + teacherId + '/json');
        if (!url) return;

        $('#viewTeacherFirstName, #viewTeacherMiddleName, #viewTeacherLastName, #viewTeacherEmail, #viewTeacherContactNo').text('Loading...');
        var $img = $('#viewTeacherImage');
        if ($img.length) $img.addClass('d-none').attr('src', '');

        $.get(url)
            .done(function(data) {
                $('#viewTeacherFirstName').text(data.first_name || '—');
                $('#viewTeacherMiddleName').text(data.middle_name || '—');
                $('#viewTeacherLastName').text(data.last_name || '—');
                $('#viewTeacherEmail').text(data.email || '—');
                $('#viewTeacherContactNo').text(data.contact_no || '—');

                if ($img.length) {
                    if (data.image_url) {
                        $img.attr('src', data.image_url).removeClass('d-none');
                    } else {
                        $img.addClass('d-none').attr('src', '');
                    }
                }
            })
            .fail(function(jqXHR) {
                var msg = 'Failed to load teacher details.';
                if (jqXHR && jqXHR.status) msg += ' (' + jqXHR.status + ')';
                $('#viewTeacherFirstName').text('—');
                $('#viewTeacherMiddleName').text('—');
                $('#viewTeacherLastName').text('—');
                $('#viewTeacherEmail').text('—');
                $('#viewTeacherContactNo').text('—');
                if ($img.length) $img.addClass('d-none').attr('src', '');
                alert(msg);
            });
    });

    // ---------- Delete Student (populate modal via jQuery) ----------
    // Used in Student list (Delete button opens #deleteStudentModal and sets form action).
    $(document).on('show.bs.modal', '#deleteStudentModal', function(e) {
        var trigger = e.relatedTarget;
        if (!trigger) return;

        var $trigger = $(trigger);
        var action = $trigger.data('student-action');
        var name = $trigger.data('student-name');

        if (action) $('#deleteStudentForm').attr('action', action);
        if (name) $('#studentNameInModal').text(name);
    });

    // ---------- Create Student ----------
    // Used on student create page: #savedStudent
    $(document).on('click', '#savedStudent', function(e) {
        e.preventDefault();
        console.debug('savedStudent clicked');

        var url = $(this).data('url') || appUrl('/student');

        var fname = $('#firstName').val();
        var mname = $('#middleName').val();
        var lname = $('#lastName').val();
        var email = $('#email').val();
        var degree = $('#degree').val();
        var contactNo = $('#contactNo').val();
        var username = $('#username').val();
        var password = $('#password').val();

        var formData = new FormData();
        var token = $('input[name="_token"]').first().val();
        if (token) formData.append('_token', token);
        formData.append('first_name', fname);
        formData.append('middle_name', mname || '');
        formData.append('last_name', lname);
        formData.append('email', email);
        formData.append('degree_id', degree || '');
        formData.append('contact_no', contactNo || '');
        formData.append('username', username);
        formData.append('password', password);

        var imageInput = document.getElementById('profileImage');
        if (imageInput && imageInput.files && imageInput.files[0]) {
            formData.append('profile_image', imageInput.files[0]);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Student created successfully!');
                window.location.href = appUrl('/manageStudents');
            },
            error: function(xhr) {
                console.debug('AJAX error (student):', xhr && xhr.status, xhr && xhr.responseText);
                alert(validationMessage(xhr) || ('Failed to create student. ' + httpHint(xhr && xhr.status)));
            }
        });
    });

    // ---------- Create Teacher (enhanced error alert) ----------
    // Used on teacher create page: #savedTeacher
    $(document).on('click', '#savedTeacher', function(e) {
        e.preventDefault();
        console.debug('savedTeacher clicked');

        var url = $(this).data('url') || appUrl('/teacher');

        var fname = $('#firstName').val();
        var mname = $('#middleName').val();
        var lname = $('#lastName').val();
        var email = $('#email').val();
        var contactNo = $('#contactNo').val();
        var username = $('#username').val();
        var password = $('#password').val();

        var formData = new FormData();
        var token = $('input[name="_token"]').first().val();
        if (token) formData.append('_token', token);
        formData.append('first_name', fname);
        formData.append('middle_name', mname || '');
        formData.append('last_name', lname);
        formData.append('email', email);
        formData.append('contact_no', contactNo || '');
        formData.append('username', username);
        formData.append('password', password);

        var imageInput = document.getElementById('profileImage');
        if (imageInput && imageInput.files && imageInput.files[0]) {
            formData.append('profile_image', imageInput.files[0]);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Teacher created successfully!');
                window.location.href = appUrl('/manageStudents');
            },
            error: function(xhr) {
                console.debug('AJAX error (teacher):', xhr && xhr.status, xhr && xhr.responseText);
                alert(validationMessage(xhr) || ('Failed to create teacher. ' + httpHint(xhr && xhr.status)));
            }
        });
    });

    // ---------- Update Teacher (AJAX) ----------
    // Used on teacher edit page: <form id="editTeacherForm"> (POST with _method=PUT)
    $(document).on('submit', '#editTeacherForm', function(e) {
        e.preventDefault();
        var form = $(this);

        var formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                alert('Teacher updated successfully!');
                window.location.href = appUrl('/manageStudents');
            },
            error: function(xhr) {
                alert(validationMessage(xhr) || ('Failed to update teacher. ' + httpHint(xhr && xhr.status)));
            }
        });
    });

    // ---------- Update Student (AJAX) ----------
    // Used on student edit page (form submit version): <form id="editStudentForm">
    $(document).on('submit', '#editStudentForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        var formData = new FormData(form[0]);
        if (!formData.has('_method')) formData.append('_method', 'PUT');
        if (!formData.has('email') && form.find('#email').length) {
            formData.append('email', form.find('#email').val());
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                alert('Student updated successfully!');
                window.location.href = appUrl('/manageStudents');
            },
            error: function(xhr) {
                alert(validationMessage(xhr) || ('Failed to update student. ' + httpHint(xhr && xhr.status)));
            }
        });
    });

    // ---------- Create Degree (AJAX) ----------
    // Used on degree create page (form submit version): <form id="addDegreeForm">
    $(document).on('submit', '#addDegreeForm', function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function() {
                alert('Degree created successfully!');
                window.location.href = appUrl('/degree');
            },
            error: function(xhr) {
                alert(validationMessage(xhr) || ('Failed to create degree. ' + httpHint(xhr && xhr.status)));
            }
        });
    });

    // ---------- Delete Teacher (enhanced error alert) ----------
    // Used in teacher list: .btn-delete-teacher (data-action)
    $(document).on('click', '.btn-delete-teacher', function(e) {
        e.preventDefault();
        if (!confirm('Delete this teacher?')) return;
        var btn = $(this);
        var url = btn.data('action');

        $.ajax({
            url: url,
            type: 'DELETE',
            success: function(response) {
                btn.closest('tr').remove();
                alert('Teacher deleted successfully!');
            },
            error: function(xhr) {
                alert('Failed to delete teacher. ' + httpHint(xhr && xhr.status));
            }
        });
    });

    // ---------- Delete Student (AJAX via modal form) ----------
    // Used in delete confirmation modal: <form id="deleteStudentForm"> inside #deleteStudentModal
    $(document).on('submit', '#deleteStudentForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        if (!url) {
            alert('Delete action is missing.');
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _method: 'DELETE'
            },
            success: function() {
                var modalEl = document.getElementById('deleteStudentModal');
                if (modalEl && window.bootstrap) {
                    var modal = window.bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                }
                autoReloadStudents();
                alert('Student deleted successfully!');
            },
            error: function(xhr) {
                alert('Failed to delete student. ' + httpHint(xhr && xhr.status));
            }
        });
    });

    // ---------- Login (AJAX) ----------
    // Used on login page: #loginBtn, #loginAlert
$(document).on('click', '#loginBtn', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var $alert = $('#loginAlert');
    var username = $('#username').val();
    var password = $('#password').val();

    // Basic client-side validation
    if (!username || !password) {
        if ($alert.length) {
            $alert.removeClass('d-none').text('Please enter both username and password.');
        } else {
            alert('Please enter both username and password.');
        }
        return;
    }

    // Disable button to prevent double submission
    $btn.prop('disabled', true).text('Logging in...');
    if ($alert.length) $alert.addClass('d-none').text('');

    $.ajax({
        url: appUrl('/'),   // Login POST endpoint
        type: 'POST',
        data: {
            username: username,
            password: password
        },
        success: function(response) {
            // Prefer server-provided redirect (e.g. force password change)
            if (response.redirect) {
                window.location.href = response.redirect;
                return;
            }

            // Redirect based on user role
            if (response.role === 'student') {
                window.location.href = appUrl('/studentDashboard');
            } else if (response.role === 'teacher') {
                window.location.href = appUrl('/teacherDashboard');
            } else {
                window.location.href = appUrl('/student');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).text('Login');
            var message = validationMessage(xhr) || 'Login failed. ' + httpHint(xhr && xhr.status);

            if ($alert.length) {
                $alert.removeClass('d-none').text(message);
            } else {
                alert(message);
            }
        }
    });
});

    // ---------- Update Student (AJAX) – no <form> version ----------
    // Used on student edit page (button version in resources/views/editStudent.blade.php): #updateStudentBtn
$(document).on('click', '#updateStudentBtn', function(e) {
    e.preventDefault();
    var $btn = $(this);
    var $alert = $('#editStudentAlert'); // error alert container
    var url = $btn.data('url') || window.location.href; // fallback to current URL

    var formData = new FormData();
    formData.append('_token', $('#csrf_token').val());
    formData.append('_method', 'PUT');
    formData.append('first_name', $('#firstName').val());
    formData.append('middle_name', $('#middleName').val() || '');
    formData.append('last_name', $('#lastName').val());
    formData.append('email', $('#email').val());
    formData.append('contact_no', $('#contactNo').val() || '');
    formData.append('degree_id', $('#degree').val() || '');

    var imageInput = document.getElementById('profileImage');
    if (imageInput && imageInput.files && imageInput.files[0]) {
        formData.append('profile_image', imageInput.files[0]);
    }

    // Basic client-side validation
    if (!formData.get('first_name') || !formData.get('last_name')) {
        if ($alert.length) {
            $alert.removeClass('d-none').text('First Name and Last Name are required.');
        } else {
            alert('First Name and Last Name are required.');
        }
        return;
    }

    // Disable button, clear previous errors
    $btn.prop('disabled', true).text('Updating...');
    if ($alert.length) $alert.addClass('d-none').text('');

    $.ajax({
        url: url,
        type: 'POST',               // because _method=PUT overrides
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert('Student updated successfully!');
            window.location.href = appUrl('/manageStudents');  // adjust redirect as needed
        },
        error: function(xhr) {
            $btn.prop('disabled', false).text('Update');
            var message = validationMessage(xhr) || ('Update failed. ' + httpHint(xhr && xhr.status));

            if ($alert.length) {
                $alert.removeClass('d-none').text(message);
            } else {
                alert(message);
            }
        }
    });
});
    // ---------- Create Degree (AJAX) – no <form> version ----------
    // Used on degree create page (button version in resources/views/adddegree.blade.php): #saveDegreeBtn
$(document).on('click', '#saveDegreeBtn', function(e) {
    e.preventDefault();

    var $btn = $(this);
    var $alert = $('#addDegreeAlert');
    var degreeName = $('#degreeName').val();
    var token = $('#csrf_token').val();

    // Basic client-side validation
    if (!degreeName.trim()) {
        $alert.removeClass('d-none').text('Degree name is required.');
        return;
    }

    // Disable button, clear previous errors
    $btn.prop('disabled', true).text('Saving...');
    $alert.addClass('d-none').text('');

    $.ajax({
        url: appUrl('/degree'),
        type: 'POST',
        data: {
            _token: token,
            degree_name: degreeName
        },
        success: function(response) {
            alert('Degree created successfully!');
            window.location.href = appUrl('/degree');   // redirect to degree index
        },
        error: function(xhr) {
            $btn.prop('disabled', false).text('Save');
            var message = validationMessage(xhr) || ('Failed to create degree. ' + httpHint(xhr && xhr.status));

            $alert.removeClass('d-none').text(message);
        }
    });
});
   
    // Student details page (resources/views/studentDetails.blade.php):
    // Loads JSON into #detail_* spans and toggles #loadingSpinner.
    function loadStudentDetailsIfPresent() {
        if (!$('#studentDetailsContainer').length) return;

        var studentId = $('#studentId').val();
        if (!studentId) {
            var pathParts = window.location.pathname.split('/');
            studentId = pathParts[2];
        }

        if (!studentId) {
            $('#studentDetailsContainer').html('<div class="alert alert-warning">No student ID provided.</div>');
            return;
        }

        var url = appUrl('/student/' + studentId + '/json');
        $('#loadingSpinner').show();

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#detail_first_name').text(data.first_name || '—');
                $('#detail_middle_name').text(data.middle_name || '—');
                $('#detail_last_name').text(data.last_name || '—');
                $('#detail_email').text(data.email || '—');
                $('#detail_contact_no').text(data.contact_no || '—');
                $('#detail_degree').text(data.degree_name || '—');
            },
            error: function(xhr) {
                console.error('Failed to load student details', xhr);
                $('#studentDetailsContainer').html('<div class="alert alert-danger">Could not load student details.</div>');
            },
            complete: function() {
                $('#loadingSpinner').hide();
            }
        });
    }

    initFilter();
    initToggleTables();
    loadStudentDetailsIfPresent();
});