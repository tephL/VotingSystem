// admin's pagination behavior
let ADMINS_PAGE = 1;
let ADMINS_PAGE_MAX = false;


loadAdminAccounts(ADMINS_PAGE);
function loadAdminAccounts(page){
    $.ajax({
        url: "../../control/admin/adminsControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "getAdmins",
            page: page
        },
        success: function(response){
            // clear out the page
            let admins_container = $("#admins");
            admins_container.empty();

            if(response.status){
                console.log(response);
                if(response.is_last_page){
                    ADMINS_PAGE_MAX = true;
                } else{
                    ADMINS_PAGE_MAX = false;
                }
                renderTableBody();
                renderAdmins(response.admins);
                renderPaginationForAdmins();
            } else{
                if(response.is_last_page){
                    ADMINS_PAGE_MAX = true;
                } else{
                    ADMINS_PAGE_MAX = false;
                }

                if(ADMINS_PAGE != 1){
                    ADMINS_PAGE -= 1;
                    loadAdminAccounts(ADMINS_PAGE);
                }
                renderTableBody();
                renderEmptyAdmins();
                renderPaginationForAdmins();
                // renderTableBody();
                // renderEmptyUsers();
                // renderPaginationForDeactivatedUsers();
            }
        },
        error: function(response){
            console.log(response);
        }
    });
}


function renderTableBody(){
    // users type title handler
    let title = "Admin Accounts";

    // title with pagination
    let admins_header = $("<div>")
        .attr("id", "users_header");
    let admins_title = $("<h1>")
        .text(title)
        .attr("id", "admins_title");
    let new_admin_button = $("<button>")
        .text("New Admin")
        .on("click", function(){
            addNewAdmin();
        });
    admins_header
        .append(admins_title)
        .append(new_admin_button);

    // table header
    let table = $("<table>").attr("id", "table_body");
    let header = $("<tr>");
    let status_col = $("<th>").text("Status");
    let user_id_col = $("<th>").text("User ID");
    let rolename_col = $("<th>").text("Role Name");
    let username_col = $("<th>").text("Username");
    let email_col = $("<th>").text("Email");
    let date_col = $("<th>").text("Created Date");
    let actions_col = $("<th>").text("Actions");
    header
        .append(status_col)
        .append(user_id_col)
        .append(rolename_col)
        .append(username_col)
        .append(email_col)
        .append(date_col)
        .append(actions_col);
    table
        .append(header);

    // appending to container
    let admins_container = $("#admins")
        .append(admins_header)
        .append(table);
}


function addNewAdmin(){
    clearNewAdminForm();
    let edit_window_bg = $("#new_window_bg")
        .css("display", "flex");
    let edit_subtext = $("#new_subtext").text(`Create new Admin`);
}


function cancelCreation(){
    let new_window_bg = $("#new_window_bg")
        .css("display", "none");
}


function isSameUsername(username){
    let session_username = $("#session_username").text();
    
    if(username == session_username){
        return true;
    }
}


function submitNewAdminInfo(){
    let new_admin_info = {
        new_first_name: $("#new_first_name").val(),
        new_middle_name: $("#new_middle_name").val(),
        new_last_name: $("#new_last_name").val(),
        new_contact_number: $("#new_contact_number").val(),
        new_username: $("#new_username").val(),
        new_email: $("#new_email").val(),
        new_password: $("#new_password").val(),
        new_activated_status: $("#new_status").val(),
        new_role_id: $("#new_role").val()
    };

    $.ajax({
        url: "../../control/admin/adminsControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "createNewAdmin",
            new_data: new_admin_info
        },
        success: function(response){
            console.log(response);
            $("#new_hint_text").fadeIn(3000);
            $("#new_hint_text").text(response.message);
              
            if(response.status){
                alert("Admin created Successfully!");
                cancelCreation();
                loadAdminAccounts(ADMINS_PAGE);
            }
        },
        error: function(response){
            console.log(response.responseText);
        }
    });

    console.log(new_admin_info);
}


function renderEmptyAdmins(){
    let subtext_message = $("<p>")
        .text("There are no Admins");
    let subtext_container = $("<div>")
        .append(subtext_message);
    let table_row = $("<tr>")
        .append(subtext_container);
    let table = $("#table_body")
        .append(table_row);
}


function renderAdmins(admins){
    console.log(admins);
    // admins traversal for getting all data for table
    admins.forEach((admin) => {
        // getting data
        let status = admin.activated_status == 1 ? "Activated" : "Deactivated";
        let user_id = admin.user_id;
        let role_id = admin.role_id;
        let role_name = admin.role_name;
        let username = admin.username;
        let email = admin.email;
        let created_date = admin.created_date;
        let first_name = admin.first_name;
        let middle_name = admin.middle_name;
        let last_name = admin.last_name;
        let contact_number = admin.contact_number;

        // assigning to tags
        let status_col = $("<td>").text(status);
        let user_id_col = $("<td>").text(user_id);
        let role_name_col = $("<td>").text(role_name);
        let username_col = $("<td>").text(username);
        let email_col = $("<td>").text(email);
        let created_date_col = $("<td>").text(created_date);

        // actions
        let actions_column = $("<td>");
        let actions_container;
        let view_button = $("<button>")
            .text("View")
            .addClass("view_button")
            .on("click", function(){
                viewAdminDetails(role_id, role_name, user_id, username, email, first_name, middle_name, last_name, contact_number, admin.activated_status);
            });
        let edit_button = $("<button>")
            .text("Edit")
            .addClass("edit_button")
            .on("click", function(){
                editAdmin(role_id, role_name, user_id, username, email, first_name, middle_name, last_name, contact_number, admin.activated_status);
            });
        let delete_button = $("<button>")
            .text("Delete")
            .addClass("delete_button")
            .on("click", function(){
                deleteAdmin(user_id, username);
            });

        // only append view if same user
        let isSameUser = isSameUsername(username);
        if(isSameUser){
            actions_container = $("<div>")
                .append(view_button);
        } else{
            actions_container = $("<div>")
                .append(view_button)
                .append(edit_button)
                .append(delete_button);
        }

        actions_column.append(actions_container);

        // appending to row
        let row = $("<tr>")
            .append(status_col)
            .append(user_id_col)
            .append(role_name_col)
            .append(username_col)
            .append(email_col)
            .append(created_date_col)
            .append(actions_column);


        // appending to table
        let table = $("#table_body").append(row);
    });
}


function renderPaginationForAdmins(){
    // pagination buttons
    prev_button = $("<button>")
        .attr("id", "admins_prev_button")
        .text("<")
        .prop("disabled", ADMINS_PAGE == 1)
        .on("click", function(){
            ADMINS_PAGE -= 1;
            loadAdminAccounts(ADMINS_PAGE);
        });
    next_button = $("<button>")
        .attr("id", "admins_next_button")
        .text(">")
        .prop("disabled", ADMINS_PAGE_MAX)
        .on("click", function(){
            ADMINS_PAGE += 1;
            loadAdminAccounts(ADMINS_PAGE);
        });
    admins_pagination = $("<div>")
        .addClass("pagination")
        .append(prev_button)
        .append(next_button);

    let admins_container = $("#admins")
        .append(admins_pagination);
}


function deleteAdmin(user_id, username){
    if(!confirm(`Are you sure you want to DELETE ${username} (${user_id})?`)) return;

    $.ajax({
        url: "../../control/admin/adminsControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "deleteAdminWithUserId",
            user_id: `${user_id}`
        },
        success: function(response){
            console.log(response);
            loadAdminAccounts(ADMINS_PAGE);
            alert(`Deleted ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}


function viewAdminDetails(role_id, role_name, user_id, username, email, first_name, middle_name, last_name, contact_number, status){

    let display_status = status == 1 ? "Activated" : "Deactivated";

    $("#view_subtext").text(`Viewing Admin: ${username} (${user_id})`);

    $("#view_status").text(display_status);
    $("#view_role_name").text(role_name);
    $("#view_first_name").text(first_name);
    $("#view_middle_name").text(middle_name);
    $("#view_last_name").text(last_name);
    $("#view_contact_number").text(contact_number);
    $("#view_email").text(email);

    let view_window_bg = $("#view_window_bg")
        .css("display", "flex");
}


function cancelView(){
    let view_window_bg = $("#view_window_bg")
        .css("display", "none");
}


function editAdmin(role_id, role_name, user_id, username, email, first_name, middle_name, last_name, contact_number, status){
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

    let old_role_id = $("#old_role_id").val(role_id);
    let old_username = $("#old_username").val(username);
    let edit_username = $("#edit_username").val(username);
    let edit_user_id = $("#edit_user_id").val(user_id);
    let edit_email = $("#edit_email").val(email);
    let edit_first_name = $("#edit_first_name").val(first_name);
    let edit_middle_name = $("#edit_middle_name").val(middle_name);
    let edit_last_name = $("#edit_last_name").val(last_name);
    let edit_contact_number = $("#edit_contact_number").val(contact_number);

    switch(role_id){
        case '3000':
            $("#edit_master_admin").prop("selected", true);
            break;
        case '3001':
            $("#edit_election_admin").prop("selected", true);
            break;
        case '3002':
            $("#edit_voters_admin").prop("selected", true);
            break;
    }

    switch(status){
        case '1':
            $("#edit_activated").prop("selected", true);
            break;
        case '0':
            $("#edit_deactivated").prop("selected", true);
            break;
    }
}


function cancelEdit(){
    let edit_window_bg = $("#edit_window_bg")
        .css("display", "none");
}


function submitUpdatedAdminInfo(){
    let edit_user_id = $("#edit_user_id").val();
    let old_username = $("#old_username").val();
    let edit_username = $("#edit_username").val();
    let edit_email = $("#edit_email").val();
    let edit_password = $("#edit_password").val();
    let edit_status = $("#edit_status").val();
    let edit_role = $("#edit_role").val();
    let edit_first_name = $("#edit_first_name").val();
    let edit_middle_name = $("#edit_middle_name").val();
    let edit_last_name = $("#edit_last_name").val();
    let edit_contact_number = $("#edit_contact_number").val();


    let updatedAdminInfo = {
        old_username: old_username,
        user_id: edit_user_id,
        new_username: edit_username,
        new_email: edit_email,
        new_password: edit_password,
        new_activated_status: edit_status,
        new_role_id: edit_role,
        new_first_name: edit_first_name,
        new_middle_name: edit_middle_name,
        new_last_name: edit_last_name,
        new_contact_number: edit_contact_number
    }

    console.log(updatedAdminInfo);

    $.ajax({
        url: "../../control/admin/adminsControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "updateAdminInfo",
            new_data: updatedAdminInfo
        },
        success: function(response){
            console.log( response);
            $("#edit_hint_text").fadeIn(3000);
            $("#edit_hint_text").text(response.message);
              
            if(response.status){
                cancelEdit();
                loadAdminAccounts(ADMINS_PAGE);
                alert("Updated Successfully.");
            }
        },
        error: function(response){
            console.log(response.responseText);
        }
    });

}


function clearNewAdminForm(){
    $("#new_first_name").val("");
    $("#new_middle_name").val("");
    $("#new_last_name").val("");
    $("#new_contact_number").val("");
    $("#new_username").val("");
    $("#new_email").val("");
    $("#new_password").val("");
}