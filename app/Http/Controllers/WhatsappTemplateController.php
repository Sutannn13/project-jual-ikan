<?php

namespace App\Http\Controllers;

use App\Models\WhatsappTemplate;
use App\Models\WhatsappBlastLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappTemplateController extends Controller
{
    public function index()
    {
        $templates = WhatsappTemplate::with('creator')
            ->latest()
            ->paginate(20);

        $blastLogs = WhatsappBlastLog::with(['template', 'sender'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.whatsapp-templates.index', compact('templates', 'blastLogs'));
    }

    public function create()
    {
        return view('admin.whatsapp-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'deskripsi'  => 'nullable|string|max:500',
            'pesan'      => 'required|string',
            'is_active'  => 'sometimes|boolean',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['created_by'] = Auth::id();

        WhatsappTemplate::create($validated);

        return redirect()->route('admin.whatsapp-templates.index')
            ->with('success', 'Template pesan berhasil dibuat!');
    }

    public function show(WhatsappTemplate $whatsappTemplate)
    {
        return redirect()->route('admin.whatsapp-templates.edit', $whatsappTemplate);
    }

    public function edit(WhatsappTemplate $whatsappTemplate)
    {
        $template = $whatsappTemplate;
        return view('admin.whatsapp-templates.edit', compact('template'));
    }

    public function update(Request $request, WhatsappTemplate $whatsappTemplate)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'pesan'     => 'required|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $whatsappTemplate->update($validated);

        return redirect()->route('admin.whatsapp-templates.index')
            ->with('success', 'Template pesan berhasil diperbarui!');
    }

    public function destroy(WhatsappTemplate $whatsappTemplate)
    {
        $whatsappTemplate->delete();
        return redirect()->route('admin.whatsapp-templates.index')
            ->with('success', 'Template berhasil dihapus!');
    }

    /**
     * Preview rendered message with sample data
     */
    public function preview(WhatsappTemplate $whatsappTemplate)
    {
        $sample = [
            '{nama}'   => 'Bapak/Ibu Pelanggan',
            '{produk}' => 'Ikan Nila Premium',
            '{harga}'  => 'Rp 35.000/Kg',
            '{stok}'   => '50 Kg',
        ];

        $rendered = $whatsappTemplate->renderMessage($sample);

        return response()->json(['pesan' => $rendered]);
    }

    /**
     * Show blast form
     */
    public function blastForm(WhatsappTemplate $whatsappTemplate)
    {
        $template = $whatsappTemplate;
        return view('admin.whatsapp-templates.blast-form', compact('template'));
    }

    /**
     * Generate WhatsApp blast: create wa.me links for all customers
     * (Opens WhatsApp Web for each number — real blast requires WA Business API)
     */
    public function blast(Request $request, WhatsappTemplate $whatsappTemplate)
    {
        $request->validate([
            'target'        => 'required|in:all,with_orders,custom',
            'custom_phones' => 'nullable|string',
            'var_produk'    => 'nullable|string|max:255',
            'var_harga'     => 'nullable|string|max:255',
            'var_stok'      => 'nullable|string|max:255',
        ]);

        // Get users with phone numbers
        $usersQuery = User::where('role', 'customer')->whereNotNull('no_hp');
        if ($request->target === 'with_orders') {
            $usersQuery->whereHas('orders', fn($q) => $q->where('status', 'completed'));
        }

        if ($request->target === 'custom') {
            // Parse custom phones — comma or newline separated
            $rawPhones = array_filter(array_map('trim', preg_split('/[\n,]+/', $request->custom_phones ?? '')));
            $users = collect($rawPhones)->map(fn($p) => (object) ['name' => $p, 'no_hp' => $p]);
        } else {
            $users = $usersQuery->get();
        }

        $encodedMsg = function(string $name) use ($whatsappTemplate, $request) {
            $variables = [
                '{nama}'   => $name,
                '{produk}' => $request->input('var_produk', ''),
                '{harga}'  => $request->input('var_harga', ''),
                '{stok}'   => $request->input('var_stok', ''),
                '{toko}'   => config('app.name', 'Toko Ikan Segar'),
                '{tanggal}' => now()->translatedFormat('d F Y'),
            ];
            return urlencode($whatsappTemplate->renderMessage($variables));
        };

        // Normalize phone number helper
        $normalizePhone = function(string $phone): string {
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($phone, '62')) return $phone;
            if (str_starts_with($phone, '0')) return '62' . substr($phone, 1);
            return $phone;
        };

        $links = [];
        foreach ($users as $user) {
            $phone = $normalizePhone($user->no_hp);
            if (!$phone) continue;
            $links[] = [
                'name'  => $user->name,
                'phone' => $user->no_hp,
                'url'   => "https://wa.me/{$phone}?text=" . $encodedMsg($user->name),
            ];
        }

        // Log the blast
        $sampleMsg = $whatsappTemplate->renderMessage([
            '{nama}' => 'Pelanggan', '{produk}' => $request->input('var_produk', ''),
            '{harga}' => $request->input('var_harga', ''), '{stok}' => $request->input('var_stok', ''),
        ]);

        $log = WhatsappBlastLog::create([
            'template_id'     => $whatsappTemplate->id,
            'sent_by'         => Auth::id(),
            'pesan_terkirim'  => $sampleMsg,
            'jumlah_penerima' => count($links),
            'target_phones'   => array_column($links, 'phone'),
        ]);

        $template = $whatsappTemplate;

        return view('admin.whatsapp-templates.blast-result', compact('template', 'links', 'log'));
    }
}
