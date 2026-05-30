@extends('layouts.layout')

@section('title')
    Student Page
    @endsection

    @section('header')
        @parent
    @endsection

    @section('content')
       <h1>This is the Student Page</h1>
       
             <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Number</th>
                            <th scope="col">Name</th>
                            <th scope="col">Age</th>
                            <th scope="col">Course and Program</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student['name'] }}</td>
                                <td>{{ $student['Age'] }}</td>
                                <td>{{ $student['Course'] }}</td>
                                <td>
                                    @if ($student['Age']==19)
                                        {{ $stat="Freshman Student" }}
                                    @endif
                                    @if ($student['Age']==20)
                                        {{ $stat="Sophomore Student" }}
                                    @endif
                                    @if ($student['Age']==21)
                                        {{ $stat="Junior Student" }}
                                    @endif
                                    @if ($student['Age']==22)
                                        {{ $stat="Senior Student" }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">There are no students to display</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
             </div>

    @endsection

    @section('footer')
        @parent
    @endsection



