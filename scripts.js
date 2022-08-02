
$("#searchbtn").on('click', (e) => {
    $("#results").empty();
    $("#loading").html("loading...");
    fetch('/search', {
        method: 'POST',
        headers: {
            'Accept': 'application/json, text/plain, */*',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({term: $("#searchterm").val()})
    });
});

$("#generatebtn").on('click', (e) => {
    $("#blogcontent").html("loading...");

    fetch('/generate', {
    method: 'POST',
    headers: {
        'Accept': 'application/json, text/plain, */*',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({prompt: $("#prompt").val()})
    });

})

const socket = io();

socket.on("generated", function(message) { 
    const blog = message.split(/\r?\n/);
    $("#blogcontent").html(blog.join('<br>'));
});

socket.on("results", function(message) { 
    const results = JSON.parse(message);
    $("#loading").html(`<h2>Recommended Post length ${results.wordcount}</h2>`);
    for(let c=0;c<results.results.length;c++){
        let item = results.results[c];
        $("#results").append(`
        <tr>
            <th scope="row">${item.title}</th>
            <td><a href="${item.url}" target="_blank"> ${url_domain(item.url)}</a></td>
            <td>${item.description}</td>
            <td>${item.words}</td>
            <td>
                <div>
                    <div><strong>h1:</strong> ${arrayToLists(item.h1)}</div>
                    <div><strong>h2:</strong> ${arrayToLists(item.h2)}</div>            
                </div>
            </td>
        </tr>
        `)
    }  
});

function url_domain(data) {
    let  a  = document.createElement('a');
    a.href = data;
    return a.hostname;
}
function arrayToLists(arr){
    let elm = "<ul>";
    arr.forEach( item => {
        elm += `<li>${item}</li>`;
    });
    elm += "</ul>";
    return elm;
}

function arrayToList(arr){
    let elm = $("ul");
    arr.forEach( item => {
        $(elm).append(`<li>${item}</li>`);
    })
    return elm;
}

function openCity(evt, cityName) {
var i, tabcontent, tablinks;
tabcontent = document.getElementsByClassName("tabcontent");
for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
}
tablinks = document.getElementsByClassName("tablinks");
for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
}
document.getElementById(cityName).style.display = "block";
evt.currentTarget.className += " active";
}