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
        url: "control/authenticationControl.php",
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
                setTimeout(()=>{
                    window.location.href = response.redirect; 
                }, 3000);
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
        url: "control/registerControl.php",
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
                window.location.href = "index.html"
            } else {
                $("#hint").text(response.message[0]);
            }
        },
        error: function(response) {
            console.log("Error: " + response.responseText); 
        }
    });
});



