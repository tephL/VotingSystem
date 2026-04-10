function initializeVotesHistory(){
    $.ajax({
        url: "../../control/voter/historyController.php",
        type: "POST",
        dataType: "json",
        data: {
            action:"getMyVotingHistory"
        },
        success: function(response){
            console.log(response);

            if (response.success) {
                renderVotingHistory(response.data);
            } else {
                $("#main_content").html("<p>No voting history found.</p>");
            }
        },
        error: function(response){  
            console.log("ERR");
            console.log(response.responseText);
        }
    });
}


function renderVotingHistory(data) {
    const $main = $("#main_content").empty();

    data.forEach(election => {
        const $electionCard = $('<div>').addClass('election-card');

        // Election Title
        $('<h2>')
            .addClass('election-title')
            .text(election.election_title)
            .appendTo($electionCard);

        election.positions.forEach(position => {
            const $positionBlock = $('<div>').addClass('position-block');

            // Position Title
            $('<h3>')
                .addClass('position-title')
                .text(position.position_name)
                .appendTo($positionBlock);

            position.parties.forEach(party => {
                const $partyBlock = $('<div>').addClass('party-block');

                // Party Name
                $('<h4>')
                    .addClass('party-name')
                    .text(party.party_name)
                    .appendTo($partyBlock);

                // Candidates List
                const $candidateList = $('<ul>').addClass('candidate-list');

                if (party.candidates && party.candidates.length > 0) {
                    party.candidates.forEach(candidate => {
                        const $li = $('<li>');

                        $('<strong>')
                            .text(candidate.candidate_name)
                            .appendTo($li);

                        $('<span>')
                            .addClass('vote-date')
                            .text(` (${candidate.vote_date})`)
                            .appendTo($li);

                        $candidateList.append($li);
                    });
                } else {
                    $('<li>').text('No candidates').appendTo($candidateList);
                }

                $partyBlock.append($candidateList);
                $positionBlock.append($partyBlock);
            });

            $electionCard.append($positionBlock);
        });

        $main.append($electionCard);
        $main.append('<hr>');
    });
}

initializeVotesHistory();