 $(document).ready(function () {
 	//catch form submit
 	$(".form-horizontal").submit(function () {
    	return false;
    });
 	

    //button login click
    $("#btn-login").click(function () {
        var  un    = $("#login-username"),
             pa    = $("#login-password");

       //validate login form
       if(login.validateLogin(un, pa) === true) { 
           //validation passed, prepare data that will be sent to server
            var data = {
                username: un.val(),
                password: pa.val(),
                id: {
                    username: "login-username",
                    password: "login-password"
                }
            };
            
            //send login data to server
            login.loginUser(data);
       }

    });


    //set focus on username field when page is loaded
    $("#login-username").focus();
});


/** LOGIN NAMESPACE
 ======================================== */
var login = {};

login.loginUser = function (data) {
    var btn = $("#btn-login");
    asengine.loadingButton(btn, $_lang.logging_in);

    //encrypt password before sending it through the network
    data.password = CryptoJS.SHA512(data.password).toString();

    $.ajax({
        url: "ASEngine/ASAjax.php",
        type: "POST",
        dataType: "json",
        data: {
            action  : "checkLogin",
            username: data.username,
            password: data.password,
            id      : data.id
        },
        success: function (result) {
           asengine.removeLoadingButton(btn);
           if( result.status === 'success' )
               window.location = result.page;
           else {
               asengine.displayErrorMessage($("#login-username"));
               asengine.displayErrorMessage($("#login-password"), result.message);
           }

        }
    });
};

login.validateLogin = function (un, pass) {
    var valid = true;

    //remove previous error messages
    asengine.removeErrorMessages();

    if($.trim(un.val()) == "") {
        asengine.displayErrorMessage(un);
        valid = false;
    }
    if($.trim(pass.val()) == "") {
        asengine.displayErrorMessage(pass);
        valid = false;
    }

    return valid;
};