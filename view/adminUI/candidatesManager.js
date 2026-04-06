$(document).ready(function () {

    let currentElectionId = "";
    let currentPartyId = "";
    let currentPositionId = "";
    let selectedStudentId = "";

    let allPositions = [];
    let allParties = [];

    init();

    function init() {
        hideAllTabsAndViews();
        loadElections();
    }

    function hideAllTabsAndViews() {
        $("#manage_view, #student_view, #slate_view").hide();
        $("#party_tabs_section, #tabs_section, #view_toggle_section").hide();
    }

    function showStatus(message, type) {
        $("#status_message")
            .removeClass("success error")
            .addClass(type)
            .text(message)
            .show();

        setTimeout(function() {
            $("#status_message").hide().text("");
        }, 3000);
    }

    // LOAD ELECTIONS 
    function loadElections() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: { action: "get_elections" },
            dataType: "json",
            success: function (res) {
                let dropdown = $("#election_dropdown").empty();
                dropdown.append('<option value="">-- Select an Election --</option>');

                if (res.success && res.data) {
                    res.data.forEach(function(e) {
                        dropdown.append(`<option value="${e.election_id}">${e.election_title}</option>`);
                    });
                }
            }
        });
    }

    // ELECTION DROPDOWN CHANGE 
    $("#election_dropdown").on("change", function () {
        currentElectionId = $(this).val();

        if (currentElectionId === "") {
            hideAllTabsAndViews();
            return;
        }

        resetAll();

        $("#manage_view").show();
        $("#party_tabs_section, #view_toggle_section").show();

        loadStudentReference();
        loadParties();
    });

    function loadParties() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: { action: "get_parties", election_id: currentElectionId },
            dataType: "json",
            success: function (res) {
                allParties = [];
                if (res.data) {
                    allParties = res.data;
                }
                buildPartyTabs();

                loadPositions();
            }
        });
    }

    function loadPositions() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: { action: "get_positions", election_id: currentElectionId },
            dataType: "json",
            success: function (res) {
                allPositions = [];
                if (res.data) {
                    allPositions = res.data;
                }
                buildPositionTabs();

                // Auto select first party
                if (allParties.length > 0) {
                    $(".party_tab_btn").first().click();
                }
            }
        });
    }

    // PARTY TABS 
    function buildPartyTabs() {
        let container = $("#party_tabs_container").empty();

        allParties.forEach(function(p) {
            container.append(`
                <button class="party_tab_btn tab_btn"
                        data-party-id="${p.party_id}">
                    ${p.party_name}
                </button>
            `);
        });
    }

    $(document).on("click", ".party_tab_btn", function () {
        $(".party_tab_btn").removeClass("active");
        $(this).addClass("active");

        currentPartyId = $(this).data("party-id");

        buildPositionTabs();
        $("#tabs_section").show();
        $("#candidates_section, #add_candidate_section").hide();

        if (allPositions.length > 0) {
            $(".position_tab_btn").first().click();
        }
    });

    // POSITION TABS 
    function buildPositionTabs() {
        let container = $("#tabs_container").empty();

        allPositions.forEach(function(pos) {
            container.append(`
                <button class="position_tab_btn tab_btn"
                        data-position-id="${pos.position_id}">
                    ${pos.position_name}
                </button>
            `);
        });
    }

    $(document).on("click", ".position_tab_btn", function () {
        $(".position_tab_btn").removeClass("active");
        $(this).addClass("active");

        currentPositionId = $(this).data("position-id");

        loadCandidates();
        $("#candidates_section, #add_candidate_section").show();
    });

    // LOAD CANDIDATES 
    function loadCandidates() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: {
                action: "get_candidates_by_party_position",
                party_id: currentPartyId,
                position_id: currentPositionId
            },
            dataType: "json",
            success: function (res) {
                let list = $("#candidates_list").empty();

                if (!res.success || res.data.length === 0) {
                    list.append('<p>No candidates yet for this position.</p>');
                    return;
                }

                res.data.forEach(function(c) {
                    list.append(`
                        <div class="candidate_item" data-candidate-id="${c.candidate_id}">
                            <span>
                                <span class="candidate_name">${c.first_name} ${c.last_name}</span>
                                <span class="candidate_meta">ID: ${c.student_id}</span>
                            </span>
                            <button class="remove_btn" data-candidate-id="${c.candidate_id}">Remove</button>
                        </div>
                    `);
                });
            }
        });
    }

    // ADD & REMOVE 
    $("#add_candidate_btn").on("click", function () {
        if (!selectedStudentId) {
            showStatus("Please select a student first.", "error");
            return;
        }

        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "POST",
            data: {
                action: "add_candidate",
                student_id: selectedStudentId,
                position_id: currentPositionId,
                party_id: currentPartyId,
                election_id: currentElectionId
            },
            dataType: "json",
            success: function (res) {
                showStatus(res.message, res.success ? "success" : "error");
                if (res.success) {
                    loadCandidates();
                    resetStudentSearch();
                }
            }
        });
    });

    $(document).on("click", ".remove_btn", function () {
        if (!confirm("Remove this candidate?")) return;

        let candidateId = $(this).data("candidate-id");

        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "POST",
            data: { action: "remove_candidate", candidate_id: candidateId },
            dataType: "json",
            success: function (res) {
                showStatus(res.message, res.success ? "success" : "error");
                if (res.success) loadCandidates();
            }
        });
    });

    // STUDENT SEARCH 
    $("#student_search_input").on("keyup", function () {
        let term = $(this).val().trim();

        if (term.length < 2) {
            $("#student_dropdown").hide().empty();
            return;
        }

        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: {
                action: "search_students",
                search_term: term,
                election_id: currentElectionId
            },
            dataType: "json",
            success: function (res) {
                let dropdown = $("#student_dropdown").empty();

                if (!res.success || res.data.length === 0) {
                    dropdown.hide();
                    return;
                }

                dropdown.append('<option value="">-- Select a Student --</option>');

                res.data.forEach(function(s) {
                    dropdown.append(`<option value="${s.student_id}">
                        ${s.first_name} ${s.last_name} (ID: ${s.student_id})
                    </option>`);
                });

                dropdown.show();
            }
        });
    });

    $("#student_dropdown").on("change", function () {
        selectedStudentId = $(this).val();
    });

    function loadStudentReference() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: {
                action: "search_students",
                search_term: "",
                election_id: currentElectionId
            },
            dataType: "json",
            success: function (res) {
                let tbody = $("#student_reference_table tbody").empty();

                if (!res.success || res.data.length === 0) {
                    tbody.append("<tr><td colspan='2'>No students available</td></tr>");
                    return;
                }

                res.data.forEach(function(s) {
                    tbody.append(`
                        <tr>
                            <td>${s.student_id}</td>
                            <td>${s.first_name} ${s.last_name}</td>
                        </tr>
                    `);
                });
            }
        });
    }

    // ==================== VIEW TOGGLE ====================
    $(".view_toggle_btn").click(function () {
        $(".view_toggle_btn").removeClass("active");
        $(this).addClass("active");

        $("#manage_view, #student_view, #slate_view").hide();

        if ($(this).attr("id") === "btn_manage_view") {
            $("#manage_view").show();
        }
        else if ($(this).attr("id") === "btn_student_view") {
            $("#student_view").show();
            loadStudentReference();
        }
        else if ($(this).attr("id") === "btn_slate_view") {
            $("#slate_view").show();
            loadSlate();
        }
    });

    // ==================== SLATE VIEW ====================
    function loadSlate() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: { action: "get_slate", election_id: currentElectionId },
            dataType: "json",
            success: function (res) {
                if (res.success) {
                    buildSlate(res.data);
                }
            }
        });
    }

    function buildSlate(data) {
        let container = $("#slate_container").empty();

        data.parties.forEach(function(party) {
            let col = $('<div class="slate_col"></div>');
            col.append(`<div class="slate_party_name">${party.party_name}</div>`);

            data.positions.forEach(function(position) {
                let matches = data.candidates.filter(function(c) {
                    return c.party_id == party.party_id && c.position_id == position.position_id;
                });

                let block = $('<div class="slate_position_block"></div>');
                block.append(`<div class="slate_position_label">${position.position_name}</div>`);

                if (matches.length === 0) {
                    block.append('<div class="slate_candidate_name empty">—</div>');
                } else {
                    matches.forEach(function(c) {
                        block.append(`
                            <div class="slate_candidate_name">
                                <strong>${c.first_name} ${c.last_name}</strong><br>
                                <small>ID: ${c.student_id}</small><br>
                                <small>College: ${c.college_name || "N/A"}</small>
                            </div>
                        `);
                    });
                }
                col.append(block);
            });

            container.append(col);
        });
    }

    // ==================== RESET ====================
    function resetStudentSearch() {
        selectedStudentId = "";
        $("#student_search_input").val("");
        $("#student_dropdown").hide().empty();
    }

    function resetAll() {
        currentPartyId = "";
        currentPositionId = "";
        selectedStudentId = "";
        allParties = [];
        allPositions = [];

        $("#party_tabs_container, #tabs_container, #candidates_list").empty();
        resetStudentSearch();
        hideAllTabsAndViews();   
    }

});