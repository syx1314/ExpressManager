<?php

namespace Util;

/**
 * 随机生成类
 */
class Mapmath
{
    /**
     * 判断点是否在多边形内部
     
     *
     * $polygon = array(
     * 0 => array(经度, 纬度),
     * 1 => array(18.5, 15.6),
     * 2 => array(18.5, 20.6),
     * 3 => array(12.5, 20.6)
     * );
     * IsPtInPoly(15.0, 18.0, $polygon);
     **/
    public function is_pt_in_poly($ALon, $ALat, $APoints)
    {
        $iSum = 0;
        if (count($APoints) < 3) {
            return false;
        }
        $iCount = count($APoints);
        for ($i = 0; $i < $iCount; $i++) {
            if ($i == $iCount - 1) {
                $dLon1 = $APoints[$i][0];
                $dLat1 = $APoints[$i][1];
                $dLon2 = $APoints[0][0];
                $dLat2 = $APoints[0][1];
            } else {
                $dLon1 = $APoints[$i][0];
                $dLat1 = $APoints[$i][1];
                $dLon2 = $APoints[$i + 1][0];
                $dLat2 = $APoints[$i + 1][1];
            }
            //以下语句判断A点是否在边的两端点的水平平行线之间，在则可能有交点，开始判断交点是否在左射线上
            if ((($ALat >= $dLat1) && ($ALat < $dLat2)) || (($ALat >= $dLat2) && ($ALat < $dLat1))) {
                if (abs($dLat1 - $dLat2) > 0) {
                    //得到 A点向左射线与边的交点的x坐标：
                    $dLon = $dLon1 - (($dLon1 - $dLon2) * ($dLat1 - $ALat)) / ($dLat1 - $dLat2);
                    if ($dLon < $ALon)
                        $iSum++;
                }
            }
        }
        if ($iSum % 2 != 0) {
            return true;
        }
        return false;
    }

}
