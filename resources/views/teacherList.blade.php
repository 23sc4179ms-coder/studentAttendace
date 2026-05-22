<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($teachers as $teacher)
                @php
                    $fullName = trim($teacher->first_name . ' ' . ($teacher->middle_name ?? '') . ' ' . $teacher->last_name);
                @endphp
                <tr>
                    <td>{{ $fullName }}</td>
                    <td>{{ $teacher->email }}</td>
                    <td>{{ $teacher->contact_no }}</td>
                    <td class="text-end">
                        <div class="btn-group" role="group" aria-label="Teacher actions">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary btn-view-teacher"
                                data-bs-toggle="modal"
                                data-bs-target="#viewTeacherModal"
                                data-teacher-id="{{ $teacher->id }}"
                                data-url="{{ route('teacher.showJson', $teacher->id) }}"
                            >View</button>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.edit', $teacher->id) }}">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger btn-delete-teacher"
                                data-action="{{ route('teacher.destroy', $teacher->id) }}"
                            >Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No teachers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($teachers, 'links'))
    <div class="mt-3">
        {{ $teachers->links('pagination::bootstrap-5') }}
    </div>
@endif
