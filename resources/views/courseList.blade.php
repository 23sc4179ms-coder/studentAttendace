<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Course</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td>{{ $course->course_name }}</td>
                    <td class="text-end">
                        <div class="btn-group" role="group" aria-label="Course actions">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('course.edit', $course->id) }}">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger btn-delete-course"
                                data-action="{{ route('course.destroy', $course->id) }}"
                            >Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-muted py-4">No courses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($courses, 'links'))
    <div class="mt-3">
        {{ $courses->links('pagination::bootstrap-5') }}
    </div>
@endif
