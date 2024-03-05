<!DOCTYPE html>
<html lang="en">

<head>
    <link rel ="website icon" type="png" href="media/qvs.png">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/styleAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>
    <div class="maincontent">
        <?php
        $page = 'adminHome.php';
        include("includes/headerAdmin.php");
        ?>
        <div class="info">
            <div class="welcomeText">
                <p>Welcome to the online Hobbies Fair.</p><br>
                <p>This is a webpage where you can administrate all information provided by the website.</p><br>
                <p>This includes: pupil Info, hobby Info and passwords.</p>
            </div>
        </div>
        <br>
        <div class="buttons">
            
            <button class="createRegister" onclick="window.location.href='viewRegisters.php'"><h3>VIEW REGISTERS</h3></button>
            
        </div>
        <div class="buttons">
            
            <button class="amendPupils" onclick="window.location.href='amendPupils.php';"><h3> AMEND PUPILS</h3></button>
            
            <button class="amendHobbies" onclick="window.location.href='amendHobbies.php';"><h3>AMEND HOBBIES</h3></button>
            
        </div>
    </div>

</body>
<?php 
        include("includes/footer.php");
    ?>

</html>
