<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;
use App\Models\Campaign;


class BlastController extends Controller
{
    public function index(Campaign $campaign)
    {
       $blasts = $campaign->blasts()->orderBy('updated_at', 'desc')->paginate(20);
       $campaign_name = $campaign->name;
        return view('theme::pages.campaign.datablasts', compact('blasts', 'campaign_name'));
    }
}
?>