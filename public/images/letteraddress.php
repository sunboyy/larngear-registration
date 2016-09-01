<?php

  require('../backends/env.php');
  require('../backends/connect.php');
  require('../backends/token.php');

  $token = new Token(getenv('TOKEN'));



  if(isset($_GET['pid']) && isset($_GET['token']) && $token->check($_GET['pid'], $_GET['token'])) {
    $code = $conn->query("SELECT prefix,name,surname,code,address,province,postcode FROM student WHERE personalID=\"{$_GET['pid']}\"");
    $data = $code->fetch_assoc();
    $code = is_null($data['code']) ? 'XXXXXX' : $data['code'];

    switch($data['prefix']) {
      case 'master' : $data['prefix'] = 'เด็กชาย'; break;
      case 'miss' : $data['prefix'] = 'เด็กหญิง'; break;
      case 'mr' : $data['prefix'] = 'นาย'; break;
      case 'mrs' : $data['prefix'] = 'นางสาว'; break;
    }

    $scale = 1;
    if(isset($_GET['download'])) {
      header('Content-Disposition: Attachment;filename='.$code.'.png');
      $scale = 2;
    }

    header('Content-Type: image/png');

    $img = imagecreate(630 * $scale, 437 * $scale);

    $whiteCol = imagecolorallocate($img, 255, 255, 255);
    $blackCol = imagecolorallocate($img, 0, 0, 0);

    imagerectangle ( $img , 50 * $scale, 50 * $scale, 580 * $scale, 387 * $scale, $blackCol );
    imagerectangle ( $img , 50 * $scale, 50 * $scale, 580 * $scale, 100 * $scale, $blackCol );
    imagerectangle ( $img , 50 * $scale, 50 * $scale, 580 * $scale, 307 * $scale, $blackCol );

    imagerectangle ( $img , 50 * $scale, 100 * $scale, 315 * $scale, 307 * $scale, $blackCol );

    $font = './th-sarabun-new.ttf';
    $subFont = './angsana-new.ttf';
    imagettftext ( $img, 20 * $scale, 0, 180 * $scale, 82 * $scale, $blackCol, $font, "ใบปะหน้าซอง ค่ายลานเกียร์ครั้งที่ 16");

    $addressMe = $data['prefix']." ".$data['name']." ".$data['surname']."\n";
    $data['address'] = implode(" ", explode("\n", $data['address']));
    $addressMe .= implode("\n",str_split($data['address'], 99))."\n";
    $addressMe .= $data['province']."\n";
    $addressMe .= $data['postcode']."\n";

    $addressSent = "กิจการนิสิต คณะวิศวกรรมศาสตร์\n";
    $addressSent .= "จุฬาลงกรณ์มหาวิทยาลัย\n";
    $addressSent .= "254 ถนนพญาไท แขวงวังใหม่ เขตปทุมวัน\nกรุงเทพมหานคร\n";
    $addressSent .= "10330\n";

    if(isset($_GET['download'])) {
      imagettftext ( $img, 14 * $scale, 0, 60 * $scale, 125 * $scale, $blackCol, $subFont, "ที่อยู่ผู้ส่ง :");
      imagettftext ( $img, 12 * $scale, 0, 60 * $scale, 145 * $scale, $blackCol, $subFont, $addressMe);
      imagettftext ( $img, 14 * $scale, 0, 325 * $scale, 125 * $scale, $blackCol, $subFont, "ที่อยู่ผู้รับ :");
      imagettftext ( $img, 12 * $scale, 0, 325 * $scale, 145 * $scale, $blackCol, $subFont, $addressSent);
    } else {
      imagettftext ( $img, 12 * $scale, 0, 60 * $scale, 125 * $scale, $blackCol, $subFont, "..........\n..........");
      imagettftext ( $img, 12 * $scale, 0, 325 * $scale, 125 * $scale, $blackCol, $subFont, "..........\n.........");
    }

    imagettftext ( $img, 50 * $scale, 0, 220 * $scale, 365 * $scale, $blackCol, $font, $code);

    imagepng($img);
    imagedestroy($img);
  } else {
    echo 'Permission Denied';
  }

?>
