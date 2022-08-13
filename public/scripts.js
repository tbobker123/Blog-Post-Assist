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
    evt.currentTarget.className += " active";
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

    // Initialize the tooltip.
    $('#copy-button').tooltip();
  
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

    $("#searchbtn").on('click', (e) => {
        $("#results").empty();
        $("#loading").html("loading...");
    
        const fetchPromise = fetch("/api/search", {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({query: $("#searchterm").val()})
        });
        fetchPromise.then(response => {
            return response.json();
        }).then(data => {
            parseResults(data);
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
    
    function parseResults(result){
        console.log(result);
        const results = result;
        $("#recommended-word-length").html(`<h2>Recommended Post length ${Math.round(results.wordcount)}</h2>`);
        for(let c=0;c<results.results.length;c++){
            let item = results.results[c];
            $("#results").append(`
            <tr>
                <th scope="row">${item.title}</th>
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
            `)
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

  });