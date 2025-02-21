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
    var currentVoteId = element.getAttribute('data-voteid');
    let idSession = sessionStorage.getItem(token);

    if (compareSessionWithID(currentVoteId) && !idSession) {
        sessionStorage.setItem(token, currentVoteId);
        staticPointToken.classList.remove("selected");
        element.classList.add("selected");
    }  else if (idSession === currentVoteId) {
        
            sessionStorage.removeItem(token);
            staticPointToken.classList.add("selected");
            element.classList.remove("selected");
        
    }else if (idSession && !compareSessionValue()) {
        
        document.querySelector(`#puntuacion-din .${token}_id-${idSession}`).classList.remove("selected");
        element.classList.add("selected");
        sessionStorage.setItem(token, currentVoteId);
    
}else {
        alert("No se puede dar dos votos al mismo tema");
    }
}


function compareSessionWithID(currentId) {
    let idSession3 = sessionStorage.getItem("token-3") || "0";
    let idSession2 = sessionStorage.getItem("token-2") || "0";
    let idSession1 = sessionStorage.getItem("token-1") || "0";
    if (idSession3 === currentId || idSession2 === currentId || idSession1 === currentId) {          
        return false;
    }else{  
        return true;
    }
}
function compareSessionValue() {
    let idSession3 = sessionStorage.getItem("token-3") || "0";
    let idSession2 = sessionStorage.getItem("token-2") || "0";
    let idSession1 = sessionStorage.getItem("token-1") || "0";
    if(idSession3 === idSession2 || idSession2 === idSession1 || idSession1 === idSession3){
        return false; 
    }else{  
        return true;
    }
}

function clearSessionStorage() {
    sessionStorage.clear();
}
window.addEventListener("beforeunload", function () {
    sessionStorage.clear();
});
/* Comprobar si un valor existe */
function sessionValueExists(key) {
    return sessionStorage.getItem(key) !== null;
}