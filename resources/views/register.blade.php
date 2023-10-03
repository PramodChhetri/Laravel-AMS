@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error )
            <li style="color: red">{{$error}}</li>
        @endforeach
    </ul>
@endif

<form action="{{route('userRegister')}}" method="POST">
    @csrf

    <input type="text" name="name" placeholder="Enter Name">
    <br><br>
    <input type="email" name="email" placeholder="Enter Email">
    <br><br>
    <input type="password" name="password" placeholder="Enter Password">
    <br><br>
    <input type="password" name="password_confirmation" placeholder="Comfirm Password">
    <br><br>
    <input type="submit" value="Register">

</form>


@if (\Session::has('success'))
    <p style="color: green">{{\Session::get('success')}}</p>
@endif