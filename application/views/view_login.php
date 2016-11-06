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
        echo form_open('main/login_validation');
                
        echo "<p>Email: ";
        echo form_input('email', $this->input->post('email'));
        echo "<p>";

        echo "<p>Password: ";
        echo form_password('password');
        echo "<p>";

        echo "<p>";
        echo form_submit('login_submit', 'Login');
        echo "<p>";
        
        echo validation_errors();

        echo form_close();
    ?>
    
    <a href='<?php echo base_url()."main/signup"?>'> Sign Up! </a>
        
</div>

</body>
</html>