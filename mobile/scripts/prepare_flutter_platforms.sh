#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

flutter create . --platforms=android,windows --org com.emp

ANDROID_GRADLE="android/app/build.gradle"
ANDROID_MANIFEST="android/app/src/main/AndroidManifest.xml"

if [ -f "$ANDROID_GRADLE" ]; then
  sed -i "s/namespace = .*/namespace = \"com.emp.platform\"/" "$ANDROID_GRADLE"
  sed -i "s/applicationId = .*/applicationId = \"com.emp.platform\"/" "$ANDROID_GRADLE"
  sed -i "s/namespace '.*/namespace 'com.emp.platform'/" "$ANDROID_GRADLE"
  sed -i "s/applicationId '.*/applicationId 'com.emp.platform'/" "$ANDROID_GRADLE"
fi

if [ -f "$ANDROID_MANIFEST" ] && ! grep -q "android.permission.ACCESS_FINE_LOCATION" "$ANDROID_MANIFEST"; then
  sed -i "/<manifest/a\\    <uses-permission android:name=\"android.permission.ACCESS_FINE_LOCATION\" />\\n    <uses-permission android:name=\"android.permission.ACCESS_COARSE_LOCATION\" />\\n    <uses-permission android:name=\"android.permission.CAMERA\" />\\n    <uses-permission android:name=\"android.permission.READ_MEDIA_IMAGES\" />" "$ANDROID_MANIFEST"
fi

flutter pub get

echo "Flutter platforms are ready."
