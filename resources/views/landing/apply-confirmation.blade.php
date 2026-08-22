<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - {{ $siteName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1rem; }
        .conf-box { background: #fff; border-radius: 0.5rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 2rem; max-width: 480px; text-align: center; }
        .conf-box .icon { width: 64px; height: 64px; border-radius: 50%; background: #28a745; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.75rem; }
    </style>
</head>
<body>
    <div class="conf-box">
        <div class="icon"><i class="fas fa-check"></i></div>
        <h5 class="mb-2">Application Submitted</h5>
        <p class="text-muted mb-0">You have filled the form successfully. We will get back to you.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
