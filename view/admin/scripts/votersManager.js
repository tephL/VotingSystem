console.log("hi");

function loadDeactivatedUsers(){
    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "getDeactivatedUsers"
        },
        success: function(response){
            // console.log(response);

            showTable();
            renderDeactivatedUsers(response.deactivated_users);
            
        },
        error: function(response){
            console.log(response);
        }
    });
}

loadDeactivatedUsers();


function showTable(){

}


function renderDeactivatedUsers(deactivated_users){
    console.log(deactivated_users);
    let deactivated_users_container = $("#deactivated_users");

    let table = $("<table>");

    let header = $("<th>");
    let user_id_col = $("<th>");
    let username_col = $("<th>");

    let data = $("<td>");

    deactivated_users.forEach((user) => {
        
    });
}