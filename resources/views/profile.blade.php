@if($errors->any())
        <h4 style="color: red">{{$errors->first()}}</h4>
        @endif
<!DOCTYPE html>
<html>
<head>
    <title>IE Project 4</title>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('Stylesheets/Profile.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('Stylesheets/LoginRegister.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('Stylesheets/Users.css') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div id="data">
    <!--<img src=""><br>
    <span>First Name: </span>
    <sapn >farid</span><br>
    <span>Last Name: </span>
    <span>laya</span><br>
    <span>Email: </span>
    <span>e@gmail.com</span>!-->
</div>
<!--<form id="registeration" style="margin-left: auto;margin-right: auto;">
    <div style="width: 70%;height: 300px;margin-left: auto;margin-right: auto;">
        <div class="titles">
            <h5>First Name :</h5>
            <h5>Last Name :</h5>
            <h5>Email :</h5>
            <h5>Password :</h5>
            <h5>Image :</h5>
        </div>
        <div class="inputs">
            <input type="text" name="firstname">
            <input type="text" name="lastname">
            <input type="email" name="email">
            <input type="password" name="pass">
            <input type="file" name="image">
        </div>
        <input type="submit" value="Save Changes" style="background-color: #97d9ff">
    </div>
</form>-->

{!! Form::open(['url' => url('/server'),'id' => 'registeration','enctype'=>'multipart/form-data' , 'style' => 'margin-left: auto;margin-right: auto;']) !!}
    <div style="width: 70%;height: 300px;margin-left: auto;margin-right: auto;">
        <div class="titles">
            {!! Form::label('firstname','First name: ') !!}
            {!! Form::label('lastname','Last name: ') !!}
            {!! Form::label('email','Email: ') !!}
            {!! Form::label('password','Password: ') !!}
            {!! Form::label('avatar','Image: ') !!}
        </div>
        <div class="inputs">
            {!! Form::text('firstname','',['required']) !!}
            {!! Form::text('lastname','',['required']) !!}
            {!! Form::email('email','',['required']) !!}
            {!! Form::password('password','' )!!}
            {!! Form::file('avatar','') !!}
            {!! Form::hidden('type','profile_edit') !!}
        </div>
        {!! Form::submit('save changes' , ['style' => 'background-color: #97d9ff']) !!}
    </div>
{!! Form::close() !!}
<a href="{{URL::to('/inbox')}}"><button>inbox</button></a>
<a href="{{URL::to('/Users')}}"><button>Users</button></a>


<div id="contacts">
    <!--<div class="person">
        <img src=""><br>
        <span>First Name: </span>
        <span >hasan</span><br>
        <span>Last Name: </span>
        <span>hasani</span><br>
        <span>Email: </span>
        <span>hasan@e.ir</span><br>
        </div>
    <div class="person">
        <img src=""><br>
        <span>First Name: </span>
        <span >hasan</span><br>
        <span>Last Name: </span>
        <span>hasani</span><br>
        <span>Email: </span>
        <span>hasan@e.ir</span><br>
    </div>!-->
</div>
</body>
</html>
<script src="{{ URL::asset('js/jquery-1.12.0.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#password').attr('required','required');
        $('#password').attr('pattern','.{6,}');
        $('#password').attr('title',"Six or more characters");
        $.ajax({
            type: "GET",
            url: "/server/type=profile",
            dataType: "xml",
            cache: false,
            async: false,
            success: function (xml) {
                var data=$(xml).children('data');
                var contacts=data.children('contacts');

                var it='<img style="width:80px;height:80px;" src="'+data.children('img').text()+'"><br><span>First Name: </span><sapn >'+data.children('first').text()+'</span><br><span>Last Name: </span><span>'+data.children('last').text()+'</span><br><span>Email: </span><span>'+data.children('username').text()+'</span>';
                    it += '<br/><span>Last login: </span><span>'+data.children('login').text()+'</span>';
                $("#data").append(it);
                contacts.children('contact').each(function(){
                    var person='<div class="person"><img src="'+$(this).children('img').text()+'"><br><span>First Name: </span><span >'+$(this).children('first').text()+'</span><br><span>Last Name: </span><span>'+$(this).children('last').text()+'</span><br><span>Email: </span><span>'+$(this).children('username').text()+'</span><br></div>';
                    $("#contacts").append(person);
                });

            }, error: function (jqXHR, textStatus, errorThrown) {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                console.log(jqXHR.responseText);
                // STOP LOADING SPINNER
            }
        });
    });


</script>