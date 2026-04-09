let ELECTION_ID;
let POSITIONS = [];

intitializeData();

function intitializeData(){
    $.ajax({
        url: "../../control/voter/voteController.php",
        type: "POST",
        dataType: "json",
        data: {
            action:"getElectionFormDetails"
        },
        success: function(response){

            console.log(response);

            if(response.status == "completed"){
                renderTitle("Theres no ongoing Election");
                return;
            } else if(response.status == "upcoming"){
                renderTitle("Election hasnt started yet");
                return;
            } else if(response.status == "already_voted"){
                renderElectionTitleAndYear("no u alr voted nigga", "", "");
                return;
            }

            renderElectionTitleAndYear(response.election_title, response.election_year, response.election_id);
            renderCandidatesGoatedly(response.positions);
            renderSubmitButton();
            initializePositions(response.positions);
        },
        error: function(response){  
            console.log("ERR");
            console.log(response);
        }
    });
}

function initializePositions(positions){
    positions.forEach((position) => {
        POSITIONS.push(position.position_id);
    });
};

function renderElectionTitleAndYear(title, year, election_id){
    $("#election_title")
        .text(title)
        .attr("data-election-id", election_id)
    $("#election_year").text(year);
}

function renderCandidatesGoatedly(positions){

    positions.forEach((position) => {

        let ABSTAIN_EXISTS = false;

        // main container for positions division shi
        let position_container = $("<div>")
            .addClass("position")
            .attr("id", position.position_name);
        
        // position title and max of it
        let header = position.position_name + ` (${position.max_votes} max)`;
        let position_header = $("<p>")
            .addClass("position_header")
            .text(header);
        position_container.append(position_header);

        // party_container per parties under that position
        let parties = position.political_parties;

        let PARTIES_COUNT = parties.length;
        let parties_container = $("<div>")
            .addClass("parties_container")
            .css({
                display: "grid",
                gridTemplateColumns: `repeat(${PARTIES_COUNT}, 1fr)` // css styling to make it go brr brr (divide by hm parties there are)
            });
        
        
        parties.forEach((party) => {

            // party container
            let party_container = $("<div>")
                .addClass("party");
        
            // party header
            let party_header = $("<p>")
                .text(party.party_name);
            party_container.append(party_header);

            // party candidates under that partylist
            let candidates = party.candidates;
            candidates.forEach((candidate) => {
                // console.log(candidate); // output tests

                // candidate checkbox container
                let candidate_container = $("<div>")
                    .addClass("candidate_input");
                let candidate_checkbox = $("<input>")
                    .addClass(position.position_name)
                    .attr("type", "checkbox")
                    .attr("id", candidate.candidate_id)
                    .attr("data-position-id", position.position_id)
                    .attr("data-max-votes", position.max_votes);
                let candidate_name = `${candidate.first_name} ${candidate.middle_name} ${candidate.last_name}`; 
                let candidate_label = $("<label>")
                    .attr("for", candidate.candidate_id)
                    .text(candidate_name);
                candidate_container.append(candidate_checkbox);
                candidate_container.append(candidate_label);
                party_container.append(candidate_container);
            });

            // abstain checkbox container
            if(!ABSTAIN_EXISTS){
                let abstain_container = $("<div>")
                    .addClass("abstain_input");
                let abstain_checkbox = $("<input>")
                    .addClass(position.position_name)
                    .attr("type", "checkbox")
                    .attr("id", 0)
                    .attr("data-position-id", position.position_id)
                    .attr("data-max-votes", position.max_votes);
                let abstain_label = $("<label>")
                    .text("Abstain");
                abstain_container.append(abstain_checkbox);
                abstain_container.append(abstain_label);
                party_container.append(abstain_container);
                ABSTAIN_EXISTS = true;
            }

            // append to a main parties container for css 
            parties_container.append(party_container);
            position_container.append(parties_container);
        });
        
        $("#main_content").append(position_container);
    });
}

function renderSubmitButton(){
    let submit_button = $("<button>")
        .attr("id", "submit_button")
        .text("Submit Button");
    
    $("#main_content").append(submit_button);
}


// let MAX = 1;

// $(document).on("change", "input", function(){
//     console.log($(".President:checked").length);
//     if($("input:checked").length > 1){
//         $(this).prop("checked", false);
//     }
// })


$(document).on("change", "input[type='checkbox']", maxVoteChecker);
function maxVoteChecker(){
    const position_id = $(this).data("position-id");
    const max = parseInt($(this).data("max-votes"));
    const is_abstain = $(this).attr("id") == 0;

    const group = $(`input[data-position-id='${position_id}']`);
    const candidates = group.filter(function() { return $(this).attr("id") != 0; });
    const abstain = group.filter(function() { return $(this).attr("id") == 0; });

    if (is_abstain) {
        if (abstain.is(":checked")) {
            // uncheck and disable all candidates
            candidates.prop("checked", false).prop("disabled", true);
        } else {
            // re-enable candidates when abstain is unchecked
            candidates.prop("disabled", false);
        }
        return;
    }

    // if a candidate is checked, uncheck and disable abstain
    const checked_count = candidates.filter(":checked").length;

    if (checked_count > max) {
        $(this).prop("checked", false);
        return;
    }

    abstain.prop("checked", false).prop("disabled", checked_count > 0);
    candidates.not(":checked").prop("disabled", checked_count >= max);
    candidates.filter(":checked").prop("disabled", false);
}


$(document).on("click", "#submit_button", submitFormAnswers);
function submitFormAnswers(){
    
    // stopper if min votes ain reacheffd
    const incomplete = minimumVoteChecker();
    if (incomplete.length > 0) {
        alert(`Please vote or abstain for: ${incomplete.join(", ")}`);
        return;
    }

    if(!confirm("Are you sure about your selection?")) return;
    disableSubmitButton();

    let index_of_position = 0;
    let election_id = $("#election_title").data("election-id");
    let vote_json = {
        election_id: election_id,
        positions: {}
    };

    POSITIONS.forEach((position) => {

        let chosen_candidate = $(`input[data-position-id='${position}']:checked`).map(function(){
            return $(this).attr("id");
        }).get();

        vote_json.positions[index_of_position] = {
            position_id: position,
            candidates: chosen_candidate
        };

        index_of_position++;
        
    });

    console.log(vote_json);

    $.ajax({
        url: "../../control/voter/voteController.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "submitFormAnswers",
            vote_json: JSON.stringify(vote_json)
        },
        success: function(response){
            alert("Successfully Submitted Vote");
            setInterval(() => {
                window.location.href = "./dashboard.php";
            }, 2000);
        },
        error: function(response){
            alert("Something went wrong");
            console.log(response);
        }

    });
}

// checker if minimum were reached when submitting 
function minimumVoteChecker() {
    let unvoted_positions = [];

    $(".position").each(function () {
        const position_id = $(this).find("input[type='checkbox']").first().data("position-id");
        const group = $(`input[data-position-id='${position_id}']`);
        const has_candidate = group.filter(function () { return $(this).attr("id") != 0; }).filter(":checked").length > 0;
        const has_abstain = group.filter(function () { return $(this).attr("id") == 0; }).is(":checked");

        if (!has_candidate && !has_abstain) {
            unvoted_positions.push($(this).attr("id")); // position_name is the id
        }
    });

    return unvoted_positions; // empty = all positions answered
}

function disableSubmitButton(){
    $("#submit_button")
        .prop("disabled", true);
}