$("#create-panel").hide();

$("#createbutton").click(function () {
    $("#election-panel").hide();
    $("#create-panel").show();
});

$("#cancel-btn").click(() => {
    resetAfterEdit();
});

// ==================== CREATE / UPDATE ====================
$("#create-btn").click(function () {

    let title = $("#title-input").val().trim();
    let start = $("#start-date").val();
    let end = $("#end-date").val();
    let status = $("#status").val();

    if (!title || !start || !end || !status) {
        alert("Please fill in all fields.");
        return;
    }

    if (confirm("Are you sure?")) {

        let url = "./../../control/electionControl.php?action=create";
        let data = {
            title: title,
            status: status,
            start: start,
            end: end
        };

        if (currentEditId !== null) {
            url = "./../../control/electionControl.php?action=update";
            data.id = currentEditId;
        }

        $.ajax({
            url: url,
            method: "POST",
            data: data,

            success: function (response) {

                response = response.trim();

                if (response === "success") {

                    if (currentEditId !== null) {
                        alert("Election updated successfully!");
                    } else {
                        alert("Election created successfully!");
                    }

                    resetAfterEdit();
                    loadElections();

                } else {
                    alert("Error: " + response);
                }
            },

            error: function () {
                alert("Connection error. Please try again.");
            }
        });
    }
});

// ==================== LOAD ALL ELECTIONS ====================
function loadElections() {
    $.ajax({
        url: "./../../control/electionControl.php?action=getAll",
        method: "GET",
        success: function (response) {
            let elections = JSON.parse(response);
            $("#history-list").empty();

            if (elections.length === 0) {
                $("#history-list").html('<p class="no-elect">No elections yet.</p>');
                return;
            }

            elections.forEach(e => {
                let startDate = e.start_date;
                let endDate = e.end_date;

                if (startDate && startDate.includes(" ")) {
                    startDate = startDate.split(" ")[0];
                }
                if (endDate && endDate.includes(" ")) {
                    endDate = endDate.split(" ")[0];
                }

                let row = `
                    <ul class="history-row">
                        <li>${e.election_title}</li>
                        <li>${e.status}</li>
                        <li>${startDate}</li>
                        <li>${endDate}</li>
                        <li class="action-cell">
                            <button class="edit-btn" id="edit_${e.election_id}">Edit</button>
                            <button class="delete-btn" id="delete_${e.election_id}">Delete</button>
                        </li>
                    </ul>
                `;
                $("#history-list").append(row);
            });

            deleteButton();
        },
        error: function () {
            alert("Failed to load elections.");
        }
    });
}

// ==================== DELETE BUTTON ====================
function deleteButton() {
    $(".delete-btn").off().click(function () {
        let btnId = $(this).attr("id");
        let id = btnId.split("_")[1];

        if (confirm("Are you sure you want to delete this election?")) {
            $.ajax({
                url: "./../../control/electionControl.php?action=delete",
                method: "POST",
                data: { id: id },
                success: function (response) {
                    if (response === "success") {
                        alert("Election deleted!");
                        loadElections();
                    } else {
                        alert("Failed to delete.");
                    }
                }
            });
        }
    });

    $(".edit-btn").off().click(function () {
        let btnId = $(this).attr("id");
        let id = btnId.split("_")[1];
        editElection(id);
    });
}

// ==================== EDIT ELECTION ====================
let currentEditId = null;

function editElection(id) {
    currentEditId = id;

    $.ajax({
        url: "./../../control/electionControl.php?action=getById&id=" + id,
        method: "GET",
        success: function (response) {
            let e = JSON.parse(response);

            let startDate = e.start_date;
            let endDate = e.end_date;

            if (startDate && startDate.includes(" ")) {
                startDate = startDate.split(" ")[0];
            }
            if (endDate && endDate.includes(" ")) {
                endDate = endDate.split(" ")[0];
            }

            $("#title-input").val(e.election_title);
            $("#start-date").val(startDate);
            $("#end-date").val(endDate);
            $("#status").val(e.status);
            $("#create-btn").text("Update Election");
            $("#create-panel h1").text("Edit Election");

            $("#election-panel").hide();
            $("#create-panel").show();
        }
    });
}

// ==================== RESET AFTER CREATE OR UPDATE ====================
function resetAfterEdit() {
    currentEditId = null;
    $("#create-btn").text("Create");
    $("#create-panel h1").text("Create Election");

    $("#title-input").val('');
    $("#start-date").val('');
    $("#end-date").val('');
    $("#status").val('');

    $("#create-panel").hide();
    $("#election-panel").show();
}

loadElections();
