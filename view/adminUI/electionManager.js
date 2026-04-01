$("#create-panel").hide();

$("#createbutton").click(function () {
    $("#election-panel").hide();
    $("#create-panel").show();
});

$("#cancel-btn").click(() => {
    resetAfterEdit();
});

// ==================== ADD POSITION ROW ====================
$("#add-position-btn").click(function () {
    let newRow = `
        <div class="position-row">
            <label>Position Name</label>
            <input type="text" class="pos-name" placeholder="e.g Senator">
            <label>Max Votes</label>
            <input type="number" class="pos-max" step="1" min="1" max="8">
            <button type="button" class="remove-pos-btn">Remove</button>
        </div>
    `;
    $("#positions-box").append(newRow);
});

$(".pos-max").on("input", function () {
    if (this.value > 8) {
        this.value = 8;
    }
    if (this.value < 1 && this.value !== "") {
        this.value = 1;
    }
});

// remove position row
$(document).on("click", ".remove-pos-btn", function () {
    $(this).closest(".position-row").remove();
});

// ==================== CREATE / UPDATE ====================
$("#create-btn").click(function () {

    let title = $("#title-input").val().trim();
    let start = $("#start-date").val();
    let end = $("#end-date").val();

    if (!title || !start || !end) {
        alert("Please fill in all fields.");
        return;
    }

    let positions = [];
    $(".position-row").each(function () {
        let name = $(this).find(".pos-name").val().trim();
        let max = $(this).find(".pos-max").val();
        if (name && max) {
            positions.push({
                name: name,
                max: max
            });
        }
    });

    if (positions.length === 0) {
        alert("Please add at least one position.");
        return;
    }

    if (confirm("Are you sure?")) {

        let url = "./../../control/electionControl.php?action=create";
        let data = {
            title: title,
            start: start,
            end: end,
            positions: JSON.stringify(positions)
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
                } else if (response === "active") {
                    alert("Cannot create election while another is active.");
                } else if (response === "invalid") {
                    alert("End date cannot be earlier than start date.");
                } else if (response === "past") {
                    alert("Start date cannot be in the past.");
                    alert("Error: " + response);
                }
            },
            error: function () {
                alert("Connection error. Please try again.");
            }
        });
    }
});

// ==================== FORMAT DATE ====================
function formatDate(dateStr) {
    if (!dateStr) return "";

    let d = new Date(dateStr.replace(" ", "T") + "+08:00");

    let datePart = d.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        timeZone: 'Asia/Manila'
    });

    let timePart = d.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        timeZone: 'Asia/Manila'
    });

    return `<span class="date-part">${datePart}</span><br>
            <span class="time-part">${timePart}</span>`;
}
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
                let row = `
                    <ul class="history-row">
                        <li>${e.election_title}</li>
                        <li>${e.status}</li>
                        <li>${formatDate(e.start_date)}</li>
                        <li>${formatDate(e.end_date)}</li>
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

// ==================== DELETE ====================
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
                    if (response.trim() === "success") {
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

            $("#title-input").val(e.election_title);

            let startVal = e.start_date ? e.start_date.replace(" ", "T").slice(0, 16) : "";
            let endVal = e.end_date ? e.end_date.replace(" ", "T").slice(0, 16) : "";

            $("#start-date").val(startVal);
            $("#end-date").val(endVal);

            $("#create-btn").text("Update Election");
            $("#create-panel h1").text("Edit Election");

            loadExistingPositions(id);

            $("#election-panel").hide();
            $("#create-panel").show();
        },
        error: function () {
            alert("Failed to load election details.");
        }
    });
}

// Load positions when editing
function loadExistingPositions(electionId) {
    $.ajax({
        url: "./../../control/electionControl.php?action=getPositions&id=" + electionId,
        method: "GET",
        success: function (response) {
            let positions = JSON.parse(response);
            $("#positions-box").empty();

            if (positions.length === 0) {
                addDefaultPositionRow();
                return;
            }

            positions.forEach(pos => {
                let row = `
                    <div class="position-row" data-position-id="${pos.position_id}">
                        <div class="pos-input-group">
                            <label>Position Name</label>
                            <input type="text" class="pos-name" value="${pos.position_name}" placeholder="e.g. President">
                        </div>
                        <div class="pos-input-group">
                            <label>Max Votes</label>
                            <input type="number" class="pos-max" step="1" min="1" max="8" value="${pos.max_votes}">
                        </div>
                        <button type="button" class="remove-pos-btn">Remove</button>
                    </div>
                `;
                $("#positions-box").append(row);
            });
        }
    });
}

function addDefaultPositionRow() {
    let emptyRow = `
        <div class="position-row">
            <div class="pos-input-group">
                <label>Position Name</label>
                <input type="text" class="pos-name" placeholder="e.g. President">
            </div>
            <div class="pos-input-group">
                <label>Max Votes</label>
                <input type="number" class="pos-max" step="1" min="1" max="8" value="1">
            </div>
            <button type="button" class="remove-pos-btn">Remove</button>
        </div>
    `;
    $("#positions-box").append(emptyRow);
}

// ==================== RESET ====================
function resetAfterEdit() {
    currentEditId = null;

    $("#create-btn").text("Create");
    $("#create-panel h1").text("Create Election");

    $("#title-input").val('');
    $("#start-date").val('');
    $("#end-date").val('');

    $("#positions-box").html(`
        <div class="position-row">
            <label>Position Name</label>
            <input type="text" class="pos-name" placeholder="e.g President">
            <label>Max Votes</label>
            <input type="number" class="pos-max" step="1" min="1" max="8">
        </div>
    `);

    $("#create-panel").hide();
    $("#election-panel").show();
}

loadElections();

// ==================== LIMIT MAX VOTES TO 8 GLOBALLY ====================
$(document).on("input", ".pos-max", function () {
    let val = parseInt(this.value);

    if (val > 8) {
        this.value = 8;
    }
    if (val < 1 && this.value !== "") {
        this.value = 1;
    }
});

setInterval(() => {
    loadElections();
}, 10000); // every 10 seconds


