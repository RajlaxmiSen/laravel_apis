 $(function(){

    $('#rolesLink').mouseover(function(){

        $(this).next().show();

    });



    $('#sample_1 .btn-danger').click(function(){

  	bootbox.confirm({

    size: "small",

    message: "Are you sure you want to delete?",

    callback: function(result){ /* result is a boolean; true = OK, false = Cancel*/ }

  })

  });



});



    





document.addEventListener('DOMContentLoaded', function () {

    toastr.options = {

        "closeButton": false,

        "debug": false,

        "newestOnTop": false,

        "progressBar": false,

        "positionClass": "toast-top-left",

        "preventDuplicates": false,

        "onclick": null,

        "showDuration": "100",

        "hideDuration": "500",

        "timeOut": "8000",

        "extendedTimeOut": "1000",

        "showEasing": "swing",

        "hideEasing": "linear",

        "showMethod": "fadeIn",

        "hideMethod": "fadeOut"

    };

});



function getGridData(url, container_id,data) {

    jQuery.ajax({

        url: url,

        data:data

    }).done(function (data) {

        jQuery('#' + container_id).html(data);

                jQuery('.abl-date').datepicker({format:'yyyy-mm-dd'})



    }).error(function (xhr,status,error) {

        if(xhr.status==403){

            var newDoc = document.open("text/html", "replace");

            newDoc.write(xhr.responseText);

            newDoc.close();

        }

    });

}



// FUNCTION TO SHORTEN THE DISPLAY TEXT

$(function(){ /* to make sure the script runs after page load */



    $('.abl_short_item').each(function(event){ /* select all divs with the item class */

    

        var max_length = 150; /* set the max content length before a read more link will be added */

        

        if($(this).html().length > max_length){ /* check for content length */

            

            var short_content   = $(this).html().substr(0,max_length); /* split the content in two parts */

            var long_content    = $(this).html().substr(max_length);

            

            $(this).html(short_content+

                         '<a href="javascript:" class="read_more"><br/>Read More</a>'+

                         '<span class="more_text" style="display:none;">'+long_content+'<br/></span>'); /* Alter the html to allow the read more functionality */

                         

            $(this).find('a.read_more').click(function(event){ /* find the a.read_more element within the new html and bind the following code to it */

 

                event.preventDefault(); /* prevent the a from changing the url */

                $(this).hide(); /* hide the read more button */

                $(this).parents('.abl_short_item').find('.more_text').show(); /* show the .more_text span */

                $(this).parents('.abl_short_item').find('.read_less').show(); /* show the .more_text span */

                

            });

           



            

        }

        

    });



           

 

 

});