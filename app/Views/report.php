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

            <div id="serp-analysis" class="col-12 tabcontent" style="display: block">

                <div class="mt-2">
                <?php if(isset($serp_query) AND !isset($serp_query['error'])) : ?>
                    <h1 class="display-4 m-3 text-center"><strong class="fw-bold"><?=urldecode($serp_query);?></strong></h1>
                    <div class="fs-3 fw-bold alert alert-success">Recommended word length <?=$wordcount;?></div>
                <?php endif; ?> 
                </div>

            </div>

        </div>

        <div class="row hide-until-results" style="min-height:900px">

                <div class="container">
                    <div class="row">

                        <div class="col-md-3 mb-4 overflow-scroll"> 
                            <a class="btn btn-warning mb-2 w-100 fw-bold" href="/content-editor?reportid=<?=$report_id;?>">Editor</a>
                            
                            <?php if(isset($highlighted_keywords) AND count($highlighted_keywords) > 0) :?>
                            <h3>Related Keywords</h3> 
                            <table class="table table-light mt-3 table-responsive">
                                <thead>
                                <tr>
                                    <th scope="col">Keyword</th>
                                </tr>
                                </thead>
                                <tbody id="keywords-tbody">
                                    <?php foreach($highlighted_keywords as $highlight) : ?>
                                        <tr>
                                            <td><?=$highlight;?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table> 
                            <?php endif; ?>

                            
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

                            <div class="mb-5">
                                <h3>Top 10 Titles</h3>

                                <table class="table table-light mt-3 table-responsive">
                                    <thead>
                                    <tr>
                                        <th scope="col">Position</th>
                                        <th scope="col">Domain</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Word Count</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($top_10_titles as $titles) : ?>
                                            <tr>
                                                <td><?=$titles['position'];?></td>
                                                <td><?=$titles['domain'];?></td>
                                                <td><a target="_blank" href="<?=$titles['link'];?>"><?=$titles['title'];?></a></td>
                                                <td><?=$titles['wordcount'];?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
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
                                    <tbody>
                                        <?php if(count($related_questions) > 1) : ?>
                                            <?php foreach($related_questions as $question) : ?>
                                                <tr>
                                                    <td><?=$question->question;?></td>
                                                    <td><?=parse_url($question->link)['host'];?></td>
                                                    <td><a href="<?=$question->link;?>" target="_blank"><?=$question->title;?></a></td>
                                                </tr>
                                            <?php endforeach; ?>                                          
                                        <?php else : ?>
                                            <tr>
                                                <td>No related questions</td>
                                            </tr>  
                                        <?php endif; ?>                                      
                                    </tbody>  
                                </table>  
                            </div>
                        
                            <div class="mb-5 border-top pt-5">
                                <h3>Search Results</h3>

                                <table class="table table-light mt-3 table-responsive">
                                    <thead>
                                    <tr>
                                        <th scope="col">Position</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Domain</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Words</th>
                                        <th scope="col">Content</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($serp_report_php as $result): $random_id = rand(); ?>
                                        <tr>
                                            <td><?=$result->position;?></td>
                                            <td><?=$result->title;?></td>
                                            <td><?=parse_url($result->link)['host'];?></td>
                                            <td><?=$result->snippet;?></td>
                                            <td><?=$result->wordcount;?></td>
                                            <td>
                                                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#s<?=$random_id;?>">
                                                    Structure
                                                </button>
                                                <div class="modal fade modal-dialog-scrollable" id="s<?=$random_id;?>">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="staticBackdropLabel"><?=$result->title;?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div>
                                                                    <?php if(isset($result->structure) AND count($result->structure) > 0) : ?>
                                                                        <?php foreach ($result->structure as $headings): $h_parts = explode("-", $headings); $margin = preg_replace('/[^0-9]/', '', trim($h_parts[0])); ?>
                                                                            <div class="ms-<?=$margin;?> p-2 d-flex border" style="font-size: 16px;background-color:#f5fbfb">
                                                                                <div class="text-primary fw-bold p-1"><?=trim($h_parts[0]);?></div>
                                                                                <div class="fw-bold p-1"><?=trim($h_parts[1]);?></div>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    <?php else : ?>
                                                                        <h3>No structure found</h3>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>                                       
                                    </tbody>
                                </table>  
                            </div>

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

    <div class="modal-lg fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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

    <!--<script src="scripts.js?r=<?= time() ?>"></script>-->
    <input type="hidden" id="csrf_token_name" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <?php if(isset($serp_report_php)) : ?>
    <script>
        const saved_report = <?=json_encode($serp_report_php);?>;
        console.log(saved_report);
    </script>
    <?php endif; ?>

<?php endif; ?>

    <?php echo view('sections/footer.php'); ?>

</body>
</html>
