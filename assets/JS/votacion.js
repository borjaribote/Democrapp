document.addEventListener("DOMContentLoaded", function () {


    document.querySelectorAll("#puntuacion-din .token-3").forEach(token3 => {
        token3.addEventListener("click", function () {
            manageTokens(this,"token-3");               
        }); 
    });
    document.querySelectorAll("#puntuacion-din .token-2").forEach(token2 => {
        token2.addEventListener("click", function () {
            manageTokens(this,"token-2");
        }); 
    });
    document.querySelectorAll("#puntuacion-din .token-1").forEach(token1 => {
        token1.addEventListener("click", function () {
            manageTokens(this,"token-1");
        }); 
    });

 
    
});

function manageTokens(element, token) {
    var staticPointToken = document.querySelector(`#puntuacion-static .${token}`);
    var topicId = element.closest("#puntuacion-din").getAttribute('data-topicid');
    var input = document.querySelector(`input[name="${token}"]`);
    if (checkIdValueOnImputs(topicId)) {//El topic seleccionado tiene algún token?
        //Si 
            if (input.value === topicId){//El id del token y el topic es el mismo? 
                //Sería Eliminar el token seleccionado 
                // Delete
                input.value = "";
                staticPointToken.classList.add("selected");
                element.classList.remove("selected");     
            }else{//ERROR
                //No se pueden dar dos valores distintos al mismo topic
                if (!document.getElementById("warningMessage")) {
                    displayError();
                }
            }
        
        //no
    }else if (input.value != "") {//Está marcado el token que queremos meter?
        //Existe (ese valor del token está dado a otra topic)
        //Update (borrar el otro token y dárselo a este)
        document.querySelector(`#puntuacion-din .${token}_id-${input.value}`).classList.remove("selected");
        element.classList.add("selected");
        input.value = topicId;
    }else{
        staticPointToken.classList.remove("selected");
        element.classList.add("selected");
        input.value = topicId;
    }
    if (allImputsFilled()){
        document.getElementById("submitVote").removeAttribute("disabled");
    }else{
        document.getElementById("submitVote").setAttribute("disabled", "true");
    }
}

function checkIdValueOnImputs(topicId) {
    
    let input_token3 = document.querySelector('input[name="token-3"]').value;
    let input_token2 = document.querySelector('input[name="token-2"]').value;
    let input_token1 = document.querySelector('input[name="token-1"]').value;
    if (input_token3 === topicId || input_token2 === topicId || input_token1 === topicId) {          
        return true;
    }else{  
        return false;
    }
}
function displayError(){
    const warningMessage = document.createElement("div");
            warningMessage.id = "warningMessage";
            warningMessage.textContent = "No se puede dar dos votos al mismo tema";
            warningMessage.classList.add("alert", "alert-danger", "text-center", "mt-3");
            document.getElementById('mainContent').appendChild(warningMessage);

            setTimeout(() => {
                warningMessage.style.transition = "opacity 1s";
                warningMessage.style.opacity = "0";
                setTimeout(() => {
                    warningMessage.remove();
                }, 1000);
            }, 3000);
}



function allImputsFilled(){
    let input_token3 = document.querySelector('input[name="token-3"]').value;
    let input_token2 = document.querySelector('input[name="token-2"]').value;
    let input_token1 = document.querySelector('input[name="token-1"]').value;
    if (input_token3 != "" && input_token2 != "" && input_token1 != "") {          
        return true;
    }else{  
        return false;
    }
}