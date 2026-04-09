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

            if(response.is_last_page){
                ADMINS_PAGE_MAX = true;
            } else{
                ADMINS_PAGE_MAX = false;
            }
            renderTableBody();
            renderAdmins(response.admins);
            renderPaginationForAdmins();
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
    let deactivated_title = $("<h1>")
        .text(title)
        .attr("id", "deactivated_title");
    admins_header
        .append(deactivated_title);

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

        // assigning to tags
        let status_col = $("<td>").text(status);
        let user_id_col = $("<td>").text(user_id);
        let role_name_col = $("<td>").text(role_name);
        let username_col = $("<td>").text(username);
        let email_col = $("<td>").text(email);
        let created_date_col = $("<td>").text(created_date);

        // actions
        let edit_button = $("<button>")
            .text("Edit")
            .on("click", function(){
                editAdmin(role_id, role_name, user_id, username, email);
            });
        let delete_button = $("<button>")
            .text("Delete")
            .on("click", function(){
                deleteAdmin(user_id, username);
            });
        let actions_container = $("<div>")
            .append(edit_button)
            .append(delete_button);

        // appending to row
        let row = $("<tr>")
            .append(status_col)
            .append(user_id_col)
            .append(role_name_col)
            .append(username_col)
            .append(email_col)
            .append(created_date_col)
            .append(actions_container);

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
            loadUsers(ADMINS_PAGE);
        });
    next_button = $("<button>")
        .attr("id", "admins_next_button")
        .text(">")
        .prop("disabled", ADMINS_PAGE_MAX)
        .on("click", function(){
            ADMINS_PAGE += 1;
            loadUsers(ADMINS_PAGE);
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
        url: "../../control/admin/votersControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "deleteUserWithUserId",
            user_id: `${user_id}`
        },
        success: function(response){
            loadAdminAccounts(ADMINS_PAGE);
            alert(`Deleted ${username} (${user_id})`);
        },
        error: function(response){
            console.log(response.responseText);
        }
    });
}


function editAdmin(role_id, role_name, user_id, username, email){
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