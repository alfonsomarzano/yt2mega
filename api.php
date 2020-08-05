<?php
header('Content-type: application/json');
try {
    switch ($_POST["op"]) {
        case "meta":
            $res["content"] = youtube_info($_POST["url"]);
            break;
        case "download":
            start_download($_POST["url"], $_POST["title"], $_POST["artist"], $_POST["album"]);
            break;
        default:
            throw new Exception("Operation not managed");
    }
    $res["esit"] = true;
    $res["message"] = "";
} catch (Exception $e) {
    $res["esit"] = false;
    $res["message"] = $e->getMessage();
}
echo json_encode($res);

function youtube_info($url)
{
    $output = get_youtube_info_command($url);
    $output = explode("\n", $output);
    $infos["title"] = $output[0];
    $infos["thumb"] = $output[1];
    $output = array_slice($output, 2);
    $infos["description"] = implode("\n", $output);
    $infos["description"] = nl2br($infos["description"]);
    return $infos;
}

function get_youtube_info_command($url)
{
    return shell_exec("youtube-dl --skip-download --get-title --get-description --get-thumbnail " . $url);
}

function start_download($url, $title, $artist, $album)
{
    if (!empty($artist) && !empty($album)) {
        $path = "downloads/" . $artist . "/" . $album;
    } else {
        $path = "downloads/various";
    }
    if (empty($title)) {
        $title = youtube_info($url)["title"];
    }
    $cmd = 'youtube-dl -x --audio-format "mp3" --postprocessor-args "-metadata album=\"' . $album . '\" -metadata artist=\"' . $artist . '\"" --output "' . $path . '/' . $title . '.%(ext)s" ' . $url;

    $out = shell_exec($cmd);
    if (strpos($out, "ERROR")) {
        throw new Exception("Error while downloading");
    }
}
