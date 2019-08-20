<div>
    <p>Hello <?php echo $getToName(); ?>,<p>
    <p>We could not validate hash commit message.</p>
    <p>
        <b>Hash:</b>
        <a href="<?php echo $getRepoUrl(); ?>"><?php echo $hashCommit->hash_rev; ?></a>
        <br>
        <b>Branch:</b> <?php echo $hashCommit->branch->name; ?>
        <br>
        <b>Description:</b> <pre><?php echo $hashCommit->commit_description; ?></pre>
    </p>
    <p>
        Please find bellow validation errors:
        <ul>
            <?php foreach ($errors as $error) { ?>
            <li><?php echo $error; ?></li>
            <?php } ?>
        </ul>
    </p>
    <p>
        You can edit description <a href="<?php echo $getEditUrl(); ?>" >here</a>
    </p>
    <p>This is an automatic email! Please do not reply</p>
</div>
