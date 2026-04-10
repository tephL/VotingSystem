//Charts and Tables
$(document).ready(function(){
    $("#upcoming_election_section").hide();
    $("#closed_election_section").hide();
 
    loadResults();
    setInterval(function(){
        loadResults();
    }, 5000);
 
    function loadResults(){
        $.ajax({
            url: "../../control/voter/VoterReport.php",
            method: "GET",
            dataType: "json",
            success: function(response){
                if(!response.success){
                    console.error("Error fetching results:", response.message);
                    alert("Error loading results: " + response.message);
                    return;
                }

                const data = response.data;
                const status = data.election_status;
                const username = data.username;
                const election_title = data.election_title;
                const positions = data.positions || [];

                $("#greeting").text("Good Day, " + username);

                $("#no_election_section").hide();
                $("#upcoming_election_section").hide();
                $("#closed_election_section").hide();

                if(status === "no_elections"){
                    renderNoElection();
                } else if(status === "upcoming"){
                    renderUpcoming(election_title, positions);
                } else if(status === "completed"){
                    renderClosed(election_title, positions);
                }
            },
            error: function(){ alert("Failed to load results. Please try again."); }
        });
    }

    function renderNoElection(){
        $("#no_election_section").show();
    }

    function renderUpcoming(election_title, positions){
        const title_container = document.getElementById("upcoming_election_title");
        
        title_container.innerHTML = "";
        let election_heading = document.createElement("h1");
        election_heading.textContent = election_title;
        title_container.appendChild(election_heading);
        
        $("#upcoming_election_section").show();

        const candidates_container = document.getElementById("upcoming_charts_container");
        candidates_container.innerHTML = "";

        for(let i in positions){
            const position = positions[i];
            const position_name = position.position_name;
            const max_votes = position.max_votes || 1;
            const political_parties = position.political_parties || [];

            let position_container = document.createElement("div");
            position_container.className = "position";
            position_container.id = position_name;

            let position_header = document.createElement("h2");
            position_header.className = "position_header";
            position_header.textContent = position_name + " (" + max_votes + " max)";
            position_container.appendChild(position_header);

            let parties_container = document.createElement("div");
            parties_container.className = "parties_container";
            parties_container.style.display = "grid";
            parties_container.style.gridTemplateColumns = "repeat(" + political_parties.length + ", 1fr)";

            for(let j in political_parties){
                const party = political_parties[j];
                const party_name = party.party_name;
                const candidates = party.candidates || [];

                let party_box = document.createElement("div");
                party_box.className = "party_box";

                let party_heading = document.createElement("h3");
                party_heading.className = "party_heading";
                party_heading.textContent = party_name;
                party_box.appendChild(party_heading);

                let candidates_list = document.createElement("ul");
                candidates_list.className = "candidates_list";

                for(let k in candidates){
                    const candidate = candidates[k];
                    const full_name = candidate.first_name + " " + (candidate.middle_name ? candidate.middle_name + " " : "") + candidate.last_name;
                    
                    let candidate_item = document.createElement("li");
                    candidate_item.textContent = full_name;
                    candidates_list.appendChild(candidate_item);
                }

                party_box.appendChild(candidates_list);
                parties_container.appendChild(party_box);
            }

            position_container.appendChild(parties_container);
            candidates_container.appendChild(position_container);
        }
    }

    function renderClosed(election_title, positions){
        $("#closed_election_title").text(election_title + " — Results");
        $("#closed_election_section").show();

        const winners_container = document.getElementById("winners_container");
        const charts_container = document.getElementById("closed_charts_container");

        winners_container.innerHTML = "";
        charts_container.innerHTML  = "";

        for(let i in positions){
            const pos_name   = positions[i].position_name;
            const candidates = positions[i].candidates;
            if(!candidates || !candidates.length){
                continue;
            }

            // Winner Card
            const winner = candidates[0];
            const winner_card_id = "winner_card_" + i;
 
            let winner_card = document.createElement("div");
            winner_card.className = "winner_card";
            winner_card.id = winner_card_id;
 
            let card_position = document.createElement("h3");
            card_position.innerHTML = pos_name;
            winner_card.appendChild(card_position);
 
            let card_name = document.createElement("p");
            card_name.innerHTML = "<strong>" + winner.candidate_name + "</strong>";
            winner_card.appendChild(card_name);
 
            let card_votes = document.createElement("p");
            card_votes.innerHTML = "Votes: " + winner.vote_count;
            winner_card.appendChild(card_votes);
 
            let card_percentage = document.createElement("p");
            card_percentage.innerHTML = "Percentage: " + winner.percentage + "%";
            winner_card.appendChild(card_percentage);
 
            winners_container.appendChild(winner_card);

            // Charts Block
            const pie_id = "canvas_pie_" + i;
            const bar_id = "canvas_bar_closed_" + i;

            let block = document.createElement("div");
            block.className = `position_result_block ${candidates.length <= 2 ? "two-col" : "full-width"}`;

            // Pie Chart Section
            let pie_heading = document.createElement("h2");
            pie_heading.textContent = pos_name + " Vote Share";
            block.appendChild(pie_heading);

            let pieWrapper = document.createElement("div");
            pieWrapper.className = "pie-wrapper";
            let pie_canvas = document.createElement("canvas");
            pie_canvas.id = pie_id;
            pieWrapper.appendChild(pie_canvas);
            block.appendChild(pieWrapper);

            // Bar Chart Section
            let bar_heading = document.createElement("h2");
            bar_heading.textContent = pos_name + " Vote Ranking";
            block.appendChild(bar_heading);

            let bar_canvas = document.createElement("canvas");
            bar_canvas.id = bar_id;
            block.appendChild(bar_canvas);

            charts_container.appendChild(block);

            let labels = [];
            let votes = [];
            let percentages = [];
            for(let j in candidates){
                labels.push(candidates[j].candidate_name);
                votes.push(parseInt(candidates[j].vote_count));
                percentages.push(candidates[j].percentage);
            }
 
            renderPieChart(pie_id, labels, percentages, pos_name);
            renderBarChart(bar_id, labels, votes, pos_name);
        }
    }

    function renderBarChart(canvas_id, labels, data, pos_name){
        const canvas = document.getElementById(canvas_id);
        if(!canvas) return;
 
        new Chart(canvas.getContext("2d"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: pos_name,
                    data: data,
                    backgroundColor: generateColors(data.length),
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    }

    function renderPieChart(canvas_id, labels, data, pos_name){
        const canvas = document.getElementById(canvas_id);
        if(!canvas) return;
 
        new Chart(canvas.getContext("2d"), {
            type: "pie",
            data: {
                labels: labels,
                datasets: [{
                    label: pos_name,
                    data: data,
                    backgroundColor: generateColors(data.length),
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    }

    function generateColors(count){
        const color_pool = [
            "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0",
            "#9966FF", "#FF9F40", "#C9CBCF", "#E7E9ED",
            "#71B37C", "#E55C5C"
        ];
        const colors = [];
        for(let i = 0; i < count; i++){
            colors.push(color_pool[i % color_pool.length]);
        }
        return colors;
    }
});
