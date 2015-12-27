<?php
/**
 * SteamOpenIDService class file.
 *
 * @author Dmitry Ananichev <a@qozz.ru>
 * @link http://github.com/Nodge/yii2-eauth/
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace app\components;

use nodge\eauth\openid\Service;
use Yii;

/**
 * Steam provider class.
 */
class SteamOpenIDService extends Service
{

    protected $name = 'steam';
    protected $title = 'Steam';
    protected $type = 'OpenID';
    protected $jsArguments = ['popup' => ['width' => 990, 'height' => 615]];

    protected $url = 'http://steamcommunity.com/openid/';

    protected function fetchAttributes()
    {
        if (isset($this->attributes['id'])) {
            $urlChunks = explode('/', $this->attributes['id']);
            if ($count = count($urlChunks)) {
                $steamID = $urlChunks[$count - 1];
                $this->attributes['steamid'] = $steamID;

                $url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . Yii::$app->params['steamAPI'] . '&steamids=' . $steamID;
                $json_object = file_get_contents($url);
                $json_decoded = json_decode($json_object);

                foreach ($json_decoded->response->players as $player) {
                    $this->attributes['username'] = $player->personaname;
                    $this->attributes['profile_url'] = $player->profileurl;
                    $this->attributes['avatar'] = $player->avatar;
                    $this->attributes['avatar_md'] = $player->avatarmedium;
                    $this->attributes['avatar_lg'] = $player->avatarfull;
                }
            }
        }
    }

}
