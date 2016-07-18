@if($errors->any())
    <h4 style="color: red">{{$errors->first()}}</h4>
    @endif
<!DOCTYPE html>
<html>
<head>
    <title>IE Project 4</title>
    <link rel="stylesheet" type="text/css" href="{{  URL::asset('Stylesheets/ComposeEmail.css') }}">
</head>
<body>
<div id="container">
    <?php $user = Auth::user();  ?>
    <div class="forms">
        {!! Form::open(['url' => url('/server'),'id' => 'sendmail','enctype'=>'multipart/form-data']) !!}
        <div class="titles">
            {!! Form::label('receiver_id','To: ') !!}
            {!! Form::label('title','Title: ') !!}
            {!! Form::label('body','Text: ') !!}
            {!! Form::label('attachment','Attachment: ',['id' => 'attach']) !!}
            {!! Form::submit('send') !!}
        </div>
        <div class="inputs">
            {!! Form::email('receiver_id','',['required']) !!}
            {!! Form::text('title','',['required']) !!}
            {!! Form::textarea('body','',['required', 'rows' => 10, 'cols' => 70]) !!}
            {!! Form::file('attachment','') !!}
            {!! Form::hidden('sender_id',$user['id']) !!}
            {!! Form::hidden('type','compose') !!}
        </div>
        {!! Form::close() !!}
        <a href="{{  URL::to('inbox') }}"><button id="return">Inbox</button></a>
    </div>

    <!--<div class="forms">
        <form id="sendmail" method="post" enctype="multipart/form-data">
            <div class="titles">
                <h5>To:</h5>
                <h5>Subject:</h5>
                <h5>Text:</h5>
                <h5 id="attach">Attachment:</h5>
                <input type="submit" value="Send">

            </div>
            <div class="inputs">
                <input type="email" name="receiver_id" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required >
                <input type="hidden" name="sender_id" value="/*<?php// $user = Auth::user(); echo $user['id'];  ?>*/">
                <input type="text" name="title" required>
                <textarea rows="10" cols="70" name="body"></textarea>
                <input type="file" name="attachment">
                <div id='response'></div>
            </div>
        </form>
    </div>-->
</div>
</body>
</html>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script src="{{  URL::asset('js/jquery-1.12.0.js') }}"></script>
<!--<script type="text/javascript">
    $(document).ready(function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#sendmail').submit(function(){

            // show that something is loading
            $('#response').html("<b>Loading response...</b>");

            /*
             * 'post_receiver.php' - where you will pass the form data
             * $(this).serialize() - to easily read form data
             * function(data){... - data contains the response from post_receiver.php
             */
            $.ajax({
                        type: 'POST',
                        url: 'server/type=sendmail',
                        //data: $(this).serialize()
                        data: new FormData( this ),
                        processData: false,
                        contentType: false
                    })
                    .done(function(data){

                        // show the response
                        if(data==1){
                            $('#response').html("succesful");
                            window.location = "Inbox.php";
                        }
                        else if(data==0){
                            $('#response').html("Email Is Not In Your Contact List");
                        }
                        else if(data==-1){
                            $('#response').html("Not Registered Email");
                        }

                    })
                    .fail(function() {

                        // just in case posting your form failed
                        alert( "Posting failed." );

                    });

            // to prevent refreshing the whole page page
            return false;

        });
    });


</script>-->