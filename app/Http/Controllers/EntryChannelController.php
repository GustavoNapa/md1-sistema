<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntryChannel;

class EntryChannelController extends Controller
{
    public function index()
    {
        $entryChannels = EntryChannel::all();
        return view('entry_channels.index', compact('entryChannels'));
    }

    public function create()
    {
        return view('entry_channels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        EntryChannel::create($request->all());

        return redirect()->route('entry_channels.index')->with('success', 'Canal de entrada criado com sucesso!');
    }

    public function edit(EntryChannel $entryChannel)
    {
        return view('entry_channels.edit', compact('entryChannel'));
    }

    public function update(Request $request, EntryChannel $entryChannel)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $entryChannel->update($request->all());

        return redirect()->route('entry_channels.index')->with('success', 'Canal de entrada atualizado com sucesso!');
    }

    public function destroy(EntryChannel $entryChannel)
    {
        $entryChannel->delete();

        return redirect()->route('entry_channels.index')->with('success', 'Canal de entrada excluído com sucesso!');
    }
}
