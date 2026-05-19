<?php

include "../controllers/ContentController.php";

?>

<!DOCTYPE html>

<html>

<head>

    <title>FTP Server</title>

    <style>

        body{
            font-family: Arial;
            padding:20px;
        }

        .card{
            border:1px solid gray;
            padding:10px;
            margin:10px;
        }

    </style>

</head>

<body>

<h1>Midnight Media</h1>

<hr>

<h2>Search Content</h2>

<input
type="text"
id="search"
placeholder="Search Here">

<button onclick="searchData()">
    Search
</button>

<div id="searchResult"></div>

<hr>

<h2>All Contents</h2>

<?php

while($row = mysqli_fetch_assoc($result))
{

?>

<div class="card">

    <h3>
        <?php echo $row['title']; ?>
    </h3>

    <p>
        <?php echo $row['description']; ?>
    </p>

    <a
    href="../public/uploads/contents/<?php echo $row['file_path']; ?>"
    download>

        Download

    </a>

</div>

<?php

}

?>

<hr>

<h2>Request Content</h2>

<form id="requestForm">

<input
type="text"
id="title"
placeholder="Content Title">

<br><br>

<select id="category">

<option>Movies</option>
<option>Games</option>
<option>Software</option>

</select>

<br><br>

<textarea
id="message"
placeholder="Message"></textarea>

<br><br>

<button
type="button"
onclick="sendRequest()">

Submit Request

</button>

</form>

<p id="msg"></p>

<script>

function searchData()
{
    let q =
    document.getElementById("search").value;

    fetch("../api/search.php?q=" + q)

    .then(res => res.json())

    .then(data => {

        let output = "";

        data.forEach(item => {

            output += `

            <div class='card'>

                <h3>${item.title}</h3>

                <p>${item.description}</p>

            </div>

            `;

        });

        document.getElementById(
        "searchResult"
        ).innerHTML = output;

    });
}



function sendRequest()
{
    let formData = new FormData();

    formData.append(
        "title",
        document.getElementById("title").value
    );

    formData.append(
        "category",
        document.getElementById("category").value
    );

    formData.append(
        "message",
        document.getElementById("message").value
    );

    fetch(
        "../api/addRequest.php",
        {
            method:"POST",
            body:formData
        }
    )

    .then(res => res.json())

    .then(data => {

        document.getElementById(
        "msg"
        ).innerHTML = data.message;

    });
}

</script>

</body>
</html>