<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PluginController extends Controller
{
    // the uuid and key must be same
    protected $pluginsAvailable = [
        'chatgpt' => [
            'uuid' => 'chatgpt',
            'name' => 'ChatGPT',
            'main_field_label' => 'API Key',
            'extra_fields' => [
                'dataset' => ['label' => 'Dataset Teks', 'type' => 'textarea'],
                'command_start' => ['label' => 'Command Start(Optional)', 'type' => 'text'],
                'command_stop' => ['label' => 'Command Stop(Optional)', 'type' => 'text'],
            ],
        ],
        'gemini' => [
            'uuid' => 'gemini',
            'name' => 'GeminiAI',
            'main_field_label' => 'API Key',
            'extra_fields' => [
                'dataset' => ['label' => 'Dataset Teks', 'type' => 'textarea'],
                'command_start' => ['label' => 'Command Start(Optional)', 'type' => 'text'],
                'command_stop' => ['label' => 'Command Stop(Optional)', 'type' => 'text'],
            ],
        ],
        'claude' => [
            'uuid' => 'claude',
            'name' => 'Claude AI',
            'main_field_label' => 'API Key',
            'extra_fields' => [
                'dataset' => ['label' => 'Dataset Teks', 'type' => 'textarea'],
                'command_start' => ['label' => 'Command Start(Optional)', 'type' => 'text'],
                'command_stop' => ['label' => 'Command Stop(Optional)', 'type' => 'text'],
            ],
        ],
        'spreadsheet' => [
            'uuid' => 'spreadsheet',
            'name' => 'Spreadsheet',
            'main_field_label' => 'Sheet URL',
            'extra_fields' => [
                'googlekey' => ['label' => 'GOOGLE KEY', 'type' => 'text'],
            ]
        ],
        'sticker' => [
            'uuid' => 'sticker',
            'name' => 'StickerBot',
            'main_field_label' => null,
            'extra_fields' => [
                'command' => ['label' => 'Command Start(Optional)', 'type' => 'text'],
            ]
        ],
    ];


    public function index()
    {
        $pluginsAvailable = $this->pluginsAvailable;
        $plugins = Plugin::where('device_id', Session::get('selectedDevice'))->get();

        return view('pages.plugins', compact('pluginsAvailable', 'plugins'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'plugin_type' => 'required|string',
            'main_data' => 'nullable|string',
            'extra_data' => 'nullable|array',
            'typeBot' => 'required|in:all,group,personal',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $deviceId = $request->device ?? Session::get('selectedDevice');
        $uuid = $this->pluginsAvailable[$data['plugin_type']]['uuid'];

        $existing = Plugin::where('device_id', $deviceId)->where('uuid', $uuid)->first();
        if ($existing) {
            return back()->with('alert', ['type' => 'danger', 'msg' => 'Same plugin already exists']);
        }

        $aiPluginUUIDs = [
            // $this->pluginsAvailable['chatgpt']['uuid'],
            // $this->pluginsAvailable['gemini']['uuid'],
            // $this->pluginsAvailable['claude']['uuid'],
        ];

        if (in_array($uuid, $aiPluginUUIDs) && $request->has('is_active')) {
            Plugin::where('device_id', $deviceId)
                ->whereIn('uuid', $aiPluginUUIDs)
                ->update(['is_active' => false]);
        }

        Plugin::create([
            'uuid' => $this->pluginsAvailable[$data['plugin_type']]['uuid'],
            'device_id' => $request->device,
            'name' => $this->pluginsAvailable[$data['plugin_type']]['name'],
            'main_data' => $data['main_data'] ?? "-",
            'extra_data' => $data['extra_data'] ?? null,
            'typeBot' => $data['typeBot'],
            'description' => $data['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Plugin berhasil ditambahkan.');
    }

    public function editData(Plugin $plugin)
    {

        return response()->json([
            'id' => $plugin->id,
            'plugin_type' => $plugin->uuid,
            'main_data' => $plugin->main_data,
            'extra_data' => $plugin->extra_data,
            'typeBot' => $plugin->typeBot,
            'description' => $plugin->description,
            'is_active' => $plugin->is_active,
        ]);
    }


    public function update(Request $request, Plugin $plugin)
    {
        $data = $request->validate([
            'main_data' => 'nullable|string',
            'extra_data' => 'nullable|array',
            'typeBot' => 'required|in:all,group,personal',
            'is_active' => 'nullable|boolean',
        ]);

        // Cek apakah plugin ini termasuk tipe AI
        $aiPluginUUIDs = [
            // $this->pluginsAvailable['chatgpt']['uuid'],
            // $this->pluginsAvailable['gemini']['uuid'],
            // $this->pluginsAvailable['claude']['uuid'],
        ];

        if (in_array($plugin->uuid, $aiPluginUUIDs) && $request->has('is_active')) {
            Plugin::where('device_id', $plugin->device_id)
                ->whereIn('uuid', $aiPluginUUIDs)
                ->update(['is_active' => false]);
        }

        $plugin->update([
            'main_data' => $data['main_data'] ?? "-",
            'extra_data' => $data['extra_data'] ?? null,
            'typeBot' => $data['typeBot'],
            'is_active' => $request->has('is_active'),
        ]);

        clearCacheNode();

        return back()->with('success', 'Plugin berhasil diperbarui.');
    }
}
