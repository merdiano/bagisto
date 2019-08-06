<?php
/**
 * Created by PhpStorm.
 * User: merdan
 * Date: 7/26/2019
 * Time: 18:09
 */

namespace Payment\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
}