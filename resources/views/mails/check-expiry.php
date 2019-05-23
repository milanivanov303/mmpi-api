<div>
    <p>Hello,<p>
    <p>
        Please be informed that certificate for project
        <?php echo $data['message']['project_name']?>
        expires on
        <?php echo $data['message']['valid_to']?>.
    </p>
    <br><br>
    <p>Best Regards</p>
    <p>--------------------------------------</p>
    <p>
        <?php if (app('env') !== 'production') {
                include 'original-recipients.php';
        }?>
    </p>
</div>