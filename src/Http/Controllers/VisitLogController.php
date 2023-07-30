<?php

namespace SrDev93\VisitLog\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redirect;
use SrDev93\VisitLog\Models\VisitLog as VisitLogModel;

class VisitLogController extends BaseController
{

    public function __construct()
    {
        if (config('visitlog.http_authentication')) {
            $this->middleware('auth.basic');
        }
    }

    /**
     * Displays all visitor information in table.
     */
    public function index()
    {
        if (!config('visitlog.visitlog_page')) {
            abort(404);
        }

        $visitlogs = VisitLogModel::all();

        return view('visitlog::index', compact('visitlogs'));
    }


    /**
     * Deletes a record.
     *
     * @param $id
     * @param VisitLogModel $visitLog
     * @return mixed
     */
    public function destroy($id, VisitLogModel $visitLog)
    {
        $visitLog = $visitLog->find($id);

        if ($visitLog && !$visitLog->delete()) {
            return Redirect::back()->withErrors($visitLog->errors());
        }

        return Redirect::back();
    }

    /**
     * Deletes all records.
     *
     * @param VisitLogModel $visitLog
     * @return mixed
     */
    public function destroyAll(VisitLogModel $visitLog)
    {
        if (!$visitLog->truncate()) {
            return Redirect::back()->withErrors($visitLog->errors());
        }

        return Redirect::back();
    }

    /**
     * Ban A User by it's IP
     *
     * @param $id
     * @return mixed
     */
    public function banOrUnbanUserIp($id)
    {
        $visitLog = VisitLogModel::find($id);

        $visitLog->is_banned ? $visitLog->is_banned = 0 : $visitLog->is_banned = 1;
        $visitLog->save();

        return Redirect::back();
    }
}
