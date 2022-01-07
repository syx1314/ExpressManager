<?php
namespace Map;
/**
 * 百度地图api
 * Created by PhpStorm.
 * User: 12048
 * Date: 2018-01-18
 * Time: 22:18
 */
class Bmap
{
    const API_URL = "http://api.map.baidu.com/";
    private $ak = "ow64HYbf3mwNGos5H4wZ0Hc74XcGmo1S";
    public $msg;

    public function __construct()
    {

    }

    /**
     * 行政区划区域检索
     * $ret = $bmap->place_search_city('大观楼', '宜宾', '', true);
     * @param $query 查询关键词
     * @param string $region 地区，支持省、市、县
     * @param string $tag 检索分类，如美食、学校，多个以“，”隔开
     * @param bool|false $city_limit 是否限制在检索区域内
     * @return bool|mixed
     */
    public function place_search_city($query, $region = '', $tag = '', $city_limit = false)
    {
        return $this->http_get('place/v2/search', [
            'query' => $query,
            'tag' => $tag,
            'region' => $region,
            'output' => 'json',
            'city_limit' => $city_limit,
            'page_size' => 10
        ]);
    }

    /**
     * 周边检索,坐标周边指定距离检索
     * $ret = $bmap->place_search_radius('银行', '39.915,116.404');
     * @param $query 检索关键词
     * @param $location 经纬度
     * @param int $radius 检索距离（单位米）
     * @param bool|false $radius_limit 是否限制在检索距离内
     * @return bool|mixed
     */
    public function place_search_radius($query, $location, $radius = 1000, $radius_limit = false)
    {
        return $this->http_get('place/v2/search', [
            'query' => $query,
            'location' => $location,
            'radius' => $radius,
            'output' => 'json',
            'radius_limit' => $radius_limit,
            'page_size' => 10
        ]);
    }

    /**
     * 矩形区域检索
     * $ret = $bmap->place_search_bounds('银行', '39.915,116.404,39.975,116.414');
     * @param $query 检索关键词
     * @param $bounds 检索矩形区域，多组坐标间以","分隔[38.76623,116.43213,39.54321,116.46773 =>lat,lng(左下角坐标),lat,lng(右上角坐标)]
     * @return bool|mixed
     */
    public function place_search_bounds($query, $bounds)
    {
        return $this->http_get('place/v2/search', [
            'query' => $query,
            'bounds' => $bounds,
            'output' => 'json',
            'page_size' => 10
        ]);
    }

    /**
     * 地点详情检索服务
     *  $ret = $bmap->place_detail('6334ddeb6a99710bfea77863');
     * @param $uid poi的uid
     * @param int $scope 检索结果详细程度。取值为1 或空，则返回基本信息；取值为2，返回检索POI详细信息
     * @return bool|mixed
     */
    public function place_detail($uid, $scope = 2)
    {
        return $this->http_get('place/v2/detail', [
            'uid' => $uid,
            'scope' => $scope,
            'output' => 'json',
        ]);
    }

    /**
     * 地点输入提示
     * @param $query 关键词
     * @param int $region 地区 省、市、县
     * @param bool|false $city_limit 是否限制在检索区域内
     */
    public function place_suggestion($query, $region = '', $city_limit = false)
    {
        return $this->http_get('place/v2/suggestion', [
            'query' => $query,
            'region' => $region,
            'city_limit' => $city_limit,
            'output' => 'json',
            'page_size' => 10
        ]);
    }

    /**
     * 地理编码
     * @param $address 待解析的地址。最多支持84个字节。
     * @return bool|mixed
     */
    public function geocoder($address)
    {
        return $this->http_get('geocoder/v2/', [
            'address' => $address,
            'output' => 'json',
        ]);
    }

    /**
     * 逆地理编码
     * @param $location 经纬度坐标
     * @return bool|mixed
     */
    public function ungeocoder($location)
    {
        return $this->http_get('geocoder/v2/', [
            'location' => $location,
            'output' => 'json',
        ]);
    }

    /**
     * 公交路线规划
     * @param $origin 起点坐标
     * @param $destination 终点坐标
     * @return bool|mixed
     */
    public function direction_transit($origin, $destination)
    {
        return $this->http_get('direction/v2/transit', [
            'origin' => $origin,
            'destination' => $destination,
            'output' => 'json'
        ]);
    }

    /**
     * 骑行路线规划
     * @param $origin 起点坐标
     * @param $destination 终点坐标
     * @return bool|mixed
     */
    public function direction_riding($origin, $destination)
    {
        return $this->http_get('direction/v2/riding', [
            'origin' => $origin,
            'destination' => $destination,
            'output' => 'json',
        ]);
    }


    /**
     * 驾车路线规划
     * @param $origin 起点坐标
     * @param $destination 终点坐标
     * @return bool|mixed
     */
    public function direction_driving($origin, $destination)
    {
        return $this->http_get('direction/v2/driving', [
            'origin' => $origin,
            'destination' => $destination,
            'output' => 'json',
        ]);
    }

    /**
     * 不行线路规划 可多个
     * @param $origins 起点坐标 多个用“|”分割
     * @param $destinations 终点坐标 多个用“|”分割
     * @return bool|mixed
     */
    public function direction_walking($origins, $destinations)
    {
        return $this->http_get('routematrix/v2/walking', [
            'origins' => $origins,
            'destinations' => $destinations,
            'output' => 'json',
        ]);
    }

    /**
     * 利用IP获取大致位置
     * @param $ip 用户上网的IP地址
     * @return bool|mixed
     */
    public function location_ip($ip)
    {
        return $this->http_get('location/ip', [
            'ip' => $ip,
            'output' => 'json',
        ]);
    }

    /**
     * 坐标转换服务
     * @param $coords 需转换的源坐标，多组坐标以“；”分隔(经度，纬度）
     * @param $from 源坐标类型：
     * 1：GPS设备获取的角度坐标，wgs84坐标;
     * 2：GPS获取的米制坐标、sogou地图所用坐标;
     * 3：google地图、soso地图、aliyun地图、mapabc地图和amap地图所用坐标，国测局（gcj02）坐标;
     * 4：3中列表地图坐标对应的米制坐标;
     * 5：百度地图采用的经纬度坐标;
     * 6：百度地图采用的米制坐标;
     * 7：mapbar地图坐标;
     * 8：51地图坐标
     * @param $to 目标坐标类型：
     * 5：bd09ll(百度经纬度坐标),
     * 6：bd09mc(百度米制经纬度坐标);
     * @return bool|mixed
     */
    public function geoconv($coords, $from = 3, $to = 5)
    {
        return $this->http_get('geoconv/v1/', [
            'coords' => $coords,
            'from' => $from,
            'to' => $to,
            'output' => 'json',
        ]);
    }

    /**
     * get请求
     * @param $methond
     * @param $param
     * @return bool|mixed
     */
    private function http_get($methond, $param)
    {
        $param['ak'] = $this->ak;
        $url = self::API_URL . $methond . "?" . http_build_query($param);
        $result = json_decode(file_get_contents($url), true);
        if ($result['status'] == 0) {
            return $result;
        } else {
//            dump($result);
            return false;
        }
    }
}