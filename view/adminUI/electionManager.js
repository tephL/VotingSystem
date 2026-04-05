function showCreatePanel() {
    $("#election-panel").hide();
    $("#create-panel").show();
}

function showElectionPanel() {
    $("#create-panel").hide();
    $("#election-panel").show();
}

$("#create-panel").hide();

$("#createbutton").click(function () {
    showCreatePanel();
});

// ==================== HTML BUILDERS ====================
function newPositionRow(name, max, positionId) {
    let dataAttr = "";
    if (positionId) {
        dataAttr = 'data-position-id="' + positionId + '"';
    }

    if (!name) { name = ""; }
    if (!max) { max = ""; }

    return `
        <div class="position-row" ${dataAttr}>
            <label>Position Name</label>
            <input type="text" class="pos-name" value="${name}" placeholder="e.g President">
            <label>Max Votes</label>
            <input type="number" class="pos-max" step="1" min="1" max="8" value="${max}">
            <button type="button" class="remove-pos-btn">Remove</button>
        </div>
    `;
}

function newPartyRow(name, partyId) {
    let dataAttr = "";
    if (partyId) {
        dataAttr = 'data-party-id="' + partyId + '"';
    }

    if (!name) { name = ""; }

    return `
        <div class="party-row" ${dataAttr}>
            <label>Party Name</label>
            <input type="text" class="party-name" value="${name}" placeholder="e.g Partido Uno">
            <button type="button" class="remove-party-btn">Remove</button>
        </div>
    `;
}

function electionRow(e) {
    return `
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
}

// ==================== ADD / REMOVE POSITION ROW ====================
$("#add-position-btn").click(function () {
    $("#positions-box").append(newPositionRow());
});

$(document).on("click", ".remove-pos-btn", function () {
    $(this).closest(".position-row").remove();
});

$(document).on("input", ".pos-max", function () {
    let val = parseInt(this.value);
    if (val > 8) { this.value = 8; }
    if (val < 1 && this.value !== "") { this.value = 1; }
});

// ==================== ADD / REMOVE PARTY ROW ====================
$("#add-party-btn").click(function () {
    $("#parties-box").append(newPartyRow());
});

$(document).on("click", ".remove-party-btn", function () {
    let row = $(this).closest(".party-row");

    let partyId = row.attr("data-party-id");
    if (partyId && currentEditId !== null) {
        if (!confirm("Remove this party? Candidates under this party will also be removed.")) {
            return;
        }

        $.ajax({
            url: "./../../control/electionControl.php?action=removeParty",
            method: "POST",
            data: { party_id: partyId },
            success: function (response) {
                if (response.trim() === "success") {
                    row.remove();
                } else {
                    alert("Failed to remove party: " + response.trim());
                }
            },
            error: function () {
                alert("Connection error while removing party.");
            }
        });
    } else {
        row.remove();
    }
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
        let positionId = $(this).attr("data-position-id") || null;
        if (name && max) {
            positions.push({ name: name, max: max, position_id: positionId });
        }
    });

    if (positions.length === 0) {
        alert("Please add at least one position.");
        return;
    }

    let parties = [];
    $(".party-row").each(function () {
        let name = $(this).find(".party-name").val().trim();
        let partyId = $(this).attr("data-party-id") || null;
        if (name) {
            parties.push({ name: name, party_id: partyId });
        }
    });

    if (confirm("Are you sure?")) {

        let url = "./../../control/electionControl.php?action=create";
        let data = {
            title: title,
            start: start,
            end: end,
            positions: JSON.stringify(positions),
            parties: JSON.stringify(parties)
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
                }
                else if (response === "min_parties") {
                    alert("There must be at least TWO party lists.");
                }
                else {
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
    if (!dateStr) { return ""; }

    let d = new Date(dateStr.replace(" ", "T") + "+08:00");

    let datePart = d.toLocaleDateString('en-US', {
        month: 'long', day: 'numeric', year: 'numeric', timeZone: 'Asia/Manila'
    });

    let timePart = d.toLocaleTimeString('en-US', {
        hour: '2-digit', minute: '2-digit', hour12: true, timeZone: 'Asia/Manila'
    });

    return `<span class="date-part">${datePart}</span><br><span class="time-part">${timePart}</span>`;
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
                $("#history-list").append(electionRow(e));
            });

            attachButtons();
        },
        error: function () {
            alert("Failed to load elections.");
        }
    });
}

// ==================== ATTACH EDIT / DELETE BUTTONS ====================
function attachButtons() {
    $(".delete-btn").off().click(function () {
        let id = $(this).attr("id").split("_")[1];

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
        let id = $(this).attr("id").split("_")[1];
        editElection(id);
    });
}

// ==================== EDIT ====================
let currentEditId = null;

function editElection(id) {
    currentEditId = id;

    $.ajax({
        url: "./../../control/electionControl.php?action=getById&id=" + id,
        method: "GET",
        success: function (response) {
            let e = JSON.parse(response);

            let startVal = "";
            let endVal = "";

            if (e.start_date) { startVal = e.start_date.replace(" ", "T").slice(0, 16); }
            if (e.end_date) { endVal = e.end_date.replace(" ", "T").slice(0, 16); }

            $("#title-input").val(e.election_title);
            $("#start-date").val(startVal);
            $("#end-date").val(endVal);

            $("#create-btn").text("Update Election");
            $("#create-panel h1").text("Edit Election");

            loadExistingPositions(id);
            loadExistingParties(id);

            showCreatePanel();
        },
        error: function () {
            alert("Failed to load election details.");
        }
    });
}

// ==================== LOAD EXISTING POSITIONS ====================
function loadExistingPositions(electionId) {
    $.ajax({
        url: "./../../control/electionControl.php?action=getPositions&id=" + electionId,
        method: "GET",
        success: function (response) {
            let positions = JSON.parse(response);
            $("#positions-box").empty();

            if (positions.length === 0) {
                $("#positions-box").append(newPositionRow());
                return;
            }

            positions.forEach(pos => {
                $("#positions-box").append(newPositionRow(pos.position_name, pos.max_votes, pos.position_id));
            });
        }
    });
}

// ==================== LOAD EXISTING PARTIES ====================
function loadExistingParties(electionId) {
    $.ajax({
        url: "./../../control/electionControl.php?action=getParties&id=" + electionId,
        method: "GET",
        success: function (response) {
            let parties = JSON.parse(response);
            $("#parties-box").empty();

            if (parties.length === 0) {
                $("#parties-box").append(newPartyRow());
                return;
            }

            parties.forEach(p => {
                $("#parties-box").append(newPartyRow(p.party_name, p.party_id));
            });
        },
        error: function () {
            alert("Failed to load parties.");
        }
    });
}

// ==================== CANCEL BUTTON ====================
$("#cancel-btn").click(function () {
    resetAfterEdit();
});

// ==================== RESET ====================
function resetAfterEdit() {
    currentEditId = null;
    $("#create-btn").text("Create");
    $("#create-panel h1").text("Create Election");
    $("#title-input").val('');
    $("#start-date").val('');
    $("#end-date").val('');
    $("#positions-box").html(newPositionRow());
    $("#parties-box").html(newPartyRow());
    showElectionPanel();
}

// ==================== INIT ====================
loadElections();

setInterval(function () {
    loadElections();
}, 10000);
