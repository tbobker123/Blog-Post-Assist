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
</head>
<body>

<?php echo view('sections/header.php'); ?>

    <div class="container">

    <div class="container mt-5">
          <div class="row">
            <div class="col-md-8 offset-2 mt-3">
                <h2>Settings</h2>

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
                
                

                <h4>Update API Keys</h4>

                <?php foreach($apikeys as $apikey): ?>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label"><?=$apikey['name'];?></label>
                        <input type="password" name="<?=$apikey['name'];?>-key" class="form-control" value="">
                        <input type="hidden" name="<?=$apikey['name'];?>-id" class="form-control" value="<?=$apikey['id'];?>">
                    </div>

                <?php endforeach; ?>

                <h4>Prompts</h4>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Blog Post Topics</label>
                    <input type="text" name="topic" class="form-control" id="blog-post-topics-config" value="<?=$settings['openAI_topic']?>">
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Blog Post Outline</label>
                    <input type="text" name="outline" class="form-control" id="blog-post-outline-config" value="<?=$settings['openAI_outline']?>">
                </div>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Blog Post Section</label>
                    <input type="text" name="section" class="form-control" id="blog-post-section-config" value="<?=$settings['openAI_section']?>">
                </div>

                <h4>Other Settings</h4>

                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">SERP Results</label>
                    <input type="text" name="serp" class="form-control" id="serp-results-config" value="<?=$settings['serp']?>">
                </div>

                <div class="mb-3">
                    <input type="submit" name="update_configuration" class="form-control btn btn-success" id="save-config" value="Update">
                </div>

                </form>

            </div>
          </div>
          </div>

          <?php echo view('sections/footer.php'); ?>

</body>
</html>
