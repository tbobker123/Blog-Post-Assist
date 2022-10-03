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
            let selected = "";
            if(item.country_name == "United Kingdom") selected = "selected"
            $("#search-locations-select").append(`<option value="${item.country_code}" ${selected}>${item.country_name}</option>`);
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
    init_instance_callback: function (editor) {
        editor.on('keyUp', function (e) {
          compareTextWithExtractedKeywords(editor.getContent({format: 'text'}));
        });
    },
    height: 700
});

function compareTextWithExtractedKeywords(text){
    let matched = [];
    let notMatched = [];
    const inputText = text.toLowerCase();
    const extractedKeywords = $(".extracted-keywords div");

    /**
     * Loop through all extract keywords
     */
    extractedKeywords.each(function(i) {

        /**
         * start matching each keyword to the text
         */
        let keyword = $(this).text().trim();

        /**
         * If the keyword is found in the main text
         */
        if(inputText.indexOf(keyword.toLowerCase()) > -1){

            /**
             * check if is not already tracked and add to 
             * the matched array
             */
            if(matched.indexOf(keyword) == -1){
                matched.push(extractedKeywords[i]);
            }

        } else {
            /**
             * Otherwise the keyword does not match the text
             * so we can make sure it remains color black.
             */
            notMatched.push(extractedKeywords[i]);
        }
    });

    matched.forEach((m) => {
        m.style.color = '#08bd26';
    });
    notMatched.forEach((n) => {
        n.style.color = 'black';
    });


}

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

    $(".hide-until-results").hide();

    function fetchSERPUsageANDSavedReports(){

        fetch('/api/serp', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }})
        .then((res) => { return res.json(); })
        .then(data => {
            $("#serpapi-account-info").html(
                `<div class="fw-bold display-7">Current Plan: ${data.plan}, 
                Plan Searches:${data.searches}, 
                Plan Remining:${data.remaining}, 
                Total Remaining: ${data.total_remaining},
                Plan Usage:${data.usage}</div>`
            );
        });

        $("#saved-reports").empty();
        $("#saved-reports").append(`<option selected>select report</option>`);

        fetch('/api/reports', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }})
            .then((res) => { return res.json(); })
            .then(data => {
                data.map(d => {
                    let query = d.query.replace(/\+/g, ' ')
                    $("#saved-reports").append(
                        `<option value="${d.id}">${query} (${d.wordcount})</option>`
                    );
                })
            });
    }
    fetchSERPUsageANDSavedReports();



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

    $("#generate-blog-post-button").click(function() {
        
        const ID = $("#generate-content-type").val();

        if(ID == "select"){
            alert("Select a type");
            return;
        }
    
        const endpoint = ID.split("-")[2];
    
        const resultArea = $("#generated-content-area");
    
        $("#generate-blog-post-button").html("loading....");
    
        fetch('/api/generator', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({prompt: $("#generate-content-description").val(), type: endpoint})
        })
        .then((res) => { return res.json(); })
        .then(data => {
            $(resultArea).html(data.result);
            $("#generate-blog-post-button").html("Generate");
        });
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

    loadGoogleCountries();
    
    $("#loadreport").on('click', (e) => {
        const query = $("#saved-reports option:selected").text().split("(")[0].trim();
        $("#searchterm").val(query);
        $("#searchbtn").click();
    });

    $("#deletereport").on('click', e => {
        const id = $("#saved-reports option:selected").val();
        fetch('/api/deletereport', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({reportid: id})
            })
            .then((res) => { return res.json(); })
            .then(data => {
                alert(data.status.toString());
            });
    })

    $("#searchbtn").on('click', (e) => {

        $(".hide-until-results").hide();

        $("#results").empty();
        $("#recommended-word-length").empty();
        $("#related-questions").empty();
        $(".extracted-keywords").empty();
        $("#top-title").empty();
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
            fetchSERPUsageANDSavedReports();
            $(".hide-until-results").show();
            $("#loading").empty();
        });
        
    });

    function parseResults(result){
        localStorage.setItem("serpresults", JSON.stringify(result));
        console.log(result);

        const results = result;
        let titles = [];
        $("#recommended-word-length").html(`<span class="h2">Recommended Post length ${Math.round(results.wordcount)}</span>`).fadeIn();
        for(let c=0;c<results.results.length;c++)
        {
            let item = results.results[c];
            if(item.wordcount > (results.wordcount * 1.2)){
                
                $("#top-title").append(`
                    <tr>
                    <td scope="row">${item.position}</td>
                    <td><a target="_blank" href="${item.link}" target="_blank">${item.title}</a> </td>
                    <td>${item.wordcount}</td>
                    </tr> 
                `);
            }

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

        for(let kw=0;kw<results.keywords.length;kw++){
            let keyword = results.keywords[kw];
            $(".extracted-keywords").append(`
                <div class="d-inline col-md-3 h5 p-2 m-2">${keyword.keyword}</div>
            `);
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