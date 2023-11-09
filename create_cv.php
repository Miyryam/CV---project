<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mycvdatabase"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Грешка при връзката с MySQL сървъра: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);

    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $birth_date = date('Y-m-d', strtotime($_POST["birth_date"]));
    $university = $_POST["university"];
    $skills = implode(", ", $_POST["skills"]);

    $sql = "INSERT INTO cv (first_name, middle_name, last_name, birth_date, university, skills) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $first_name, $middle_name, $last_name, $birth_date, $university, $skills);

   if ($stmt->execute()) {
    echo "Данните бяха успешно записани в базата данни.";
} else {
    echo "Грешка при запис на данните: " . $stmt->error;
}


    $stmt->close();
}


$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Уеб сайт за CV</title>
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }

        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Създаване на CV</h1>

    <form method="post" action="">
        <label for="first_name"></label>
        <input type="text" name="first_name" placeholder="Име..." required><br>
        <br>

        <label for="middle_name"></label>
        <input type="text" name="middle_name" placeholder="Презиме..." required><br>
        <br>
        
        <label for="last_name"></label>
        <input type="text" name="last_name" placeholder="Фамилия..." required><br>
        <br>
        
        <label for="birth_date">Дата на раждане:</label>
        <input type="date" name="birth_date" required>

        <br>

        <label for="university"></label>
        <select name="university" id="universitySelect">
            <option value="">Изберете университет....</option>
        </select>

        <button id="addUniversityButton" type="button" onclick="addUniversity()">&#9998;</button>
        <br>
        <div class="popup" id="universityPopup">
            <div class="popup-content">
                <h4>Popup въвеждане на нов Университет</h4>

                <label for="new_university"></label>
                <input type="text" name="new_university" id="new_university" placeholder="Име на университет..." required><br>
                <label for="accreditation"></label>
                <input type="text" name="accreditation" id="accreditation" placeholder="Акредитационна оценка..." required><br>
                <button onclick="saveUniversity()" id="saveUniversityButton">Запис</button>
                <style>
                    #saveUniversityButton {
                        float: right;
                    }
                </style>
            </div>
        </div>
        <br>

        <label for="skills">Умения в технологии (multichoice):</label>
        <br>
        <select name="skills" id="skillsSelect" multiple>
            <option value="PHP">PHP</option>
            <option value="Laravel">Laravel</option>
            <option value="Symfony">Symfony</option>
            <option value="Zend">Zend framework</option>
            <option value="Ruby">Ruby</option>
            <option value="MySQL">MySQL</option>
            <option value="CSS3">CSS3</option>
        </select>
        <button id="addSkillButton" type="button" onclick="addSkill()">&#9998;</button>
        <div class="popup" id="skillPopup">
            <div class="popup-content">
                <h4>Popup въвеждане на ново умение в технология</h4>
                <label for="new_skill"></label>
                <input type="text" name="new_skill" id="new_skill" placeholder="Име на технологията..." required>
                <button id="saveSkillButton" onclick="saveSkill()">Запис</button>
                <style>
                    #saveSkillButton {
                        float: right;
                    }
                </style>
            </div>
        </div>
        <br> <br>
            <form method="post" action="createdbd.php"> 
    
            </form>
        <button id="saveButton" type="button" onclick="saveCV()">Запази CV</button>
        <div id="cvData"></div>
        <br>
        <button type="button" onclick="redirectToSearchPage()">Търси CV</button>
    </form>

   

    <script>
        var cvData = [];

        function addUniversity() {
            var universityPopup = document.getElementById("universityPopup");
            universityPopup.style.display = "block";
        }

        function saveUniversity() {
            var universityName = document.getElementById("new_university").value;
            var accreditation = document.getElementById("accreditation").value;
            var universitySelect = document.getElementById("universitySelect");

            if (universityName && accreditation) {
                var newOption = document.createElement("option");
                newOption.text = universityName;
                newOption.value = accreditation;
                universitySelect.add(newOption);
            }

            var universityPopup = document.getElementById("universityPopup");
            universityPopup.style.display = "none";
        }

        function addSkill() {
            var skillPopup = document.getElementById("skillPopup");
            skillPopup.style.display = "block";

        }

        function saveSkill() {
            var newSkill = document.getElementById("new_skill").value;
            var skillsSelect = document.getElementById("skillsSelect");

            if (newSkill) {
                var newOption = document.createElement("option");
                newOption.text = newSkill;
                skillsSelect.add(newOption);
            }

            var skillPopup = document.getElementById("skillPopup");
            skillPopup.style.display = "none";
        }

        function saveCV() {
            var firstName = document.querySelector('input[name="first_name"]').value;
            var middleName = document.querySelector('input[name="middle_name"]').value;
            var lastName = document.querySelector('input[name="last_name"]').value;
            var birthDate = document.querySelector('input[name="birth_date"]').value;
            var universitySelect = document.querySelector('select[name="university"]');
            var university = universitySelect.options[universitySelect.selectedIndex].text;
            var skills = Array.from(document.querySelectorAll('select[name="skills"] option:checked')).map(option => option.value).join(", ");

            if (!firstName || !middleName || !lastName || !birthDate || !university || skills.length === 0) {
                alert("Моля, попълнете всички задължителни полета.");
                return;
            }

            var cv = {
                firstName: firstName,
                middleName: middleName,
                lastName: lastName,
                birthDate: birthDate,
                university: university,
                skills: skills,
            };

            cvData.push(cv);
            displayCVData();
            saveCVDataToLocalStorage();
        }

        function displayCVData() {
            var cvDataDiv = document.getElementById("cvData");
            cvDataDiv.innerHTML = "<h3>Запазени CV-та:</h3>";

            cvData.forEach(function (cv, index) {
                cvDataDiv.innerHTML += "<p><strong>CV " + (index + 1) + ":</strong></p>";
                cvDataDiv.innerHTML += "<p>Име: " + cv.firstName + "</p>";
                cvDataDiv.innerHTML += "<p>Презиме: " + cv.middleName + "</p>";
                cvDataDiv.innerHTML += "<p>Фамилия: " + cv.lastName + "</p>";
                cvDataDiv.innerHTML += "<p>Дата на раждане: " + cv.birthDate + "</p>";
                cvDataDiv.innerHTML += "<p>Университет: " + cv.university + "</p>";
                cvDataDiv.innerHTML += "<p>Умения: " + cv.skills + "</p>";
                cvDataDiv.innerHTML += "<hr>";
            });

            displayAggregatedInfo();
        }

        function saveCVDataToLocalStorage() {
            localStorage.setItem('cvData', JSON.stringify(cvData));
        }

        function loadCVDataFromLocalStorage() {
            var loadedCVData = localStorage.getItem('cvData');
            cvData = loadedCVData ? JSON.parse(loadedCVData) : [];
        }

        function displayAggregatedInfo() {
            var ageGroupsInfo = document.getElementById("age_groups_info");
            ageGroupsInfo.innerHTML = "<h3>Възрастови групи:</h3>";

            cvData.forEach(function (cv, index) {
                ageGroupsInfo.innerHTML += "<p><strong>CV " + (index + 1) + ":</strong></p>";
              
            });
        }

        function redirectToSearchPage() {
            window.location.href = 'search.php';
        }

        loadCVDataFromLocalStorage();
        displayCVData();
    </script>
</body>
</html>
