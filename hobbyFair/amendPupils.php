<?php
    include('includes/dbconnect.php');

    function sanitiseAddInputs() {
        if($_POST['username'] != filter_var($_POST['username'], @FILTER_SANITIZE_STRING)) {
            $_SESSION['errorMessage'] = "<p>Non-conforming characters in the username field.</p><p>Please review and re-enter this field</p>";
        } else {
            if($_POST['password'] != filter_var($_POST['password'], @FILTER_SANITIZE_STRING)) {
                $_SESSION['errorMessage'] = "<p>Non conforming characters in the password field.</p><p>Please review and re-enter this field.</p>";
            }
        }
    }

    function sanitiseDelInputs() {
        if($_POST['username'] != filter_var($_POST['username'], @FILTER_SANITIZE_STRING)) {
            $_SESSION['errorMessage'] = "<p>Non-conforming characters in the username field.</p><p>Please review and re-enter this field</p>";
        }
    }

    function sanitiseName($newName) {
        if($newName != filter_var($newName, @FILTER_SANITIZE_STRING)) {
            $_SESSION['errorMessage'] = "<p>Non-conforming characters in the new Name field.</p><p>Please review and re-enter this field</p>";
        }
    }

    function addPupil($link, $fullName, $username, $newPass, $houseId, $yearGroup) {
        $sql = "INSERT INTO Pupil(PupilName, UserName, Password, HouseId, YearGroup) VALUES ('$fullName', '$username', '$newPass', $houseId, '$yearGroup')";
        mysqli_query($link, $sql);
        echo("<script> alert('Pupil added') </script>");
    }

    function addStaff($link, $fullName, $username, $newPass, $userTypeId) {
        $sql = "INSERT INTO Staff(StaffName, UserName, Password, UserTypeId) VALUES ('$fullName', '$username', '$newPass', $userTypeId)";
        mysqli_query($link, $sql);
        echo("<script> alert('Staff Member added.') </script>");
    }

    function authenticateExists($link, $fullName) {
        $sql = "SELECT * FROM Pupil WHERE PupilName = '$fullName'";
        $result = mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 0) { //If cant find a pupil user which matches details; look for a staff user
            mysqli_free_result($result);
            $sql = "SELECT *  FROM Staff WHERE StaffName = '$fullName'";
            $result = mysqli_query($link, $sql);
            if (mysqli_num_rows($result) == 0) {
                mysqli_free_result($result);
                die("<p> Sorry that username does not exist - please try again.</p>"); //If no users at all; exit with message.
            } else {
                $_SESSION["usersType"] = 'staff';
            }
        } else {
            $_SESSION["usersType"] = 'pupil';
        }
    }

    function delMember($link, $fullName) {
       if ($_SESSION["usersType"] == 'staff') {
           
           $sql = "SELECT Staff.StaffId FROM Staff WHERE StaffName = '$fullName'";
           $result = mysqli_query($link, $sql);
           $staffid = mysqli_fetch_array($result);
           
           $sql = "SELECT staffhobbies.HobbyId FROM staffhobbies WHERE StaffId = '$staffid[0]'";
           $result = mysqli_query($link, $sql);
           while ($hobbyid = mysqli_fetch_array($result)) {
           
               $sql = "DELETE FROM staffhobbies WHERE HobbyId = '$hobbyid[0]'";
               mysqli_query($link, $sql);
               
               $sql = "DELETE FROM pupilhobbies WHERE HobbyId = '$hobbyid[0]'";
               mysqli_query($link, $sql);
               
               $sql = "DELETE FROM Hobby WHERE HobbyId = '$hobbyid[0]'";
               mysqli_query($link, $sql);
               
           }
           
            $sql = "DELETE FROM Staff WHERE StaffName = '$fullName'";
            mysqli_query($link, $sql);
            echo("<script> alert('Deleted Staff Member') </script>");
           
        } elseif ($_SESSION["usersType"] == 'pupil') {
           
           $sql = "SELECT pupil.PupilId FROM Pupil WHERE PupilName = '$fullName'";
           $result = mysqli_query($link, $sql);
           $id = mysqli_fetch_array($result);
           
           $sql = "DELETE FROM pupilhobbies WHERE PupilId = '$id[0]'";
           mysqli_query($link, $sql);
           
            $sql = "DELETE FROM Pupil WHERE PupilName = '$fullName'";
            mysqli_query($link, $sql);
            echo("<script> alert('Deleted Pupil') </script>");
        }
    }

    function updateYearGroup($link, $fullName, $year) {
        $sql = "UPDATE pupil SET YearGroup = '$year' WHERE PupilName = '$fullName'";
        mysqli_query($link, $sql);
        echo("<script> alert('Year Group Updated')</script>"); 
    }

    function updateName($link, $fullName, $newName, $newUsername) {
        if ($_SESSION["usersType"] == 'pupil') {
            $sql = "UPDATE pupil SET PupilName = '$newName', UserName = '$newUsername' WHERE PupilName = '$fullName'";
            mysqli_query($link, $sql);
            echo("<script> alert(' Pupil Name and Username Updated')</script>"); 
        } else {
            $sql = "UPDATE staff SET StaffName = '$newName', UserName = '$newUsername' WHERE StaffName = '$fullName'";
            mysqli_query($link, $sql);
            echo("<script> alert('Staff Name and Username Updated')</script>");
        }
    }

    function updateHouse($link, $fullName, $houseId) {
        $sql = "UPDATE pupil SET HouseId = $houseId WHERE PupilName = '$fullName'";
        mysqli_query($link, $sql);
        echo("<script> alert('House of Pupil Updated')</script>"); 
    }

    function changePassword($link, $fullName, $newPass) {
        if ($_SESSION["usersType"] == 'pupil') {
            $sql = "UPDATE pupil SET Password = '$newPass' WHERE PupilName = '$fullName'";
            mysqli_query($link, $sql);
            echo("<script> alert('Pupil Password Updated')</script>");
            
        } else {
            $sql = "UPDATE staff SET Password = '$newPass' WHERE StaffName = '$fullName'";
            mysqli_query($link, $sql);
            echo("<script> alert('Staff Password Updated')</script>");
        }
    }

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <link rel ="website icon" type="png" href="media/qvs.png">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/styleamend.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="script.js"></script>
</head>

<body>
    <div class="maincontent">
        <?php
        $page = 'amendPupils.php';
        include("includes/headerAdmin.php");
        ?>
        <div class="info">
            <button class="addMember" onclick="addMember()">
                <h3>ADD MEMBER</h3>
                <div id="formAdd" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendPupils.php" method="post">

                        <div class="inputs">
                            UserType:
                            <select name="users" id="usertype" onchange="checkPupil()">
                                <option value="Pupil" selected>Pupil</option>
                                <option value="staff">Staff</option>
                                <option value="SLT">SLT</option>
                            </select>
                        </div>

                        <div class='inputs' id="house">
                            House:
                            <select name="house">
                                <option value="Cunningham">Cunningham</option>
                                <option value="Haig">Haig</option>
                                <option value="Wavell">Wavell</option>
                                <option value="Trenchard">Trenchard</option>
                            </select>
                        </div>
                        <div class='inputs' id="year">
                            YearGroup:
                            <select name="year">
                                <option value="P7">P7</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="S4">S4</option>
                                <option value="S5">S5</option>
                                <option value="S6">S6</option>
                            </select>
                        </div>


                        <div class="inputs">
                            Full Name: <input type="text" name="username" required size="10">
                        </div>

                        <div class="inputs">
                            User Password: <input type="text" name="password" required size="10">
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="ADD">

                    </form>
                </div>
            </button>
        </div>
        <br>
        <div class="info">
            <button class="deleteMember" onclick="deleteMember()">
                <h3>DELETE MEMBER</h3>

                <div id="formDelete" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendPupils.php" method="post">


                        <div class="inputs">
                            Member to Delete: <input type="text" name="username" required placeholder="Enter full name here ...">
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="DELETE">

                    </form>
                </div>

            </button>
        </div>

        <br>

        <div class="info">
            <button class="updateInfo" onclick="updateInfo()">
                <h3>UPDATE MEMBER INFO</h3>

                <div id="formUpdate" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendPupils.php" method="post">

                        <div class="inputs">
                            Item to Update:
                            <select name="change" id="valToChange" onchange="checkUpdate()">
                                <option value="yeargroup" selected>Year Group</option>
                                <option value="name">Full Name</option>
                                <option value="house">House</option>
                            </select>
                        </div>

                        <div class="inputs">
                            User To Change: <input type="text" name="username" required placeholder="Enter full name here...">
                        </div>

                        <div class='inputs' id="newYear">
                            New YearGroup:
                            <select name="year">
                                <option value="P7">P7</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="S4">S4</option>
                                <option value="S5">S5</option>
                                <option value="S6">S6</option>
                            </select>
                        </div>

                        <div class='inputs' id="newHouse" style="display: none;">
                            New House:
                            <select name="house">
                                <option value="Cunningham">Cunningham</option>
                                <option value="Haig">Haig</option>
                                <option value="Wavell">Wavell</option>
                                <option value="Trenchard">Trenchard</option>
                            </select>
                        </div>

                        <div class='inputs' id="newName" style="display:none;">
                            New Name: <input type="text" name="newUsername" placeholder="Enter new name here...">
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="UPDATE">

                    </form>
                </div>

            </button>
        </div>

        <br>

        <div class="info">
            <button class="changePass" onclick="changeP()">
                <h3>CHANGE PASSWORDS</h3>

                <div id="changePasswords" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendPupils.php" method="post">


                        <div class="inputs">
                            Members' Full Name: <input type="text" name="username" required>
                        </div>

                        <div class="inputs">
                            New Password:<input type="text" name="password" required>
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="CHANGE">

                    </form>
                </div>

            </button>
        </div>
    </div>

    <div id="error">
        <?php
                        if($_SERVER["REQUEST_METHOD"] == "POST") {
                            
                            $_SESSION["errorMessage"] == "";
                            
                            while($_SESSION["errorMessage"] == "") {
                                
                                
                                $formType = $_POST["submitButton"];
                                $fullName = $_POST["username"];
                                
                                if ($formType == "ADD") {
                                    
                                    sanitiseAddInputs();
                                    
                                    $_SESSION["newUserType"] = $_POST["users"];
                                    $username = str_replace(' ', '', strtolower($fullName));
                                    $newPass = $_POST["password"]; 
            
                                    if ($_SESSION["newUserType"] == "Pupil") {
                                        
                                        $house = $_POST["house"];
                                        if ($house == "Cunningham") {   
                                            $houseId = 1;
                                        }elseif ($house == "Wavell") {
                                            $houseId = 2;
                                        }elseif ($house == "Haig") {
                                            $houseId = 3;
                                        }else {
                                            $houseId = 4;
                                        }
                                        
                                        $yearGroup = $_POST["year"];
                                        
                                        addPupil($link, $fullName, $username, $newPass, $houseId, $yearGroup);  
                                        
                                    } else {
                                        
                                        if ($_SESSION["newUserType"] == "staff") {
                                            $userTypeId = 1;
                                        } elseif($_SESSION["newUserType"] == "SLT") {
                                            $userTypeId = 2;
                                        }
                                        addStaff($link, $fullName, $username, $newPass, $userTypeId);
                                    }
                                    
                                    
     
                                } elseif ($formType == "DELETE") {
                                    
                                    authenticateExists($link, $fullName);
                                    sanitiseDelInputs();
                                    
                                    
                                    delMember($link, $fullName);
                                    
                                    
                                } elseif ($formType == "UPDATE") {
                                    
                                    authenticateExists($link, $fullName);
                                    sanitiseDelInputs();
                                    $_SESSION["itemChanging"] = $_POST["change"];
                                    
                                    if ($_SESSION["itemChanging"] == "yeargroup") {
                                        $year = $_POST["year"]; 
                                        updateYearGroup($link, $fullName, $year);
                                        
                                    } elseif ($_SESSION["itemChanging"] == "name") {
                                        $newName = $_POST["newUsername"];
                                        $newUsername = str_replace(' ', '', strtolower($newName));
                                        sanitiseName($newName);  
                                        updateName($link, $fullName, $newName, $newUsername);
                                        
                                    } else {
                                        $house = $_POST["house"];
                                        if ($house == "Cunningham") {   
                                            $houseId = 1;
                                        }elseif ($house == "Wavell") {
                                            $houseId = 2;
                                        }elseif ($house == "Haig") {
                                            $houseId = 3;
                                        }else {
                                            $houseId = 4;
                                        }
                                        updateHouse($link, $fullName, $houseId);
                                    }
                                    
                                    
                                } elseif ($formType == "CHANGE") {
                                    
                                    authenticateExists($link, $fullName);
                                    sanitiseAddInputs();
                                    $newPass = $_POST["password"];
                                    changePassword($link, $fullName, $newPass);
                                    
                                    
                                }
                                
                                mysqli_close($link);
                                break;
                                
                                } 
                                    echo $_SESSION["errorMessage"];   
                            }     
                    ?>


    </div>

</body>
<?php 
        include("includes/footer.php");
    ?>

</html>
