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
    let studentid = $("#studentid").val().trim();
    let email = $("#email").val().trim();
    let username = $("#username").val().trim();
    let password = $("#password").val().trim();
    let confirmpassword = $("#confirmpassword").val().trim();

    $.ajax({
        url: "../control/register.php",
        type: "POST",
        dataType: "json",
        data: {
            studentid: studentid,
            email: email,
            username: username,
            password: password,
            confirmpassword: confirmpassword
        },
        success: function(response){
            if (response.success) {
                alert("Registered successfully!");
            } else {
                $("#hint").text(response.message);
            }
        },
        error: function(xhr) {
            alert("Error: " + xhr.responseText); 
        }
    });
});



