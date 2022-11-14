<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


<script>

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;
    xhr.addEventListener("loadend", function () {
        try {
            var data = JSON.parse(xhr.responseText);
            if (data.hasOwnProperty('reload')) {
                if (data["reload"] == true) {
                    window.location.replace(window.location.href.replace(/[&?]bm-verify=.*/, ""));
                }
            } else if (data.hasOwnProperty('location')) {
                window.location.replace(data["location"]);
            } else {
                window.location.reload();
            }
        } catch (e) {
            var data = {}
            window.location.reload();
        }

    });
    xhr.open("POST", "/_sec/verify?provider=interstitial", false);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify({
        "bm-verify": "AAQAAAAG/////57GPxUd8F1h/k9jEQGci1HKSlGX8+31lhR3QTgGX97x8TM1JKqecC4P66/H6eVLDzs9G7Oxj+xCa8974/wkf4R7exjoDdQzXCXkC3Ux6c8NhbpHsDbjp7QpTnqciwH8fqu04z/I94EW8bSXKzCg4o7H7TnH7tWyHh8ldawpOZYvZRD7NTQJK/dyG0DuEL13wQGd0sREq0FdlFqYv0e3+qdtvt0LgeFv1KugBkL+852S2JkUhlx9WJd4sLcDYJoSzOiVsVZPAL/1vT1xhbhJUEiX6YdjJtDivBKinglq65olakkImWiFUKOciWxZWwkOXKNwY6xTSks8KQej2rT5Ve3BSTyVzUdevoulV+DzL8UgBf3CrCWrF9S6PaCsySHiTuGJNb/032ATyAboEpxgu9qBpnQO8+UmMEzm3A==",
        "pow": j
    }));

</script>

</body>
</html>
