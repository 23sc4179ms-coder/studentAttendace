<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Degree</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                @php
                    $fullName = trim($student->first_name . ' ' . ($student->middle_name ?? '') . ' ' . $student->last_name);
                    $degreeName = optional($student->degree)->degree_name;
                @endphp
                <tr>
                    <td>{{ $fullName }}</td>
                    <td>{{ optional($student->userAccount)->email ?? '—' }}</td>
                    <td>{{ $student->contact_no }}</td>
                    <td>{{ $degreeName ?: '—' }}</td>
                    <td class="text-end">
                        <div class="btn-group" role="group" aria-label="Student actions">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary btn-view-student"
                                data-bs-toggle="modal"
                                data-bs-target="#viewStudentModal"
                                data-student-id="{{ $student->id }}"
                                data-url="{{ route('student.showJson', $student->id) }}"
                            >View</button>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-success btn-enroll-student"
                                data-bs-toggle="modal"
                                data-bs-target="#enrollStudentModal"
                                data-student-id="{{ $student->id }}"
                                data-student-name="{{ $fullName }}"
                            >Enroll</button>

                            <a class="btn btn-sm btn-outline-primary" href="{{ route('student.edit', $student->id) }}">Edit</a>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteStudentModal"
                                data-student-action="{{ route('student.destroy', $student->id) }}"
                                data-student-name="{{ $fullName }}"
                            >Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No students found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($students, 'links'))
    <div class="mt-3">
        {{ $students->links('pagination::bootstrap-5') }}
    </div>
@endif