<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ICTLab | Log In</title>
</head>
<body>

<div id="container">
    <h1>Login</h1>
        
    <?php
        echo form_open('main/doforget');
                
        echo "<p>Email: ";
        echo form_input('email', $this->input->post('email'));
        echo "<p>";

        echo "<p>";
        echo form_submit('value', 'Reset');
        echo "<p>";
        
        echo form_close();
    ?>
            
</div>

</body>
</html>