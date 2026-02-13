<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FishMarket</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); padding: 40px 30px; text-align: center;">
                            <div style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 30px;">🐟</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">Reset Password</h1>
                            <p style="margin: 10px 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">FishMarket - Toko Ikan Online</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px; color: #1f2937; font-size: 16px; line-height: 1.6;">
                                Halo <strong>{{ $user->name }}</strong>,
                            </p>
                            
                            <p style="margin: 0 0 30px; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Kami menerima permintaan untuk mereset password akun FishMarket Anda. Klik tombol di bawah ini untuk membuat password baru:
                            </p>

                            <!-- Button -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px;">
                                        <a href="{{ $resetLink }}" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #0891b2 0%, #14b8a6 100%); color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(8,145,178,0.3);">
                                            🔐 Reset Password Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Warning Box -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin-bottom: 20px; border-radius: 4px;">
                                <p style="margin: 0 0 8px; color: #92400e; font-size: 14px; font-weight: 600;">
                                    ⏰ Link ini berlaku selama 60 menit
                                </p>
                                <p style="margin: 0; color: #78350f; font-size: 13px; line-height: 1.5;">
                                    Setelah itu, Anda perlu meminta link reset baru.
                                </p>
                            </div>

                            <!-- Security Tips -->
                            <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                                <p style="margin: 0 0 12px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                    🔒 Tips Keamanan:
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 13px; line-height: 1.8;">
                                    <li>Jangan bagikan link ini ke siapapun</li>
                                    <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                                    <li>Password akun Anda tidak akan berubah sampai Anda membuat password baru</li>
                                </ul>
                            </div>

                            <!-- Alternate Link -->
                            <div style="padding: 20px; background-color: #f9fafb; border-radius: 8px; border: 1px dashed #d1d5db;">
                                <p style="margin: 0 0 8px; color: #6b7280; font-size: 12px;">
                                    Jika tombol tidak berfungsi, salin dan tempel link berikut di browser Anda:
                                </p>
                                <p style="margin: 0; word-break: break-all;">
                                    <a href="{{ $resetLink }}" style="color: #0891b2; font-size: 12px; text-decoration: none;">{{ $resetLink }}</a>
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px; color: #1f2937; font-size: 14px; font-weight: 600;">
                                Terima kasih,<br>FishMarket Team 🐟
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                © {{ date('Y') }} FishMarket. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Bottom Note -->
                <p style="margin: 20px 0 0; color: #9ca3af; font-size: 11px; text-align: center; max-width: 600px;">
                    Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
