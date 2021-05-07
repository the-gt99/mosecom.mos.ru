<?php

$salt = sha1("sdad");
$id = "8765";
$id = strval($id);
$newSha1Id = sha1( $salt . "_" . $id);
$idLen = mb_strlen($id);

$start = "";
$center1 = "";
$center2 = "";
$end = "";

if($idLen >= 4)
{
    for($i=0,$j=1,$k=2,$l=3; $i<$idLen && $j<$idLen && $k<$idLen && $l<$idLen; $i+=4,$j+=4,$k+=4,$l+=4)
    {
        $start .= $id[$i];
        $center1 .= $id[$j];
        $center2 .= $id[$k];
        $end .= $id[$l];
    }
}
else if($idLen >= 3)
{
    for($i=0,$j=1,$k=2; $i<$idLen && $j<$idLen && $k<$idLen; $i+=3,$j+=3,$k+=3)
    {
        $end .= $id[$i];
        $center1 .= $id[$j];
        $start .= $id[$k];
    }
}
else if($idLen >= 2)
{
    for($i=0,$j=1; $i<$idLen && $j<$idLen; $i+=2,$j+=2)
    {
        $center1 .= $id[$i];
        $end .= $id[$j];
    }
}
else
{
    if($idLen % 2 == 0)
    {
        if( date("d",time()) % 2 == 0)
        {
            $start = $id;
        }
        else
        {
            $center1 = $id;
        }
    }
    else if($idLen % 2 != 0)
    {
        if( date("d",time()) % 2 == 0)
        {
            $center2 = $id;
        }
        else
        {
            $end = $id;
        }
    }
}

$startSha1 = substr($newSha1Id, 0, 10);
$center1Sha1 = substr($newSha1Id, 10, 10);
$center2Sha1 = substr($newSha1Id, 20, 10);
$endSha1 = substr($newSha1Id, 30, 10);

if($steartLen = mb_strlen($start) > 0)
{
    $startSha1 = $start . substr($startSha1, $steartLen);
}

if($center1Len = mb_strlen($center1) > 0)
{
    $center1Sha1 = $center1 . substr($center1Sha1, $center1Len);
}

if($center2Len = mb_strlen($center2) > 0)
{
    $center2Sha1 = $center2 . substr($center2Sha1, $center2Len);
}

if($endLen = mb_strlen($end) > 0)
{
    $endSha1 = $end . substr($endSha1, $endLen);
}

$endSha1 = substr($endSha1, 0, (mb_strlen(strval($idLen)) + 1) * -1) . "a". $idLen;

$sha1 = $startSha1 . $center1Sha1 . $center2Sha1 . $endSha1;

echo($sha1);
echo(" -  " . getIdBySha1Id($sha1,$salt));

function getIdBySha1Id($sha1, $salt = null)
{
    $startSha1 = substr($sha1, 0, 10);
    $center1Sha1 = substr($sha1, 10, 10);
    $center2Sha1 = substr($sha1, 20, 10);
    $endSha1 = substr($sha1, 30, 10);
    $id = "";

    $idLen = getIdLenBySha1($sha1);
    

    if($idLen >= 4) {
        for($i=0,$j=0; $j<$idLen; $i++,$j+=4)
        {
                $id .= $startSha1[$i];
                $id .= $center1Sha1[$i];
                $id .= $center2Sha1[$i];
                $id .= $endSha1[$i];
        }
    }
    else if($idLen >= 3) {
        for($i=0,$j=0; $j<$idLen; $i++,$j+=3)
        {
            $id .= $endSha1[$i];
            $id .= $center1Sha1[$i];
            $id .= $startSha1[$i];
        }
    }
    else if($idLen >= 2) {
        for($i=0,$j=0; $j<$idLen; $i++,$j+=3)
        {
            $id .= $center1Sha1[$i];
            $id .= $endSha1[$i];
        }
    }
    else
    {
        if($idLen % 2 == 0)
        {
            if( date("d",time()) % 2 == 0)
                $id = $startSha1[0];
            else
                $id = $center1Sha1[0];
            
        }
        else if($idLen % 2 != 0)
        {
            if( date("d",time()) % 2 == 0)
                $id = $center2Sha1[0];
            else
                $id = $endSha1[0];
        }
    }

    return $id;
}

function getIdLenBySha1($sha1)
{
    $num = "";

    for($i=mb_strlen($sha1) - 1; $i>0; $i--)
    {
        if($sha1[$i] != "a")
            $num .= $sha1[$i];
        else
            break;
    }

    return intval($num);
}