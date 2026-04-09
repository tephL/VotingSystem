// type of users
let is_activated_users = true;
// user's pagination behavior
let USERS_PAGE = 1;
let USERS_PAGE_MAX = false;


function loadUsers(page){
    // clear out the page
    let deactivated_users_container = $("#deactivated_users");
    deactivated_users_container.empty();

    // action handler
    let action;
    if(is_activated_users){
        action = "getActivatedUsers";
    }else{
        action = "getDeactivatedUsers";
    }

    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: action,
            page: page
        },
        success: function(response){
            console.log(response);
            if(response.status){
                // next button handler
                if(response.is_last_page){
                    USERS_PAGE_MAX = true;
                } else{
                    USERS_PAGE_MAX = false;
                }
                renderDeactivatedUsers(response.deactivated_users);
                renderPaginationForDeactivatedUsers();
            } else{
                if(USERS_PAGE != 1){
                    USERS_PAGE -= 1;
                    loadDeactivatedUsers(USERS_PAGE);
                }
            }
        },
        error: function(response){
            console.log(response.responseText);
        }
    });

}

function renderDeactivatedUsers(deactivated_users){
    // users type title handler
    let title;
    if(is_activated_users){
        title = "Activated Users";
    } else{
        title = "Deactivated Users";
    }

    // title with pagination
    let users_header = $("<div>")
        .attr("id", "users_header");
    let prev = $("<button>")
        .text("<")
        .prop("disabled", is_activated_users)
        .on("click", function(){
            is_activated_users = true;
            USERS_PAGE_MAX = false;
            USERS_PAGE = 1;
            loadDeactivatedUsers(USERS_PAGE);
        });;
    let deactivated_title = $("<h1>")
        .text(title)
        .attr("id", "deactivated_title");
    let next = $("<button>")
        .text(">")
        .prop("disabled", !is_activated_users)
        .on("click", function(){
            is_activated_users = false;
            USERS_PAGE_MAX = false;
            USERS_PAGE = 1;
            loadDeactivatedUsers(USERS_PAGE);
        });
    users_header
        .append(prev)
        .append(deactivated_title)
        .append(next);

    // table header
    let table = $("<table>");
    let header = $("<tr>");
    let user_id_col = $("<th>").text("User ID");
    let username_col = $("<th>").text("Username");
    let email_col = $("<th>").text("Email");
    let date_col = $("<th>").text("Created Date");
    let student_id_col = $("<th>").text("Student ID");
    let actions_col = $("<th>").text("Actions");
    header
        .append(user_id_col)
        .append(username_col)
        .append(email_col)
        .append(date_col)
        .append(student_id_col)
        .append(actions_col);
    table
        .append(header);

    // users traversal for table data and actions
    deactivated_users.forEach((user) => {
        let new_data_row = $("<tr>");
        let user_id = $("<td>").text(user.user_id);
        let username = $("<td>").text(user.username);
        let email = $("<td>").text(user.email);
        let date = $("<td>").text(user.created_date);
        let student_id = $("<td>").text(user.student_id);

        // actions for deactivated users
        let actions_container;
        if(!is_activated_users){
            let accept_button = $("<button>")
                .text("Accept")
                .on("click", function() {
                    acceptUser(user.user_id, user.username);
                });
            let reject_button = $("<button>")
                .text("Reject")
                .on("click", function() {
                    rejectUser(user.user_id, user.username);
                });
            actions_container = $("<div>")
                .append(accept_button)
                .append(reject_button);
        } else if(is_activated_users){
            let delete_button = $("<button>")
                .text("Delete")
                .on("click", function() {
                    deleteUser(user.user_id, user.username);
                });
            actions_container = $("<div>")
                .append(delete_button);
        }

        let actions = $("<td>")
            .append(actions_container);

        new_data_row.append(user_id)
            .append(username)
            .append(email)
            .append(date)
            .append(student_id)
            .append(actions);
        table.append(new_data_row);   
    });

    // appending to container
    let deactivated_users_container = $("#deactivated_users")
        .append(users_header)
        .append(table);

}


function renderPaginationForDeactivatedUsers(){
    // pagination buttons
    prev_button = $("<button>")
        .attr("id", "deactivated_prev_button")
        .text("<")
        .prop("disabled", USERS_PAGE == 1)
        .on("click", function(){
            USERS_PAGE -= 1;
            loadDeactivatedUsers(USERS_PAGE);
        });
    next_button = $("<button>")
        .text(">")
        .prop("disabled", USERS_PAGE_MAX)
        .on("click", function(){
            USERS_PAGE += 1;
            loadDeactivatedUsers(USERS_PAGE);
        });
    deactivated_pagination = $("<div>")
        .addClass("pagination")
        .append(prev_button)
        .append(next_button);

    let deactivated_users_container = $("#deactivated_users")
        .append(deactivated_pagination);

}


loadUsers(USERS_PAGE);
// setInterval(loadDeactivatedUsers, 3000); // for live reload but shi it looks so buns with the lag


function acceptUser(user_id, username){
    if(!confirm(`Are you sure you want to ACCEPT ${username} (${user_id})?`)) return;

    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "acceptUserWithUserId",
            user_id: `${user_id}`
        },
        success: function(response){
            loadDeactivatedUsers(USERS_PAGE);
            alert(`Accepted ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}


function rejectUser(user_id, username){
    if(!confirm(`Are you sure you want to REJECT ${username} (${user_id})?`)) return;
    
    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "rejectUserWithUserId",
            user_id: `${user_id}`
        },
        success: function(response){
            loadDeactivatedUsers(USERS_PAGE);
            alert(`Rejected ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}


function deleteUser(user_id, username){
    if(!confirm(`Are you sure you want to DELETE ${username} (${user_id})?`)) return;

    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "deleteUserWithUserId",
            user_id: `${user_id}`
        },
        success: function(response){
            loadDeactivatedUsers(USERS_PAGE);
            alert(`Deleted ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}