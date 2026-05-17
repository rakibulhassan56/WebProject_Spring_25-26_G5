const timer =
    document.getElementById('timer');

const quizForm =
    document.getElementById('quiz-form');

let secondsLeft =
    Number(quizForm.dataset.timeLimit) * 60;

function updateTimer() {

    const minutes =
        Math.floor(secondsLeft / 60);

    const seconds =
        secondsLeft % 60;

    timer.textContent =
        minutes + ':' + String(seconds).padStart(2, '0');

    if(secondsLeft <= 0) {

        quizForm.submit();

        return;
    }

    secondsLeft--;
}

updateTimer();

setInterval(updateTimer, 1000);
