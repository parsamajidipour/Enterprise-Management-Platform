# Enterprise Management Platform Mobile - Flutter Android

این پوشه نسخه Flutter اپ موبایل تکنسین برای دمو است.

## وضعیت فعلی

- Framework: Flutter
- Platform اصلی دمو: Android APK
- Package name: `com.emp.platform`
- API فعلی دمو: `http://localhost/api`
- APK آماده نصب:

```text
mobile/emp-platform-mobile.apk
```

APK دمو فعلا به صورت موقت از HTTP استفاده می‌کند چون HTTPS دامنه روی پورت 443 آماده نیست. در Android Manifest مقدار `usesCleartextTraffic=true` برای همین دمو فعال شده است.

HTTPS will be enabled in production after SSL reverse proxy setup.

## قابلیت‌های دمو

- Login با Laravel Sanctum
- مشاهده jobs اختصاص داده‌شده به تکنسین
- مشاهده جزئیات work order و asset
- Accept Job
- تغییر وضعیت‌ها:
  - `on_the_way`
  - `arrived`
  - `in_progress`
- ارسال GPS
- آپلود Evidence Photo
- Complete Job با notes و condition score
- مشاهده timeline خلاصه

## Evidence Upload

Backend اکنون هر دو شکل upload را قبول می‌کند:

- `files[]` برای حالت استاندارد چندفایلی
- `file` برای سازگاری با کلاینت‌های ساده یا نسخه‌های قدیمی اپ

اپ Flutter فایل evidence را با فیلد `files[]` ارسال می‌کند.

## نصب APK روی گوشی

اگر گوشی با ADB وصل است:

```bash
cd ~/emp-platform/mobile
adb install -r emp-platform-mobile.apk
```

اگر قبلا نسخه‌ای نصب شده و مشکل دارد:

```bash
adb uninstall com.emp.platform
adb install -r emp-platform-mobile.apk
```

اگر ADB نداری، فایل زیر را به گوشی منتقل کن و دستی نصب کن:

```text
~/emp-platform/mobile/emp-platform-mobile.apk
```

## ساخت APK جدید

اگر Flutter و Android SDK روی سیستم آماده است:

```bash
cd ~/emp-platform/mobile
flutter clean
flutter pub get
flutter build apk --release --dart-define=API_BASE=http://localhost/api
cp build/app/outputs/flutter-apk/app-release.apk emp-platform-mobile.apk
```

در این workspace یک toolchain portable هم زیر `.local-build/` ساخته شده است. اگر Flutter در PATH نبود، از همان تنظیمات shell قبلی استفاده کن یا Flutter را در PATH قرار بده.

## لاگین دمو

بعد از reset و assign شدن job در backend:

```text
tech.north@example.com
password
```

## عیب‌یابی سریع

اگر login خطای handshake داد:

- یعنی اپ با HTTPS ساخته شده یا HTTPS دامنه فعال نیست.
- APK فعلی باید با `http://localhost/api` ساخته شود.

اگر jobs خالی است:

- در backend دستور `demo:reset --assign` اجرا نشده
- یا job به تکنسین دیگری assign شده است

اگر GPS کار نکرد:

- Location permission را روی گوشی Allow کن

اگر Evidence کار نکرد:

- Camera permission را Allow کن
- backend باید نسخه جدید سازگار با `file` و `files[]` باشد
