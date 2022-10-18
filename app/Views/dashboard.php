<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
    <?php if($tinymce_key != "") : ?>
        <script src="https://cdn.tiny.cloud/1/<?=$tinymce_key;?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
</head>
<body>

<?php if($update_keys) : ?> 
    <div class="container">
        <div class="row">
            <div class="col">
            <div class="alert alert-danger text-center">No API keys found. <a href="/configuration">Add all your API keys</a></div>
            <ul>
            <?php foreach($keys as $key) : ?>
                <li> <span class="text-uppercase"><?= $key['name']; ?></span> is <?php echo ($key['key'] == "") ? "empty" : "updated"; ?></li>
            <?php endforeach; ?>
            </ul>
            </div>
        </div>
    </div>

<?php else: ?>
   
    <div class="container">

        <div class="row mb-4 mt-3">
            <div class="col-12 p-2">
                <span class="h1 d-block text-xs-center">
                  <a href="/">Blog Post Assist</a>
                </span>
                <span class="float-md-end"><a href="/logout">Logout</a> <?=session()->get('username');?></span>
                <span class="float-md-end ms-0 ms-md-3 me-3"><a href="/configuration">Settings</a></span>
            </div>
            <div class="col-12 d-flex p-2 flex-row-reverse">
                <!--<a href="https://join.slack.com/t/flipsnap-net/shared_invite/zt-1hbmkqtqp-rg4_GlBF3fmMc7xifCKAqQ" target="_blank">Join the Slack channel for support and chatter</a>    
-->             <div>
                    <select id="saved-reports" class="form-control-sm m-0 p-2 fs-6">
                        <option>Select SERP Report</option>
                    </select>
                    <button type="button" id="loadreport" class="mt-3 d-inline input-group-text btn-success">Load</button>                    
                    <button type="button" id="deletereport" class="mt-3 d-inline input-group-text btn-danger">Delete</button>  
                </div>

            </div>
        </div>

        <div class="row p-3 pb-0">

            <div class="col-12 m-0 p-0 d-flex justify-content-between border-bottom">

                <div class="tab">
                    <button class="tablinks firstload" data-tab="serp-analysis">SERP Analysis</button>
                    <button class="tablinks" data-tab="editor">Content Editor</button>
                    <button class="tablinks" data-tab="generate-content">Generate Content</button>
                </div>

            </div>
        </div>

    </div>

    <div class="container">

        <div class="row">

            <div id="serp-analysis" class="col-12 tabcontent" style="display: block">

                <div class="mt-2">

                <div id="serpapi-account-info" class="alert alert-info"></div>

                <div id="recommended-word-length" class="alert alert-success"></div>

                <div class="col-12 mt-3">
                <form class="mb-4">
                    <div class="mb-3">
                        <label for="searchterm" class="form-label d-block fw-bold">Search</label>
                        <div class="d-md-flex">
                            <input type="text" class="form-control form-control-md" placeholder="Search Here" id="searchterm">
                            <select id="search-locations-select" class="form-select form-control-lg mt-2 mt-md-0 ms-md-2">
                                <option value="585069bfee19ad271e9bc66f">select location</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="searchbtn" class="input-group-text btn-success">Search</button>                    
                </form>
            </div>

        </div>

        <div class="row hide-until-results" style="min-height:900px">

                <div class="mb-5 mt-5">
                    <h3>Keywords</h3>
                    <table class="table table-light mt-3 table-responsive">
                        <thead>
                        <tr>
                            <th scope="col">Keywords</th>
                        </tr>
                        </thead>
                        <tbody>
                            <td class="extracted-keywords"></td>
                        </tbody>
                    </table>  
                </div>

                <div class="mb-5">
                    <h3>Top Titles</h3>

                    <table class="table table-light mt-3 table-responsive">
                        <thead>
                        <tr>
                            <th scope="col">Position</th>
                            <th scope="col">Title</th>
                            <th scope="col">Word Count</th>
                        </tr>
                        </thead>
                        <tbody id="top-title"></tbody>
                    </table>  
                </div>


                <div class="mb-5">
                    <h3>Related Questions</h3>

                    <table class="table table-light mt-3 table-responsive">
                        <thead>
                        <tr>
                            <th scope="col">Question</th>
                            <th scope="col">Domain</th>
                            <th scope="col">Title</th>
                        </tr>
                        </thead>
                        <tbody id="related-questions"></tbody>  
                    </table>  
                </div>
                
                <div class="mb-5 border-top pt-5">
                    <h3>Search Results</h3>

                        <table class="table table-light mt-3 table-responsive">
                            <thead>
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Domain</th>
                                <th scope="col">Description</th>
                                <th scope="col">Words</th>
                                <th scope="col">Headings</th>
                            </tr>
                            </thead>
                            <tbody id="results"></tbody>
                        </table>  
                </div>

                </div>
            </div>
        </div>

        <div class="row">

            <div class="container editor p-3 col-12 tabcontent" id="editor">

                <div class="row">
                    <div class="col-9">

                        <div class="col mt-3 mb-3">
                            <label for="blog-post-draft" class="d-block fw-bold">Blog Post Drafts</label>
                            <div class="col d-flex flex-row">
                            `   <select id="blog-post-drafts" class="form-select" aria-label="Default select example">
                                    <option id="blog-post-drafts-option" value="select" selected>Load a keyword</option>
                                </select>
                                <button type="button" class="btn btn-primary ms-1 me-1" id="load-blog-post-draft">Load</button>
                                <button type="button" class="btn btn-danger me-1" id="delete-blog-post-draft">Delete</button>
                            </div>
                        </div>

                        <h3>Blog Post</h3>

                        <div class="col mt-3 mb-3">
                            <label for="blog-post-title" class="d-block fw-bold">Blog Post Title</label>
                            <input type="text" id="blog-post-title" class="w-100" value="">
                        </div>

                        <div class="col mt-3 mb-3">
                            <label for="blog-post" class="d-block fw-bold">Blog Post Content</label>
                            <textarea id="blog-post"></textarea>

                            <div class="m-4">
                                <input type="hidden" value="" data-keyword="" id="blog-post-id">
                                <button class="btn btn-success w-100" id="save-blog-post-draft">Save</button>
                            </div>
                        </div>

                    </div>


                    <div class="col-3 mb-5 pt-2 bg-light">
                        <h3>Keywords</h3>
                        <div class="extracted-keywords"></div>
                    </div>
                </div>

            </div>
        
        </div>

        <div class="row">

            <div id="generate-content" class="col-12 tabcontent mt-3">
            <div class="row">
                            
                <div class="col-12 col-md-4">
                    <div class="mb-3">
                        <lable for="generate-content-type">Type</lable>
                        <select id="generate-content-type" class="form-select" aria-label="Default select example">
                            <option value="select" selected>Open this select menu</option>
                            <option value="blog-post-topics">Post Titles</option>
                            <option value="blog-post-outline">Post Outlines</option>
                            <option value="blog-post-section">Post Section</option>
                            <option value="blog-post-open">Custom Prompt</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="generate-content-response" class="form-label">Description</label>
                        <textarea class="form-control" id="generate-content-description" rows="3"></textarea>
                        <small>Enter a keyword phrase or description of what want returned</small>
                        <small>Example when selecting "Post Outline": How does OpenAI work.</small>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary mb-3" id="generate-blog-post-button">Generate</button>
                    </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <textarea class="p-2 border" style="min-height:350px;" id="generated-content-area" cols="50" rows="10"></textarea>
                    </div>
                </div>

            </div>
        </div>

        
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="font-family: 'Cascadia Code', sans-serif;">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Copy Raw Content</h5>
                    <button onclick="javascript: copyDivToClipboard()" type="button" class="btn btn-warning">Copy</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
                <div class="modal-body" id="export-post"></div>
            </div>
        </div>
    </div>

    <script src="scripts.js"></script>
    <input type="hidden" id="csrf_token_name" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

<?php endif; ?>

    <?php echo view('footer.php'); ?>

</body>
</html>
