
/**
 * Created by PhpStorm.
 * User: SKings
 * Date: 7/1/2016
 * Time: 8:33 AM
 */
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script src="{{ URL::asset('js/jquery-1.12.0.js') }}"></script>
<script type="text/javascript" >
    $('document').ready( function() {
        alert('here');
        /* $.ajax({
         type: 'GET',
         url: "/server/type=profile",

         cache: false,
         async: false,

         success: function (data) {
         alert(data);
         },
         error: function (jqXHR, textStatus, errorThrown) {
         // Handle errors here
         console.log('ERRORS: ' + textStatus);
         console.log(jqXHR.responseText);
         // STOP LOADING SPINNER
         }

         })
         })*/


        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/server',
            type: 'POST',
            data: {_token: CSRF_TOKEN,salam:'akbar'},
            //dataType: 'JSON',
            success: function (data) {
                console.log(data);
            },error: function (jqXHR, textStatus, errorThrown) {
                // Handle errors here
                console.log('ERRORS: ' + textStatus+ '   ' + errorThrown);
                console.log(jqXHR.responseText);
                // STOP LOADING SPINNER
            }
        });
    });
</script>