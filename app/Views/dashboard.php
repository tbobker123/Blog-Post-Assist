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
    <script src="https://cdn.tiny.cloud/1/3hoje20mzsz3vr1f5dsu7mbm3jivkrwpmt7cf2keu17tpcdk/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container">

        <div class="row mb-4 mt-3">
            <div class="col p-2">
                <span class="h1" style="font-family: 'Kaushan Script', cursive;">Blog Post Creator</span>
                <span class="float-end"><a href="/logout">Logout</a> <?=session()->get('username');?></span>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="tab">
                    <button class="tablinks" onclick="openCity(event, 'serp-analysis')">SERP Analysis</button>
                    <button class="tablinks" onclick="openCity(event, 'generate-content')">Generate Content</button>
                    <button class="tablinks" onclick="openCity(event, 'editor')">Content Editor</button>
                  </div>
            </div>
        </div>

        <div class="row">
            <div id="serp-analysis" class="col-12 tabcontent" style="display: block">
                <div style="min-height:900px" class="mt-2">

                <!--<div class="form-group">
                    <input type="text" class="form-control w-75 d-inline" id="searchterm">
                    <input id="searchbtn" type="button" class="btn btn-primary" value="Search">
                </div>-->

                <form action="" class="">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" placeholder="Search Here" id="searchterm">
                        <button type="button" id="searchbtn" class="input-group-text btn-success"><i class="bi bi-search me-2"></i> Search</button>
                    </div>
                </form>

                <div id="recommended-word-length"></div>

                    <table class="table table-light mt-3">
                        <thead>
                          <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Domain</th>
                            <th scope="col">Description</th>
                            <th scope="col">Word Count</th>
                            <th scope="col">Headings</th>
                          </tr>
                        </thead>
                        <tbody id="results"></tbody>
                    </table>

                    <div id="loading"></div>

                </div>
            </div>
        </div>

        <div class="row">
            <div id="generate-content" class="col-12 tabcontent">
                <div class="h-100">
                    <div class="container">
                        
                        <div class="row mt-2 mb-2 p-3 pb-4 border-bottom">
                            <div class="col-12">
                                <h3>Blog Post Topics</h3>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" name="searchterm" id="prompt" class="w-75">
                                <input class="openai-button" id="blog-post-topics" type="button" value="Post Topics">
                            </div>
                            <div class="col-12 col-md-6">
                                <textarea class="p-4" id="blog-post-topics-textarea" cols="50" rows="10"></textarea>
                            </div>
                        </div>

                        <div class="row mt-2 mb-2 p-3 pb-4 border-bottom">
                            <div class="col-12">
                                 <h3>Blog Post Outline</h3>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" name="searchterm" id="prompt" class="w-75">
                                <input class="openai-button" id="blog-post-outline" type="button" value="Post Outline">
                            </div>
                            <div class="col-12 col-md-6">
                                <textarea class="p-4" id="blog-post-outline-textarea" cols="50" rows="10"></textarea>
                            </div>
                        </div>

                        <div class="row mt-2 mb-2 p-3 pb-4 border-bottom">
                            <div class="col-12">
                                <h3>Blog Post Section</h3>
                            </div>
                            <div class="col-12 col-md-6">
                                <input type="text" name="searchterm" id="prompt" class="w-75">
                                <input class="openai-button" id="blog-post-section" type="button" value="Post Section">
                            </div>
                            <div class="col-12 col-md-6">
                                <textarea class="p-4" id="blog-post-section-textarea" cols="50" rows="10"></textarea>
                            </div>
                        </div>

                    </div>
                    <div class="" id="blogcontent"></div>   
                </div>
            </div>
        </div>

        <div class="row">
            <div class="editor p-3 col-12 tabcontent" id="editor">
                <div class="float-end">
                    <button class="btn btn-success" onclick="mySave()">Save</button>
                    <button onclick="javascript: clearSaved()" type="button" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Export</button>
                </div>

                <h3>Blog Post</h3>
                <textarea id="blog-post"></textarea>
                <div id="saved"></div>
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
    <script>
        setInterval(mySave, 60000);
    </script>

</body>
</html>
