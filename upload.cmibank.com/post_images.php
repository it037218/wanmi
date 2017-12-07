<?php
function insertImg($img , $ppid = 0){
    //$ci = & get_instance();
    $imagepost = base64_encode(file_get_contents($img));
    //$ci->load->library('curl');
    $url  = 'http://image.xyzs.com/imageup.php';
//    $w_h_arr[1]['w'] = 32;
//	$w_h_arr[1]['h'] = 32;
//	$w_h_arr[2]['w'] = 75;
//	$w_h_arr[2]['h'] = 75;
//	$w_h_arr[3]['w'] = 175;
//	$w_h_arr[3]['h'] = 175;
	$w_h_arr = array();
    $para = array(
        'uid'       => $ppid,
        'imagepost' => $imagepost,
        'method'    => 'post',
        'jsonwh'    => json_encode($w_h_arr),
        'folder'    => "app",
        'is_app_logo' => 1
    );
    $r = file_content_post($url , $para);
    echo $r; exit;
    $re_arr = json_decode($r , true);
    print_r($re_arr);
}

function file_content_post($url,$data){
    $post_data = http_build_query($data);
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt($ch, CURLOPT_URL,$url);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
    ob_start();  
    curl_exec($ch);  
    $result = ob_get_contents() ;
    ob_end_clean();  
    return $result;
}

insertImg("138029045856557_0.jpg" , 1);

?>