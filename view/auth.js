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

    // student id
    if (studentid.length === 0) {
        $("#hint").text("Student ID is required!"); return;
    }
    if (studentid.length < 11 || !/^\d+$/.test(studentid)) {
        $("#hint").text("Invalid Student ID format!"); return;
    }
    // username
    if (username.length === 0) {
        $("#hint").text("Username is required!"); return;
    }
    if (username.length < 3) {
        $("#hint").text("Username must be at least 3 characters!"); return;
    }
    if (/\s/.test(username)) {
        $("#hint").text("Username cannot contain spaces!"); return;
    }
    // password
    if (password.length === 0) {
        $("#hint").text("Password is required!"); return;
    }
    if (password.length < 8) {
        $("#hint").text("Password must be at least 8 characters!"); return;
    }
    // confirm password
    if (confirmpassword.length === 0) {
        $("#hint").text("Please confirm your password!"); return;
    }
    if (password !== confirmpassword) {
        $("#hint").text("Passwords do not match!"); return;
    }

    $.ajax({
        url: "../control/register.php",
        type: "POST",
        dataType: "json",
        data: {
            studentid: studentid,
            email: email,
            username: username,
            password: password
        },
        success: function(res){
            if (res.success) {
                alert("Registered successfully!");
            } else {
                alert(res.message); 
            }
        },
        error: function(xhr) {
            alert("Error: " + xhr.responseText); 
        }
    });
});



