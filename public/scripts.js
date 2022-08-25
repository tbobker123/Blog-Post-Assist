function url_domain(data) {
    let  a  = document.createElement('a');
    a.href = data;
    return a.hostname;
}
function arrayToLists(arr){
    try{
        let elm = "<ul>";
        arr.forEach( item => {
            elm += `<li>${item}</li>`;
        });
        elm += "</ul>";
        return elm;
    } catch(e) {
        return "";
    } 
}

function loadGoogleCountries(){
    fetch(`/google-countries.json`)
    .then((response) => response.json())
    .then((data) => {
        $("#search-locations-select").empty();
        data.map(item => {
            $("#search-locations-select").append(`<option value="${item.country_code}">${item.country_name}</option>`);
        });
    })
}


function openCity(evt, cityName) 
{
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
    evt.className += " active";
    //console.log(evt);
}

tinymce.init({
    selector: '#blog-post',
    plugins: 'advlist wordcount autolink lists link image charmap preview anchor pagebreak',
    toolbar_mode: 'floating',
    menubar: true,
    setup: function (editor) {
        editor.on('init', function (e) {
            editor.setContent(localStorage.getItem("blogContent"));
            mySave();
        });
    },
    height: 700
});

function mySave() {
    console.log("Saved");
    localStorage.setItem("blogContent", tinyMCE.activeEditor.getContent({format : 'raw'}));
    $("#saved").html(`Saved at ${new Date().toLocaleString().replace(',','')}`);
    $("#export-post").text(tinymce.activeEditor.getContent());
}

function copyDivToClipboard() {
    var range = document.createRange();
    range.selectNode(document.getElementById("export-post"));
    window.getSelection().removeAllRanges(); // clear current selection
    window.getSelection().addRange(range); // to select text
    document.execCommand("copy");
    window.getSelection().removeAllRanges();// to deselect
}

function clearSaved(){
    localStorage.setItem("blogContent", "");
    tinyMCE.activeEditor.setContent(localStorage.getItem("blogContent"));
}


$(document).ready(function() {

    // When the copy button is clicked, select the value of the text box, attempt
    // to execute the copy command, and trigger event to update tooltip message
    // to indicate whether the text was successfully copied.
    $('#copy-button').bind('click', function() {
      var input = document.querySelector('#export-post');
      //input.setSelectionRange(0, input.value.length + 1);
      try {
        var success = document.execCommand('copy');
        if (success) {
          $('#copy-button').trigger('copied', ['Copied!']);
        } else {
          $('#copy-button').trigger('copied', ['Copy with Ctrl-c']);
        }
      } catch (err) {
        $('#copy-button').trigger('copied', ['Copy with Ctrl-c']);
      }
    });
  
    // Handler for updating the tooltip message.
    $('#copy-button').bind('copied', function(event, message) {
      $(this).attr('title', message)
          .tooltip('fixTitle')
          .tooltip('show')
          .attr('title', "Copy to Clipboard")
          .tooltip('fixTitle');
    });

    
    $(".openai-button").click(function() {
        
        const ID = $(this).attr("id");
    
        const endpoint = ID.split("-")[2];
    
        const resultArea = document.getElementById(ID + "-textarea");
    
        $(resultArea).html("loading....");
    
        fetch('/api/' + endpoint, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({prompt: $("#prompt").val()})
        })
        .then((res) => { return res.json(); })
        .then(data => {
            $(resultArea).html(data.result);
        });
    });

    /**
     * Fetch location data from JSON file
     */
     /*let typingTimer;
      $("#search-locations").keyup(function(){
        clearTimeout(typingTimer);
        if ($('#search-locations').val()) {
            typingTimer = setTimeout(function(){
                const search_value = $('#search-locations').val().replace(" ", "+");
                fetch(`http://localhost:8080/api/locations?q=${search_value}`)
                .then((response) => response.json())
                .then((data) => {
                    console.log(data);
                    $("#search-locations-select").empty();
                    data.map(item => {
                        $("#search-locations-select").append(`<option value="${item.id}">${item.canonical_name}</option>`);
                    });
                });
            }, 1000);
        }
    });*/

    loadGoogleCountries();
    
    

    $("#searchbtn").on('click', (e) => {

        $("#results").empty();
        $("#recommended-word-length").empty();
        $("#related-questions").empty();
        $("#loading").html("loading...");

        const postData = {query: $("#searchterm").val(), location: $("#search-locations-select").val()};    
        const fetchPromise = fetch("/api/search", {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(postData)
        });
        fetchPromise.then(response => {
            //console.log(response);
            return response.json();
        }).then(data => {
            parseResults(data);
            $("#loading").html("");
        });
        
    });

    $("#save-search").on("click", function(e){

    })
    
    function parseResults(result){
        localStorage.setItem("serpresults", JSON.stringify(result));
        console.log(result);

        const results = result;
        $("#recommended-word-length").html(`<span class="h2">Recommended Post length ${Math.round(results.wordcount)}</span>`).fadeIn();
        for(let c=0;c<results.results.length;c++){
            let item = results.results[c];
            $("#results").append(`
            <tr>
                <td scope="row">${item.title}</td>
                <td><a href="${item.link}" target="_blank"> ${url_domain(item.link)}</a></td>
                <td>${item.snippet}</td>
                <td>${item.wordcount}</td>
                <td>
                    <div>
                        <div><strong>h1:</strong> ${arrayToLists(item.headings.h1)}</div>
                        <div><strong>h2:</strong> ${arrayToLists(item.headings.h2)}</div>            
                    </div>
                </td>
            </tr>
            `);
        }

        if(results.relatedquestions[0] == "No related questions"){
            $("#related-questions").append(`
            <tr>
                <td colspan="3">No related questions</td>
            </tr>
        `);
        } else {
            for(let d=0;d<results.relatedquestions.length;d++){
                let question = results.relatedquestions[d];
                $("#related-questions").append(`
                    <tr>
                        <td scope="row">${question.question}</td>
                        <td><a href="${question.link}" target="_blank"> ${url_domain(question.link)}</a></td>
                        <td>${question.title}</td>
                    </tr>
                `);
             }  
        }

    }
    
    $("#login-submit").on("click", function(){
        
        const login_username = $("#username").val();
        const login_password = $("#password").val();
    
        const data = JSON.stringify({
            username: login_username, 
            password: login_password
        });
    
        fetch("/login", { 
            method:"POST", 
            headers: {"Content-Type": "application/json"},
            body: data
        })
        .then((res) => { return res.text(); })
        .then((txt) => {
          if(txt == "true"){
            window.location.href = "/dashboard";
          } else {
            $("#warning").text(txt);
          }
        })
        .catch((err) => {
          alert("Server error - " + err.message);
          console.error(err);
        });
    
        return false;
    });

    openCity(document.getElementsByClassName("firstload")[0], 'serp-analysis');

  });