async function loadLeaderboard() {

    const quizId =
        document.getElementById('quiz-filter').value;

    let url =
        'api/leaderboard/index.php';

    if(quizId) {

        url += '?quiz_id=' + quizId;
    }

    const response =
        await fetch(url);

    const data =
        await response.json();

    const tbody =
        document.getElementById('leaderboard-body');

    tbody.innerHTML = '';

    data.forEach(function(row, index) {

        const tr =
            document.createElement('tr');

        tr.innerHTML =
            '<td>' + (index + 1) + '</td>' +
            '<td>' + row.name + '</td>' +
            '<td>' + row.title + '</td>' +
            '<td>' + row.score + '</td>' +
            '<td>' + row.total_marks + '</td>' +
            '<td>' + row.completed_at + '</td>';

        tbody.appendChild(tr);
    });
}

setInterval(loadLeaderboard, 5000);
