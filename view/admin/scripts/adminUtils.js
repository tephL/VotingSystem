$(".items").hover(function(){
    $(this).css("color", "red");
});

$(".items").mouseout(function(){
    $(this).css("color", "black");
});

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

$("#start_election").click(function(){
    console.log("clicked");
    alert("YOURE AB TO dieeee");
    window.location.href = "https://ddlc.moe/";
});