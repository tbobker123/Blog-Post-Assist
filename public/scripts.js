function url_domain(data) 
{
    let  a  = document.createElement('a');
    a.href = data;
    return a.hostname;
}
function arrayToLists(arr)
{
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


function topTabs(evt, cityName) 
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


function copyDivToClipboard() 
{
    var range = document.createRange();
    range.selectNode(document.getElementById("export-post"));
    window.getSelection().removeAllRanges(); // clear current selection
    window.getSelection().addRange(range); // to select text
    document.execCommand("copy");
    window.getSelection().removeAllRanges();// to deselect
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

    $("#saved-reports").empty();
    $("#saved-reports").append(`<option>select SERP report</option>`);

    fetch('/api/reports', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }})
        .then((res) => { 
            return res.json(); 
        })
        .then(data => {
            data.map(d => {
                let query = d.query.replace(/\+/g, ' ');
                let isSelected = (d.id == query_id) ? 'selected' : '';
                $("#saved-reports").append(
                    `<option value="${d.id}" ${isSelected}>${query} (${d.wordcount})</option>`
                );
            })
        });
}


function parseResults(result)
{
    const results = result;

    /**
     * Store blog posts drafts in browser storage
     */
     const blogposts = results.blogposts; console.log(blogposts);
     localStorage.setItem('blogs', JSON.stringify(blogposts));

    let titles = [];

    $("#recommended-word-length").html(`<span class="h2">Recommended Post length ${Math.round(results.wordcount)}</span>`).fadeIn();

    /**
     * Show SERP results and top results
     */
    for(let c=0;c<results.results.length;c++)
    {
        let item = results.results[c];
        if(item.wordcount > (results.wordcount * 1.2)){
            
            $("#top-title").append(`<tr>`+
                `<td scope="row">${item.position}</td>`+
                `<td><a target="_blank" href="${item.link}" target="_blank">${item.title}</a> </td>`+
                `<td>${item.wordcount}</td>`+
                `</tr>`);
        }

        $("#results").append(`<tr>`+
        `<td scope="row">${item.title}</td>`+
        `<td><a href="${item.link}" target="_blank">${url_domain(item.link)}</a></td>`+
        `<td>${item.snippet}</td><td>${item.wordcount}</td>`+
        `<td><div><div><strong>h1:</strong> ${arrayToLists(item.headings.h1)}</div><div><strong>h2:</strong> ${arrayToLists(item.headings.h2)}</div></div></td>`+
        `</tr>`);
        
    }

    /**
     * Show related questions
     */
    if(results.relatedquestions[0] == "No related questions"){
        $("#related-questions").append(`<tr><td colspan="3">No related questions</td></tr>`);
    } else {
        for(let d=0;d<results.relatedquestions.length;d++){
            let question = results.relatedquestions[d];
            $("#related-questions").append(`<tr>`+
                `<td scope="row">${question.question}</td>`+
                `<td><a href="${question.link}" target="_blank"> ${url_domain(question.link)}</a></td>`+
                `<td>${question.title}</td>`+
            `</tr>`);
        }  
    }

    /**
     * Print out the related keyword words/phrases
     */
    for(let kw=0;kw<results.keywords.length;kw++){
        let keyword = results.keywords[kw];
        $(".extracted-keywords").append(`
            <div class="h5 float-left d-inline ps-1 pe-1 m-1"><u>${keyword.keyword}</u></div>
        `);
    }

    /**
     * Populate Blog post drafts
     */
    $("#blog-post-drafts").empty();
    if(Array.isArray(blogposts)){
        $("#blog-post-drafts-option").text("---Select blog post draft ---");
        blogposts.map(d => {
            $("#blog-post-drafts").append(
                `<option value="${d.id ?? ''}">${d.title ?? 'No title found for draft'}</option>`
            );
        })
    } else {
        $("#blog-post-drafts").append(
            `<option value="">No drafts found for keyword</option>`
        );
    }


}

function fetchSERPResults(query){
                    
    $(".hide-until-results").hide();
    $("#results").empty();
    $("#recommended-word-length").empty();
    $("#related-questions").empty();
    $(".extracted-keywords").empty();
    $("#top-title").empty();
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
        parseResults(data);
        $("#loading").html("");
        fetchSERPUsageANDSavedReports(query_id);
        $(".hide-until-results").show();
        $("#searchbtn").text("Search");
        updateCSRFHash(data.csrf_hash);
    }).catch((error) => {
        console.log(error);
        alert(error);
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

    $("#deletereport").on('click', e => {
        const id = $("#saved-reports option:selected").val();

        if(id == 'select report'){
            alert("select a report");
            return;
        }

        fetch('/api/deletereport', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({reportid: id, csrf_token_name: $("#csrf_token_name").val()})
            })
            .then((res) => { return res.json(); })
            .then(data => {
                alert(data.status.toString());
                $('#saved-reports option:selected').remove();
                updateCSRFHash(data.csrf_hash);
            });
    })

    $("#loadreport").on('click', (e) => {

        const selectedReport = $("#saved-reports option:selected");
        const query_id = $("#saved-reports option:selected").val();

        if(selectedReport.val() == 'select report'){
            alert("select a report");
            return;
        }
        
        if(query_id != ""){
            const postData = {query_id: query_id, location: $("#search-locations-select").val(), csrf_token_name: $("#csrf_token_name").val()};  
            fetchSERPResults(postData)
        } else {
            alert("Error: report not found");
        }
        selectedReport.attr('selected','selected');
        $("#blog-post-id").val('');
    });

    $("#searchbtn").on('click', (e) => {

        if($("#searchterm").val() !== ""){   
            const postData = {query: $.trim($("#searchterm").val()), location: $("#search-locations-select").val(), csrf_token_name: $("#csrf_token_name").val()};  
            fetchSERPResults(postData)
        } else {
            alert("Enter a search term");
        }
    
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

    topTabs(document.getElementsByClassName("firstload")[0], 'serp-analysis');

  });