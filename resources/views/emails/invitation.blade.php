<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación a Chanta Puntos</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 560px; margin: 0 auto; padding: 20px;">
    <p>Hola,</p>
    <p><strong>{{ $inviterName }}</strong> te invitó a sumarte a Chanta Puntos para ver y gestionar los mismos hijos (puntos, canjes, historial).</p>
    <p>Para aceptar la invitación, hacé clic en el enlace de abajo. El enlace es válido por 7 días.</p>
    <p style="margin: 24px 0;">
        <a href="{{ $acceptUrl }}" style="display: inline-block; padding: 12px 24px; background: #4f46e5; color: #fff; text-decoration: none; border-radius: 8px;">Aceptar invitación</a>
    </p>
    <p style="font-size: 14px; color: #666;">Si el botón no funciona, copiá y pegá este enlace en el navegador:</p>
    <p style="font-size: 13px; word-break: break-all; color: #4f46e5;">{{ $acceptUrl }}</p>
    <p style="margin-top: 32px; font-size: 13px; color: #888;">Chanta Puntos</p>
</body>
</html>
