<?php

namespace SrDev93\VisitLog;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use SrDev93\VisitLog\Models\VisitLog as VisitLogModel;

class VisitLog
{
    protected $browser = null;
    protected $cachePrefix = 'visitlog';
    protected $freegeoipUrl = 'http://ip-api.com/json';

    /**
     * VisitLog constructor.
     * @param Browser $browser
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * Saves visit info into db.
     *
     * @return mixed
     */
    public function save($type , $id)
    {
        $data = $this->getData($type , $id);

        if (config('visitlog.unique')) {
            $model = VisitLogModel::where('ip', $this->getUserIP())->where('visitable_type', $type)->where('visitable_id', $id)->whereDate('created_at', Carbon::today())->first();

            if ($model) {
                // update record of same IP eg new visit times, etc
                $model->touch();
                return $model->update($data);
            }
        }

        $item = $type::find($id);
        if ($item){
            $item->timestamps = false;
            $item->increment('visit');
            $item->save();
        }

//        return VisitLogModel::create($data);\
        return $item->visits()->save(new VisitLogModel($data));
    }

    /**
     * Returns all saved information.
     */
    public function all()
    {
        return VisitLogModel::all();
    }

    /**
     * Get's IP address of visitor.
     *
     * @return mixed
     */
    protected function getUserIP()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip ?: '0.0.0.0';
    }

    /**
     * Gets OS information.
     *
     * @return string
     */
    protected function getBrowserInfo()
    {
        $browser = $this->browser->getBrowser() ?: 'Other';
        $browserVersion = $this->browser->getVersion();

        if (trim($browserVersion)) {
            return $browser . ' (' . $browserVersion . ')';
        }

        return $browser;
    }

    /**
     * Returns visit data to be saved in db.
     *
     * @return array
     */
    protected function getData($type , $id)
    {
        $ip = $this->getUserIP();
        $cacheKey = $this->cachePrefix . $ip;
        $url = $this->freegeoipUrl . '/' . $ip;

        // basic info
        $data = [
            'ip' => $ip,
            'browser' => $this->getBrowserInfo(),
            'os' => $this->browser->getPlatform() ?: 'Unknown',
            'visitable_type' => $type,
            'visitable_id' => $id,
        ];

        // info from http://freegeoip.net
        if (config('visitlog.iptolocation')) {
            if (config('visitlog.cache')) {
                $freegeoipData = unserialize(Cache::get($cacheKey));

                if (!$freegeoipData) {
                    $freegeoipData = @json_decode(file_get_contents($url), true);

                    if ($freegeoipData) {
                        Cache::forever($cacheKey, serialize($freegeoipData));
                    }
                }
            } else {
                $freegeoipData = @json_decode(file_get_contents($url), true);
            }

            if ($freegeoipData and $freegeoipData['status'] != 'fail') {
                $data = array_merge($data, $freegeoipData);
            }
        }

        $userData = $this->getUser();

        if ($userData) {
            $data = array_merge($data, $userData);
        }

        return $data;
    }

    /**
     * Gets logged user info.
     */
    protected function getUser()
    {
        $userData = [];

        if (config('visitlog.log_user')) {
            $name = '';
            $userNameFields = config('visitlog.user_name_fields');

            if (Auth::check()) {
                $user = Auth::user();
                $userData['user_id'] = $user->id;

                if (is_array($userNameFields)) {
                    foreach ($userNameFields as $userNameField) {
                        $name .= @$user->$userNameField . ' ';
                    }

                    $name = rtrim($name);
                } else {
                    $name = @$user->$userNameFields;
                }

                $userData['user_name'] = $name;
            } else {
                $userData['user_id'] = 0;
                $userData['user_name'] = 'Guest';
            }
        }

        return $userData;
    }
}
