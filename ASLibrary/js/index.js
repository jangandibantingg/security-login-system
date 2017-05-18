$(document).ready(function () {
    
    //comment button click
    $("#comment").click(function () {
        //remove all error messages
        asengine.removeErrorMessages();
        
        var comment = $("#comment-text"),
             btn    = $(this);
             
        //validate comment
        if($.trim(comment.val()) == "") {
            asengine.displayErrorMessage(comment, $_lang.field_required);
            return;
        }
        
        //set button to posting state
        asengine.loadingButton(btn, $_lang.posting);
        
         $.ajax({
            url: "ASEngine/ASAjax.php",
            type: "POST",
            data: {
                action : "postComment",
                comment: comment.val()
            },
            success: function (result) {
                //return button to normal state
                asengine.removeLoadingButton(btn);
                try {
                   //try to parse result to JSON
                   var res = JSON.parse(result);
                   
                   //generate comment html and display it
                   var html  = "<blockquote>";
                        html += "<p>"+res.comment+"</p>";
                        html += "<small>"+res.user+" <em> "+ $_lang.at +res.postTime+"</em></small>";
                        html += "</blockquote>";
                    if( $(".comments-comments blockquote").length >= 7 )
                        $(".comments-comments blockquote").last().remove();
                    $(".comments-comments").prepend($(html));
                    comment.val("");
                }
                catch(e){
                   //parsing error, display error message
                   asengine.displayErrorMessage(comment, $_lang.error_writing_to_db);
                }
            }
        });
    });
	
});
