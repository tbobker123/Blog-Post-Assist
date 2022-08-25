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
                <span class="h1" style="font-family: 'Kaushan Script', cursive;">
                  <a href="/">Blog Post Creator</a>
                </span>
                <span class="float-end"><a href="/logout">Logout</a> <?=session()->get('username');?></span>
                <span class="float-end ms-3 me-3"><a href="/configuration">Settings</a></span>
                <span class="float-end ms-3 me-3"><a href="/">Dashboard</a></span>
            </div>
        </div>

        <div class="container">
          <div class="row">
            <div class="col-md-8 offset-2 mt-3">
                <h4>Settings</h4>

                <?php if (session()->getFlashdata('config') !== NULL) : ?>
                <div id="alert" class="alert alert-success" role="alert">
                    <?php echo session()->getFlashdata('config'); ?>
                </div>
                <script>
                    setTimeout(function(){
                        $("#alert").fadeOut();
                    },2000)
                </script>
                <?php endif; ?>

                <form method="post" action="/configuration/update">
                <?=csrf_field();?>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Blog Post Topics</label>
                    <input type="text" name="topic" class="form-control" id="blog-post-topics-config" value="<?=$settings[0]['openAI_topic']?>">
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Blog Post Outline</label>
                    <input type="text" name="outline" class="form-control" id="blog-post-outline-config" value="<?=$settings[0]['openAI_outline']?>">
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Blog Post Section</label>
                    <input type="text" name="section" class="form-control" id="blog-post-section-config" value="<?=$settings[0]['openAI_section']?>">
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">SERP Results</label>
                    <input type="text" name="serp" class="form-control" id="serp-results-config" value="<?=$settings[0]['serp']?>">
                </div>

                <div class="mb-3">
                    <input type="submit" name="update_configuration" class="form-control btn btn-success" id="save-config" value="Update">
                </div>

                </form>

            </div>
          </div>
          </div>

</body>
</html>
