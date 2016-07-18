<!DOCTYPE html>
<html>
<head>
    <title>IE Project 4</title>
    <link rel="stylesheet" type="text/css" href="{{  URL::asset('Stylesheets/Profile.css') }}">
    <link rel="stylesheet" type="text/css" href="{{  URL::asset('Stylesheets/Users.css') }}">
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
</body>
</html>
<script src="{{ URL::asset('js/jquery-1.12.0.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $.ajax({
         type:"GET",
         url : "/server/type=notifications",
         dataType : "xml",
         cache : false ,
         async : false ,
         success : function (xml) {


         //var data=$(xml).children('users');
         var all=$(xml).children('users').children("user");
         //contacts.children('user').each(function(){
         all.each(function(){
         var person='<div class="person"><img src="'+$(this).children('img').text()+'"><br><span>First Name: </span><span >'+$(this).children('first').text()+'</span><br><span>Last Name: </span><span>'+$(this).children('last').text()+'</span><br><span>Email: </span><span>'+$(this).children('username').text()+'</span></div>';
         $('body').append(person);
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