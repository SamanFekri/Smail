<?php require_once 'config.php'?>
<!DOCTYPE html>
<html>
<head>
    <title>IE Project 4</title>
    <link rel="stylesheet" type="text/css" href="{{  URL::asset('Stylesheets/Inbox.css') }}">
</head>
<body>
<div id="header">
    <h1>
    <?php $user = Auth::user();
     echo $user['firstname']?>
        `s <span id="headInbox">Inbox</span></h1>
    <input type="hidden" id="id_of_user" value="<?php echo $user['id']?>">

</div>

<div id="box">

    <a id='notifBtn' href="{{ URL::to('/notification')}}" style="display: none"><h5 id="notif">New Friend Request</h5></a>
    <h5 id="refresh">Refresh</h5>
    <a href="{{ URL::to('/compose')}}"><h5 id="compose">Compose</h5></a>
    <h5 id="inbox">Inbox</h5>
    <h5 id="sent">Sent</h5>
    <a href="{{ URL::to('/profile')}}"><h5>Profile</h5></a>
    <a href="{{ URL::to('/logout')}}"><h5>Logout</h5></a>
    Num of Mail:<input type="text" name="numOfMail" id="numOfMail" value="<?php echo EMAIL_COUNT ?>"><br>
    <input type="radio" name="sortby" id="date" checked="checked" value="date">Sort By Date<br>
    <input type="radio" name="sortby" id="sender"  value="sender">Sort By sender<br>
    <input type="radio" name="sortby" id="attach"  value="attach">Sort By attachment<br>

</div>
<div id="mails">

</div>
</body>
</html>
<script src="{{ URL::asset('js/jquery-1.12.0.js') }}"></script>
<script type="text/javascript">
    var state = 'inbox';
    $(document).ready(function(){
        var numOfMail=$("#numOfMail").val();
        $("#mails").empty();
        $.ajax({
            type:"GET",
            url : "/server/type=refresh&nom="+numOfMail,
            dataType : "xml",
            cache : false ,
            async : false ,
            success : function (xml) {
                var all=$(xml).children('mails').children("mail");
                all.each(function(){
                    var email='<div id='+$(this).children("id").text()+' class="eachMail">'+'<div class="from">'+$(this).children("from").text();
                    email+='</div><div class="subject">'+$(this).children("subject").text();
                    email+='</div><div class="text">'+$(this).children("text").text();
                    email+='</div><div class="date">'+$(this).children("date").text()+'</div></div>';
                    $("#mails").append(email);
                    if($(this).attr("read")!==undefined){//the email has been read
                        $("#mails").children(":last").css('background-color','green');
                        $("#mails").children(":last").attr('stat','read')
                    }else if($(this).attr("spam")!==undefined){
                        $("#mails").children(":last").css('background-color','yellow');
                        $("#mails").children(":last").attr('stat','spam')
                    }else{
                        $("#mails").children(":last").css('background-color','white');
                        $("#mails").children(":last").attr('stat','not seen')
                    }
                });

                $(document).on('click','.eachMail',function(){
                    //window.location="/server.php?email=true & from="+$(this).children(".from").text()+"& date="+$(this).children(".date").text();
                    var x=$(this);
                    var id=(x).attr("id");
                    var stat = x.attr('stat');
                    if(stat.localeCompare('spam') == 0){
                        var r = confirm("This mail is spam. Do you wanna read?");
                        if (r == true) {
                            window.location="readEmail/"+id;
                        } else {
                        }
                    }else{
                        window.location="readEmail/"+id;
                    }

                });
            },error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                console.log(jqXHR.responseText);
                // STOP LOADING SPINNER
            }
        });

        $.ajax({
            type:"GET",
            url : "/server/type=notif",
            dataType : "xml",
            cache : false ,
            async : false ,
            success : function (xml) {
                var num=$(xml).children('num').text();
                if(num > 0){
                    $('#notif').prepend(num + ' ');
                    $('#notif').css('background-color','#bb0000');
                    $('#notifBtn').css('display','block');
                }
            },error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                console.log(jqXHR.responseText);
                // STOP LOADING SPINNER
            }
        });

        $(document).on('click','#refresh',function(){
            numOfMail=$("#numOfMail").val();
            //uid=$("#id_of_user").val();
            //alert(state);
            $("#mails").empty();
            $.ajax({
                type:"get",
                url : "/server/type=refresh&nom="+numOfMail+"&state="+state,
                dataType : "xml",
                cache : false ,
                async : false ,
                success : function (xml) {
                    var all=$(xml).children('mails').children("mail");
                    //alert(all.length);
                    all.each(function(){
                        var email='<div id='+$(this).children("id").text()+' class="eachMail">'+'<div class="from">';
                                if(state.localeCompare('sent') == 0){
                                    email += $(this).children("to").text();
                                }else{
                                    email += $(this).children("from").text();
                                }
                        email+='</div><div class="subject">'+$(this).children("subject").text();
                        email+='</div><div class="text">'+$(this).children("text").text();
                        email+='</div><div class="date">'+$(this).children("date").text()+'</div></div>';
                        //alert(email);
                        $("#mails").append(email);
                        if($(this).attr("read")!==undefined){//the email has been read
                            $("#mails").children(":last").css('background-color','green');
                            $("#mails").children(":last").attr('stat','read')
                        }else if($(this).attr("spam")!==undefined){
                            $("#mails").children(":last").css('background-color','yellow');
                            $("#mails").children(":last").attr('stat','spam')
                        }else{
                            $("#mails").children(":last").css('background-color','white');
                            $("#mails").children(":last").attr('stat','not seen')
                        }
                    });
                    $(document).on('click','.eachMail',function(){
                        //window.location="/server.php?email=true & from="+$(this).children(".from").text()+"& date="+$(this).children(".date").text();
                        var x=$(this);
                        var id=(x).attr("id");
                        var stat = x.attr('stat');
                        if(stat.localeCompare('spam') == 0){
                            var r = confirm("This mail is spam. Do you wanna read?");
                            if (r == true) {
                                window.location="readEmail/"+id;
                            } else {
                            }
                        }else{
                            window.location="readEmail/"+id;
                        }

                    });
                },error: function(jqXHR, textStatus, errorThrown)
                {
                    // Handle errors here
                    console.log('ERRORS: ' + textStatus);
                    console.log(jqXHR.responseText);
                    // STOP LOADING SPINNER
                }

            });
        });

        /*$(document).on('click','#compose',function(){
            //window.location="/server/compose=true";

        });*/

        $(document).on('click','#inbox',function(){
            numOfMail=$("#numOfMail").val();
            $("#mails").empty();
            state = 'inbox';
            $('#headInbox').html(state);
            $.ajax({
                type:"GET",
                url : "/server/type=refresh&nom="+numOfMail,
                dataType : "xml",
                cache : false ,
                async : false ,
                success : function (xml) {
                    var all=$(xml).children('mails').children("mail");
                    all.each(function(){
                        var email='<div id='+$(this).children("id").text()+' class="eachMail">'+'<div class="from">'+$(this).children("from").text();
                        email+='</div><div class="subject">'+$(this).children("subject").text();
                        email+='</div><div class="text">'+$(this).children("text").text();
                        email+='</div><div class="date">'+$(this).children("date").text()+'</div></div>';
                        $("#mails").append(email);
                        if($(this).attr("read")!==undefined){//the email has been read
                            $("#mails").children(":last").css('background-color','green');
                            $("#mails").children(":last").attr('stat','read')
                        }else if($(this).attr("spam")!==undefined){
                            $("#mails").children(":last").css('background-color','yellow');
                            $("#mails").children(":last").attr('stat','spam')
                        }else{
                            $("#mails").children(":last").css('background-color','white');
                            $("#mails").children(":last").attr('stat','not seen')
                        }
                    });
                    $(document).on('click','.eachMail',function(){
                        //window.location="/server.php?email=true & from="+$(this).children(".from").text()+"& date="+$(this).children(".date").text();
                        var x=$(this);
                        var id=(x).attr("id");
                        var stat = x.attr('stat');
                        if(stat.localeCompare('spam') == 0){
                            var r = confirm("This mail is spam. Do you wanna read?");
                            if (r == true) {
                                window.location="readEmail/"+id;
                            } else {
                            }
                        }else{
                            window.location="readEmail/"+id;
                        }

                    });
                },error: function(jqXHR, textStatus, errorThrown)
                {
                    // Handle errors here
                    console.log('ERRORS: ' + textStatus);
                    console.log(jqXHR.responseText);
                    // STOP LOADING SPINNER
                }
            });
        });
        $(document).on('click','#sent',function(){
            numOfMail=$("#numOfMail").val();
            state = 'sent';
            $('#headInbox').html(state);
            $("#mails").empty();
            $.ajax({
                type:"GET",
                url : "/server/type=refresh&nom="+numOfMail+"&state="+state,
                dataType : "xml",
                cache : false ,
                async : false ,
                success : function (xml) {

                    var all=$(xml).children('mails').children("mail");
                    all.each(function(){
                        var email='<div id='+$(this).children("id").text()+' class="eachMail">'+'<div class="from">'+$(this).children("to").text();
                        email+='</div><div class="subject">'+$(this).children("subject").text();
                        email+='</div><div class="text">'+$(this).children("text").text();
                        email+='</div><div class="date">'+$(this).children("date").text()+'</div></div>';
                        $("#mails").append(email);
                        if($(this).attr("read")!==undefined){//the email has been read
                            $("#mails").children(":last").css('background-color','green');
                            $("#mails").children(":last").attr('stat','read')
                        }else if($(this).attr("spam")!==undefined){
                            $("#mails").children(":last").css('background-color','yellow');
                            $("#mails").children(":last").attr('stat','spam')
                        }else{
                            $("#mails").children(":last").css('background-color','white');
                            $("#mails").children(":last").attr('stat','not seen')
                        }
                    });
                    $(document).on('click','.eachMail',function(){
                        //window.location="/server.php?email=true & from="+$(this).children(".from").text()+"& date="+$(this).children(".date").text();
                        var x=$(this);
                        var id=(x).attr("id");
                        var stat = x.attr('stat');
                        if(stat.localeCompare('spam') == 0){
                            var r = confirm("This mail is spam. Do you wanna read?");
                            if (r == true) {
                                window.location="readEmail/"+id;
                            } else {
                            }
                        }else{
                            window.location="readEmail/"+id;
                        }

                    });
                },error: function(jqXHR, textStatus, errorThrown)
                {
                    // Handle errors here
                    console.log('ERRORS: ' + textStatus);
                    console.log(jqXHR.responseText);
                    // STOP LOADING SPINNER
                }
            });
        });


        $('input[type=radio][name=sortby]').change(function() {
            numOfMail=$("#numOfMail").val();
            $("#mails").empty();
            $.ajax({
                type:"GET",
                url : "/server/type=refresh&sortby="+this.value+"&nom="+numOfMail+"&state="+state,
                dataType : "xml",
                cache : false ,
                async : false ,
                success : function (xml) {
                    var all=$(xml).children('mails').children("mail");
                    all.each(function(){
                        var email='<div id='+$(this).children("id").text()+' class="eachMail">'+'<div class="from">';
                        if(state.localeCompare('sent') == 0){
                            email += $(this).children("to").text();
                        }else{
                            email += $(this).children("from").text();
                        }
                        email+='</div><div class="subject">'+$(this).children("subject").text();
                        email+='</div><div class="text">'+$(this).children("text").text();
                        email+='</div><div class="date">'+$(this).children("date").text()+'</div></div>';
                        $("#mails").append(email);
                        if($(this).attr("read")!==undefined){//the email has been read
                            $("#mails").children(":last").css('background-color','green');
                            $("#mails").children(":last").attr('stat','read')
                        }else if($(this).attr("spam")!==undefined){
                            $("#mails").children(":last").css('background-color','yellow');
                            $("#mails").children(":last").attr('stat','spam')
                        }else{
                            $("#mails").children(":last").css('background-color','white');
                            $("#mails").children(":last").attr('stat','not seen')
                        }
                    });
                    $(document).on('click','.eachMail',function(){
                        //window.location="/server.php?email=true & from="+$(this).children(".from").text()+"& date="+$(this).children(".date").text();
                        var x=$(this);
                        var id=(x).attr("id");
                        var stat = x.attr('stat');
                        if(stat.localeCompare('spam') == 0){
                            var r = confirm("This mail is spam. Do you wanna read?");
                            if (r == true) {
                                window.location="readEmail/"+id;
                            } else {
                            }
                        }else{
                            window.location="readEmail/"+id;
                        }

                    });
                },error: function(jqXHR, textStatus, errorThrown)
                {
                    // Handle errors here
                    console.log('ERRORS: ' + textStatus);
                    console.log(jqXHR.responseText);
                    // STOP LOADING SPINNER
                }
            });
        });

    });




</script>
