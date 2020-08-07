<?php
header('Content-type: application/json');
try {
    switch ($_REQUEST["op"]) {
        case "meta":
            $res["content"] = youtube_info($_REQUEST["url"]);
            break;
        case "download":
            $res["content"] = start_download($_REQUEST["url"], $_REQUEST["title"], $_REQUEST["artist"], $_REQUEST["album"]);
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
    cleanURL($url);
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
    cleanURL($url);
    if (!empty($artist) && !empty($album)) {
        $path = "downloads/" . $artist . "/" . $album;
    } else {
        $path = "downloads/various";
    }
    if (empty($title)) {
        $title = youtube_info($url)["title"];
    }
    $cmd = 'youtube-dl -x --audio-format "mp3" --postprocessor-args "-metadata album=\"' . $album . '\" -metadata artist=\"' . $artist . '\" -metadata title=\"' . $title . '\"" --output "' . $path . '/' . $title . '.%(ext)s" ' . $url;
    //echo $cmd;
    return nl2br($cmd . "\n\n\n" . shell_exec($cmd));
    // $out = shell_exec($cmd);
    // if (strpos($out, "ERROR")) {
    //     throw new Exception("Error while downloading");
    // }
}

function cleanURL(&$url)
{
    $qs = explode('?', $url)[1];
    $params = explode("&", $qs);
    foreach ($params as $p) {
        if (substr($p, 0, 2) == "v=") {
            $url = 'https://youtube.com/watch?' . $p;
        }
        break;
    }
}
