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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap" rel="stylesheet">
</head>
<body>

    <?php echo view('sections/header.php'); ?>

    <div class="container editor p-3 col-12" id="editor">

        <div class="row mb-4">
            <div class="col">
                <h2 class="h1 text-center">Keyword: <strong><?=$query;?></strong></h2>
            </div>
        </div>

        <div class="row">

            <div class="col-md-3 mb-4 overflow-scroll" style="max-height: 650px">
                <h3>Top Keywords</h3> 
                <table class="table table-light mt-3 table-responsive">
                    <thead>
                    <tr>
                        <th scope="col">Keyword</th>
                        <th scope="col">Score</th>
                    </tr>
                    </thead>
                    <tbody id="keywords-tbody">
                        <?php foreach($keywords as $keyword) : ?>
                            <tr>
                                <td><?=$keyword->keyword;?></td>
                                <td><?=round($keyword->score*100000);?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>  
            </div>

            <div class="col-md-9">

                <div class="col mb-3">
                    <h3>Content Editor</h3> 
                </div>

                <div class="col mt-3 mb-3">
                    <label for="blog-post-title" class="d-block fw-bold form-label">Blog Post Title</label>
                    <input type="text" class="form-control" id="blog-post-title" placeholder="Blog Title" value="<?php echo (isset($title_outline) ? $title_outline['title'] : ""); ?>">
                    <a href="#" id="ai-title" class="cursor-pointer">AI Title</a>
                </div>

                <div class="col mt-3 mb-3">
                    <label for="blog-post" class="d-block fw-bold">Blog Post Content</label>
                    <a href="#" id="ai-outline" class="">AI Outline</a>
                    <textarea class="form-control" id="blog-post" rows="20">
                        <?php echo (isset($title_outline) ? $title_outline['outline'] : ""); ?>
                    </textarea>

                    <div class="m-4 text-center">
                        <input type="hidden" value="" data-keyword="" id="blog-post-id">
                        <button class="btn btn-success w-25" id="save-blog-post-draft">Save</button>
                        <button class="btn btn-danger w-25" id="save-blog-post-draft">Delete</button>                    </div>
                    </div>
                </div>

        </div>

        <input type="hidden" id="csrf_token_name" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    </div>

    <script>

        $(document).ready(function(){
            $("#ai-title").on("click", function(){
                fetch('/api/content', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({prompt: "<?=$query;?>", type: "title", csrf_token_name: $("#csrf_token_name").val()})
                })
                .then((res) => { return res.json(); })
                .then(data => {
                    $("#blog-post-title").val(data.result.toString());
                    $("#csrf_token_name").val(data.csrf_hash.toString());
                });
            });

            $("#ai-outline").on("click", function(){
                fetch('/api/content', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({prompt: "<?=$query;?>", type: "outline", csrf_token_name: $("#csrf_token_name").val()})
                })
                .then((res) => { return res.json(); })
                .then(data => {
                    $("#blog-post").val(data.result.toString().trim());
                    $("#csrf_token_name").val(data.csrf_hash.toString());
                });
            });
        });

    </script>

<?php echo view('sections/footer.php'); ?>
</body>
</html>