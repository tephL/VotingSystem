// candidatesManager.js

$(document).ready(function () {

    const CTRL = "../../control/candidatesControl.php";

    // ── State ──
    let currentElectionId   = "";
    let currentPartyId      = "";
    let currentPartyName    = "";
    let currentPositionId   = "";
    let selectedStudentId   = "";
    let selectedStudentName = "";
    let allPositions        = [];
    let allParties          = [];

    // ── Init hidden sections ──
    $("#view_toggle_section").hide();
    $("#party_tabs_section").hide();
    $("#tabs_section").hide();
    $("#candidates_section").hide();
    $("#add_candidate_section").hide();
    $("#slate_view").hide();

    // ── Load elections ──
    loadElections();

    function loadElections() {
        $.ajax({
            url: CTRL, type: "GET",
            data: { action: "get_elections" },
            dataType: "json",
            success: function (result) {
                let dropdown = $("#election_dropdown");
                dropdown.empty();
                dropdown.append('<option value="">-- Select an Election --</option>');
                if (result.success) {
                    result.data.forEach(function (e) {
                        dropdown.append('<option value="' + e.election_id + '">' + e.election_title + '</option>');
                    });
                }
            }
        });
    }

    // ── Election selected ──
    $("#election_dropdown").on("change", function () {
        currentElectionId = $(this).val();
        resetAll();

        if (currentElectionId === "") return;

        $.when(
            $.ajax({ url: CTRL, type: "GET", data: { action: "get_parties",   election_id: currentElectionId }, dataType: "json" }),
            $.ajax({ url: CTRL, type: "GET", data: { action: "get_positions", election_id: currentElectionId }, dataType: "json" })
        ).done(function (partiesRes, positionsRes) {
            allParties   = partiesRes[0].data  || [];
            allPositions = positionsRes[0].data || [];

            if (allParties.length === 0) {
                showStatus("No parties found. Add parties in Election Manager first.", "error");
                return;
            }
            if (allPositions.length === 0) {
                showStatus("No positions found for this election.", "error");
                return;
            }

            buildPartyTabs(allParties);
            $("#party_tabs_section").show();
            $("#view_toggle_section").show();

            $(".party_tab_btn").first().trigger("click");
        });
    });

    // ── Build party tabs ──
    function buildPartyTabs(parties) {
        let container = $("#party_tabs_container");
        container.empty();

        parties.forEach(function (p) {
            let btn = $('<button class="party_tab_btn tab_btn"></button>');
            btn.text(p.party_name);
            btn.attr("data-party-id",   p.party_id);
            btn.attr("data-party-name", p.party_name);
            container.append(btn);
        });
    }

    // ── Party tab clicked ──
    $(document).on("click", ".party_tab_btn", function () {
        $(".party_tab_btn").removeClass("active");
        $(this).addClass("active");

        currentPartyId    = $(this).attr("data-party-id");
        currentPartyName  = $(this).attr("data-party-name");
        currentPositionId = "";

        resetStudentSearch();
        $("#candidates_section").hide();
        $("#add_candidate_section").hide();

        buildPositionTabs(allPositions);
        $("#tabs_section").show();

        $(".position_tab_btn").first().trigger("click");
    });

    // ── Build position tabs ──
    function buildPositionTabs(positions) {
        let container = $("#tabs_container");
        container.empty();

        positions.forEach(function (p) {
            let btn = $('<button class="position_tab_btn tab_btn"></button>');
            btn.text(p.position_name);
            btn.attr("data-position-id", p.position_id);
            container.append(btn);
        });
    }

    // ── Position tab clicked ──
    $(document).on("click", ".position_tab_btn", function () {
        $(".position_tab_btn").removeClass("active");
        $(this).addClass("active");

        currentPositionId = $(this).attr("data-position-id");

        resetStudentSearch();
        loadCandidates(currentPartyId, currentPositionId);
        $("#candidates_section").show();
        $("#add_candidate_section").show();
    });

    // ── Load candidates by party + position ──
    function loadCandidates(party_id, position_id) {
        $.ajax({
            url: CTRL, type: "GET",
            data: { action: "get_candidates_by_party_position", party_id: party_id, position_id: position_id },
            dataType: "json",
            success: function (result) {
                if (result.success) {
                    displayCandidates(result.data);
                } else {
                    showStatus("Failed to load candidates.", "error");
                }
            }
        });
    }

    function displayCandidates(candidates) {
        let list = $("#candidates_list");
        list.empty();

        if (candidates.length === 0) {
            list.append('<p id="no_candidates_msg">No candidates yet for this position.</p>');
            return;
        }

        candidates.forEach(function (c) {
            let fullName = c.first_name + " " + c.last_name;
            let item = '<div class="candidate_item" data-candidate-id="' + c.candidate_id + '">';
            item += '<span>';
            item += '<span class="candidate_name">' + fullName + '</span>';
            item += '<span class="candidate_meta">ID: ' + c.student_id + '</span>';
            item += '</span>';
            item += '<button class="remove_btn" data-candidate-id="' + c.candidate_id + '">Remove</button>';
            item += '</div>';
            list.append(item);
        });
    }

    // ── Remove candidate ──
    $(document).on("click", ".remove_btn", function () {
        let candidate_id = $(this).attr("data-candidate-id");
        if (!confirm("Remove this candidate?")) return;

        $.ajax({
            url: CTRL, type: "POST",
            data: { action: "remove_candidate", candidate_id: candidate_id },
            dataType: "json",
            success: function (result) {
                showStatus(result.message, result.success ? "success" : "error");
                if (result.success) loadCandidates(currentPartyId, currentPositionId);
            }
        });
    });

    // ── Student search ──
    let searchTimeout = null;

    $("#student_search_input").on("keyup", function () {
        let term = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (term.length < 2) {
            $("#student_dropdown").hide().empty();
            return;
        }

        searchTimeout = setTimeout(function () {
            $.ajax({
                url: CTRL, type: "GET",
                data: { action: "search_students", search_term: term, election_id: currentElectionId },
                dataType: "json",
                success: function (result) {
                    let dropdown = $("#student_dropdown");
                    dropdown.empty();

                    if (!result.success || result.data.length === 0) {
                        dropdown.hide();
                        return;
                    }

                    dropdown.append('<option value="">-- Select a Student --</option>');
                    result.data.forEach(function (s) {
                        dropdown.append('<option value="' + s.student_id + '">'
                            + s.first_name + " " + s.last_name
                            + ' (ID: ' + s.student_id + ')'
                            + '</option>');
                    });
                    dropdown.show();
                }
            });
        }, 400);
    });

    // ── Student selected ──
    $(document).on("change", "#student_dropdown", function () {
        selectedStudentId   = $(this).val();
        selectedStudentName = $(this).find("option:selected").text();

        if (selectedStudentId === "") {
            $("#selected_student_display").text("No student selected.");
        } else {
            $("#selected_student_display").text("Selected: " + selectedStudentName);
        }
    });

    // ── Add candidate ──
    $("#add_candidate_btn").on("click", function () {
        if (!currentElectionId) { showStatus("Select an election first.", "error"); return; }
        if (!currentPartyId)    { showStatus("Select a party first.", "error"); return; }
        if (!currentPositionId) { showStatus("Select a position first.", "error"); return; }
        if (!selectedStudentId) { showStatus("Search and select a student first.", "error"); return; }

        $.ajax({
            url: CTRL, type: "POST",
            data: {
                action:      "add_candidate",
                student_id:  selectedStudentId,
                position_id: currentPositionId,
                party_id:    currentPartyId,
                election_id: currentElectionId
            },
            dataType: "json",
            success: function (result) {
                showStatus(result.message, result.success ? "success" : "error");
                if (result.success) {
                    loadCandidates(currentPartyId, currentPositionId);
                    resetStudentSearch();
                }
            }
        });
    });

    // ── View toggle ──
    $("#btn_manage_view").on("click", function () {
        $(this).addClass("active");
        $("#btn_slate_view").removeClass("active");
        $("#manage_view").show();
        $("#slate_view").hide();
    });

    $("#btn_slate_view").on("click", function () {
        $(this).addClass("active");
        $("#btn_manage_view").removeClass("active");
        $("#manage_view").hide();
        $("#slate_view").show();
        loadSlate();
    });

    // ── Slate view ──
    function loadSlate() {
        $.ajax({
            url: CTRL, type: "GET",
            data: { action: "get_slate", election_id: currentElectionId },
            dataType: "json",
            success: function (result) {
                if (result.success) buildSlate(result.data);
            }
        });
    }

    function buildSlate(data) {
        let container = $("#slate_container");
        container.empty();

        if (!data.parties || data.parties.length === 0) {
            container.append('<p class="no_slate_msg">No data to display.</p>');
            return;
        }

        let row = $('<div class="slate_row"></div>');

        data.parties.forEach(function (party) {
            let col = $('<div class="slate_col"></div>');
            col.append('<div class="slate_party_name">' + party.party_name + '</div>');

            data.positions.forEach(function (pos) {
                let matches = data.candidates.filter(function (c) {
                    return c.party_id == party.party_id && c.position_id == pos.position_id;
                });

                let posBlock = $('<div class="slate_position_block"></div>');
                posBlock.append('<div class="slate_position_label">' + pos.position_name + '</div>');

                if (matches.length === 0) {
                    posBlock.append('<div class="slate_candidate_name empty">—</div>');
                } else {
                    matches.forEach(function (c) {
                        posBlock.append('<div class="slate_candidate_name">' + c.first_name + ' ' + c.last_name + '</div>');
                    });
                }

                col.append(posBlock);
            });

            row.append(col);
        });

        container.append(row);
    }

    // ── Helpers ──
    function resetStudentSearch() {
        selectedStudentId   = "";
        selectedStudentName = "";
        $("#student_search_input").val("");
        $("#student_dropdown").hide().empty();
        $("#selected_student_display").text("No student selected.");
    }

    function resetAll() {
        currentPartyId    = "";
        currentPartyName  = "";
        currentPositionId = "";
        allParties        = [];
        allPositions      = [];

        $("#view_toggle_section").hide();
        $("#party_tabs_section").hide();
        $("#party_tabs_container").empty();
        $("#tabs_section").hide();
        $("#tabs_container").empty();
        $("#candidates_section").hide();
        $("#candidates_list").empty();
        $("#add_candidate_section").hide();
        $("#slate_view").hide();
        $("#manage_view").show();
        $("#btn_manage_view").addClass("active");
        $("#btn_slate_view").removeClass("active");

        resetStudentSearch();
        hideStatus();
    }

    function showStatus(message, type) {
        let box = $("#status_message");
        box.removeClass("success error").addClass(type).text(message).show();
        setTimeout(hideStatus, 4000);
    }

    function hideStatus() {
        $("#status_message").hide().text("");
    }

});