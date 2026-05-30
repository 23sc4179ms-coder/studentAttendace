@extends('layouts.admin')

@section('title')
    Degree Page
@endsection

@section('header')
    @parent
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <h1 class="h4 m-0">Degrees</h1>
        <a href="{{ route('degree.create') }}" class="btn-ui btn-ui--primary">Add Degree</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Degree</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($degrees as $degree)
                    <tr>
                        <td>{{ $degree->degree_name }}</td>
                        <td>
                            <div class="actions-inline">
                                <button
                                    type="button"
                                    class="btn-ui btn-ui--ghost btn-ui--sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewDegreeModal"
                                    data-degree-name="{{ $degree->degree_name }}"
                                >
                                    View
                                </button>

                                <a
                                    href="{{ route('degree.edit', $degree->id) }}"
                                    class="btn-ui btn-ui--ghost btn-ui--sm"
                                >
                                    Edit
                                </a>

                                <!-- <button
                                    type="button"
                                    class="btn-ui btn-ui--danger btn-ui--sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteDegreeModal"
                                    data-degree-action="{{ route('degree.destroy', $degree->id) }}"
                                    data-degree-name="{{ $degree->degree_name }}"
                                >
                                    Delete
                                </button> -->
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">No degrees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $degrees->links() }}

    @include('layouts.includes.degree_modals')
@endsection

@section('footer')
    @parent
@endsection


