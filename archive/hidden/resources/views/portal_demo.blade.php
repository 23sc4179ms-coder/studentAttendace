@extends('format.portal_layout')

@section('title')
    Demo Page
    @endsection

    @section('header')
        @parent
    @endsection

    @section('content')
            <!-- <button id="demoButton" class="btn btn-primary">Click me</button>

       <h1 id="demoParagraph">
        This is the Demo Page
        This is the Demo Page
        This is the Demo Page
    </h1>  -->
       <!-- <form id="demoForm"> -->
        <!-- <div class="mb-3">

            <label for="demoInput" class="form-label">Enter something:</label>
            <input type="text" class="form-control" id="demoInput" placeholder="Type here...">
         </div>
        <button id="demoSubmit" class="btn btn-success">Submit</button>
        </form> -->
        <!-- <button id="demoHover" class="btn btn-primary">Hover me</button> -->
        <!-- <button id="viewStudents" class="btn btn-info">View Students</button> -->
        <div id="studentsList" data-url="{{ route('student.list') }}"></div>
        @endsection

    @section('footer')
        @parent
    @endsection


