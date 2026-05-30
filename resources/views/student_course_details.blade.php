<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <div class="fw-semibold">{{ $course->course_name }}</div>
        <div class="text-muted small">
            Section: {{ $attendance->section ?: '—' }}
            @if($teacherName)
                &nbsp;|&nbsp; Teacher: {{ $teacherName }}
            @else
                &nbsp;|&nbsp; Teacher: —
            @endif
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Classmates</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Degree</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($classmates as $student)
                @php
                    $fullName = trim($student->first_name . ' ' . ($student->middle_name ?? '') . ' ' . $student->last_name);
                    $degreeName = optional($student->degree)->degree_name;
                @endphp
                <tr>
                    <td>{{ $fullName }}</td>
                    <td>{{ optional($student->userAccount)->email ?? '—' }}</td>
                    <td>{{ $student->contact_no }}</td>
                    <td>{{ $degreeName ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No classmates found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($classmates, 'links'))
    <div class="mt-3">
        {{ $classmates->links('pagination::bootstrap-5') }}
    </div>
@endif
