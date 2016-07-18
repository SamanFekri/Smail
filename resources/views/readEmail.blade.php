<?php //echo $messages_id ?>
<!DOCTYPE html>
<html>
<head>
    <title>IE Project 4</title>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('Stylesheets/ReadEmail.css') }}">
</head>
<body>
<div id="email">

    <a href="/attachment/" target="_blank" id="attachurl" style="height: 32px; display: block"></a>

    <button type="button" id="deleteMail">Delete this Email</button>
    <span  id="mid" style="display: none" value="<?php echo $messages_id ?>"><?php echo $messages_id; ?></span>
    <a href="{{ URL::to('/inbox') }}"  style="height: 32px; display: block"><h4>inbox</h4></a>


</div>

</body>
</html>
<script src="{{ URL::asset('js/jquery-1.12.0.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){

        /*$.urlParam = function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            console.log(results)
            return results[1] || 0;
        }*/
        var id=$("#mid").html();
        //alert (id);

        /* var date=$.urlParam('date');*/
        url="/server/type=readMail&id="+id;

        $.ajax({
            type:"GET",
            //url : "../server.php?email=true&from="+from+"&date="+date,
            url : url,
            dataType : "xml",
            cache : false ,
            async : false ,
            success : function (xml) {

                var mail=$(xml).children("mail");
                console.log(mail)
                var email='<p>An email from:</p><p id="from">'+mail.children("from").text();
                email+='</p><br><p>with subject of:</p><p id="subject">'+mail.children("subject").text();
                email+='</p><br><p>received in date:</p><p id="date">'+mail.children("date").text();
                email+='</p><br><div id="test"><p id="text">'+mail.children("text").text()+'</p></div>';
                $("#email").prepend(email);
                console.log('hererere')
                console.log(mail.children('attachments').children('attach').text());
                $("#attachurl").html(mail.children('attachments').children('attach').text())
                $("#attachurl").attr('href',$("#attachurl").attr('href')+mail.children('attachments').children('attach').text())
                $(document).on('click','#deleteMail',function(){
                    $.ajax({
                        method: 'get',
                        url: '/server/type=deletemail&id=+'+id,
                        success: function(data){
                            alert(data);
                            window.location = data;
                        },error: function(jqXHR, textStatus, errorThrown)
                        {
                            alert('here')
                            // Handle errors here
                            console.log('ERRORS: ' + textStatus);
                            console.log(jqXHR.responseText);
                            // STOP LOADING SPINNER
                        }
                    });

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

</script>

