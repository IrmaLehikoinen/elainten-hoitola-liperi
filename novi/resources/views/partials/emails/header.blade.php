@php
    $brandManager = app(\App\Core\Branding\BrandManager::class);
    $brand = $brandManager->current();
    $company = $brandManager->company();
@endphp
<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #111827; padding: 24px; background: #f9fafb; margin: 0;">
    <div style="max-width: 480px; margin: 0 auto; background: white; border-radius: 8px; padding: 32px; border-top: 4px solid {{ $brand['primary_color'] ?? '#4F46E5' }};">