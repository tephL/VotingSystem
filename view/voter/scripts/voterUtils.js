// UX effects
$(".items").hover(function(){
    $(this).css("color", "rgb(230, 230, 230)");
});

$(".items").mouseout(function(){
    $(this).css("color", "white");
});


// electionForm



// voterSettings
$("#signout_btn").click(function(){
    
    if(!confirm("Are you sure you want to sign out?")) return;

    $.ajax({
        url: "../../control/authenticationControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "signOut",
        },
        success: function(response){
            alert("Successfully signed out");
            console.log(response);
            console.log("logged off");
            window.location.href = "../../index.html";
        },
        error: function(response){
            alert("An error has occured");
        }
    });
});