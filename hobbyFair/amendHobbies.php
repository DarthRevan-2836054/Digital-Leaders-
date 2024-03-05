<?php
    include('includes/dbconnect.php');

    function sanitiseAddInputs($hobbyname) {
        if($hobbyname != filter_var($hobbyname, @FILTER_SANITIZE_STRING)) {
            $_SESSION['errorMessage'] = "<p>Non-conforming characters in the hobby Name field.</p><p>Please review and re-enter this field</p>";
        } else {
            if($_POST['venue'] != filter_var($_POST['venue'], @FILTER_SANITIZE_STRING)) {
                $_SESSION['errorMessage'] = "<p>Non conforming characters in the venue field.</p><p>Please review and re-enter this field.</p>";
            }
        }
    }

    function sanitiseDelInputs() {
        if($_POST['hobby'] != filter_var($_POST['hobby'], @FILTER_SANITIZE_STRING)) {
            $_SESSION['errorMessage'] = "<p>Non-conforming characters in the hobby Name field.</p><p>Please review and re-enter this field.</p>";
        } else {
            if($_POST['leader'] != filter_var($_POST['leader'], @FILTER_SANITIZE_STRING)) {
                $_SESSION['errorMessage'] = "<p>Non-conforming characters in the leading staff field.</p><p>Please review and re-enter this field.</p>";
            }
        }
    }

    function authenticateStaffHobby($link, $hobbyName, $Leadingstaff) {
        $true = "False";
        $sql = "SELECT hobby.HobbyName FROM hobby WHERE HobbyName = '$hobbyName'";
        $result = mysqli_query($link, $sql);
        
        if (mysqli_num_rows($result) == 0) {
            mysqli_free_result(result);
            die("<p>No hobby matching this name - check name is spelt correctly and capitalised</p>");
            } else {
            $sql = "SELECT staff.StaffName FROM staff WHERE StaffName = '$Leadingstaff'";
            $result = mysqli_query($link, $sql);
            
            if (mysqli_num_rows($result) == 0) {
                mysqli_free_result($result);
                die("<p>No staff matching this name - check spelt correctly and capitalised.</p>");
            } else {
                $true = "True";
                return($true);
            }
        }
        }

    function deleteHobby($link, $hobbyName, $Leadingstaff) {
        $sql = "SELECT staff.StaffId FROM staff WHERE StaffName = '$Leadingstaff'";
        $result = mysqli_query($link, $sql);
        $staffId = mysqli_fetch_array($result);
        
        $sql = "SELECT hobby.HobbyId FROM hobby WHERE HobbyName = '$hobbyName'";
        $result = mysqli_query($link, $sql);
        $hobbyId = mysqli_fetch_array($result);
        
        $sql = "DELETE FROM staffHobbies WHERE StaffId = '$staffId[0]' AND HobbyId = '$hobbyId[0]'";
        mysqli_query($link, $sql);
        
        $sql = "DELETE FROM pupilhobbies WHERE hobbyId = '$hobbyId[0]'";
        mysqli_query($link, $sql);
        
        $sql = "DELETE FROM hobby WHERE HobbyName = '$hobbyName' AND HobbyId = '$hobbyId[0]'";
        mysqli_query($link, $sql);
        
        echo("<script> alert('Hobby deleted') </script>"); //REMINDER TO FIGURE OUT IF TWO HOBBIES HAVE THE SAME NAME
        
    }

    function addHobby($link, $hobbyName, $venue, $day, $timeStart, $timeEnd, $staff) {
        $sql="SELECT staff.StaffId FROM staff WHERE StaffName= '$staff'";
        $result = mysqli_query($link, $sql);
        
        if (mysqli_num_rows($result) == 0) { //If cant find a pupil user which matches details; look for a staff user
            mysqli_free_result($result);
            die("<p>No staff member matching this name - add staff first / ensure name is spelt correctly</p>");
        } else {
            
        $staffId = mysqli_fetch_array($result);
            
        $sql = "SELECT * FROM venue WHERE VenueName = '$venue'";
        $result=mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 0) {
            
            $sql = "INSERT INTO venue(VenueName) VALUES ('$venue')"; //Check if there is already a venue with this name existing
            mysqli_query($link, $sql);                                  //REMINDER TO ADD A Check FOR if the times of hobbies clash before adding them
            
        }
        
        $sql="SELECT venue.VenueId FROM venue WHERE VenueName = '$venue'";
        $result = mysqli_query($link, $sql);
        $venueId = mysqli_fetch_array($result);
            
        $sql = "INSERT INTO hobby(HobbyName, VenueId, dayHeld, timeStart, timeEnd) VALUES ('$hobbyName', '$venueId[0]', '$day', '$timeStart', '$timeEnd')";
        
        mysqli_query($link, $sql);
        $sql = "SELECT hobby.HobbyId FROM hobby WHERE hobbyName = '$hobbyName'";
        $result = mysqli_query($link, $sql);
        $hobbyId = mysqli_fetch_array($result);
            
        $sql="INSERT INTO staffhobbies(StaffId, HobbyId) VALUES($staffId[0], $hobbyId[0])";
        mysqli_query($link, $sql);
        echo("<script> alert('Hobby added') </script>");
        }
    }

    function authenticateHobby($link, $hobbyName) {
        $true = "False";
        $sql = "SELECT hobby.HobbyName FROM hobby WHERE HobbyName = '$hobbyName'";
        $result = mysqli_query($link, $sql);
        
        if (mysqli_num_rows($result) == 0) {
            mysqli_free_result(result);
            die("<p>No hobby matching this name - check name is spelt correctly and capitalised</p>");
        } else {
            $true = "True";
            return($true);
            }
        }

    function updateVenue($link, $hobbyName, $venue) {
        $sql = "SELECT * FROM venue WHERE VenueName = '$venue'";
        $result=mysqli_query($link, $sql);
        if (mysqli_num_rows($result) == 0) {
            
            $sql = "INSERT INTO venue(VenueName) VALUES ('$venue')"; //Check if there is already a venue with this name existing
            mysqli_query($link, $sql);                                  //REMINDER TO ADD A Check FOR if the times of hobbies clash before adding them
            
        }
        
        $sql = "SELECT venue.VenueId FROM venue WHERE VenueName = '$venue'";
        $result = mysqli_query($link, $sql);
        $venId = mysqli_fetch_array($result);
        
        $sql = "UPDATE hobby SET venueId = $venId[0] WHERE HobbyName = '$hobbyName'";
        mysqli_query($link, $sql);
        echo("<script> alert('Venue Updated') </script>");
    }

    function updateDay($link, $hobbyName, $newDay) {
        $sql = "UPDATE hobby SET dayHeld = '$newDay' WHERE HobbyName = '$hobbyName'";
        mysqli_query($link, $sql);
        echo("<script> alert('Day of hobby updated') </script>");
        
    }

    function updateTime($link, $hobbyName, $start, $end) {
        $sql = "UPDATE hobby SET timeStart = '$start', timeEnd = '$end' WHERE HobbyName = '$hobbyName'";
        mysqli_query($link, $sql);
        echo("<script> alert('Timings of hobby updated') </script>");
    }

    function updateName($link, $hobbyName, $newHobby) {
        $sql = "UPDATE hobby SET HobbyName = '$newHobby' WHERE HobbyName = '$hobbyName'";
        mysqli_query($link, $sql);
        echo("<script> alert('Hobby name updated') </script>");
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
        $page = 'amendHobbies.php';
        include("includes/headerAdmin.php");
        ?>
        <div class="info">
            <button class="addMember" onclick="addMember()">
                <h3>ADD HOBBY</h3>
                <div id="formAdd" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendHobbies.php" method="post">

                        <div class="inputs">
                            Hobby Name: <input type="text" name="hobby" required size=7>
                        </div>

                        <div class="inputs">
                            Leading Staff: <input type="text" name="staff" required size=7>
                        </div>

                        <div class="inputs">
                            Venue:<input type="text" name="venue" required size=7>
                        </div>

                        <div class="inputs">
                            Day Held:
                            <select name="day" required>
                                <option value="monday">Monday</option>
                                <option value="teusday">Teusday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>

                        <div class="inputs">
                            Time Start:<input type="time" name="timeStart" required>
                        </div>

                        <div class="inputs">
                            Time End:<input type="time" name="timeEnd" required>
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="ADD">

                    </form>
                </div>
            </button>
        </div>
        <br>

        <div class="info">
            <button class="deleteMember" onclick="deleteMember()">
                <h3>DELETE HOBBY</h3>
                <div id="formDelete" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendHobbies.php" method="post">

                        <div class="inputs">
                            Hobbies' Full Name: <input type="text" name="hobby" required>
                        </div>
                        <div class="inputs">
                            Leading staff member: <input type="text" name="leader" required>
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="DELETE">

                    </form>
                </div>
            </button>
        </div>

        <br>

        <div class="info">
            <button class="updateInfo" onclick="updateInfo()">
                <h3>UPDATE HOBBY INFO</h3>
                <div id="formUpdate" class="forms" onkeyup="(arguments[0] || window.event).preventDefault();" onclick="(arguments[0] || window.event).stopPropagation();">
                    <!--Ensures that when form items are clicked, addMember() function is not carried out -->

                    <form action="amendHobbies.php" method="post">

                        <div class="inputs">
                            Info to Update:
                            <select name="change" id="hobbyValToChange" onchange="changeHobbyInfo()">
                                <option value="hobbyname" selected>Hobby Name</option>
                                <option value="startend">Start / End time</option>
                                <option value="dayheld">Day Held</option>
                                <option value="venue">Venue</option>
                            </select>
                        </div>

                        <div class="inputs">
                            Hobbies' Full Name: <input type="text" name="hobby" required>
                        </div>

                        <div class="inputs" id="newName">
                            Hobbies' New Name: <input type="text" name="Newhobby">
                        </div>

                        <div class="inputs" style="display: none;" id="starttime">
                            Start time: <input type="time" name="startTime">
                        </div>
                        <div class="inputs" style="display: none;" id="endtime">
                            End time: <input type="time" name="endTime">
                        </div>

                        <div class="inputs" style="display: none;" id="dayheld">
                            Day Held:
                            <select name="Newday">
                                <option value="monday">Monday</option>
                                <option value="teusday">Teusday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                        </div>

                        <div class="inputs" style="display: none;" id="ven">
                            New Venue: <input type="text" name="venue">
                        </div>

                        <input type="submit" name="submitButton" class="submit" value="UPDATE">

                    </form>
                </div>
            </button>
        </div>
    </div>

    <div id="error">
        <?php
                        if($_SERVER["REQUEST_METHOD"] == "POST") {
                            
                            $_SESSION['errorMessage'] == "";
                            
                            while($_SESSION['errorMessage'] == "") {
                                
                                
                                $formType = $_POST["submitButton"];
                                $hobbyName = $_POST["hobby"];
                                $hobbyName = str_replace(' ', '', strtolower($hobbyName));
                                
                                if ($formType == "ADD") {
                                    
                                    sanitiseAddInputs($hobbyName); 
                                    $venue = $_POST["venue"];
                                    $day = $_POST["day"];
                                    $timeStart = $_POST["timeStart"];
                                    $timeEnd = $_POST["timeEnd"];
                                    $staff = $_POST["staff"];
                                        
                                    addHobby($link, $hobbyName, $venue, $day, $timeStart, $timeEnd, $staff);  
                                        
                                } elseif ($formType == "DELETE") {
                                    
                                    sanitiseDelInputs();
                                    $Leadingstaff= $_POST["leader"];
                                    $true = authenticateStaffHobby($link, $hobbyName, $Leadingstaff);
                                    if ($true == "True") {
                                        deleteHobby($link, $hobbyName, $Leadingstaff);
                                    }
                                    
                                } else {
                                    
                                    $true = authenticateHobby($link, $hobbyName);
                                    if ($true == "True") {
                                        sanitiseAddInputs();
                                        $_SESSION["itemChanging"] = $_POST["change"];
                                    
                                        if ($_SESSION["itemChanging"] == "venue") {
                                            $venue = $_POST["venue"]; 
                                            updateVenue($link, $hobbyName, $venue);
                                        
                                        } elseif ($_SESSION["itemChanging"] == "dayheld") {
                                            $newDay = $_POST["Newday"];
                                            updateDay($link, $hobbyName, $newDay);
                                        
                                        } elseif ($_SESSION["itemChanging"] == "startend") {
                                            $start = $_POST["startTime"];
                                            $end = $_POST["endTime"];
                                            updateTime($link, $hobbyName, $start, $end);
                                        } else {
                                            $newHobby = $_POST["Newhobby"];
                                            updateName($link, $hobbyName, $newHobby);
                                        }
                                    
                                    }
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
