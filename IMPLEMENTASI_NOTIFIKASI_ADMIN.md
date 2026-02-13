# 🔔 IMPLEMENTASI SISTEM NOTIFIKASI ADMIN

## 📋 Overview

Sistem notifikasi real-time untuk admin agar bisa langsung merespon:

- ✅ Pesanan baru
- ⚠️ **Bukti pembayaran (PRIORITAS TINGGI)**
- 💬 Chat baru
- ⭐ Review baru
- 📦 Stok menipis

---

## 1️⃣ DATABASE MIGRATION

### Create Migration

```bash
php artisan make:migration create_admin_notifications_table
```

### Migration File

```php
<?php
// database/migrations/xxxx_create_admin_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'new_order',
                'payment_uploaded',
                'new_chat',
                'new_review',
                'low_stock',
                'order_cancelled'
            ]);
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium');
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable();
            $table->string('color')->default('blue'); // blue, red, green, yellow

            // Polymorphic relation
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type')->nullable();

            // Action URL
            $table->string('action_url')->nullable();
            $table->string('action_text')->default('View');

            // Read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('users');

            $table->timestamps();

            // Index untuk performa
            $table->index(['type', 'is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
```

### Run Migration

```bash
php artisan migrate
```

---

## 2️⃣ MODEL

### Create Model

```bash
php artisan make:model AdminNotification
```

### AdminNotification Model

```php
<?php
// app/Models/AdminNotification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'type',
        'priority',
        'title',
        'message',
        'icon',
        'color',
        'related_id',
        'related_type',
        'action_url',
        'action_text',
        'is_read',
        'read_at',
        'read_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Polymorphic relation
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get unread notifications
     */
    public static function unread()
    {
        return self::where('is_read', false)
                   ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
                   ->orderBy('created_at', 'desc');
    }

    /**
     * Get unread count
     */
    public static function unreadCount(): int
    {
        return self::where('is_read', false)->count();
    }

    /**
     * Get urgent notifications (unread)
     */
    public static function urgent()
    {
        return self::where('is_read', false)
                   ->whereIn('priority', ['urgent', 'high'])
                   ->orderBy('created_at', 'desc');
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => auth()->id(),
        ]);
    }

    /**
     * Mark all as read
     */
    public static function markAllAsRead(): void
    {
        self::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
            'read_by' => auth()->id(),
        ]);
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute($value): string
    {
        if ($value) return $value;

        return match($this->type) {
            'new_order' => 'fas fa-shopping-cart',
            'payment_uploaded' => 'fas fa-money-bill-wave',
            'new_chat' => 'fas fa-comments',
            'new_review' => 'fas fa-star',
            'low_stock' => 'fas fa-exclamation-triangle',
            'order_cancelled' => 'fas fa-times-circle',
            default => 'fas fa-bell',
        };
    }

    /**
     * Get color based on priority
     */
    public function getColorAttribute($value): string
    {
        if ($value) return $value;

        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'medium' => 'blue',
            'low' => 'gray',
            default => 'blue',
        };
    }
}
```

---

## 3️⃣ HELPER SERVICE

### Create NotificationService

```bash
php artisan make:class Services/NotificationService
```

### NotificationService

```php
<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Review;
use App\Models\ChatMessage;

class NotificationService
{
    /**
     * Notifikasi Order Baru
     */
    public static function newOrder(Order $order): void
    {
        AdminNotification::create([
            'type' => 'new_order',
            'priority' => 'medium',
            'title' => 'Pesanan Baru',
            'message' => "Pesanan {$order->order_number} dari {$order->user->name}",
            'related_id' => $order->id,
            'related_type' => Order::class,
            'action_url' => route('admin.orders.show', $order),
            'action_text' => 'Lihat Pesanan',
            'color' => 'blue',
        ]);
    }

    /**
     * Notifikasi Payment Upload (URGENT!)
     */
    public static function paymentUploaded(Order $order): void
    {
        AdminNotification::create([
            'type' => 'payment_uploaded',
            'priority' => 'urgent', // PRIORITAS TINGGI!
            'title' => '⚠️ VERIFIKASI PEMBAYARAN',
            'message' => "Pesanan {$order->order_number} mengunggah bukti pembayaran. Segera verifikasi!",
            'related_id' => $order->id,
            'related_type' => Order::class,
            'action_url' => route('admin.orders.show', $order),
            'action_text' => 'Verifikasi Sekarang',
            'color' => 'red',
        ]);
    }

    /**
     * Notifikasi Chat Baru
     */
    public static function newChat(ChatMessage $message): void
    {
        // Cek apakah pesan dari customer ke admin
        if ($message->receiver->role === 'admin') {
            AdminNotification::create([
                'type' => 'new_chat',
                'priority' => 'medium',
                'title' => 'Pesan Baru',
                'message' => "{$message->sender->name}: " . \Str::limit($message->message, 50),
                'related_id' => $message->id,
                'related_type' => ChatMessage::class,
                'action_url' => route('admin.chat.show', $message->sender),
                'action_text' => 'Balas',
                'color' => 'green',
            ]);
        }
    }

    /**
     * Notifikasi Review Baru
     */
    public static function newReview(Review $review): void
    {
        AdminNotification::create([
            'type' => 'new_review',
            'priority' => 'low',
            'title' => 'Review Baru',
            'message' => "{$review->user->name} memberikan rating {$review->rating}⭐ untuk {$review->produk->nama}",
            'related_id' => $review->id,
            'related_type' => Review::class,
            'action_url' => route('produk.show', $review->produk),
            'action_text' => 'Lihat Review',
            'color' => 'yellow',
        ]);
    }

    /**
     * Notifikasi Stok Menipis
     */
    public static function lowStock(Produk $produk): void
    {
        AdminNotification::create([
            'type' => 'low_stock',
            'priority' => 'high',
            'title' => 'Stok Menipis!',
            'message' => "Stok {$produk->nama} tinggal {$produk->stok} unit. Segera restock!",
            'related_id' => $produk->id,
            'related_type' => Produk::class,
            'action_url' => route('admin.produk.edit', $produk),
            'action_text' => 'Update Stok',
            'color' => 'orange',
        ]);
    }

    /**
     * Notifikasi Order Dibatalkan
     */
    public static function orderCancelled(Order $order): void
    {
        AdminNotification::create([
            'type' => 'order_cancelled',
            'priority' => 'medium',
            'title' => 'Pesanan Dibatalkan',
            'message' => "Pesanan {$order->order_number} dibatalkan oleh {$order->user->name}",
            'related_id' => $order->id,
            'related_type' => Order::class,
            'action_url' => route('admin.orders.show', $order),
            'action_text' => 'Lihat Detail',
            'color' => 'gray',
        ]);
    }
}
```

---

## 4️⃣ INTEGRASIKAN KE CONTROLLER

### Update StoreController.php (Customer Upload Payment)

```php
<?php
// app/Http/Controllers/StoreController.php

use App\Services\NotificationService;

public function uploadPaymentProof(Request $request, Order $order)
{
    // ... validasi & upload file ...

    $order->update([
        'status' => 'waiting_payment',
        'payment_proof' => $path,
        'payment_proof_uploaded_at' => now(),
    ]);

    // 🔔 TRIGGER NOTIFIKASI URGENT!
    NotificationService::paymentUploaded($order);

    return redirect()->route('order.track', $order)
        ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
}
```

### Update StoreController.php (Create Order)

```php
public function checkout(Request $request)
{
    // ... create order logic ...

    // 🔔 Notifikasi order baru
    NotificationService::newOrder($order);

    return redirect()->route('order.success', $order);
}
```

### Update ChatController.php (Customer Send Message)

```php
public function customerSend(Request $request)
{
    // ... create message ...

    // 🔔 Notifikasi chat baru
    NotificationService::newChat($message);

    return response()->json(['status' => 'success']);
}
```

### Update ReviewController.php (Post Review)

```php
public function store(Request $request, Produk $produk)
{
    // ... create review ...

    // 🔔 Notifikasi review baru
    NotificationService::newReview($review);

    return back()->with('success', 'Review berhasil ditambahkan');
}
```

---

## 5️⃣ OBSERVER UNTUK AUTO-TRIGGER

### Create Observer

```bash
php artisan make:observer ProdukObserver --model=Produk
```

### ProdukObserver

```php
<?php
// app/Observers/ProdukObserver.php

namespace App\Observers;

use App\Models\Produk;
use App\Services\NotificationService;

class ProdukObserver
{
    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk): void
    {
        // Cek jika stok berubah dan sekarang <= 10
        if ($produk->isDirty('stok') && $produk->stok <= 10 && $produk->stok > 0) {
            NotificationService::lowStock($produk);
        }
    }
}
```

### Register Observer di AppServiceProvider

```php
<?php
// app/Providers/AppServiceProvider.php

use App\Models\Produk;
use App\Observers\ProdukObserver;

public function boot(): void
{
    Produk::observe(ProdukObserver::class);
}
```

---

## 6️⃣ API ENDPOINT UNTUK NOTIFIKASI

### Create Controller

```bash
php artisan make:controller AdminNotificationController
```

### AdminNotificationController

```php
<?php
// app/Http/Controllers/AdminNotificationController.php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Get unread notifications (JSON untuk AJAX)
     */
    public function getUnread()
    {
        $notifications = AdminNotification::unread()->take(10)->get();

        return response()->json([
            'count' => AdminNotification::unreadCount(),
            'urgent_count' => AdminNotification::urgent()->count(),
            'notifications' => $notifications->map(function($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->type,
                    'priority' => $notif->priority,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'icon' => $notif->icon,
                    'color' => $notif->color,
                    'action_url' => $notif->action_url,
                    'action_text' => $notif->action_text,
                    'created_at' => $notif->created_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['status' => 'success']);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        AdminNotification::markAllAsRead();

        return response()->json(['status' => 'success']);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        AdminNotification::findOrFail($id)->delete();

        return response()->json(['status' => 'success']);
    }
}
```

### Add Routes

```php
// routes/web.php

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // ... existing routes ...

    // Notifications
    Route::get('/notifications/unread', [AdminNotificationController::class, 'getUnread'])
        ->name('notifications.unread');
    Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});
```

---

## 7️⃣ UPDATE ADMIN LAYOUT (SIDEBAR)

### Update layouts/admin.blade.php

```blade
{{-- Bell Icon dengan Badge di Header --}}
<div class="flex items-center gap-4" x-data="{
    showNotifications: false,
    notifications: [],
    unreadCount: 0,
    urgentCount: 0,

    async loadNotifications() {
        try {
            const response = await fetch('{{ route('admin.notifications.unread') }}');
            const data = await response.json();
            this.notifications = data.notifications;
            this.unreadCount = data.count;
            this.urgentCount = data.urgent_count;
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    },

    async markAsRead(id) {
        try {
            await fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            this.loadNotifications();
        } catch (error) {
            console.error('Failed to mark as read:', error);
        }
    },

    async markAllAsRead() {
        try {
            await fetch('{{ route('admin.notifications.readAll') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
            this.loadNotifications();
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    }
}" x-init="
    loadNotifications();
    setInterval(() => loadNotifications(), 30000); // Refresh setiap 30 detik
">

    {{-- Bell Icon --}}
    <button @click="showNotifications = !showNotifications"
            class="relative w-10 h-10 flex items-center justify-center text-white/70 rounded-xl hover:bg-white/10 transition-colors">
        <i class="fas fa-bell text-lg"></i>

        {{-- Badge Counter --}}
        <span x-show="unreadCount > 0"
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              :class="urgentCount > 0 ? 'animate-pulse bg-red-500' : 'bg-orange-500'"
              class="absolute -top-1 -right-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold text-white min-w-[18px] text-center">
        </span>
    </button>

    {{-- Notification Dropdown --}}
    <div x-show="showNotifications"
         @click.away="showNotifications = false"
         x-transition
         class="absolute top-16 right-4 w-96 max-h-[500px] bg-gray-900/95 backdrop-blur-lg border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-50">

        {{-- Header --}}
        <div class="p-4 border-b border-white/10 flex items-center justify-between">
            <h3 class="font-bold text-white">Notifikasi</h3>
            <button @click="markAllAsRead()"
                    class="text-xs text-cyan-400 hover:text-cyan-300">
                Tandai Semua Dibaca
            </button>
        </div>

        {{-- Notification List --}}
        <div class="overflow-y-auto max-h-96">
            <template x-if="notifications.length === 0">
                <div class="p-8 text-center text-white/30">
                    <i class="fas fa-bell-slash text-4xl mb-3"></i>
                    <p>Tidak ada notifikasi baru</p>
                </div>
            </template>

            <template x-for="notif in notifications" :key="notif.id">
                <div class="p-4 border-b border-white/5 hover:bg-white/5 transition-colors">
                    <div class="flex gap-3">
                        {{-- Icon --}}
                        <div :class="`w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-${notif.color}-500/15`">
                            <i :class="notif.icon + ' text-' + notif.color + '-400'"></i>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-semibold text-sm text-white" x-text="notif.title"></p>
                                <span x-show="notif.priority === 'urgent'"
                                      class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500 text-white animate-pulse">
                                    URGENT
                                </span>
                            </div>
                            <p class="text-xs text-white/60 mt-1" x-text="notif.message"></p>
                            <div class="flex items-center gap-2 mt-2">
                                <a :href="notif.action_url"
                                   @click="markAsRead(notif.id)"
                                   class="text-xs text-cyan-400 hover:text-cyan-300 font-semibold">
                                    <span x-text="notif.action_text"></span> →
                                </a>
                                <span class="text-[10px] text-white/40" x-text="notif.created_at"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
```

---

## 8️⃣ TESTING

### Test Notification Creation

```php
// Run in tinker
php artisan tinker

use App\Services\NotificationService;
use App\Models\Order;

$order = Order::first();
NotificationService::paymentUploaded($order);

// Check
\App\Models\AdminNotification::count(); // Should increase
\App\Models\AdminNotification::unreadCount(); // Should show unread
```

### Test API Endpoint

```bash
# Get unread notifications
curl http://localhost/admin/notifications/unread

# Mark as read
curl -X POST http://localhost/admin/notifications/1/read

# Mark all as read
curl -X POST http://localhost/admin/notifications/read-all
```

---

## 9️⃣ BROWSER NOTIFICATION (OPTIONAL)

### Add to admin.blade.php

```javascript
<script>
// Request permission untuk browser notification
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

// Function untuk show browser notification
function showBrowserNotification(title, body, url) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const notification = new Notification(title, {
            body: body,
            icon: '/logo.png',
            badge: '/logo.png',
            tag: 'admin-notification'
        });

notification.onclick = function() {
            window.open(url);
            notification.close();
        };
    }
}

// Polling untuk notifikasi baru
let lastNotificationCount = 0;

setInterval(async () => {
    try {
        const response = await fetch('{{ route('admin.notifications.unread') }}');
        const data = await response.json();

        // Jika ada notifikasi baru
        if (data.count > lastNotificationCount) {
            const newNotifications = data.notifications.filter((_, index) => index < (data.count - lastNotificationCount));

            newNotifications.forEach(notif => {
                // Show browser notification
                showBrowserNotification(
                    notif.title,
                    notif.message,
                    notif.action_url
                );

                // Play sound (optional)
                // const audio = new Audio('/notification.mp3');
                // audio.play();
            });
        }

        lastNotificationCount = data.count;
    } catch (error) {
        console.error('Notification polling error:', error);
    }
}, 30000); // Check every 30 seconds
</script>
```

---

## 🎯 RESULT

Setelah implementasi lengkap, admin akan:

1. ✅ Melihat badge counter real-time di bell icon
2. ⚠️ Mendapat notifikasi URGENT untuk payment verification
3. 🔔 Dropdown notifikasi dengan prioritas (urgent di atas)
4. 📧 Browser notification (jika diaktifkan)
5. 🎯 Langsung klik notifikasi → redirect ke halaman terkait
6. ✅ Mark as read / Mark all as read

**Priority Order:**

```
🔴 URGENT (Red, Animated)
  └── Payment Uploaded

🟠 HIGH (Orange)
  └── Low Stock Alert

🔵 MEDIUM (Blue)
  └── New Order
  └── New Chat
  └── Order Cancelled

⚪ LOW (Gray)
  └── New Review
```

---

## 📞 NEXT STEPS

1. **Test** dengan data real
2. **Customize** warna & icon sesuai brand
3. **Add** email notification untuk critical alerts
4. **Integrate** dengan Laravel Reverb untuk WebSocket (real-time)
5. **Monitor** performa dengan banyak notifikasi

Apakah Anda ingin saya implementasikan kode ini langsung ke aplikasi? 🚀
