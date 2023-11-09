<!DOCTYPE html>
<html>
<head>
    <title>Търсене на CV</title>
</head>
<body>
   <form>
   <h1>Търсене на CV</h1>
<label for="start_date">Начална дата:</label>
<input type="date" id="start_date">
<label for="end_date">Крайна дата:</label>
<input type="date" id="end_date">
<button type="button" onclick="searchCVs()">Търси</button>
<button onclick="clearResults()">Изчисти резултатите</button>


<h2>Резултати</h2>
<div id="search_results_table"></div>
<h2>Агрегирана информация</h2>
<div id="aggregated_info"></div>
<button type="button" onclick="window.location.href='create_cv.php'">Назад</button>

</form>

<script>
var cvData = [
    { firstName: 'Иван', middleName: 'Иванов', lastName: 'Иванов', birthDate: '1990-05-15', skills: 'HTML, CSS, JavaScript' },
    { firstName: 'Петър', middleName: 'Петров', lastName: 'Петров', birthDate: '1985-08-20', skills: 'Python, Java' },
    { firstName: 'Мария', middleName: 'Иванова', lastName: 'Петкова', birthDate: '1995-03-10', skills: 'Ruby, PHP' },
    { firstName: 'Георги', middleName: 'Георгиев', lastName: 'Иванов', birthDate: '1988-11-25', skills: 'C#, SQL' },
    { firstName: 'Анна', middleName: 'Андреева', lastName: 'Петрова', birthDate: '1992-07-18', skills: 'JavaScript, React' },
];

function searchCVs() {
    var startDate = new Date(document.getElementById('start_date').value);
    var endDate = new Date(document.getElementById('end_date').value);
    var results = [];

    for (var i = 0; i < cvData.length; i++) {
        var cvBirthDate = new Date(cvData[i].birthDate);
        if (cvBirthDate >= startDate && cvBirthDate <= endDate && !isEmptyCV(cvData[i])) {
            results.push(cvData[i]);
        }
    }

    displayResults(results);
    aggregateData(results);
}

function isEmptyCV(cv) {

    return (
        !cv.firstName &&
        !cv.middleName &&
        !cv.lastName &&
        !cv.birthDate &&
        !cv.skills
    );
}



function clearResults() {
    document.getElementById('search_results').innerHTML = '';
    document.getElementById('aggregated_info').innerHTML = '';
}

function displayResults(results) {
    var resultsDiv = document.getElementById('search_results');
    resultsDiv.innerHTML = '';

    if (results.length === 0) {
        resultsDiv.innerHTML = 'Няма намерени CV-та за избрания период.';
    } else {
        var table = '<table><tr><th>Име</th><th>Презиме</th><th>Фамилия</th><th>Дата на раждане</th></tr>';
        for (var i = 0; i < results.length; i++) {
            table += '<tr><td>' + results[i].firstName + '</td><td>' + results[i].middleName + '</td><td>' + results[i].lastName + '</td><td>' + results[i].birthDate + '</td></tr>';
        }
        table += '</table>';

        resultsDiv.innerHTML = table;
    }
}


function aggregateData(results) {
    var ageGroups = {};

    for (var i = 0; i < results.length; i++) {
        var birthDate = new Date(results[i].birthDate);
        var age = calculateAge(birthDate);

        if (ageGroups[age]) {
            ageGroups[age].candidates.push(results[i]);
        } else {
            ageGroups[age] = {
                candidates: [results[i]],
                skills: [],
            };
        }

        ageGroups[age].skills = ageGroups[age].skills.concat(results[i].skills.split(','));
    }

    displayAggregatedInfo(ageGroups);
}

function calculateAge(birthDate) {
    var currentDate = new Date();
    var age = currentDate.getFullYear() - birthDate.getFullYear();
    if (currentDate.getMonth() < birthDate.getMonth() || (currentDate.getMonth() === birthDate.getMonth() && currentDate.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

function displayAggregatedInfo(ageGroups) {
    var aggregatedInfoDiv = document.getElementById('aggregated_info');
    aggregatedInfoDiv.innerHTML = '';

    for (var age in ageGroups) {
        var group = ageGroups[age];
        var ageInfo = document.createElement('div');
        ageInfo.innerHTML = '<h3>Възраст: ' + age + '</h3>';

        ageInfo.innerHTML += '<p>Брой кандидати: ' + group.candidates.length + '</p>';

        var uniqueSkills = [...new Set(group.skills)];
        ageInfo.innerHTML += '<p>Умения: ' + uniqueSkills.join(', ') + '</p>';

        aggregatedInfoDiv.appendChild(ageInfo);
    }
}


</body>
</html>
