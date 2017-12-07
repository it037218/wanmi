<?php
$imagepost   = trim(isset($_POST['imagepost'])?$_POST['imagepost']:'');
$imgName =  trim(isset($_POST['imgName'])?$_POST['imgName']:  time());
$dir = dirname(dirname(__FILE__)).'/pic.xyzs.com/tg/upload/';
$result = array('state'=>'failed','file'=>'','url'=>'','notice'=>'');
if(!empty($imagepost)){
    $imagefile = base64_decode($imagepost);
    $file = $dir.$imgName;
    if(file_exists($file)){
        $tmpArr = explode('.', $imgName);
        $imgName = time().'_'.$tmpArr[0].'.'.$tmpArr[1];
        $file = $dir.$imgName;
    }
    $rs = file_put_contents($file, $imagefile);
    if($rs){
        $result['file'] = $file;
        $result['url'] = 'http://pic.xyzs.com/tg/upload/'.$imgName;
        $result['notice'] = '上传成功';
        $result['state'] = 'success';
    }else{
        $result['notice'] = '写入失败';
    }
}else{
    $result['notice'] = '上传图片为空';
}

echo json_encode($result);







