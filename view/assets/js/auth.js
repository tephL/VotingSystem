// for hiding message box
function hideMessageBox(){
    $("#message_box").hide();
    console.log("hidden now");
}

hideMessageBox();

// login 
$("#loginBtn").click(login);

function login(){
    let username = $("#username").val();
    let password = $("#password").val();

    $.ajax({
        url: "control/authorizationControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "loginUser",
            username: username,
            password: password,
        },
        success: function(response){
            $("#message_box").text(response.message);
            $("#message_box").fadeIn(3000);
            if(response.status == "success"){
                window.location.href = response.redirect;
            }
        },
        error: function(){
            $("#message_box").text("Something was wrong. Try again.");
            $("#message_box").fadeIn();
        }
    });
}

// register
$("#registerBtn").click(function(){
    let username = $("#username").val();
    let firstname = $("#firstname").val();
    let middlename = $("#middlename").val();
    let lastname = $("#lastname").val();
    let password = $("#password").val();
    let studentcourse = $("#studentcourse").val();
    

    $.ajax({
        url: "register.php",
        type: "POST",
        data: {
            username: username,
            firstname: firstname,
            middlename: middlename,
            lastname: lastname,
            password: password,
            studentcourse: studentcourse
        },
        success: function(){
            alert("success register");
        },
        error: function(){
            alert("error register");
        }
    });
});



