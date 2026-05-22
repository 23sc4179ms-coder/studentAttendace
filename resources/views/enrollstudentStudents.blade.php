<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width: 48px;">
                    <input type="checkbox" id="selectAllEnrollStudents" />
                </th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Degree</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                @php
                    $fullName = trim($student->first_name . ' ' . ($student->middle_name ?? '') . ' ' . $student->last_name);
                    $degreeName = optional($student->degree)->degree_name;
                @endphp
                <tr>
                    <td>
                        <input type="checkbox" class="enroll-student-checkbox" value="{{ $student->id }}" />
                    </td>
                    <td>{{ $fullName }}</td>
                    <td>{{ optional($student->userAccount)->email ?? '—' }}</td>
                    <td>{{ $student->contact_no }}</td>
                    <td>{{ $degreeName ?: '—' }}</td>
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
