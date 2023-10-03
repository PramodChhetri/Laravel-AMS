@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error )
            <li style="color: red">{{$error}}</li>
        @endforeach
    </ul>
@endif

<form action="{{route('userLogin')}}" method="POST">
    @csrf

    <input type="email" name="email" placeholder="Enter Email">
    <br><br>
    <input type="password" name="password" placeholder="Enter Password">
    <br><br>
    <input type="submit" value="Login">

</form>


@if (\Session::has('error'))
    <p style="color: red">{{\Session::get('error')}}</p>
@endif