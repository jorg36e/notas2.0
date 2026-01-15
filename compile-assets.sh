#!/bin/bash
# INSTRUCCIONES DE COMPILACIÓN Y DESPLIEGUE
# NOTAS 2.0 - Sistema de Gestión de Notas

echo "═══════════════════════════════════════════════════════════"
echo "🎨 NOTAS 2.0 - Compilación de Assets"
echo "═══════════════════════════════════════════════════════════"

echo ""
echo "1️⃣  Instalando dependencias de Node.js..."
npm install

echo ""
echo "2️⃣  Compilando assets para desarrollo..."
npm run dev

# O para producción:
# npm run build

echo ""
echo "3️⃣  Limpiando cachés de Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo ""
echo "4️⃣  Compilando autoloader de Composer..."
composer dump-autoload

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "✅ ¡Compilación completada!"
echo "═══════════════════════════════════════════════════════════"

echo ""
echo "🌐 Accede a tu aplicación en:"
echo "   • Página principal: http://127.0.0.1:8000/"
echo "   • Dashboard: http://127.0.0.1:8000/admin"
echo ""
echo "🔐 Credenciales:"
echo "   • Email: admin@notas.com"
echo "   • Contraseña: 1234567890"
echo ""

# Para modo watch (desarrollo en tiempo real):
# npm run dev -- --watch

# Para producción optimizado:
# npm run build
