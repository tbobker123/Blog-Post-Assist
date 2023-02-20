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
    <?php echo view('templates/update_keys.php'); ?>
<?php else: ?>
   
    <?php echo view('sections/header.php'); ?>

    <div class="container">

        <div class="row">
            <div class="col">
                <div id="serpapi-account-info" class="alert alert-info"></div>
            </div>
        </div>

        <div class="row">
            
        <div class="col-12 mt-3">
`           <form class="mb-4">
            <div class="mb-3">
                <label for="searchterm" class="h3 form-label d-block fw-bold">New Report</label>
                <div class="d-md-flex">
                    <input type="text" class="form-control form-control-md" placeholder="Search Here" id="searchterm">
                    <select id="search-locations-select" class="form-select form-control-md mt-2 mt-md-0 ms-md-2">
                        <option value="585069bfee19ad271e9bc66f">select location</option>
                    </select>
                    <button type="button" id="searchbtn" class="input-group-text btn-success">Search</button> 
                </div>
            </div>
                               
        </form>
        <?=session()->getFlashdata('error');?>
        </div> 


            <div class="col">
                <h3 class="fw-bold">Reports</h3>

                <?php if(isset($saved_reports) AND !isset($saved_reports['Error'])) : ?>
                    <table class="table" id="list-reports-table">
                        <thead>
                            <tr>
                                <th scope="col">Keyword</th>
                                <th scope="col">Word Count</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($saved_reports as $report) : ?>
                            <tr>
                                <td><a class="fs-5" href="/report?reportid=<?=$report['id'];?>"><?=urldecode($report['query']);?></a></td>
                                <td><?=$report['wordcount'];?></td>
                                <td>                                   
                                     <a type="button" class="editor-report-btn d-inline input-group-text btn-warning" href="/content-editor?reportid=<?=$report['id'];?>">Editor</a>
                                    <a type="button" class="delete-report-btn d-inline input-group-text btn-danger" href="/delete?reportid=<?=$report['id'];?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <?=$saved_reports['Error']; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="scripts.js"></script>
    <input type="hidden" id="csrf_token_name" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">


<?php endif; ?>

    <?php echo view('sections/footer.php'); ?>

</body>
</html>
