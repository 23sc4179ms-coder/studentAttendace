@extends('format.portal_layout')

@section('title')
    Clients Page
    @endsection

    @section('header')
        @parent

    @endsection

    @section('content')
       <h1>This is the Client Page. <br></h1>
     
       

       
        @for($a=1;$a<=5;$a++)
       
            @for($b=1;$b<=$a;$b++)
            *
            @endfor
             <br>
        @endfor

        {{ $a=1 }}<br>
        @while ($a<10)
        @php
        $a++;
        @endphp
        {{ $a }}<br>
        @endwhile

       

         {{ $grade }}<br>
         @if ($grade>=75 && $grade<=100)
            
           
         Your Grade {{ $grade }} is Passed!
         @elseif($grade>=1 && $grade<=74)
        Your Grade is {{ $grade }} is Failed!
        
        @else
        Your grade is {{ $grade }} invalid
         @endif
         @if ($grade%2==0)
                It is Odd
                
                @elseif ($grade%2!=0)
                It is Even
        @endif
        <br>

        
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Sex</th>
                <th scope="col">Address</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($clients as $client)
                <tr>
                  <td>{{ $client['name'] }}</td>
                  <td>{{ $client['sex'] }}</td>
                  <td>{{ $client['address'] }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">There are no clients to display</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        
        @isset($clients)
        <p>Client storage is set!</p>
        @endisset

       
       @foreach ($clients as $client )
       @if($loop->first)
     {{ $client['name'] }}
      {{ $client['sex'] }}
       {{ $client['address'] }}
      
        @endif

         {{ $loop->count }}
        
      
       @endforeach
        
    @endsection

    @section('footer')
        @parent
    @endsection


