$(".items").hover(function(){
    $(this).css("color", "red");
});

$(".items").mouseout(function(){
    $(this).css("color", "black");
});

$("#signout_btn").click(function(){
    
    if(!confirm("Are you sure you want to sign out?")) return;

    $.ajax({
        url: "../../control/authorizationControl.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "signOut",
        },
        success: function(response){
            alert("Successfully signed out");
            console.log(response);
            console.log("logged off");
            window.location.href = "../../index.html";
        },
        error: function(response){
            alert("An error has occured");
        }
    });
});

$("#start_election").click(function(){
    console.log("clicked");
    alert("YOURE AB TO dieeee");
    window.location.href = "https://ddlc.moe/";
});

//Charts and Tables
$(document).ready(function(){
 
    $("#no_election_section").hide();
    $("#ongoing_election_section").hide();
    $("#closed_election_section").hide();
 
    loadResults();
 
    function loadResults(){
        $.ajax({
            url: "../../control/resultsControl.php",
            method: "GET",
            dataType: "json",
            success: function(response){
                if(!response.success){
                    console.error("Error fetching results:", response.message);
                    alert("Error loading results: " + response.message);
                    return;
                }
 
                // Backend handles all logic — JS only reads and renders
                const data = response.data;
                const status = data.election_status;   // "upcoming" | "active" | "completed"
                const username = data.username;
                const election_title = data.election_title;
                const positions = data.positions;
 
                $("#greeting").text("Good Day, " + username);
 
                if(status === "upcoming"){
                    renderNoElection();
                } else if(status === "active"){
                    renderOngoing(election_title, positions);
                } else if(status === "completed"){
                    renderClosed(election_title, positions);
                }
            },
            error: function(xhr, status, error){
                console.error("AJAX error:", error);
                alert("Failed to load results. Please try again.");
            }
        });
    }
    // Render: No election 
    function renderNoElection(){
        $("#no_election_section").show();
    }
 
    //Render: Ongoing election (bar charts only) 
    function renderOngoing(election_title, positions){
        $("#ongoing_election_title").text(election_title);
        $("#ongoing_election_section").show();
 
        const container = document.getElementById("ongoing_charts_container");
        container.innerHTML = "";
 
        for(let i in positions){
            const pos_name = positions[i].position_name;
            const candidates = positions[i].candidates;
            const canvas_id  = "canvas_bar_ongoing_" + i;
 
            let heading = document.createElement("h2");
            heading.innerHTML = pos_name + " Ranking";
            container.appendChild(heading);
 
            let canvas = document.createElement("canvas");
            canvas.id = canvas_id;
            container.appendChild(canvas);
 
            let labels = [];
            let votes  = [];
            for(let j in candidates){
                labels.push(candidates[j].candidate_name);
                votes.push(parseInt(candidates[j].vote_count));
            }
            renderBarChart(canvas_id, labels, votes, pos_name);
        }
    }
    //Render: Closed election (winners + pie + bar)
    function renderClosed(election_title, positions){
        $("#closed_election_title").text(election_title + " — Results");
        $("#closed_election_section").show();
 
        const winners_container = document.getElementById("winners_container");
        const charts_container  = document.getElementById("closed_charts_container");
 
        winners_container.innerHTML = "";
        charts_container.innerHTML  = "";
 
        for(let i in positions){
            const pos_name   = positions[i].position_name;
            const candidates = positions[i].candidates;
 
            // Guard: skip if no candidates
            if(!candidates || candidates.length === 0) continue;
 
            // Winner card (backend already sorts DESC by vote_count)
            const winner         = candidates[0];
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
 
            // Position result block (pie + bar)
            const pie_id = "canvas_pie_" + i;
            const bar_id = "canvas_bar_closed_" + i;
 
            let block = document.createElement("div");
            block.className = "position_result_block";
 
            let pie_heading = document.createElement("h2");
            pie_heading.innerHTML = pos_name + " Vote Share";
            block.appendChild(pie_heading);
 
            let pie_canvas = document.createElement("canvas");
            pie_canvas.id = pie_id;
            block.appendChild(pie_canvas);
 
            let bar_heading = document.createElement("h2");
            bar_heading.innerHTML = pos_name + " Vote Ranking";
            block.appendChild(bar_heading);
 
            let bar_canvas = document.createElement("canvas");
            bar_canvas.id = bar_id;
            block.appendChild(bar_canvas);
 
            charts_container.appendChild(block);
 
            // Backend already calculates percentages — JS just reads them
            let labels      = [];
            let votes       = [];
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

    //Chart factory: Bar 
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
 
    //Chart factory: Pie 
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
    
    //Color generator 
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
