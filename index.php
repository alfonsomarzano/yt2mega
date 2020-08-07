<?php
session_start();
if ($_POST["user"] == "sampras" && $_POST["pass"] == "ciao") {

    $_SESSION["logged"] = "good";
}

if (!isset($_SESSION["logged"])) {
    header("location: /login.php");
}
?>
<html>

<head>
    <title>Ty2Mega</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    </style>
</head>

<body>
    <div id="wrapper">
        <div>
            Link di Youtube:
            <input type="text" class="form-control" id="txtLink" placeholder="Es. https://www.youtube.com/watch?v=LeKySdZfCy0" />
            <div id="result" style="display:none">
                <table style="margin-top:30px">
                    <tr>
                        <td>
                            <img class="thumb" style="width:160px" />
                        </td>
                        <td>
                            <h4 class="title"></h4>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <p class="description"></p>
                        </td>
                    </tr>
                </table>
                <div id="formMeta" style="display:none">
                    Titolo:
                    <input id="txtTitle" type="text" class="form-control" />
                    Artista:
                    <input id="txtArtist" type="text" class="form-control" />
                    Album:
                    <input id="txtAlbum" type="text" class="form-control" /></div>
                <span id="cmdDownload" class="float-right btn btn-primary" style="margin-top:10px">Download</span>
            </div>
            <div id="debugLog"></div>
        </div>
    </div>

    <script>
        function isValidURL(str) {
            var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
                '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
            return !!pattern.test(str);
        }

        $("#cmdDownload").on("click", function(e) {
            var d = {};
            d.op = "download"
            d.url = $("#txtLink").val();
            d.title = $("#txtTitle").val();
            d.artist = $("#txtArtist").val();
            d.album = $("#txtAlbum").val();

            $.ajax({
                url: "api.php",
                type: "POST",
                data: d,
                success: function(res) {
                    if (res.esit) {
                        $(e.target).removeClass("btn-primary");
                        $(e.target).removeClass("btn-danger");
                        $(e.target).addClass("btn-success");
                    } else {
                        $(e.target).removeClass("btn-primary");
                        $(e.target).addClass("btn-danger");
                        $(e.target).removeClass("btn-success");
                    }
                    $('#debugLog').html(res.content);
                }
            });
        });

        $("#txtLink").on("input", function(e) {

            $(e.target).parent().find("#result").slideUp();
            $(e.target).parent().find("#formMeta").slideUp();


            var link = $("#txtLink").val();
            if (!isValidURL(link)) {
                $(e.target).parent().find(".title").html("Link non valido");
                $(e.target).parent().find("#result").slideDown();
                return;
            }

            if (!link.startsWith("https://www.youtube.com/watch")) {
                $(e.target).parent().find(".title").html("Link non valido");
                $(e.target).parent().find("#result").slideDown();
                return;
            }

            var d = {};
            d.url = link;
            d.op = "meta";
            $.ajax({
                url: "api.php",
                type: "POST",
                data: d,
                success: function(res) {
                    if (res.esit) {
                        var c = res.content;
                        $(e.target).parent().find(".title").html(c.title);
                        $(e.target).parent().find(".description").html(c.description);
                        $(e.target).parent().find(".thumb").attr("src", c.thumb);
                        $(e.target).parent().find("#formMeta").show();
                        $(e.target).parent().find("#result").slideDown();
                    }
                }
            });
        });
    </script>
</body>

</html>