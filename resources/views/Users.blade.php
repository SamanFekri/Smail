<!DOCTYPE html>
<html>
<head>
    <title>IE Project 4</title>
    <link rel="stylesheet" type="text/css" href=" {{URL::asset('Stylesheets/Profile.css') }}">
    <link rel="stylesheet" type="text/css" href="{{URL::asset('Stylesheets/Users.css') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<!--<div class="person">
    <img src=""><br>
    <span>First Name: </span>
    <span >hasan</span><br>
    <span>Last Name: </span>
    <span>hasani</span><br>
    <span>Email: </span>
    <span class="email">hasan@e.ir</span><br>
    <button type="button" class="add">Add to my contacts</button>
	</div>-->
    <a href="{{ URL::to('/inbox') }}"><button>Inbox</button></a>
</body>
</html>
<script src="{{ URL::asset('js/jquery-1.12.0.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
            type:"GET",
            url : "/server/type=users",
            dataType : "xml",
            cache : false ,
            async : false ,
            success : function (xml) {

                //var data=$(xml).children('users');
                var all=$(xml).children('users').children("user");
                //contacts.children('user').each(function(){
                all.each(function(){
                    if($(this).children('stat').text()==1){
                        var status="Already Friend";
                        var gclass="green";
                        var cclass="not";
                    }
                    else{
                        var status="Add to my contacts";
                        var cclass="add";
                    }
                    var person='<div class="person"><img id="profile" style="cursor:pointer;" src="'+$(this).children('img').text()+'"><br><span>First Name: </span><span >'+$(this).children('first').text()+'</span><br><span>Last Name: </span><span>'+$(this).children('last').text()+'</span><br><span>Email: </span><span>'+$(this).children('username').text()+'</span><br><button type="button" class="'+cclass+'" style="background-color:'+gclass+';" id="'+$(this).children('username').text()+'">'+status+'</button></div>';
                    $('body').append(person);
                });
                /*$(document).on('click','#profile',function(){
                 window.location="Profile.php";
                 });*/
                $(document).on('click','.add',function(){
                    var x=$(this);
                    //var username=x.parent().children("username").text();
                    var username=(x).attr("id");
                    $.ajax({
                        method: 'get',
                        url: '/server/type=addContact&email='+username,
                        success: function(data){
                            //alert("successfuly added to your contacts.");
                            x.html( "Already Friend" );
                            x.css('background-color', 'green');
                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            // Handle errors here
                            console.log('ERRORS: ' + textStatus);
                            console.log(jqXHR.responseText);
                            // STOP LOADING SPINNER
                        }
                    });
                });
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                console.log(jqXHR.responseText);
                // STOP LOADING SPINNER
            }
        });
    });

</script>