    <?php echo view('sections/header.php'); ?>
    <div class="container">

    <div class="row mt-5">
        <div class="col mt-4">
            <div class="alert alert-danger text-center">
                No API keys found. <a href="/configuration">Add all your API keys</a>
            </div>

            <ul>
            <?php foreach($keys as $key) : ?>
                <li> <span class="text-uppercase"><?= $key['name']; ?></span> is <?php echo ($key['key'] == "") ? "empty" : "updated"; ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>