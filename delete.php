<?php

$data = json_decode(file_get_contents('./pptxtags.json'), true);
unset($data['files'][$_POST['id']]);
file_put_contents('./pptxtags.json', json_encode($data));

unlink('./uploads/'.$_POST['id']); // delete pptx file from dir

$imgName = str_replace("pptx","png",$_POST['id']);

unlink('./uploads/'.$imgName); // delete image file from dir

?>