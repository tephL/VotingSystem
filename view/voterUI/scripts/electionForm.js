intitializeData();

function intitializeData(){
    $.ajax({
        url: "../../control/voter/voteController.php",
        type: "POST",
        dataType: "json",
        data: {
            action:"getElectionFormDetails"
        },
        success: function(response){
            console.log(response);
        },
        error: function(response){
            console.log("ERR");
            console.log(response);
        }
    });
}