$(document).ready(function () {

    let currentEditId = null;
    init();

    function init() {
        $("#create-panel").hide();
        loadElections();
    }

    function showCreatePanel() {
        $("#election-panel").hide();
        $("#create-panel").show();
    }

    function showElectionPanel() {
        $("#create-panel").hide();
        $("#election-panel").show();
    }

    $("#createbutton").click(function () {
        showCreatePanel();
    });

    function newPositionRow(name = "", max = "", positionId = "") {
        let dataAttr = "";
        if (positionId) {
            dataAttr = `data-position-id="${positionId}"`;
        }
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

    function newPartyRow(name = "", partyId = "") {
        let dataAttr = "";
        if (partyId) {
            dataAttr = `data-party-id="${partyId}"`;
        }
        return `
            <div class="party-row" ${dataAttr}>
                <label>Party Name</label>
                <input type="text" class="party-name" value="${name}" placeholder="e.g Partido Uno">
                <button type="button" class="remove-party-btn">Remove</button>
            </div>
        `;
    }

    function electionRow(e) {
        let actionBtn = "";

        if (e.status === "Completed") {
            actionBtn = `<button class="view-btn" id="view_${e.election_id}">View</button>`;

        } else if (e.status === "Active") {
            actionBtn = `
            <button class="view-btn" id="view_${e.election_id}">View</button>
        `;
        } else {
            actionBtn = `
                <button class="edit-btn" id="edit_${e.election_id}">Edit</button>
                <button class="delete-btn" id="delete_${e.election_id}">Delete</button>
            `;
        }

        return `
            <ul class="history-row">
                <li>${e.election_title}</li>
                <li>${e.status}</li>
                <li>${formatDate(e.start_date)}</li>
                <li>${formatDate(e.end_date)}</li>
                <li class="action-cell">${actionBtn}</li>
            </ul>
        `;
    }

    $("#add-position-btn").click(function () {
        $("#positions-box").append(newPositionRow());
    });

    $(document).on("click", ".remove-pos-btn", function () {
        $(this).closest(".position-row").remove();
    });

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
                url: "../../control/admin/electionControl.php?action=removeParty",
                method: "POST",
                data: { party_id: partyId },
                success: function (response) {
                    if (response.trim() === "success") {
                        row.remove();
                    } else {
                        alert("Failed to remove party.");
                    }
                }
            });
        } else {
            row.remove();
        }
    });

    $(document).on("input", ".pos-max", function () {
        if ($(this).val() > 8) {
            $(this).val(8);
        }
    });

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
            let posId = $(this).attr("data-position-id") || "";

            if (name && max) {
                let posObj = {
                    name: name,
                    max: parseInt(max)
                };
                if (posId) {
                    posObj.position_id = posId;
                }
                positions.push(posObj);
            }
        });

        let parties = [];
        $(".party-row").each(function () {
            let name = $(this).find(".party-name").val().trim();
            let partyId = $(this).attr("data-party-id") || "";

            if (name) {
                let partyObj = {
                    name: name
                };
                if (partyId) {
                    partyObj.party_id = partyId;
                }
                parties.push(partyObj);
            }
        });

        if (positions.length === 0) {
            alert("Please add at least one position.");
            return;
        }

        if (parties.length < 2) {
            alert("There must be at least TWO party lists.");
            return;
        }

        if (confirm("Are you sure?")) {
            let url = "../../control/admin/electionControl.php?action=create";
            let data = {
                title: title,
                start: start,
                end: end,
                positions: positions,
                parties: parties
            };

            if (currentEditId !== null) {
                url = "../../control/admin/electionControl.php?action=update";
                data.id = currentEditId;
            }

            $.ajax({
                url: url,
                method: "POST",
                data: data,
                success: function (response) {
                    response = response.trim();
                    if (response === "success") {
                        let message = "Election created successfully!";
                        if (currentEditId !== null) {
                            message = "Election updated successfully!";
                        }
                        alert(message);
                        resetAfterEdit();
                        loadElections();
                    } else if (response === "active") {
                        alert("Cannot update election while another is active.");
                    } else if (response === "invalid") {
                        alert("End date cannot be earlier than start date.");
                    } else if (response === "past") {
                        alert("Start date cannot be in the past.");
                    } else if (response === "min_parties") {
                        alert("There must be at least TWO party lists.");
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

    function formatDate(dateStr) {
        if (!dateStr) {
            return "";
        }

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

        return `<span class="date-part">${datePart}</span><br><span class="time-part">${timePart}</span>`;
    }

    function loadElections() {
        $.ajax({
            url: "../../control/admin/electionControl.php?action=getAll",
            method: "GET",
            success: function (response) {
                console.log("Raw response from server:", response);
                let elections = JSON.parse(response);
                $("#history-list").empty();

                if (elections.length === 0) {
                    $("#history-list").html('<p class="no-elect">No elections yet.</p>');
                    return;
                }

                elections.forEach(function (e) {
                    $("#history-list").append(electionRow(e));
                });

                attachActionButtons();
            },
            error: function () {
                alert("Failed to load elections.");
            }
        });
    }

    function attachActionButtons() {
        $(".delete-btn").off().click(function () {
            let id = $(this).attr("id").split("_")[1];

            if (confirm("Are you sure you want to delete this election?")) {
                $.ajax({
                    url: "../../control/admin/electionControl.php?action=delete",
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

        $(".edit-btn, .view-btn").off().click(function () {
            let id = $(this).attr("id").split("_")[1];
            let isView = $(this).hasClass("view-btn");
            editElection(id, isView);
        });
    }

    function editElection(id, isViewOnly) {
        if (isViewOnly) {
            currentEditId = null;
        } else {
            currentEditId = id;
        }

        $.ajax({
            url: "../../control/admin/electionControl.php?action=getById&id=" + id,
            method: "GET",
            success: function (response) {
                let e = JSON.parse(response);

                let startVal = "";
                if (e.start_date) {
                    startVal = e.start_date.replace(" ", "T").slice(0, 16);
                }

                let endVal = "";
                if (e.end_date) {
                    endVal = e.end_date.replace(" ", "T").slice(0, 16);
                }

                $("#title-input").val(e.election_title);
                $("#start-date").val(startVal);
                $("#end-date").val(endVal);

                if (isViewOnly) {
                    $("#create-btn").hide();
                    $("#create-panel h1").text("View Election");
                    $("#title-input, #start-date, #end-date").prop("disabled", true);
                    $("#add-position-btn, #add-party-btn").hide();
                } else {
                    $("#create-btn").show().text("Update Election");
                    $("#create-panel h1").text("Edit Election");
                    $("#title-input, #start-date, #end-date").prop("disabled", false);
                    $("#add-position-btn, #add-party-btn").show();
                }

                loadExistingPositions(id);
                loadExistingParties(id);

                if (isViewOnly) {
                    $(".remove-pos-btn, .remove-party-btn").hide();
                    $(".pos-name, .pos-max, .party-name").prop("disabled", true);
                } else {
                    $(".remove-pos-btn, .remove-party-btn").show();
                    $(".pos-name, .pos-max, .party-name").prop("disabled", false);
                }

                showCreatePanel();
            }
        });
    }

    function loadExistingPositions(electionId) {
        $.ajax({
            url: "../../control/admin/electionControl.php?action=getPositions&id=" + electionId,
            method: "GET",
            success: function (response) {
                let positions = JSON.parse(response);
                $("#positions-box").empty();

                if (positions.length === 0) {
                    $("#positions-box").append(newPositionRow());
                } else {
                    positions.forEach(function (pos) {
                        $("#positions-box").append(newPositionRow(pos.position_name, pos.max_votes, pos.position_id));
                    });
                }
            }
        });
    }

    function loadExistingParties(electionId) {
        $.ajax({
            url: "../../control/admin/electionControl.php?action=getParties&id=" + electionId,
            method: "GET",
            success: function (response) {
                let parties = JSON.parse(response);
                $("#parties-box").empty();

                if (parties.length === 0) {
                    $("#parties-box").append(newPartyRow());
                } else {
                    parties.forEach(function (p) {
                        $("#parties-box").append(newPartyRow(p.party_name, p.party_id));
                    });
                }
            }
        });
    }

    $("#cancel-btn").click(function () {
        resetAfterEdit();
    });

    function resetAfterEdit() {
        currentEditId = null;
        $("#create-btn").show().text("Create");
        $("#create-panel h1").text("Create Election");

        $("#title-input, #start-date, #end-date").val('').prop("disabled", false);

        $("#add-position-btn, #add-party-btn").show();
        $(".remove-pos-btn, .remove-party-btn").show();
        $(".pos-name, .pos-max, .party-name").prop("disabled", false);

        $("#positions-box").html(newPositionRow());
        $("#parties-box").html(newPartyRow());

        showElectionPanel();
    }

    setInterval(loadElections, 10000);
});
