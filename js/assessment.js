const form = document.getElementById("assessmentForm");

form.addEventListener("submit", function(e){

    const questions = document.querySelectorAll(".question");

    let unanswered = [];

    questions.forEach((q,index)=>{

        const selected = q.querySelector("input:checked");

        if(!selected){
            unanswered.push(index+1);
        }

    });

    if(unanswered.length>0){

        e.preventDefault();

        alert(
            "Please answer all questions.\n\nMissing Questions: "
            + unanswered.join(", ")
        );
    }

});