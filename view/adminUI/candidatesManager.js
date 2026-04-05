$(document).ready(function () {

    let currentElectionId = "";
    let currentPartyId = "";
    let currentPartyName = "";
    let currentPositionId = "";
    let selectedStudentId = "";

    let allPositions = [];
    let allParties = [];

    init();

    function init() {
        hideSections();
        loadElections();
    }

    function hideSections() {
        $("#view_toggle_section, #party_tabs_section, #tabs_section, #candidates_section, #add_candidate_section, #slate_view, #student_view, #manage_view").hide();
    }

    // ==================== LOAD ELECTIONS ====================
    function loadElections() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: { action: "get_elections" },
            dataType: "json",
            success: function (res) {

                let dropdown = $("#election_dropdown").empty();
                dropdown.append('<option value="">-- Select an Election --</option>');

                if (res.success) {
                    res.data.forEach(e => {
                        dropdown.append(`<option value="${e.election_id}">${e.election_title}</option>`);
                    });
                }
            }
        });
    }

    // ==================== SELECT ELECTION ====================
    $("#election_dropdown").on("change", function () {

        currentElectionId = $(this).val();
        resetAll();

        if (!currentElectionId) return;

        $("#manage_view").show();
        loadStudentReference();

        $.when(
            $.ajax({
                url: "../../control/candidatesControl.php",
                type: "GET",
                data: { action: "get_parties", election_id: currentElectionId },
                dataType: "json"
            }),
            $.ajax({
                url: "../../control/candidatesControl.php",
                type: "GET",
                data: { action: "get_positions", election_id: currentElectionId },
                dataType: "json"
            })
        ).done(function (p, pos) {

            allParties = p[0].data || [];
            allPositions = pos[0].data || [];

            if (!allParties.length) return showStatus("No parties found.", "error");
            if (!allPositions.length) return showStatus("No positions found.", "error");

            buildPartyTabs(allParties);

            $("#party_tabs_section, #view_toggle_section").show();

            $(".party_tab_btn").first().trigger("click");
        });
    });

    // ==================== PARTY TABS ====================
    function buildPartyTabs(parties) {
        let container = $("#party_tabs_container").empty();

        parties.forEach(p => {
            container.append(`
                <button class="party_tab_btn tab_btn"
                    data-party-id="${p.party_id}"
                    data-party-name="${p.party_name}">
                    ${p.party_name}
                </button>
            `);
        });
    }

    $(document).on("click", ".party_tab_btn", function () {

        $(".party_tab_btn").removeClass("active");
        $(this).addClass("active");

        currentPartyId = $(this).data("party-id");
        currentPartyName = $(this).data("party-name");

        buildPositionTabs(allPositions);

        $("#tabs_section").show();
        $("#candidates_section, #add_candidate_section").hide();

        $(".position_tab_btn").first().trigger("click");
    });

    // ==================== POSITION TABS ====================
    function buildPositionTabs(positions) {
        let container = $("#tabs_container").empty();

        positions.forEach(p => {
            container.append(`
                <button class="position_tab_btn tab_btn"
                    data-position-id="${p.position_id}">
                    ${p.position_name}
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

    // ==================== LOAD CANDIDATES ====================
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

                if (!res.success || !res.data.length) {
                    list.append('<p>No candidates yet for this position.</p>');
                    return;
                }

                res.data.forEach(c => {
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

    // ==================== REMOVE ====================
    $(document).on("click", ".remove_btn", function () {

        let id = $(this).data("candidate-id");

        if (!confirm("Remove this candidate?")) return;

        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "POST",
            data: {
                action: "remove_candidate",
                candidate_id: id
            },
            dataType: "json",
            success: function (res) {

                showStatus(res.message, res.success ? "success" : "error");

                if (res.success) loadCandidates();
            }
        });
    });

    // ==================== ADD ====================
    $("#add_candidate_btn").on("click", function () {

        if (!currentElectionId) return showStatus("Select election first.", "error");
        if (!currentPartyId) return showStatus("Select party.", "error");
        if (!currentPositionId) return showStatus("Select position.", "error");
        if (!selectedStudentId) return showStatus("Select student.", "error");

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

    // ==================== SEARCH ====================
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

                let d = $("#student_dropdown").empty();

                if (!res.success || !res.data.length) return d.hide();

                d.append('<option value="">-- Select a Student --</option>');

                res.data.forEach(s => {
                    d.append(`<option value="${s.student_id}">
                        ${s.first_name} ${s.last_name} (ID: ${s.student_id})
                    </option>`);
                });

                d.show();
            }
        });
    });

    $("#student_dropdown").on("change", function () {
        selectedStudentId = $(this).val();
    });

    // ==================== STUDENT TABLE ====================
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

                if (!res.success || !res.data.length) {
                    tbody.append("<tr><td colspan='2'>No students available</td></tr>");
                    return;
                }

                res.data.forEach(s => {
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
    $("#btn_manage_view").click(function () {
        $(this).addClass("active");
        $("#btn_student_view, #btn_slate_view").removeClass("active");
        $("#manage_view").show();
        $("#student_view, #slate_view").hide();
    });

    $("#btn_student_view").click(function () {
        $(this).addClass("active");
        $("#btn_manage_view, #btn_slate_view").removeClass("active");
        $("#manage_view, #slate_view").hide();
        $("#student_view").show();
        loadStudentReference();
    });

    $("#btn_slate_view").click(function () {
        $(this).addClass("active");
        $("#btn_manage_view, #btn_student_view").removeClass("active");
        $("#manage_view, #student_view").hide();
        $("#slate_view").show();
        loadSlate();
    });

    // ==================== SLATE ====================
    function loadSlate() {
        $.ajax({
            url: "../../control/candidatesControl.php",
            method: "GET",
            data: { action: "get_slate", election_id: currentElectionId },
            dataType: "json",
            success: function (res) {
                if (res.success) buildSlate(res.data);
            }
        });
    }

    function buildSlate(data) {
        let container = $("#slate_container").empty();
        let row = $('<div class="slate_row"></div>');

        data.parties.forEach(p => {

            let col = $('<div class="slate_col"></div>');
            col.append(`<div class="slate_party_name">${p.party_name}</div>`);

            data.positions.forEach(pos => {

                let matches = data.candidates.filter(c =>
                    c.party_id == p.party_id && c.position_id == pos.position_id
                );

                let block = $('<div class="slate_position_block"></div>');
                block.append(`<div class="slate_position_label">${pos.position_name}</div>`);

                if (!matches.length) {
                    block.append('<div class="slate_candidate_name empty">—</div>');
                } else {
                    matches.forEach(c => {
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

            row.append(col);
        });

        container.append(row);
    }

    function resetStudentSearch() {
        selectedStudentId = "";
        $("#student_search_input").val("");
        $("#student_dropdown").hide().empty();
    }

    function resetAll() {
        currentPartyId = "";
        currentPositionId = "";
        allParties = [];
        allPositions = [];

        hideSections();

        $("#party_tabs_container, #tabs_container, #candidates_list").empty();

        resetStudentSearch();
        hideStatus();
    }

    function showStatus(msg, type) {
        let box = $("#status_message");
        box.removeClass("success error").addClass(type).text(msg).show();
        setTimeout(hideStatus, 3000);
    }

    function hideStatus() {
        $("#status_message").hide().text("");
    }

});
