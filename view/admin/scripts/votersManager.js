// type of users
let is_activated_users = true;
// user's pagination behavior
let USERS_PAGE = 1;
let USERS_PAGE_MAX = false;


function loadUsers(page){

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
            // clear out the page
            let users_container = $("#users");
            users_container.empty();
            if(response.status){
                // next button handler
                if(response.is_last_page){
                    USERS_PAGE_MAX = true;
                } else{
                    USERS_PAGE_MAX = false;
                }
                renderTableBody();
                renderUsers(response.users);
                renderPaginationForDeactivatedUsers();
            } else{
                if(response.is_last_page){
                    USERS_PAGE_MAX = true;
                } else{
                    USERS_PAGE_MAX = false;
                }

                if(USERS_PAGE != 1){
                    USERS_PAGE -= 1;
                    loadUsers(USERS_PAGE);
                }
                renderTableBody();
                renderEmptyUsers();
                renderPaginationForDeactivatedUsers();
            }
        },
        error: function(response){
            console.log(response.responseText);
        }
    });

}


function renderTableBody(){
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
            loadUsers(USERS_PAGE);
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
            loadUsers(USERS_PAGE);
        });
    users_header
        .append(prev)
        .append(deactivated_title)
        .append(next);

    // table header
    let table = $("<table>").attr("id", "table_body");
    let header = $("<tr>");
    let user_id_col = $("<th>").text("User ID");
    let username_col = $("<th>").text("Username");
    let email_col = $("<th>").text("Email");
    let date_col = $("<th>").text("Created Date");
    let student_id_col = $("<th>").text("Student ID");
    let existtence_col = $("<th>").text("Student Existence");
    let actions_col = $("<th>").text("Actions");
    header
        .append(user_id_col)
        .append(username_col)
        .append(email_col)
        .append(date_col)
        .append(student_id_col)
        .append(existtence_col)
        .append(actions_col);
    table
        .append(header);

    // appending to container
    let users_container = $("#users")
        .append(users_header)
        .append(table);
}


function renderEmptyUsers() {
    let subtext = is_activated_users
        ? "No Activated Users"
        : "No Deactivated Users";

    let subtext_container = $("<td>")
        .text(subtext)
        .attr("colspan", 7) // adjust to your column count
        .css({
            "text-align": "center",
            "padding": "20px",
            "color": "#888",
            "font-style": "italic",
            "background-color": "#fafafa"
        });

    let table_row = $("<tr>")
        .append(subtext_container);

    $("#table_body").append(table_row);
}


function renderUsers(users){

    let table = $("#table_body");

    // users traversal for table data and actions
    users.forEach((user) => {
        let new_data_row = $("<tr>");
        let user_id = $("<td>").text(user.user_id);
        let username = $("<td>").text(user.username);
        let email = $("<td>").text(user.email);
        let date = $("<td>").text(user.created_date);
        let student_id = $("<td>").text(user.student_id);

        // existence orb
        let orb = $("<button>")
            .addClass("orb");

        if(!user.student_exists){
            orb.attr("id", "no_exist");
        } else{
            orb.attr("id", "yes_exist");
        }
        
        let existence = $("<td id='orb_column'>").append(orb);

        // actions for deactivated users
        let actions_container;
        if(!is_activated_users){
            let accept_button = $("<button>")
                .text("Accept")
                .addClass("accept_button")
                .on("click", function() {
                    acceptUser(user.user_id, user.username);
                });
            let reject_button = $("<button>")
                .text("Reject")
                .addClass("reject_button")
                .on("click", function() {
                    rejectUser(user.user_id, user.username);
                });
            actions_container = $("<div>")
                .append(accept_button)
                .append(reject_button);
        } else if(is_activated_users){
            let delete_button = $("<button>")
                .text("Delete")
                .addClass("delete_button")
                .on("click", function() {
                    deleteUser(user.user_id, user.username);
                });
            let edit_button = $("<button>")
                .text("Edit")
                .addClass("edit_button")
                .on("click", function() {
                    editUser(user.user_id, user.username, user.student_id, user.email);
                });
            actions_container = $("<div>")
                .append(delete_button)
                .append(edit_button);
        }

        let actions = $("<td>")
            .append(actions_container);

        new_data_row.append(user_id)
            .append(username)
            .append(email)
            .append(date)
            .append(student_id)
            .append(existence)
            .append(actions);
        table.append(new_data_row);   
    });

    

}


function renderPaginationForDeactivatedUsers(){
    // pagination buttons
    prev_button = $("<button>")
        .attr("id", "deactivated_prev_button")
        .text("<")
        .prop("disabled", USERS_PAGE == 1)
        .on("click", function(){
            USERS_PAGE -= 1;
            loadUsers(USERS_PAGE);
        });
    next_button = $("<button>")
        .text(">")
        .prop("disabled", USERS_PAGE_MAX)
        .on("click", function(){
            USERS_PAGE += 1;
            loadUsers(USERS_PAGE);
        });
    deactivated_pagination = $("<div>")
        .addClass("pagination")
        .append(prev_button)
        .append(next_button);

    let users_container = $("#users")
        .append(deactivated_pagination);

}


loadUsers(USERS_PAGE);
// setInterval(loadUsers, 3000); // for live reload but shi it looks so buns with the lag


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
            loadUsers(USERS_PAGE);
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
            loadUsers(USERS_PAGE);
            alert(`Rejected ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}


function deleteUser(user_id, username){
    if(!confirm(`Are you sure you want to DELETE ${username} (${user_id})? Their VOTES will be deleted too.`)) return;

    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "deleteUserWithUserId",
            user_id: `${user_id}`
        },
        success: function(response){
            loadUsers(USERS_PAGE);
            alert(`Deleted ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}


function editUser(user_id, username, student_id, email){
    if(!confirm(`Are you sure you want to EDIT ${username} (${user_id})?`)) return;

    let edit_window_bg = $("#edit_window_bg")
        .css("display", "flex");
    let edit_subtext = $("#edit_subtext").text(`Editing ${username} (${user_id})`);

    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "getUserPassword",
            user_id: `${user_id}`
        },
        success: function(response){
            let edit_password = $("#edit_password").val(response.password);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });

    let old_username = $("#old_username").val(username);
    let edit_username = $("#edit_username").val(username);
    let edit_user_id = $("#edit_user_id").val(user_id);
    let edit_student_id = $("#edit_student_id").val(student_id);
    let edit_email = $("#edit_email").val(email);
}


function cancelEdit(){
    let edit_window_bg = $("#edit_window_bg")
        .css("display", "none");
}


function submitUpdatedUserInfo(){
    let edit_user_id = $("#edit_user_id").val();
    let old_username = $("#old_username").val();
    let edit_username = $("#edit_username").val();
    let edit_student_id = $("#edit_student_id").val();
    let edit_email = $("#edit_email").val();
    let edit_password = $("#edit_password").val();
    let edit_status = $("#edit_status").val();

    let new_activated_status;
    if(edit_status == 'Activate'){
        new_activated_status = 1;
    } else{
        new_activated_status = 0;
    }

    let updatedUserInfo = {
        user_id: edit_user_id,
        new_username: edit_username,
        old_username: old_username,
        new_student_id: edit_student_id,
        new_email: edit_email,
        new_password: edit_password,
        new_activated_status: new_activated_status
    }

    console.log(updatedUserInfo);

    $.ajax({
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "updateUserInfo",
            new_data: updatedUserInfo
        },
        success: function(response){
            console.log(response);
            $("#edit_hint_text").fadeIn(3000);
            $("#edit_hint_text").text(response.message);
              
            if(response.status){
                alert("Successfully edited Voter");
                cancelEdit();
                loadUsers(USERS_PAGE);
            }
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}