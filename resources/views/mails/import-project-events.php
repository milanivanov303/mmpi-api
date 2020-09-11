<div>
    <p>Hello,<p>
    <p>
        Some events ware not imported due the following errors:
        <?php foreach ($data as $errors) {?>
          <pre><?=$errors?></pre>
        <?php } ?>
    </p>
    <br>
    <p>This is an automatic notification!</p>
</div>
