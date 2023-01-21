function url_domain(data) 
{
    let  a  = document.createElement('a');
    a.href = data;
    return a.hostname;
}

function arrayToLists(arr)
{
    try{
        let elm = "<div>";
        let bg = "#ffffff";
        arr.forEach( item => {
            let str = item.split("-");
            let heading_margin;
            if(str[0].indexOf("h2") != -1){
                heading_margin = "3";
            } else if(str[0].indexOf("h3") != -1){
                heading_margin = "4";
            } else if(str[0].indexOf("h4") != -1){
                heading_margin = "5";
            } else if(str[0].indexOf("h1") != -1){
                heading_margin = "0";
            } else {
                heading_margin = "0";
            }
            if(bg == "#ffffff") bg = "#f5fbfb"; else bg = "#f5fbfb";
            elm += `<div class="ms-${heading_margin} p-2 d-flex border" style="font-size: 16px;background-color:${bg}">`+
            `<div class="text-primary fw-bold p-1">${str[0].toUpperCase().trim()}</div>`+
            `<div class="fw-bold p-1">${str[1]}</div>`+
            `</div>`;
        });
        elm += "</div>";
        return elm;
    } catch(e) {
        return "You just encountered an unexpected error.";
    } 
}



function loadGoogleCountries()
{
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



if(typeof tinymce != "undefined")
{
    tinymce.init({
        selector: '#blog-post',
        plugins: 'pagebreak code emoticons image table lists advlist link charmap directionality wordcount autolink charmap preview anchor pagebreak',
        //toolbar: 'pagebreak | formatselect fontselect fontsizeselect bold italic underline strikethrough forecolor backcolor subscript superscript | alignleft aligncenter alignright alignjustify indent outdent rtl ltr | bullist numlist checklist | emoticons image table link hr charmap',
        menubar: true,
        setup: function (editor) {
            editor.on('init', function (e) {
                editor.setContent(localStorage.getItem("blogContent"));
            });
        },
        init_instance_callback: function (editor) {
            editor.on('keyUp', function (e) {
              compareTextWithExtractedKeywords(editor.getContent({format: 'text'}));
            });
        },
        height: 700
    });
}


function compareTextWithExtractedKeywords(text)
{
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

function updateCSRFHash(hash)
{
    $("#csrf_token_name").val(hash);
}

function updateBlogPostId(id){
    if(id == 'updated'){
        return;
    } else {
        $("#save-blog-post-id").val(id);
    }
}

function fetchSERPUsageANDSavedReports(query_id='')
{
    fetch('/api/serp', {
    method: 'GET',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }})
    .then((res) => { 
       return res.json();
    })
    .then(data => {
        $("#serpapi-account-info").html(
            `<div class="fw-bold display-7">Current Plan: ${data.plan}, 
            Plan Searches:${data.searches}, 
            Plan Remining:${data.remaining}, 
            Total Remaining: ${data.total_remaining},
            Plan Usage:${data.usage}</div>`
        );
    });
}


function getReportProgress(){
    let request;

    const makeApiCall = () => {
        fetch('/api/progress', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }})
            .then((res) => { 
               return res.json();
            }).then(data => {
            console.log(data.progress);
            if(data.progress == "We are saving your report."){
                clearTimeout(request);
            }
        });
        request = setTimeout(makeApiCall(), 1000);
    }
}

function fetchSERPResults(query){

    getReportProgress();
                    
    $("#searchbtn").text("loading...");

    const query_id = query.query_id ?? null;

    fetch("/api/search", {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(query)
    }).then(response => {
        return response.json();
    }).then(data => {
        if(data.error){
            alert("An error was experienced. Please try again.");
        } else {

            const id = data.id;
            const wordcount = Math.round(data.wordcount);
            const keyword = data.keyword.replaceAll("+", " ");
    
            $("#list-reports-table").find('tbody').append(`<tr>`+
            `<td><a class="fs-5" href="/report?reportid=${id.toString()}">${keyword.toString()}</a></td>`+
            `<td>${wordcount.toString()}</td>`+
            `<td><a type="button" class="delete-report-btn d-inline input-group-text btn-danger" href="/delete?reportid=${id.toString()}">Delete</a>`+
            `<a type="button" class="editor-report-btn d-inline input-group-text btn-warning" href="/content-editor?reportid=${id.toString()}">Editor</a></td>`+
            `</tr>`);
        }
        $("#searchbtn").text("Search");
        updateCSRFHash(data.csrf_hash);
    }).catch((error) => {
        console.log("error: " + error);
        $("#searchbtn").text("Search");
      });;
}



$(document).ready(function() {

    /**
     * Page load: load saved reports
     * populate the countries dropdown 
     * and load SerpAPI usage
     */

    $(".hide-until-results").hide();
    fetchSERPUsageANDSavedReports();
    loadGoogleCountries();

    $("#searchbtn").on('click', (e) => {
        if($("#searchterm").val() !== ""){   
            const postData = {query: $.trim($("#searchterm").val()), location: $("#search-locations-select").val(), csrf_token_name: $("#csrf_token_name").val()};  
            fetchSERPResults(postData)
        } else {
            alert("Enter a search term");
        }
    });

    $(".delete-report-btn").on("click", function(event){
        if (confirm("Are you sure you want to delete this report?") == false) {
            event.preventDefault()
        }
    })

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
            body: JSON.stringify({prompt: $("#generate-content-description").val(), type: endpoint, csrf_token_name: $("#csrf_token_name").val()})
        })
        .then((res) => { return res.json(); })
        .then(data => {
            console.log(data);
            $(resultArea).html(data.result);
            $("#generate-blog-post-button").html("Generate");
            updateCSRFHash(data.csrf_hash);
        });
    });

    $("#load-blog-post-draft").on("click", function(){
        console.log(localStorage.getItem("blogs"));
        const post_id = $("#blog-post-drafts option:selected").val();
        const blogs = JSON.parse(localStorage.getItem("blogs"));
        blogs.map((item) => {
            if(item.id == post_id){
                tinyMCE.activeEditor.setContent(item.text, {format: 'raw'});
                $("#blog-post-title").val(item.title);
                $("#blog-post-id").val(item.id);
            }
        })
    }); 

    
    $("#delete-blog-post-draft").on("click", function(){

        const post_id = $("#blog-post-id").val();

        if(post_id == ''){
            alert("Select a blog draft");
            return;
        }

        fetch('/api/deleteblog', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({post_id: post_id, csrf_token_name: $("#csrf_token_name").val()})
        })
        .then((res) => { return res.json(); })
        .then(data => {
            updateBlogPostId(data.id);
            updateCSRFHash(data.csrf_hash);
            alert(`Blog post draft deleted ${data.status}`);
            $("#blog-post-drafts option:selected").remove();
            console.log(data); 
        });

    });

    $("#save-blog-post-draft").on("click", function(){

        const post_id =  $("#blog-post-id").val();
        const keyword_id = $("#saved-reports option:selected").val();
        const post_title = $("#blog-post-title").val();
        const post_body = tinyMCE.activeEditor.getContent();

        const http_post_body = {
            id: post_id,
            query_id: keyword_id,
            title: post_title,
            text: post_body,
            csrf_token_name: $("#csrf_token_name").val()
        }

        if(keyword_id == 'select report'){
            alert("Select a report before saving");
            return;
        }

        
        fetch('/api/saveblog', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(http_post_body)
        })
        .then((res) => { return res.json(); })
        .then(data => {
            updateBlogPostId(data.id);
            updateCSRFHash(data.csrf_hash);
            alert(data.status);
            console.log(data);
        });

    });



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

    $(".tablinks").on('click', function(){
        let tab = $(this).data('tab');
        topTabs(this, tab);
    })


  });